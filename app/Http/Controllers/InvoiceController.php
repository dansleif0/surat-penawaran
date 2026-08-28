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
        $invoice = \App\Models\Invoice::with(['offer.items', 'offer.jasaItems', 'additions', 'payments', 'mergedItems'])->findOrFail($id);

        if ($invoice->status === 'merge') {
            return view('invoice.print_merged', compact('invoice'));
        }

        return view('invoice.print', compact('invoice'));
    }

    public function printSuratJalan($id)
    {
        $invoice = \App\Models\Invoice::with(['offer.items', 'offer.jasaItems', 'additions', 'payments', 'mergedItems'])->findOrFail($id);

        return view('invoice.print_surat_jalan', compact('invoice'));
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
            'status' => 'single',
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
        $invoice->load(['offer.items', 'offer.jasaItems', 'additions', 'payments', 'mergedItems']);

        if ($invoice->status === 'merge') {
            return view('invoice.show_merged', compact('invoice'));
        }

        return view('invoice.show', compact('invoice'));
    }

    /**
     * Menampilkan form untuk mengedit invoice.
     */
    public function edit(Invoice $invoice)
    {
        // Load relasi yang sama untuk form edit
        $invoice->load(['offer.items', 'offer.jasaItems', 'additions', 'payments', 'mergedItems']);

        if ($invoice->status === 'merge') {
            return view('invoice.edit_merged', compact('invoice'));
        }

        // Mengarahkan ke view edit biasa
        return view('invoice.edit', compact('invoice'));
    }

    /**
     * Memperbarui data invoice di database.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'merge') {
            $request->validate([
                'nama_klien' => 'required|string|max:255',
                'no_invoice' => 'required|string|max:255',
                'diskon' => 'nullable|numeric|min:0',
                'pekerjaan.*.nama' => 'nullable|string',
                'pekerjaan.*.harga' => 'nullable|numeric|min:0',
                'dp.*.keterangan' => 'nullable|string',
                'dp.*.jumlah' => 'nullable|numeric|min:0',
                'merged_items.*.date' => 'required|date',
                'merged_items.*.due_date' => 'required|date',
                'merged_items.*.invoice_no' => 'required|string',
                'merged_items.*.keterangan' => 'nullable|string',
                'merged_items.*.debit' => 'required|numeric',
            ]);

            $totalPenawaran = 0;
            if ($request->has('merged_items')) {
                foreach ($request->merged_items as $mItem) {
                    $totalPenawaran += $mItem['debit'] ?? 0;
                }
            } else {
                $totalPenawaran = $invoice->total_penawaran;
            }

            $totalTambahan = 0;
            $totalDp = 0;
            $diskon = $request->diskon ?? 0;

            if ($request->has('pekerjaan')) {
                foreach ($request->pekerjaan as $item) {
                    $totalTambahan += $item['harga'] ?? 0;
                }
            }
            if ($request->has('dp')) {
                foreach ($request->dp as $item) {
                    $totalDp += $item['jumlah'] ?? 0;
                }
            }

            $grandTotal = ($totalPenawaran + $totalTambahan) - $diskon;
            $sisaPembayaran = $grandTotal - $totalDp;

            // 1. Update Invoice Data
            $invoice->update([
                'no_invoice' => $request->no_invoice,
                'nama_klien' => $request->nama_klien,
                'total_penawaran' => $totalPenawaran,
                'total_tambahan' => $totalTambahan,
                'diskon' => $diskon,
                'grand_total' => $grandTotal,
                'total_dp' => $totalDp,
                'sisa_pembayaran' => $sisaPembayaran,
            ]);

            // 2. Update Merged Items
            $invoice->mergedItems()->delete();
            if ($request->has('merged_items')) {
                foreach ($request->merged_items as $mItem) {
                    $invoice->mergedItems()->create([
                        'ref_invoice_id' => $mItem['ref_invoice_id'] ?? null,
                        'invoice_no' => $mItem['invoice_no'],
                        'date' => $mItem['date'],
                        'due_date' => $mItem['due_date'],
                        'keterangan' => $mItem['keterangan'] ?? '-',
                        'currency' => $mItem['keterangan'] ?? '-',
                        'debit' => $mItem['debit'] ?? 0,
                    ]);
                }
            }

            // 3. Update Pekerjaan Tambahan
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

            // 4. Update Pembayaran DP
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

            return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice gabungan berhasil diperbarui!');
        }

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
        $invoice->delete();
        return redirect()->route('invoice.histori')->with('success', 'Invoice berhasil dihapus!');
    }

    /**
     * Menampilkan form khusus untuk menggabungkan beberapa invoice.
     */
    public function combineForm(Request $request)
    {
        if (!$request->has('selected_invoices') || !is_array($request->input('selected_invoices'))) {
            return redirect()->route('invoice.histori')->with('error', 'Silakan centang minimal 2 invoice pada tabel terlebih dahulu.');
        }

        $request->validate([
            'selected_invoices' => 'required|array|min:2',
            'selected_invoices.*' => 'exists:invoices,id',
        ], [
            'selected_invoices.required' => 'Pilih minimal 2 invoice untuk digabungkan.',
            'selected_invoices.min' => 'Pilih minimal 2 invoice untuk digabungkan.',
        ]);

        $ids = $request->input('selected_invoices');
        $selectedInvoices = Invoice::with(['offer.items', 'offer.jasaItems', 'additions', 'payments'])
            ->whereIn('id', $ids)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($selectedInvoices->count() < 2) {
            return redirect()->route('invoice.histori')->with('error', 'Minimal 2 invoice valid harus dipilih.');
        }

        // Akumulasi data awal
        $totalPenawaran = $selectedInvoices->sum('total_penawaran');
        
        // Ambil nama klien (cukup salah satu nama klien dari invoice terpilih)
        $clientNames = $selectedInvoices->pluck('nama_klien')->filter()->first() ?? '';

        // Kumpulkan semua pekerjaan tambahan dari invoice terpilih
        $combinedAdditions = [];
        foreach ($selectedInvoices as $inv) {
            foreach ($inv->additions as $add) {
                $combinedAdditions[] = [
                    'nama' => $add->nama_pekerjaan . " (dari " . $inv->no_invoice . ")",
                    'harga' => $add->harga
                ];
            }
        }

        // Kumpulkan semua DP dari invoice terpilih
        $combinedPayments = [];
        foreach ($selectedInvoices as $inv) {
            foreach ($inv->payments as $pay) {
                $combinedPayments[] = [
                    'keterangan' => $pay->keterangan . " (dari " . $inv->no_invoice . ")",
                    'jumlah' => $pay->jumlah
                ];
            }
        }

        // Total diskon awal (akumulasi diskon dari invoice terpilih)
        $totalDiskon = $selectedInvoices->sum('diskon');

        // Buat No. Invoice Gabungan Default: 00[IDs]/INV-GABUNG/TGI/[ROMAWI]/[TAHUN]
        $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        $currentMonth = date('n');
        $currentYear = date('Y');
        $romawi = $bulanRomawi[$currentMonth];
        $idChain = implode('-', $ids);
        $defaultNoInvoice = "00{$idChain}/INV-GABUNG/TGI/{$romawi}/{$currentYear}";

        return view('invoice.combine_form', [
            'selectedInvoices' => $selectedInvoices,
            'clientNames' => $clientNames,
            'defaultNoInvoice' => $defaultNoInvoice,
            'totalPenawaran' => $totalPenawaran,
            'combinedAdditions' => $combinedAdditions,
            'combinedPayments' => $combinedPayments,
            'totalDiskon' => $totalDiskon,
        ]);
    }

    /**
     * Menyimpan invoice gabungan baru ke database.
     */
    public function storeCombined(Request $request)
    {
        $request->validate([
            'selected_invoice_ids' => 'required|array|min:2',
            'nama_klien' => 'required|string|max:255',
            'no_invoice' => 'required|string|max:255',
            'diskon' => 'nullable|numeric|min:0',
            'pekerjaan.*.nama' => 'nullable|string',
            'pekerjaan.*.harga' => 'nullable|numeric|min:0',
            'dp.*.keterangan' => 'nullable|string',
            'dp.*.jumlah' => 'nullable|numeric|min:0',
            'merged_items.*.date' => 'required|date',
            'merged_items.*.due_date' => 'required|date',
            'merged_items.*.invoice_no' => 'required|string',
            'merged_items.*.debit' => 'required|numeric',
        ]);

        $ids = $request->input('selected_invoice_ids');
        $selectedInvoices = Invoice::whereIn('id', $ids)->get();

        if ($selectedInvoices->isEmpty()) {
            return redirect()->route('invoice.histori')->with('error', 'Invoice terpilih tidak ditemukan.');
        }

        // Ambil offer_id dari invoice pertama sebagai penanda referensi offer
        $firstInvoice = $selectedInvoices->first();
        $offerId = $firstInvoice->offer_id;

        // Total penawaran adalah total akumulasi dari penawaran invoice-invoice terpilih
        $totalPenawaran = $selectedInvoices->sum('total_penawaran');
        
        $totalTambahan = 0;
        $totalDp = 0;
        $diskon = $request->diskon ?? 0;

        if ($request->has('pekerjaan')) {
            foreach ($request->pekerjaan as $item) {
                $totalTambahan += $item['harga'] ?? 0;
            }
        }
        if ($request->has('dp')) {
            foreach ($request->dp as $item) {
                $totalDp += $item['jumlah'] ?? 0;
            }
        }

        $grandTotal = ($totalPenawaran + $totalTambahan) - $diskon;
        $sisaPembayaran = $grandTotal - $totalDp;

        // 1. Simpan Invoice Gabungan dengan status "merge"
        $invoice = Invoice::create([
            'offer_id' => $offerId,
            'status' => 'merge',
            'no_invoice' => $request->no_invoice,
            'nama_klien' => $request->nama_klien,
            'total_penawaran' => $totalPenawaran,
            'total_tambahan' => $totalTambahan,
            'diskon' => $diskon,
            'grand_total' => $grandTotal,
            'total_dp' => $totalDp,
            'sisa_pembayaran' => $sisaPembayaran,
        ]);

        // 2. Simpan Rincian Items Invoice Gabungan (Piutang Statement Items)
        if ($request->has('merged_items')) {
            foreach ($request->merged_items as $itemData) {
                $invoice->mergedItems()->create([
                    'ref_invoice_id' => $itemData['ref_invoice_id'] ?? null,
                    'invoice_no' => $itemData['invoice_no'],
                    'date' => $itemData['date'],
                    'due_date' => $itemData['due_date'],
                    'keterangan' => $itemData['keterangan'] ?? '-',
                    'currency' => $itemData['keterangan'] ?? '-',
                    'debit' => $itemData['debit'] ?? 0,
                ]);
            }
        }

        // 3. Simpan Pekerjaan Tambahan
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

        // 4. Simpan Pembayaran DP
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

        return redirect()->route('invoice.histori')->with('success', 'Invoice gabungan (Piutang Statement) berhasil disimpan!');
    }
}
