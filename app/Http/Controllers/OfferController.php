<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Offer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_klien', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        $offers = $query->latest()->paginate(15);
        return view('histori.index', compact('offers', 'search'));
    }

    public function create_combined()
    {
        $offer = new Offer();
        $products = Product::all();
        return view('penawaran.create-combine', compact('offer', 'products'));
    }

    public function store_combined(Request $request)
    {
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'client_details' => 'nullable|string',
            'produk.*.nama' => 'nullable|string',
            'jasa.*.nama' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $totalProduk = 0;
            $totalJasa = 0;

            // 1. Hitung Total Produk
            if ($request->has('produk')) {
                foreach ($request->produk as $p) {
                    if (!empty($p['nama'])) {
                        $totalProduk += ((float)($p['volume'] ?? 0) * (float)($p['harga'] ?? 0));
                    }
                }
            }

            // 2. Hitung Total Jasa
            if ($request->has('jasa')) {
                foreach ($request->jasa as $j) {
                    if (!empty($j['nama'])) {
                        $totalJasa += ((float)($j['volume'] ?? 0) * (float)($j['harga'] ?? 0));
                    }
                }
            }

            // 3. Simpan Offer Utama
            $offer = Offer::create([
                'nama_klien'            => $request->nama_klien,
                'client_details'        => $request->client_details,
                'total_keseluruhan'     => $totalProduk + $totalJasa,
                'pisah_kriteria_total'  => $request->has('pisah_kriteria_total') ? 1 : 0,
                'hilangkan_grand_total' => $request->has('hilangkan_grand_total') ? 1 : 0,
                'jenis_penawaran'       => 'jasa',
            ]);

            // 4. Simpan Item Produk
            if ($request->has('produk')) {
                foreach ($request->produk as $pData) {
                    if (!empty($pData['nama'])) {
                        $offer->items()->create([
                            'nama_produk'  => $pData['nama'],
                            'area_dinding' => $pData['area'] ?? '',
                            'volume'       => (float)($pData['volume'] ?? 0),
                            'harga_per_m2' => (float)($pData['harga'] ?? 0),
                        ]);
                    }
                }
            }

            // 5. Simpan Item Jasa
            if ($request->has('jasa')) {
                foreach ($request->jasa as $jData) {
                    if (!empty($jData['nama'])) {
                        $vJ = (float) ($jData['volume'] ?? 0);
                        $hJ = (float) ($jData['harga'] ?? 0);
                        $offer->jasaItems()->create([
                            'nama_jasa'    => $jData['nama'],
                            'volume'       => $vJ,
                            'satuan'       => $jData['satuan'] ?? 'Ls',
                            'harga_satuan' => $hJ,
                            'harga_jasa'   => $vJ * $hJ,
                        ]);
                    }
                }
            }

            return redirect()->route('histori.index')->with('success', 'Penawaran berhasil dibuat!');
        });
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'nama_klien' => 'required|string|max:255',
            'client_details' => 'nullable|string',
            'perihal' => 'nullable|string|max:255',
            'produk.*.nama' => 'nullable|string',
            'jasa.*.nama' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $offer) {
            $totalProduk = 0;
            $totalJasa = 0;

            // Hitung Total
            if ($request->has('produk')) {
                foreach ($request->produk as $p) {
                    if (!empty($p['nama'])) {
                        $totalProduk += ((float)($p['volume'] ?? 0) * (float)($p['harga'] ?? 0));
                    }
                }
            }

            if ($request->has('jasa')) {
                foreach ($request->jasa as $j) {
                    if (!empty($j['nama'])) {
                        $totalJasa += ((float)($j['volume'] ?? 0) * (float)($j['harga'] ?? 0));
                    }
                }
            }

            $data = [
                'nama_klien'            => $request->nama_klien,
                'client_details'        => $request->client_details,
                'perihal'               => $request->perihal ?? 'Penawaran Jasa Apply dan Supply Pengecatan',
                'total_keseluruhan'     => $totalProduk + $totalJasa,
                'pisah_kriteria_total'  => $request->has('pisah_kriteria_total') ? 1 : 0,
                'hilangkan_grand_total' => $request->has('hilangkan_grand_total') ? 1 : 0,
                'opsi_paket'            => $request->has('opsi_paket') ? 1 : 0,
                'jenis_penawaran'       => $offer->jenis_penawaran ?? 'jasa',
            ];

            if ($request->input('action') == 'save_and_copy') {
                if ($data['nama_klien'] === $offer->nama_klien) {
                    $data['nama_klien'] .= ' (Copy)';
                }
                $targetOffer = Offer::create($data);
                $message = 'Data berhasil disalin sebagai Penawaran Baru!';
            } else {
                $offer->update($data);
                $targetOffer = $offer;
                $targetOffer->items()->delete();
                $targetOffer->jasaItems()->delete();
                $message = 'Surat penawaran berhasil diperbarui.';
            }

            // Simpan Item Produk ke $targetOffer
            if ($request->has('produk')) {
                foreach ($request->produk as $pData) {
                    if (!empty($pData['nama'])) {
                        $targetOffer->items()->create([
                            'nama_produk'  => $pData['nama'],
                            'area_dinding' => $pData['area'] ?? '',
                            'volume'       => (float)($pData['volume'] ?? 0),
                            'harga_per_m2' => (float)($pData['harga'] ?? 0),
                        ]);
                    }
                }
            }

            // Simpan Item Jasa ke $targetOffer
            if ($request->has('jasa')) {
                foreach ($request->jasa as $jData) {
                    if (!empty($jData['nama'])) {
                        $vJ = (float) ($jData['volume'] ?? 0);
                        $hJ = (float) ($jData['harga'] ?? 0);
                        $targetOffer->jasaItems()->create([
                            'nama_jasa'    => $jData['nama'],
                            'volume'       => $vJ,
                            'satuan'       => $jData['satuan'] ?? 'Ls',
                            'harga_satuan' => $hJ,
                            'harga_jasa'   => $vJ * $hJ,
                        ]);
                    }
                }
            }

            if ($request->input('action') == 'save_and_copy') {
                return redirect()->route('histori.edit', $targetOffer->id)->with('success', $message);
            }
            return redirect()->route('histori.index')->with('success', $message);
        });
    }

    // ... method lainnya (show, edit, destroy, print) tetap sama ...
    public function show($id)
    {
        $offer = Offer::with(['items', 'jasaItems'])->findOrFail($id);
        $view = ($offer->jenis_penawaran == 'produk') ? 'histori.show_product' : 'histori.show';
        return view($view, compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $offer->load(['items', 'jasaItems']);
        $all_products = Product::all();
        return view('histori.edit', compact('offer', 'all_products'));
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('histori.index')->with('success', 'Data penawaran berhasil dihapus!');
    }

    public function print($id)
    {
        $offer = Offer::with(['items', 'jasaItems'])->findOrFail($id);
        return view('histori.print', compact('offer'));
    }
}