<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Invoice;
use App\Models\Product; // Anda meng-import ini, pastikan modelnya ada jika digunakan

class InvoiceController extends Controller
{
    /**
     * Menampilkan halaman histori dari semua invoice.
     */
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian
        $search = $request->input('search');

        // Mulai query ke model Invoice
        $query = Invoice::query();

        // Jika ada pencarian, filter berdasarkan nama klien atau no. invoice
        if ($search) {
            $query->where('nama_klien', 'like', '%' . $search . '%')
                ->orWhere('no_invoice', 'like', '%' . $search . '%');
        }

        // Ambil data terbaru dengan pagination (15 per halaman)
        $invoices = $query->latest()->paginate(15);

        // Kirim data ke view
        return view('invoice.histori', [
            'invoices' => $invoices,
            'search' => $search ?? ''
        ]);
    }

    /**
     * Menampilkan form untuk membuat invoice baru (dari nol).
     */
    public function create()
    {
        return view('invoice.create');
    }

    /**
     * Menampilkan form invoice baru, dengan data yang ditarik dari Penawaran.
     */
    public function createFromOffer(Offer $offer)
    {
        $offer->load(['items', 'jasaItems']);
        return view('invoice.create_from_offer', [
            'offer' => $offer
        ]);
    }

    /**
     * Menyimpan invoice baru yang dibuat dari penawaran.
     */

    public function print($id)
    {
        $invoice = \App\Models\Invoice::with(['offer', 'additions', 'payments'])->findOrFail($id);
        return view('invoice.print', compact('invoice'));
    }


    public function storeFromOffer(Request $request)
    {
        // Validasi data dasar
        $request->validate([
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);

        // --- Kalkulasi Total di Backend ---
        $total_penawaran = $offer->total_keseluruhan;
        $total_tambahan = 0;
        $total_dp = 0;
        $diskon = $request->diskon ?? 0;

        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $item) {
                $total_tambahan += $item['harga'] ?? 0;
            }
        }
        if ($request->has('dp')) {
            foreach ($request->dp as $item) {
                $total_dp += $item['jumlah'] ?? 0;
            }
        }

        $grand_total = ($total_penawaran + $total_tambahan) - $diskon;
        $sisa_pembayaran = $grand_total - $total_dp;
        // --- Akhir Kalkulasi ---

        // 1. Simpan data ke tabel 'invoices'
        $invoice = Invoice::create([
            'offer_id' => $offer->id,
            'no_invoice' => $request->no_invoice ?? 'INV-' . date('Ymd') . '-' . $offer->id,
            'nama_klien' => $offer->nama_klien,
            'total_penawaran' => $total_penawaran,
            'total_tambahan' => $total_tambahan,
            'diskon' => $diskon,
            'grand_total' => $grand_total,
            'total_dp' => $total_dp,
            'sisa_pembayaran' => $sisa_pembayaran,
        ]);

        // 2. Simpan data ke tabel 'invoice_additions'
        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $itemData) {
                if (!empty($itemData['nama'])) {
                    $invoice->additions()->create([
                        'nama_pekerjaan' => $itemData['nama'],
                        'harga' => $itemData['harga'] ?? 0,
                    ]);
                }
            }
        }

        // 3. Simpan data ke tabel 'invoice_payments'
        if ($request->has('dp')) {
            foreach ($request->dp as $itemData) {
                if (!empty($itemData['keterangan'])) {
                    $invoice->payments()->create([
                        'keterangan' => $itemData['keterangan'],
                        'jumlah' => $itemData['jumlah'] ?? 0,
                    ]);
                }
            }
        }

        // Alihkan ke halaman histori invoice dengan pesan sukses
        return redirect()->route('invoice.histori')->with('success', 'Invoice baru berhasil dibuat!');
    }

    /**
     * Menampilkan detail invoice.
     */
    public function show(Invoice $invoice)
    {
        // Load semua relasi yang dibutuhkan untuk 'show.blade.php'
        $invoice->load(['offer.items', 'offer.jasaItems', 'additions', 'payments']);

        return view('invoice.show', compact('invoice'));
    }

    /**
     * Menampilkan form untuk mengedit invoice.
     */
    public function edit(Invoice $invoice)
    {
        // Load relasi yang sama untuk form edit
        $invoice->load(['offer.items', 'offer.jasaItems', 'additions', 'payments']);

        // Mengarahkan ke view edit yang baru dibuat
        return view('invoice.edit', compact('invoice'));
    }


    /**
     * Memperbarui data invoice di database.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Validasi dasar (tambahkan sesuai kebutuhan)
        $request->validate([
            'diskon' => 'nullable|numeric|min:0',
            'pekerjaan.*.nama' => 'nullable|string',
            'pekerjaan.*.harga' => 'nullable|numeric|min:0',
            'dp.*.keterangan' => 'nullable|string',
            'dp.*.jumlah' => 'nullable|numeric|min:0',
        ]);

        // --- Kalkulasi Ulang Total di Backend ---
        $total_penawaran = $invoice->total_penawaran; // Ambil dari data yg ada
        $total_tambahan = 0;
        $total_dp = 0;
        $diskon = $request->diskon ?? 0;

        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $item) {
                $total_tambahan += $item['harga'] ?? 0;
            }
        }
        if ($request->has('dp')) {
            foreach ($request->dp as $item) {
                $total_dp += $item['jumlah'] ?? 0;
            }
        }

        $grand_total = ($total_penawaran + $total_tambahan) - $diskon;
        $sisa_pembayaran = $grand_total - $total_dp;
        // --- Akhir Kalkulasi ---

        // 1. Update data di tabel 'invoices'
        $invoice->update([
            'total_tambahan' => $total_tambahan,
            'diskon' => $diskon,
            'grand_total' => $grand_total,
            'total_dp' => $total_dp,
            'sisa_pembayaran' => $sisa_pembayaran,
        ]);

        // 2. Hapus data lama dan simpan data baru ke 'invoice_additions'
        $invoice->additions()->delete();
        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $itemData) {
                if (!empty($itemData['nama'])) {
                    $invoice->additions()->create([
                        'nama_pekerjaan' => $itemData['nama'],
                        'harga' => $itemData['harga'] ?? 0,
                    ]);
                }
            }
        }

        // 3. Hapus data lama dan simpan data baru ke 'invoice_payments'
        $invoice->payments()->delete();
        if ($request->has('dp')) {
            foreach ($request->dp as $itemData) {
                if (!empty($itemData['keterangan'])) {
                    $invoice->payments()->create([
                        'keterangan' => $itemData['keterangan'],
                        'jumlah' => $itemData['jumlah'] ?? 0,
                    ]);
                }
            }
        }

        // Alihkan ke halaman show invoice dengan pesan sukses
        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice berhasil diperbarui!');
    }

    /**
     * Menghapus data invoice dari database.
     */
    public function destroy(Invoice $invoice)
    {
        // Menggunakan Route-Model Binding (Invoice $invoice)
        // untuk otomatis menemukan data invoice berdasarkan ID dari URL.

        $invoice->delete();

        return redirect()->route('invoice.histori')->with('success', 'Invoice berhasil dihapus!');
    }
}
