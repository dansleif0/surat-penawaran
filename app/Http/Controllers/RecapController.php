<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Recap;
use App\Models\RecapItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class RecapController extends Controller
{
    /**
     * Tampilkan daftar semua rekapan yang pernah dibuat.
     */
    public function index(Request $request)
    {
        // Auto-verifikasi rekapan yang hanya memuat selain produk terdaftar (jika ada data baru/belum tersinkron)
        Recap::where(function ($q) {
            $q->where('status', 'belum_dikonfirmasi')->orWhereNull('status');
        })->whereNull('confirmed_at')
        ->with('items')
        ->get()
        ->each(function ($r) {
            if (!$r->hasRegisteredProducts()) {
                $r->update([
                    'status'       => 'terkonfirmasi',
                    'confirmed_at' => $r->created_at ?? now(),
                ]);
            }
        });

        $search = $request->input('search');
        $query = Recap::with(['offer', 'items']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('offer', function ($o) use ($search) {
                    $o->where('nama_klien', 'like', "%{$search}%")
                      ->orWhere('client_details', 'like', "%{$search}%");
                })->orWhere('id', $search)
                  ->orWhere('offer_id', $search);
            });
        }

        $totalOmzet = (clone $query)->sum('total_penawaran_klien');
        $totalPengeluaran = (clone $query)->sum('total_pengeluaran');
        $totalMargin = (clone $query)->sum('margin');
        $totalProyek = (clone $query)->count();

        $recaps = $query->latest()->paginate(15)->withQueryString();

        return view('recap.index', compact(
            'recaps',
            'search',
            'totalOmzet',
            'totalPengeluaran',
            'totalMargin',
            'totalProyek'
        ));
    }

    /**
     * Form untuk membuat rekapan berdasarkan penawaran tertentu.
     */

    public function show($id)
    {
        // Eager load untuk performa: ambil recap, items, offer, dan confirmer
        $recap = Recap::with(['items', 'offer', 'confirmer'])->findOrFail($id);

        // Ambil daftar produk terdaftar pada rekapan ini beserta info stoknya
        $totalNeededByProduct = [];
        foreach ($recap->items as $item) {
            $product = Product::findVariant($item->material, $item->ukuran);
            if ($product) {
                $pid = $product->id;
                $qty = (int) round((float) ($item->qty ?? 0));
                $totalNeededByProduct[$pid] = ($totalNeededByProduct[$pid] ?? 0) + $qty;
            }
        }

        $registeredItems = [];
        $unregisteredItems = [];
        $hasInsufficientStock = false;

        foreach ($recap->items as $item) {
            $product = Product::findVariant($item->material, $item->ukuran);
            if ($product) {
                $qtyButuh = (int) round((float) ($item->qty ?? 0));
                $stokSekarang = (int) $product->stok;
                $totalDibutuhkan = $totalNeededByProduct[$product->id] ?? $qtyButuh;
                $stokCukup = $stokSekarang >= $totalDibutuhkan;
                if (!$stokCukup) {
                    $hasInsufficientStock = true;
                }

                $registeredItems[] = [
                    'item'             => $item,
                    'product'          => $product,
                    'stok_sekarang'    => $stokSekarang,
                    'qty_butuh'        => $qtyButuh,
                    'total_dibutuhkan' => $totalDibutuhkan,
                    'stok_cukup'       => $stokCukup,
                ];
            } else {
                $unregisteredItems[] = $item;
            }
        }

        return view('recap.show', compact('recap', 'registeredItems', 'unregisteredItems', 'hasInsufficientStock'));
    }

    /**
     * Konfirmasi rekapan proyek oleh Staff Gudang (atau Admin).
     * Secara otomatis memotong stok produk yang telah terdaftar di database.
     */
    public function konfirmasi(Request $request, $id)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['staff_gudang', 'admin'])) {
            return redirect()->route('recap.show', $id)
                ->with('error', 'Hanya Staff Gudang atau Administrator yang dapat mengonfirmasi pengeluaran rekapan proyek.');
        }

        $recap = Recap::with(['items', 'offer'])->findOrFail($id);

        if ($recap->isConfirmed()) {
            return redirect()->route('recap.show', $id)
                ->with('error', 'Rekapan proyek ini sudah pernah dikonfirmasi sebelumnya.');
        }

        // 1. Validasi kecukupan stok sebelum pemotongan (dengan agregasi per produk)
        $aggregatedProducts = [];
        foreach ($recap->items as $item) {
            if (empty($item->material)) {
                continue;
            }

            $qty = (int) round((float) ($item->qty ?? 0));
            if ($qty <= 0) {
                continue;
            }

            $product = Product::findVariant($item->material, $item->ukuran);
            if ($product) {
                $pid = $product->id;
                if (!isset($aggregatedProducts[$pid])) {
                    $ukuranTampil = $product->ukuran ?: ($item->ukuran ?: '-');
                    $aggregatedProducts[$pid] = [
                        'product'      => $product,
                        'total_qty'    => 0,
                        'ukuranTampil' => $ukuranTampil,
                    ];
                }
                $aggregatedProducts[$pid]['total_qty'] += $qty;
            }
        }

        $insufficientProducts = [];
        $productsToDeduct = [];

        foreach ($aggregatedProducts as $pData) {
            $product = $pData['product'];
            $neededQty = $pData['total_qty'];
            $stokSekarang = (int) $product->stok;
            $ukText = ($pData['ukuranTampil'] && $pData['ukuranTampil'] !== '-') ? " ({$pData['ukuranTampil']})" : '';

            if ($stokSekarang < $neededQty) {
                $insufficientProducts[] = "• {$product->nama_produk}{$ukText}: Dibutuhkan {$neededQty}, Stok Tersedia hanya {$stokSekarang}";
            } else {
                $productsToDeduct[] = [
                    'product'      => $product,
                    'qty'          => $neededQty,
                    'ukuranTampil' => $pData['ukuranTampil'],
                ];
            }
        }

        // Jika ada produk yang stoknya tidak cukup, TOLAK konfirmasi
        if (!empty($insufficientProducts)) {
            $errorMsg = "Konfirmasi gagal! Stok gudang tidak mencukupi untuk item berikut:\n"
                . implode("\n", $insufficientProducts)
                . "\nSilakan perbarui atau restock barang terlebih dahulu sebelum melakukan konfirmasi.";

            return redirect()->route('recap.show', $id)->with('error', $errorMsg);
        }

        $deductedProducts = [];

        DB::transaction(function () use ($recap, $productsToDeduct, &$deductedProducts) {
            foreach ($productsToDeduct as $pData) {
                $product = $pData['product'];
                $qty = $pData['qty'];
                $stokAwal = (int) $product->stok;
                $product->decrement('stok', $qty);
                $stokSisa = (int) $product->fresh()->stok;

                $deductedProducts[] = [
                    'nama'       => $product->nama_produk,
                    'ukuran'     => $pData['ukuranTampil'],
                    'qty'        => $qty,
                    'stok_awal'  => $stokAwal,
                    'stok_sisa'  => $stokSisa,
                ];
            }

            $recap->update([
                'status'       => 'terkonfirmasi',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);
        });

        if (!empty($deductedProducts)) {
            $details = array_map(function ($d) {
                $uk = $d['ukuran'] && $d['ukuran'] !== '-' ? " ({$d['ukuran']})" : '';
                return "• {$d['nama']}{$uk}: dipotong {$d['qty']} (Sisa stok gudang: {$d['stok_sisa']})";
            }, $deductedProducts);

            $msg = "Rekapan proyek berhasil dikonfirmasi! Stok untuk " . count($deductedProducts) . " produk terdaftar telah dipotong:\n" . implode("\n", $details);
        } else {
            $msg = "Rekapan proyek berhasil dikonfirmasi menjadi 'Terkonfirmasi'. Tidak ada pemotongan stok karena tidak terdapat produk yang terdaftar pada rekapan ini.";
        }

        return redirect()->route('recap.show', $recap->id)->with('success', $msg);
    }

    public function print($id)
    {
        $recap = Recap::with(['items', 'offer'])->findOrFail($id);

        return view('recap.print', compact('recap'));
    }

    // Tambahkan di dalam RecapController

    public function exportExcel($id)
    {
        $recap = Recap::with(['items', 'offer'])->findOrFail($id);
        //dd($recap->items->pluck('tanggal_item'));

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Rekap_Biaya_#{$recap->id}.xls");

        return view('recap.export_template', compact('recap'));
    }

    public function exportWord($id)
    {
        $recap = Recap::with(['items', 'offer'])->findOrFail($id);

        header("Content-Type: application/vnd.ms-word");
        header("Content-Disposition: attachment; filename=Rekap_Biaya_#{$recap->id}.doc");

        return view('recap.export_template', compact('recap'));
    }

    public function create(Offer $offer)
    {
        if ($offer->jenis_penawaran === 'produk') {
            return redirect()->route('histori.index')->with('error', 'Rekapan proyek hanya tersedia untuk surat penawaran jenis proyek.');
        }

        $offer->load(['items', 'jasaItems']);

        // Hitung total penawaran klien untuk referensi
        $totalProduk = $offer->items->sum(fn($i) => (float)$i->volume * (float)$i->harga_per_m2);
        $totalJasa = $offer->jasaItems->sum(fn($j) => (float)($j->harga_jasa ?? ((float)$j->volume * (float)$j->harga_satuan)));
        $grandTotalOffer = (float)($offer->total_keseluruhan ?: ($totalProduk + $totalJasa));

        $products = Product::orderBy('nama_produk')->get();

        return view('recap.create', compact('offer', 'grandTotalOffer', 'products'));
    }

    /**
     * Simpan data rekapan baru ke database.
     */
    public function store(Request $request)
    {
        $offer = Offer::findOrFail($request->offer_id);
        if ($offer->jenis_penawaran === 'produk') {
            return redirect()->route('histori.index')->with('error', 'Rekapan proyek hanya tersedia untuk surat penawaran jenis proyek.');
        }

        $request->validate([
            'offer_id' => 'required',
            'items' => 'required|array',
        ]);

        $totalPenawaran = (float)($request->total_penawaran_klien ?? 0);
        $totalPengeluaran = (float)($request->total_pengeluaran ?? 0);

        // Jika total pengeluaran 0 di request, hitung otomatis dari items
        if ($totalPengeluaran <= 0 && is_array($request->items)) {
            foreach ($request->items as $it) {
                if (!empty($it['material'])) {
                    $totalPengeluaran += ((float)($it['harga'] ?? 0) * (float)($it['qty'] ?? 1));
                }
            }
        }

        $tanggalKeluar = $request->tanggal_keluar;
        if (!$tanggalKeluar && is_array($request->items)) {
            foreach ($request->items as $it) {
                if (!empty($it['tanggal_item'])) {
                    $tanggalKeluar = $it['tanggal_item'];
                    break;
                }
            }
        }

        $recap = Recap::create([
            'offer_id' => $request->offer_id,
            'tanggal_keluar' => $tanggalKeluar ?: now()->toDateString(),
            'total_penawaran_klien' => $totalPenawaran,
            'total_pengeluaran' => $totalPengeluaran,
            'margin' => $totalPenawaran - $totalPengeluaran,
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['material'])) {
                $recap->items()->create([
                    'tanggal_item' => $item['tanggal_item'] ?? null,
                    'material'     => $item['material'],
                    'kategori'     => $item['kategori'] ?? null,
                    'ukuran'       => $item['ukuran'] ?? null,
                    'detail'       => $item['detail'] ?? null,
                    'harga'        => $item['harga'] ?? 0,
                    'qty'          => $item['qty'] ?? 0,
                    'subtotal'     => ($item['harga'] ?? 0) * ($item['qty'] ?? 0),
                ]);
            }
        }

        // Jika rekapan hanya memuat selain produk terdaftar (non-stok), otomatis terverifikasi
        $recap->load('items');
        if (!$recap->hasRegisteredProducts()) {
            $recap->update([
                'status'       => 'terkonfirmasi',
                'confirmed_at' => now(),
            ]);
        }

        return redirect()->route('recap.index')->with('success', 'Rekapan Berhasil Disimpan!');
    }
    /**
     * Hapus rekapan.
     */
    public function edit($id)
    {
        $recap = Recap::with(['items', 'offer'])->findOrFail($id);
        $products = Product::orderBy('nama_produk')->get();
        return view('recap.edit', compact('recap', 'products'));
    }

    public function update(Request $request, $id)
    {
        // Cari rekapan utama
        $recap = Recap::findOrFail($id);

        $totalPenawaran = (float)($request->total_penawaran_klien ?? $recap->total_penawaran_klien ?? 0);
        $totalPengeluaran = (float)($request->total_pengeluaran ?? 0);

        if ($totalPengeluaran <= 0 && is_array($request->items)) {
            foreach ($request->items as $it) {
                if (!empty($it['material'])) {
                    $totalPengeluaran += ((float)($it['harga'] ?? 0) * (float)($it['qty'] ?? 1));
                }
            }
        }

        // Update Data Kepala Rekapan
        $recap->update([
            'total_penawaran_klien' => $totalPenawaran,
            'total_pengeluaran' => $totalPengeluaran,
            'margin' => $totalPenawaran - $totalPengeluaran,
        ]);

        // Hapus semua item lama terlebih dahulu (Sinkronisasi Item)
        $recap->items()->delete();

        // Masukkan kembali item dari form (Termasuk data baru & data hasil edit)
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                // Pastikan material tidak kosong sebelum insert
                if (!empty($item['material'])) {
                    $recap->items()->create([
                        'tanggal_item' => $item['tanggal_item'] ?? null,
                        'material'     => $item['material'],
                        'kategori'     => $item['kategori'] ?? null,
                        'ukuran'       => $item['ukuran'] ?? null,
                        'detail'       => $item['detail'] ?? null,
                        'harga'        => $item['harga'] ?? 0,
                        'qty'          => $item['qty'] ?? 1,
                        'subtotal'     => ($item['harga'] ?? 0) * ($item['qty'] ?? 1),
                    ]);
                }
            }
        }

        // Jika rekapan hanya memuat selain produk terdaftar (non-stok), otomatis terverifikasi
        $recap->load('items');
        if (!$recap->hasRegisteredProducts()) {
            $recap->update([
                'status'       => 'terkonfirmasi',
                'confirmed_at' => $recap->confirmed_at ?? now(),
            ]);
        }

        return redirect()->route('recap.index')->with('success', 'Rekapan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $recap = Recap::findOrFail($id);
        $recap->delete(); // Karena 'cascade' di migration, items otomatis terhapus

        return redirect()->route('recap.index')->with('success', 'Rekapan berhasil dihapus!');
    }
}
