<?php

namespace App\Http\Controllers;

use App\Models\Product; // Import Model Product
use App\Models\Offer;   // Import Model Offer
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk DB Transaction

class ProductController extends Controller
{
    // =========================================================================
    // 1. MASTER DATA PRODUK (DAFTAR HARGA)
    // =========================================================================

    // Menampilkan daftar harga (Read) dengan fitur pencarian dan filter dropdown
    public function index(Request $request)
    {
        $query = Product::query();

        // 1. Filter Pencarian (Keyword Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('performa', 'like', "%{$search}%")
                  ->orWhere('hasil_akhir', 'like', "%{$search}%")
                  ->orWhere('kriteria', 'like', "%{$search}%");
            });
        }

        // 2. Filter Dropdown Kriteria
        if ($request->filled('kriteria')) {
            $query->where('kriteria', $request->kriteria);
        }

        // 3. Filter Dropdown Nama Brand (performa)
        if ($request->filled('brand')) {
            $query->where('performa', $request->brand);
        }

        // 4. Filter Dropdown Hasil Akhir
        if ($request->filled('hasil_akhir')) {
            $query->where('hasil_akhir', $request->hasil_akhir);
        }

        $rawProducts = $query->orderBy('nama_produk', 'asc')->get();

        // Kelompokkan produk berdasarkan nama_produk agar di halaman index tetap tampil 1 baris per produk
        $products = $rawProducts->groupBy(function ($item) {
            return strtolower(trim($item->nama_produk));
        })->map(function ($variants) {
            $first = $variants->first();
            return (object) [
                'id'          => $first->id,
                'nama_produk' => $first->nama_produk,
                'performa'    => $first->performa,
                'kriteria'    => $first->kriteria,
                'hasil_akhir' => $first->hasil_akhir,
                'harga'       => $first->harga,
                'stok'        => $variants->sum('stok'),
                'variants'    => $variants,
            ];
        })->values();

        // Opsi unik dari DB untuk Dropdown Filter
        $kriteriaOptions = Product::whereNotNull('kriteria')
            ->where('kriteria', '!=', '')
            ->distinct()
            ->pluck('kriteria')
            ->sort()
            ->values();

        $brandOptions = Product::whereNotNull('performa')
            ->where('performa', '!=', '')
            ->distinct()
            ->pluck('performa')
            ->sort()
            ->values();

        $hasilAkhirOptions = Product::whereNotNull('hasil_akhir')
            ->where('hasil_akhir', '!=', '')
            ->distinct()
            ->pluck('hasil_akhir')
            ->sort()
            ->values();

        return view('harga.index', compact(
            'products',
            'kriteriaOptions',
            'brandOptions',
            'hasilAkhirOptions'
        ));
    }

    // Form tambah harga baru (Create View)
    public function create()
    {
        return view('harga.create');
    }

    // Menyimpan data harga baru (Create Action)
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_produk'       => 'required|string|max:255',
            'performa'          => 'required|string|max:255', // Ini adalah input "Nama Brand"
            'kriteria'          => 'required|string|in:Exterior,Interior',
            'hasil_akhir'       => 'required|string|max:255',
            'harga'             => 'required|integer|min:0',
            'stok'              => 'nullable|integer|min:0',
            'variants'          => 'nullable|array',
            'variants.*.ukuran' => 'nullable|string|max:100',
            'variants.*.stok'   => 'nullable|integer|min:0',
            'variants.*.harga'  => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $variantsInput = $request->input('variants', []);
            $validVariants = array_filter($variantsInput, function ($v) {
                return !empty(trim($v['ukuran'] ?? ''));
            });

            if (!empty($validVariants)) {
                foreach ($validVariants as $v) {
                    Product::create([
                        'nama_produk' => trim($request->nama_produk),
                        'performa'    => trim($request->performa),
                        'kriteria'    => $request->kriteria,
                        'hasil_akhir' => $request->hasil_akhir,
                        'ukuran'      => trim($v['ukuran']),
                        'stok'        => isset($v['stok']) && $v['stok'] !== '' ? (int)$v['stok'] : 0,
                        'harga'       => !empty($v['harga']) ? (int)$v['harga'] : (int)$request->harga,
                    ]);
                }
            } else {
                // Produk tanpa ukuran kemasan khusus
                Product::create([
                    'nama_produk' => trim($request->nama_produk),
                    'performa'    => trim($request->performa),
                    'kriteria'    => $request->kriteria,
                    'hasil_akhir' => $request->hasil_akhir,
                    'ukuran'      => null,
                    'stok'        => $request->stok ?? 0,
                    'harga'       => (int)$request->harga,
                ]);
            }
        });

        // 3. Redirect kembali ke index dengan pesan sukses
        return redirect()->route('harga.index')->with('success', 'Data harga berhasil ditambahkan!');
    }

    // Form edit harga (Edit View)
    public function edit(Product $product)
    {
        $variants = Product::where('nama_produk', $product->nama_produk)
            ->orWhereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower(trim($product->nama_produk))])
            ->orderBy('id', 'asc')
            ->get();

        return view('harga.edit', compact('product', 'variants'));
    }

    // Update data harga (Update Action)
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk'       => 'required|string|max:255',
            'performa'          => 'required|string|max:255',
            'kriteria'          => 'required|string|in:Exterior,Interior',
            'hasil_akhir'       => 'required|string|max:255',
            'harga'             => 'required|integer|min:0',
            'stok'              => 'nullable|integer|min:0',
            'variants'          => 'nullable|array',
            'variants.*.ukuran' => 'nullable|string|max:100',
            'variants.*.stok'   => 'nullable|integer|min:0',
            'variants.*.harga'  => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $product) {
            $oldName = $product->nama_produk;
            $newName = trim($request->nama_produk);

            // Ambil semua variant lama dari produk ini
            $existingVariants = Product::where('nama_produk', $oldName)
                ->orWhereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower(trim($oldName))])
                ->get();

            // Peta stok lama per ukuran agar stok tetap terjaga saat ukuran/harga disunting
            $existingStockMap = $existingVariants->keyBy(function ($item) {
                return strtolower(trim($item->ukuran ?? ''));
            });

            // Hapus variant lama
            Product::whereIn('id', $existingVariants->pluck('id'))->delete();

            // Buat variant baru sesuai input form
            $variantsInput = $request->input('variants', []);
            $validVariants = array_filter($variantsInput, function ($v) {
                return !empty(trim($v['ukuran'] ?? ''));
            });

            if (!empty($validVariants)) {
                foreach ($validVariants as $v) {
                    $uTrim = trim($v['ukuran']);
                    $uKey = strtolower($uTrim);

                    // Pertahankan stok lama jika ukuran ini sudah ada sebelumnya di database
                    $stok = 0;
                    if (isset($v['stok']) && $v['stok'] !== '') {
                        $stok = (int)$v['stok'];
                    } elseif ($existingStockMap->has($uKey)) {
                        $stok = (int)$existingStockMap->get($uKey)->stok;
                    }

                    Product::create([
                        'nama_produk' => $newName,
                        'performa'    => trim($request->performa),
                        'kriteria'    => $request->kriteria,
                        'hasil_akhir' => $request->hasil_akhir,
                        'ukuran'      => $uTrim,
                        'stok'        => $stok,
                        'harga'       => !empty($v['harga']) ? (int)$v['harga'] : (int)$request->harga,
                    ]);
                }
            } else {
                $stok = isset($request->stok) && $request->stok !== '' ? (int)$request->stok : ($existingVariants->first()->stok ?? 0);
                Product::create([
                    'nama_produk' => $newName,
                    'performa'    => trim($request->performa),
                    'kriteria'    => $request->kriteria,
                    'hasil_akhir' => $request->hasil_akhir,
                    'ukuran'      => null,
                    'stok'        => $stok,
                    'harga'       => (int)$request->harga,
                ]);
            }
        });

        return redirect()->route('harga.index')->with('success', 'Data harga berhasil diperbarui!');
    }

    // Menghapus data harga (Delete Action)
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            Product::where('nama_produk', $product->nama_produk)
                ->orWhereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower(trim($product->nama_produk))])
                ->delete();
        });

        return redirect()->route('harga.index')->with('success', 'Data produk beserta seluruh varian ukurannya berhasil dihapus!');
    }

    // =========================================================================
    // 2. LOGIKA PENAWARAN PROYEK (COMBINED)
    // =========================================================================
    // Catatan: Logika ini khusus untuk membuat Offer Tipe Proyek
    // yang menggabungkan Produk & Jasa secara manual.

    public function createCombined(Request $request)
    {
        $token = Str::uuid()->toString();
        $request->session()->put('form_token', $token);
        $products = Product::all();

        // Pastikan view ini ada
        return view('penawaran.create_combined', ['products' => $products]);
    }

    public function storeCombined(Request $request)
    {
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'perihal'    => 'nullable|string|max:255',
        ]);

        // Hitung total di backend
        $totalProduk = 0;
        if ($request->has('produk')) {
            foreach ($request->produk as $item) {
                $totalProduk += ($item['volume'] ?? 0) * ($item['harga'] ?? 0);
            }
        }

        $totalJasa = 0;
        if ($request->has('jasa')) {
            foreach ($request->jasa as $item) {
                $totalJasa += ((float)($item['volume'] ?? 0) * (float)($item['harga'] ?? 0));
            }
        }

        DB::beginTransaction(); // Gunakan transaksi database agar aman
        try {
            // 1. Simpan Offer Utama
            $offer = Offer::create([
                'nama_klien'            => $request->nama_klien,
                'client_details'        => $request->client_details,
                'perihal'               => $request->perihal ?? 'Penawaran Jasa Apply dan Supply Pengecatan',
                'total_keseluruhan'     => $totalProduk + $totalJasa,
                'pisah_kriteria_total'  => $request->has('pisah_kriteria_total') ? 1 : 0,
                'hilangkan_grand_total' => $request->has('hilangkan_grand_total') ? 1 : 0,
                'opsi_paket'            => $request->has('opsi_paket') ? 1 : 0,
                'tampilkan_detail'      => $request->has('tampilkan_detail') ? 1 : 0,
                'jenis_penawaran'       => 'proyek', // Tambahkan penanda jenis jika perlu
            ]);

            // 2. Simpan Item Produk
            if ($request->has('produk')) {
                foreach ($request->produk as $itemData) {
                    if (!empty($itemData['nama'])) {
                        $offer->items()->create([
                            'nama_produk'  => $itemData['nama'],
                            'area_dinding' => $itemData['area'],
                            'volume'       => $itemData['volume'] ?? 0,
                            'harga_per_m2' => $itemData['harga'] ?? 0,
                        ]);
                    }
                }
            }

            // 3. Simpan Item Jasa
            if ($request->has('jasa')) {
                foreach ($request->jasa as $jasaData) {
                    if (!empty($jasaData['nama'])) {
                        $vJ = (float) ($jasaData['volume'] ?? 0);
                        $hJ = (float) ($jasaData['harga'] ?? 0);
                        $offer->jasaItems()->create([
                            'nama_jasa'    => $jasaData['nama'],
                            'volume'       => $vJ,
                            'satuan'       => $jasaData['satuan'] ?? 'Ls',
                            'harga_satuan' => $hJ,
                            'harga_jasa'   => $vJ * $hJ,
                        ]);
                    }
                }
            }

            DB::commit();
            // Redirect ke histori
            return redirect()->route('histori.index')->with('success', 'Surat Penawaran Proyek berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}