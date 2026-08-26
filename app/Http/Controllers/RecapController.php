<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Recap;
use App\Models\RecapItem;
use Illuminate\Support\Facades\DB;

class RecapController extends Controller
{
    /**
     * Tampilkan daftar semua rekapan yang pernah dibuat.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $recaps = Recap::with('offer')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('offer', function ($q) use ($search) {
                    $q->where('nama_klien', 'like', "%$search%");
                })->orWhere('no_recap', 'like', "%$search%");
            })
            ->latest()
            ->paginate(15);

        return view('recap.index', compact('recaps', 'search'));
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
        $offer->load(['items', 'jasaItems']);

        // Hitung total penawaran klien untuk referensi
        $totalProduk = $offer->items->sum(fn($i) => $i->volume * $i->harga_per_m2);
        $totalJasa = $offer->jasaItems->sum('harga_jasa');
        $grandTotalOffer = $totalProduk + $totalJasa;

        return view('recap.create', compact('offer', 'grandTotalOffer'));
    }

    /**
     * Simpan data rekapan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'offer_id' => 'required',
            'items' => 'required|array',
        ]);

        $recap = Recap::create([
            'offer_id' => $request->offer_id,
            'tanggal_keluar' => $request->tanggal_keluar,
            'total_penawaran_klien' => $request->total_penawaran_klien,
            'total_pengeluaran' => $request->total_pengeluaran,
            'margin' => $request->total_penawaran_klien - $request->total_pengeluaran,
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['material'])) {
                $recap->items()->create([
                    'tanggal_item' => $item['tanggal_item'] ?? null, // PERBAIKAN: Pastikan ini ada
                    'material'     => $item['material'],
                    'detail'       => $item['detail'],
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
        return view('recap.edit', compact('recap'));
    }

    public function update(Request $request, $id)
    {
        // Cari rekapan utama
        $recap = Recap::findOrFail($id);

        // Update Data Kepala Rekapan
        $recap->update([
            'total_penawaran_klien' => $request->total_penawaran_klien,
            'total_pengeluaran' => $request->total_pengeluaran,
            'margin' => $request->total_penawaran_klien - $request->total_pengeluaran,
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
                        'detail'       => $item['detail'],
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
