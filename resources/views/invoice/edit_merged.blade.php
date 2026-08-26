@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-5xl mx-auto">

        <!-- Header Navigasi -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('invoice.show', $invoice->id) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                    &larr; Kembali ke Detail Invoice
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Edit Invoice Gabungan (Piutang Statement)</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Terjadi kesalahan pada input:</p>
                <ul class="list-disc list-inside text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Utama Edit -->
        <form id="combine-edit-form" action="{{ route('invoice.update', $invoice->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Card Informasi Invoice yang Digabungkan (Piutang Statement Items) -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daftar Invoice yang Digabungkan (Format Piutang Statement)
                    </h2>
                    <button type="button" id="add-merged-item-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1.5 px-3 rounded transition shadow-sm">
                        + Tambah Baris Invoice
                    </button>
                </div>
                <p class="text-xs text-indigo-700 mb-4">Anda dapat mengubah Tanggal, Jatuh Tempo, Keterangan (Ket), maupun Nilai Tagihan (Debit) untuk setiap invoice di bawah ini.</p>
                
                <div class="overflow-x-auto bg-white rounded-md border border-indigo-100">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-indigo-100 text-indigo-900 font-semibold text-xs uppercase">
                            <tr>
                                <th class="p-3">No. Invoice</th>
                                <th class="p-3">Tanggal (Date)</th>
                                <th class="p-3">Jatuh Tempo (Due Date)</th>
                                <th class="p-3">Keterangan (Ket)</th>
                                <th class="p-3 text-right">Tagihan (Debit)</th>
                                <th class="p-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="merged-items-tbody" class="divide-y divide-gray-100">
                            @foreach ($invoice->mergedItems as $index => $item)
                            <tr class="merged-item-row hover:bg-gray-50">
                                <td class="p-3">
                                    <input type="hidden" name="merged_items[{{ $index }}][ref_invoice_id]" value="{{ $item->ref_invoice_id }}">
                                    <input type="text" 
                                           name="merged_items[{{ $index }}][invoice_no]" 
                                           value="{{ old('merged_items.'.$index.'.invoice_no', $item->invoice_no) }}" 
                                           class="w-full rounded-md border-gray-300 text-xs font-semibold shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-3">
                                    <input type="date" 
                                           name="merged_items[{{ $index }}][date]" 
                                           value="{{ old('merged_items.'.$index.'.date', \Carbon\Carbon::parse($item->date)->format('Y-m-d')) }}" 
                                           class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-3">
                                    <input type="date" 
                                           name="merged_items[{{ $index }}][due_date]" 
                                           value="{{ old('merged_items.'.$index.'.due_date', $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') : '') }}" 
                                           class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-3">
                                    <input type="text" 
                                           name="merged_items[{{ $index }}][keterangan]" 
                                           value="{{ old('merged_items.'.$index.'.keterangan', $item->keterangan ?? '-') }}" 
                                           class="w-36 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                           placeholder="Keterangan (cth: DP, -)">
                                </td>
                                <td class="p-3 text-right">
                                    <input type="number" 
                                           name="merged_items[{{ $index }}][debit]" 
                                           value="{{ old('merged_items.'.$index.'.debit', $item->debit) }}" 
                                           class="merged-debit-input w-36 rounded-md border-gray-300 text-xs font-bold text-indigo-700 text-right shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" class="remove-merged-item-btn bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded transition" title="Hapus Baris">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 1. DATA UTAMA INVOICE GABUNGAN -->
            <div class="bg-white p-8 rounded-lg shadow-md border border-gray-200">
                <fieldset class="border-b pb-6 mb-6">
                    <legend class="text-xl font-bold text-gray-800 mb-4">1. Data Invoice Gabungan</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_klien" class="block text-sm font-medium text-gray-700 mb-1">Nama Klien / Perusahaan</label>
                            <input type="text"
                                   name="nama_klien"
                                   id="nama_klien"
                                   value="{{ old('nama_klien', $invoice->nama_klien) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                        </div>

                        <div>
                            <label for="no_invoice" class="block text-sm font-medium text-gray-700 mb-1">Nomor Invoice</label>
                            <input type="text"
                                   name="no_invoice"
                                   id="no_invoice"
                                   value="{{ old('no_invoice', $invoice->no_invoice) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium text-gray-800"
                                   required>
                        </div>
                    </div>
                </fieldset>

                <!-- 2. TOTAL PENAWARAN ASLI (AKUMULASI) -->
                <fieldset class="border-b pb-6 mb-6">
                    <legend class="text-xl font-bold text-gray-800 mb-4">2. Total Penawaran Asli (Akumulasi Debit)</legend>
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-gray-700 block">Total Akumulasi Tagihan (Debit)</span>
                            <span class="text-xs text-gray-500">Otomatis terhitung dari jumlah seluruh debit invoice di atas</span>
                        </div>
                        <div class="text-right">
                            <span id="display_calculated_penawaran" class="text-2xl font-bold text-indigo-700">Rp {{ number_format($invoice->total_penawaran, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </fieldset>

                <!-- 3. PEKERJAAN TAMBAHAN (DINAMIS) -->
                <fieldset class="border-b pb-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <legend class="text-xl font-bold text-gray-800">3. Pekerjaan Tambahan (Gabungan)</legend>
                        <button type="button" id="add-work-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-1.5 px-3 rounded transition shadow-sm flex items-center gap-1">
                            + Tambah Pekerjaan
                        </button>
                    </div>

                    <div id="work-container" class="space-y-3">
                        @forelse ($invoice->additions as $index => $addition)
                        <div class="work-row grid grid-cols-12 gap-3 items-center bg-gray-50 p-3 rounded-md border border-gray-200">
                            <div class="col-span-7">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Pekerjaan Tambahan</label>
                                <input type="text" name="pekerjaan[{{ $index }}][nama]" value="{{ old('pekerjaan.'.$index.'.nama', $addition->nama_pekerjaan) }}" class="work-name w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Deskripsi pekerjaan...">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Harga / Biaya (Rp)</label>
                                <input type="number" name="pekerjaan[{{ $index }}][harga]" value="{{ old('pekerjaan.'.$index.'.harga', $addition->harga) }}" class="work-price w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                            </div>
                            <div class="col-span-1 text-right flex justify-end items-end h-full pt-5">
                                <button type="button" class="remove-work-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-md transition" title="Hapus Pekerjaan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p id="no-work-msg" class="text-sm text-gray-500 italic p-3 text-center border rounded-md bg-gray-50">Belum ada pekerjaan tambahan. Klik "+ Tambah Pekerjaan" jika ada.</p>
                        @endforelse
                    </div>
                </fieldset>

                <!-- 4. POTONGAN DISKON -->
                <fieldset class="border-b pb-6 mb-6">
                    <legend class="text-xl font-bold text-gray-800 mb-4">4. Diskon / Potongan Harga</legend>
                    <div class="max-w-xs">
                        <label for="diskon" class="block text-sm font-medium text-gray-700 mb-1">Diskon Keseluruhan (Rp)</label>
                        <input type="number"
                               name="diskon"
                               id="diskon"
                               value="{{ old('diskon', $invoice->diskon) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="0">
                    </div>
                </fieldset>

                <!-- 5. PEMBAYARAN / DP (DINAMIS) -->
                <fieldset class="border-b pb-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <legend class="text-xl font-bold text-gray-800">5. Riwayat Pembayaran / DP (Gabungan)</legend>
                        <button type="button" id="add-dp-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-1.5 px-3 rounded transition shadow-sm flex items-center gap-1">
                            + Tambah DP
                        </button>
                    </div>

                    <div id="dp-container" class="space-y-3">
                        @forelse ($invoice->payments as $index => $payment)
                        <div class="dp-row grid grid-cols-12 gap-3 items-center bg-gray-50 p-3 rounded-md border border-gray-200">
                            <div class="col-span-7">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan Pembayaran / DP</label>
                                <input type="text" name="dp[{{ $index }}][keterangan]" value="{{ old('dp.'.$index.'.keterangan', $payment->keterangan) }}" class="dp-name w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Keterangan DP...">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Pembayaran (Rp)</label>
                                <input type="number" name="dp[{{ $index }}][jumlah]" value="{{ old('dp.'.$index.'.jumlah', $payment->jumlah) }}" class="dp-amount w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                            </div>
                            <div class="col-span-1 text-right flex justify-end items-end h-full pt-5">
                                <button type="button" class="remove-dp-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-md transition" title="Hapus DP">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p id="no-dp-msg" class="text-sm text-gray-500 italic p-3 text-center border rounded-md bg-gray-50">Belum ada riwayat DP. Klik "+ Tambah DP" jika ada.</p>
                        @endforelse
                    </div>
                </fieldset>

                <!-- RINGKASAN & KALKULASI AKHIR -->
                <div class="bg-gray-800 text-white rounded-xl p-6 shadow-md mb-8">
                    <h3 class="text-lg font-bold border-b border-gray-700 pb-3 mb-4 text-indigo-300">Ringkasan Kalkulasi Invoice Gabungan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Total Penawaran Asli:</span>
                            <span id="display_total_penawaran" class="font-semibold text-white">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Total Pekerjaan Tambahan:</span>
                            <span id="display_total_tambahan" class="font-semibold text-white">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Total Diskon:</span>
                            <span id="display_diskon" class="font-semibold text-red-400">- Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-700 pt-3 text-base">
                            <span class="font-bold text-white">Grand Total Tagihan:</span>
                            <span id="display_grand_total" class="font-bold text-emerald-400 text-xl">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-300">
                            <span>Total DP / Pembayaran Masuk:</span>
                            <span id="display_total_dp" class="font-semibold text-yellow-400">- Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-700 pt-3 text-lg font-bold">
                            <span class="text-white">Sisa Pembayaran / Pelunasan:</span>
                            <span id="display_sisa_pembayaran" class="text-amber-400 text-2xl">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- BUTTON ACTION -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('invoice.show', $invoice->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-md transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md transition shadow-md flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan Invoice Gabungan
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let mergedIndex = {{ count($invoice->mergedItems) }};
    let workIndex = {{ count($invoice->additions) }};
    let dpIndex = {{ count($invoice->payments) }};

    const mergedTbody = document.getElementById('merged-items-tbody');
    const addMergedBtn = document.getElementById('add-merged-item-btn');

    const workContainer = document.getElementById('work-container');
    const addWorkBtn = document.getElementById('add-work-btn');
    
    const dpContainer = document.getElementById('dp-container');
    const addDpBtn = document.getElementById('add-dp-btn');

    const diskonInput = document.getElementById('diskon');

    function formatRupiah(angka) {
        return 'Rp ' + Math.round(angka || 0).toLocaleString('id-ID');
    }

    function calculateTotals() {
        let totalPenawaran = 0;
        document.querySelectorAll('.merged-debit-input').forEach(input => {
            totalPenawaran += parseFloat(input.value) || 0;
        });

        let totalTambahan = 0;
        document.querySelectorAll('.work-price').forEach(input => {
            totalTambahan += parseFloat(input.value) || 0;
        });

        let totalDp = 0;
        document.querySelectorAll('.dp-amount').forEach(input => {
            totalDp += parseFloat(input.value) || 0;
        });

        const diskon = parseFloat(diskonInput.value) || 0;

        const grandTotal = (totalPenawaran + totalTambahan) - diskon;
        const sisaPembayaran = grandTotal - totalDp;

        document.getElementById('display_calculated_penawaran').textContent = formatRupiah(totalPenawaran);
        document.getElementById('display_total_penawaran').textContent = formatRupiah(totalPenawaran);
        document.getElementById('display_total_tambahan').textContent = formatRupiah(totalTambahan);
        document.getElementById('display_diskon').textContent = '- ' + formatRupiah(diskon);
        document.getElementById('display_grand_total').textContent = formatRupiah(grandTotal);
        document.getElementById('display_total_dp').textContent = '- ' + formatRupiah(totalDp);
        document.getElementById('display_sisa_pembayaran').textContent = formatRupiah(sisaPembayaran);
    }

    diskonInput.addEventListener('input', calculateTotals);

    function bindRowEvents() {
        document.querySelectorAll('.merged-debit-input, .work-price, .dp-amount').forEach(input => {
            input.removeEventListener('input', calculateTotals);
            input.addEventListener('input', calculateTotals);
        });
    }

    // Tambah Baris Invoice Merged Baru
    addMergedBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'merged-item-row hover:bg-gray-50';
        tr.innerHTML = `
            <td class="p-3">
                <input type="text" name="merged_items[${mergedIndex}][invoice_no]" class="w-full rounded-md border-gray-300 text-xs font-semibold shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0000/INV/..." required>
            </td>
            <td class="p-3">
                <input type="date" name="merged_items[${mergedIndex}][date]" value="${new Date().toISOString().split('T')[0]}" class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="p-3">
                <input type="date" name="merged_items[${mergedIndex}][due_date]" class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="p-3">
                <input type="text" name="merged_items[${mergedIndex}][keterangan]" value="-" class="w-36 rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Keterangan...">
            </td>
            <td class="p-3 text-right">
                <input type="number" name="merged_items[${mergedIndex}][debit]" value="0" class="merged-debit-input w-36 rounded-md border-gray-300 text-xs font-bold text-indigo-700 text-right shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="p-3 text-center">
                <button type="button" class="remove-merged-item-btn bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded transition" title="Hapus Baris">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        `;
        mergedTbody.appendChild(tr);
        mergedIndex++;
        bindRowEvents();
        calculateTotals();
    });

    // Tambah Pekerjaan Baru
    addWorkBtn.addEventListener('click', function() {
        const noMsg = document.getElementById('no-work-msg');
        if (noMsg) noMsg.remove();

        const div = document.createElement('div');
        div.className = 'work-row grid grid-cols-12 gap-3 items-center bg-gray-50 p-3 rounded-md border border-gray-200';
        div.innerHTML = `
            <div class="col-span-7">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Pekerjaan Tambahan</label>
                <input type="text" name="pekerjaan[${workIndex}][nama]" class="work-name w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Deskripsi pekerjaan...">
            </div>
            <div class="col-span-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Harga / Biaya (Rp)</label>
                <input type="number" name="pekerjaan[${workIndex}][harga]" class="work-price w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
            </div>
            <div class="col-span-1 text-right flex justify-end items-end h-full pt-5">
                <button type="button" class="remove-work-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-md transition" title="Hapus Pekerjaan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        `;
        workContainer.appendChild(div);
        workIndex++;
        bindRowEvents();
        calculateTotals();
    });

    // Tambah DP Baru
    addDpBtn.addEventListener('click', function() {
        const noMsg = document.getElementById('no-dp-msg');
        if (noMsg) noMsg.remove();

        const div = document.createElement('div');
        div.className = 'dp-row grid grid-cols-12 gap-3 items-center bg-gray-50 p-3 rounded-md border border-gray-200';
        div.innerHTML = `
            <div class="col-span-7">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan Pembayaran / DP</label>
                <input type="text" name="dp[${dpIndex}][keterangan]" class="dp-name w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Keterangan DP...">
            </div>
            <div class="col-span-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah Pembayaran (Rp)</label>
                <input type="number" name="dp[${dpIndex}][jumlah]" class="dp-amount w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
            </div>
            <div class="col-span-1 text-right flex justify-end items-end h-full pt-5">
                <button type="button" class="remove-dp-btn bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-md transition" title="Hapus DP">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        `;
        dpContainer.appendChild(div);
        dpIndex++;
        bindRowEvents();
        calculateTotals();
    });

    // Delegate Hapus Baris
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-merged-item-btn')) {
            e.target.closest('.merged-item-row').remove();
            calculateTotals();
        }
        if (e.target.closest('.remove-work-btn')) {
            e.target.closest('.work-row').remove();
            calculateTotals();
        }
        if (e.target.closest('.remove-dp-btn')) {
            e.target.closest('.dp-row').remove();
            calculateTotals();
        }
    });

    bindRowEvents();
    calculateTotals();
});
</script>
@endsection
