<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductOfferController extends Controller
{
    /**
     * Menampilkan form pembuatan penawaran PRODUK
     */
    public function create()
    {
        // Urutkan produk berdasarkan nama agar rapi di dropdown
        $products = Product::orderBy('nama_produk', 'asc')->get();
        return view('penawaran.create_product', compact('products'));
    }

    /**
     * Menyimpan data penawaran PRODUK ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama_klien'            => 'required|string|max:255',
            'perihal'               => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',

            // PERUBAHAN: nama_produk sekarang string biasa, bukan ID
            'items.*.nama_produk'   => 'required|string|max:255',

            'items.*.qty'           => 'required|numeric|min:1',
            'items.*.harga_satuan'  => 'required|numeric|min:0',
            'diskon_global'         => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 2. Simpan Header
            $offer = Offer::create([
                'nama_klien'            => $request->nama_klien,
                'client_details'        => $request->client_details,
                'perihal'               => $request->perihal ?? 'Penawaran supply produk cat Jotun',
                'created_at'            => $request->tanggal,
                'jenis_penawaran'       => 'produk',
                'pisah_kriteria_total'  => $request->has('pisah_kriteria_total'),
                'hilangkan_grand_total' => $request->has('hilangkan_grand_total'),
                'diskon_global'         => $request->diskon_global ?? 0,
                'total_keseluruhan'     => 0,
            ]);

            $grandTotal = 0;

            // 3. Loop Item
            foreach ($request->items as $itemData) {

                // PERUBAHAN: Tidak lagi mencari Product::findOrFail() karena input bebas
                $namaProduk  = $itemData['nama_produk'];

                $hargaSatuan = $itemData['harga_satuan'];
                $qty         = $itemData['qty'];
                $kodeWarna   = $itemData['kode_warna'] ?? '-';
                $keterangan  = $itemData['keterangan'] ?? '-';
                $ukuran      = $itemData['ukuran'] ?? '-';

                $subtotalBaris = ($hargaSatuan * $qty);
                if ($subtotalBaris < 0) $subtotalBaris = 0;

                $grandTotal += $subtotalBaris;

                OfferItem::create([
                    'offer_id'           => $offer->id,
                    'nama_produk'        => $namaProduk, // Simpan string inputan user
                    'harga_per_m2'       => $hargaSatuan,
                    'volume'             => $qty,
                    'area_dinding'       => $ukuran,
                    'warna'              => $kodeWarna,
                    'keterangan'         => $keterangan,
                ]);
            }

            $grandTotal -= ($request->diskon_global ?? 0);
            if ($grandTotal < 0) $grandTotal = 0;

            $offer->update(['total_keseluruhan' => $grandTotal]);

            DB::commit();
            return redirect()->route('histori.index')->with('success', 'Penawaran Produk berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $offer = Offer::with('items')->findOrFail($id);
        $products = Product::orderBy('nama_produk', 'asc')->get();

        // Kita arahkan ke view edit khusus produk
        return view('penawaran.edit_product', compact('offer', 'products'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi (Sama seperti store)
        $request->validate([
            'nama_klien'            => 'required|string|max:255',
            'perihal'               => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.nama_produk'   => 'required|string|max:255',
            'items.*.qty'           => 'required|numeric|min:1',
            'items.*.harga_satuan'  => 'required|numeric|min:0',
            'diskon_global'         => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $offer = Offer::findOrFail($id);

            // 2. Update Header Offer
            $offer->update([
                'nama_klien'            => $request->nama_klien,
                'client_details'        => $request->client_details,
                'perihal'               => $request->perihal ?? 'Penawaran supply produk cat Jotun',
                'created_at'            => $request->tanggal, // Bisa update tanggal juga
                'pisah_kriteria_total'  => $request->has('pisah_kriteria_total'),
                'hilangkan_grand_total' => $request->has('hilangkan_grand_total'),
                'diskon_global'         => $request->diskon_global ?? 0,
                // total_keseluruhan dihitung ulang di bawah
            ]);

            // 3. Hapus Item Lama & Ganti Baru (Cara paling aman untuk update detail)
            $offer->items()->delete();

            $grandTotal = 0;

            // 4. Loop Simpan Item Baru
            foreach ($request->items as $itemData) {
                $namaProduk  = $itemData['nama_produk'];
                $hargaSatuan = $itemData['harga_satuan'];
                $qty         = $itemData['qty'];
                $kodeWarna   = $itemData['kode_warna'] ?? '-';
                $keterangan  = $itemData['keterangan'] ?? '-';
                $ukuran      = $itemData['ukuran'] ?? '-';

                $subtotalBaris = ($hargaSatuan * $qty);
                if ($subtotalBaris < 0) $subtotalBaris = 0;

                $grandTotal += $subtotalBaris;

                OfferItem::create([
                    'offer_id'           => $offer->id,
                    'nama_produk'        => $namaProduk,
                    'harga_per_m2'       => $hargaSatuan,
                    'volume'             => $qty,
                    'area_dinding'       => $ukuran,
                    'warna'              => $kodeWarna,
                    'keterangan'         => $keterangan,
                ]);
            }

            // 5. Update Grand Total Akhir
            $grandTotal -= ($request->diskon_global ?? 0);
            if ($grandTotal < 0) $grandTotal = 0;

            $offer->update(['total_keseluruhan' => $grandTotal]);

            DB::commit();
            return redirect()->route('histori.index')->with('success', 'Penawaran Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
