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
        // Eager load untuk performa: ambil recap, items, dan offer sekaligus
        $recap = Recap::with(['items', 'offer'])->findOrFail($id);

        return view('recap.show', compact('recap'));
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

        return redirect()->route('recap.index')->with('success', 'Rekapan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $recap = Recap::findOrFail($id);
        $recap->delete(); // Karena 'cascade' di migration, items otomatis terhapus

        return redirect()->route('recap.index')->with('success', 'Rekapan berhasil dihapus!');
    }
}
