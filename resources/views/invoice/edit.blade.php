@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-2 text-center">Edit Invoice</h1>
        <h2 class="text-lg font-medium mb-6 text-center text-gray-700">No: {{ $invoice->no_invoice }}</h2>

        <!-- Diarahkan ke route 'invoice.update' -->
        <form id="invoice-form" action="{{ route('invoice.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Method untuk update -->

            <!-- ====================================================== -->
            <!-- 1. DATA YANG SUDAH TERISI DARI SURAT PENAWARAN -->
            <!-- ====================================================== -->
            <fieldset class="border-t pt-6">
                <legend class="text-lg font-semibold text-gray-700 px-2">Data Penawaran (Read-Only)</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nama Klien</label>
                        <!-- Menggunakan $invoice->offer->... -->
                        <input type="text" value="{{ $invoice->offer->nama_klien }}" class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm" readonly>
                    </div>
                   <div>
                        <label class="block text-sm font-medium text-gray-600">Nomor Surat Penawaran</label>

                        {{-- LOGIKA NOMOR SURAT OTOMATIS (ROMAWI) --}}
                        @php
                            $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];

                            // Kita ambil data dari relasi invoice ke offer
                            $offerData = $invoice->offer;

                            // Ambil bulan & tahun dari created_at penawaran
                            $romawi = $bulanRomawi[$offerData->created_at->format('n')];
                            $tahun = $offerData->created_at->format('Y');
                        @endphp

                        <input type="text"
                               value="00{{ $offerData->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}"
                               class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm text-gray-700"
                               readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Detail Pengerjaan</label>
                        <input type="text" value="{{ $invoice->offer->client_details }}" class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm" readonly>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-600">Rincian Area/Pekerjaan (dari Penawaran)</label>
                    <div class="border rounded-md mt-1 divide-y divide-gray-200 bg-gray-50">
                        @foreach($invoice->offer->items as $index => $item)
                        <div class="p-3 flex justify-between items-center text-sm">
                            <span class="text-gray-700">
                                @if($invoice->offer->opsi_paket)
                                <span class="font-bold text-blue-600">Paket {{ chr(65 + $index) }}</span> - 
                                @endif
                                {{ $item->area_dinding }} ({{ $item->nama_produk }})
                            </span>
                            <span class="font-medium">Rp {{ number_format($item->volume * $item->harga_per_m2, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @foreach($invoice->offer->jasaItems as $jasa)
                         <div class="p-3 flex justify-between items-center text-sm">
                            <span class="text-gray-700">{{ $jasa->nama_jasa }}</span>
                            <span class="font-medium">Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="p-3 flex justify-between items-center bg-gray-200 font-bold">
                            <span>Total Penawaran Asli</span>
                            <!-- ID ini penting untuk JS -->
                            <input type="hidden" id="offer_total" value="{{ $invoice->total_penawaran }}">
                            <span>Rp {{ number_format($invoice->total_penawaran, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- ====================================================== -->
            <!-- 2. FORM DINAMIS BARU UNTUK INVOICE -->
            <!-- ====================================================== -->
            <fieldset class="border-t pt-6 mt-8">
                <legend class="text-lg font-semibold text-gray-700 px-2">Data Invoice Baru</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="no_invoice" class="block text-sm font-medium text-gray-700">Nomor Invoice Baru</label>
                        <!-- Mengisi value dari data $invoice -->
                        <input type="text" name="no_invoice" id="no_invoice" value="{{ old('no_invoice', $invoice->no_invoice) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800">
                    </div>
                    <div>
                         <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (Rp)</label>
                         <!-- Mengisi value dari data $invoice -->
                         <input type="number" name="diskon" id="diskon" value="{{ old('diskon', $invoice->diskon) }}" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm calc-trigger">
                    </div>
                </div>

                <!-- Pekerjaan Tambahan -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Pekerjaan Tambahan (optional)</label>
                    <div id="pekerjaan-rows-container" class="space-y-3 mt-2">
                        <!-- Render baris yang sudah ada dari database -->
                        @foreach($invoice->additions as $index => $item)
                        <div class="pekerjaan-row grid grid-cols-12 gap-x-4 items-center">
                            <div class="col-span-7"><input type="text" name="pekerjaan[{{ $index }}][nama]" value="{{ $item->nama_pekerjaan }}" placeholder="Nama Pekerjaan" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
                            <div class="col-span-4"><input type="number" name="pekerjaan[{{ $index }}][harga]" value="{{ $item->harga }}" placeholder="Harga" class="pekerjaan-harga calc-trigger mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
                            <div class="col-span-1"><button type="button" class="remove-row-btn bg-red-500 text-white rounded w-8 h-8 flex items-center justify-center">-</button></div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-pekerjaan-btn" class="text-sm bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mt-2">+ Tambah Pekerjaan</button>
                </div>

                <!-- Down Payment (DP) -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Down Payment (DP) (optional)</label>
                    <div id="dp-rows-container" class="space-y-3 mt-2">
                        <!-- Render baris yang sudah ada dari database -->
                        @foreach($invoice->payments as $index => $item)
                        <div class="dp-row grid grid-cols-12 gap-x-4 items-center">
                            <div class="col-span-7"><input type="text" name="dp[{{ $index }}][keterangan]" value="{{ $item->keterangan }}" placeholder="Keterangan (mis: DP 1)" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
                            <div class="col-span-4"><input type="number" name="dp[{{ $index }}][jumlah]" value="{{ $item->jumlah }}" placeholder="Jumlah DP" class="dp-jumlah calc-trigger mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
                            <div class="col-span-1"><button type="button" class="remove-row-btn bg-red-500 text-white rounded w-8 h-8 flex items-center justify-center">-</button></div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-dp-btn" class="text-sm bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600 mt-2">+ Tambah DP</M></button>
                </div>
            </fieldset>

            <!-- ====================================================== -->
            <!-- 3. KALKULASI TOTAL AKHIR -->
            <!-- ====================================================== -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="w-full md:w-2/3 lg:w-1/2 ml-auto space-y-3">
                    <div class="flex justify-between items-center"><span class="text-gray-600">Total Penawaran:</span><span id="display_offer_total" class="font-medium">Rp 0</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-600">Pekerjaan Tambahan:</span><span id="display_pekerjaan_total" class="font-medium">Rp 0</span></div>
                    <div class="flex justify-between items-center"><span class="text-red-600">Diskon:</span><span id="display_diskon" class="font-medium text-red-600">- Rp 0</span></div>
                    <div class="flex justify-between items-center text-lg font-bold border-t pt-2"><span class="text-gray-800">Total Tagihan:</span><span id="display_grand_total" class="text-gray-800">Rp 0</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-600">Total DP Dibayar:</span><span id="display_dp_total" class="font-medium text-green-600">- Rp 0</span></div>
                    <div class="flex justify-between items-center text-2xl font-bold border-t-4 pt-3 mt-3"><span>Sisa Pembayaran:</span><span id="display_sisa_pembayaran" class="text-blue-600">Rp 0</span></div>
                </div>
            </div>

            <div class="mt-8"><button type="submit" class="w-full bg-yellow-500 text-white font-bold py-3 px-6 rounded hover:bg-yellow-400"> Update Invoice </button></div>
        </form>
    </div>

    <!-- Template untuk baris Pekerjaan Tambahan -->
    <template id="pekerjaan-row-template">
        <div class="pekerjaan-row grid grid-cols-12 gap-x-4 items-center">
            <div class="col-span-7"><input type="text" name="pekerjaan[0][nama]" placeholder="Nama Pekerjaan" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
            <div class="col-span-4"><input type="number" name="pekerjaan[0][harga]" placeholder="Harga" class="pekerjaan-harga calc-trigger mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
            <div class="col-span-1"><button type="button" class="remove-row-btn bg-red-500 text-white rounded w-8 h-8 flex items-center justify-center">-</button></div>
        </div>
    </template>

    <!-- Template untuk baris DP -->
    <template id="dp-row-template">
        <div class="dp-row grid grid-cols-12 gap-x-4 items-center">
            <div class="col-span-7"><input type="text" name="dp[0][keterangan]" placeholder="Keterangan (mis: DP 1)" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
            <div class="col-span-4"><input type="number" name="dp[0][jumlah]" placeholder="Jumlah DP" class="dp-jumlah calc-trigger mt-1 block w-full rounded-md border-gray-300 text-sm"></div>
            <div class="col-span-1"><button type="button" class="remove-row-btn bg-red-500 text-white rounded w-8 h-8 flex items-center justify-center">-</button></div>
        </div>
    </template>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- UPDATE JS ---
    // Mulai indeks SETELAH jumlah item yang sudah ada
    let pekerjaanRowIndex = {{ $invoice->additions->count() }};
    let dpRowIndex = {{ $invoice->payments->count() }};
    // ---------------

    const offerTotal = parseFloat(document.getElementById('offer_total').value) || 0;

    // Tampilan Total
    const displayOfferTotal = document.getElementById('display_offer_total');
    const displayPekerjaanTotal = document.getElementById('display_pekerjaan_total');
    const displayDiskon = document.getElementById('display_diskon');
    const displayGrandTotal = document.getElementById('display_grand_total');
    const displayDpTotal = document.getElementById('display_dp_total');
    const displaySisaPembayaran = document.getElementById('display_sisa_pembayaran');

    const diskonInput = document.getElementById('diskon');
    diskonInput.addEventListener('input', calculateAllTotals);

    function formatRupiah(angka) { return 'Rp ' + (angka || 0).toLocaleString('id-ID'); }

    function calculateAllTotals() {
        let totalPekerjaan = 0;
        document.querySelectorAll('.pekerjaan-harga').forEach(input => {
            totalPekerjaan += parseFloat(input.value) || 0;
        });

        let totalDp = 0;
        document.querySelectorAll('.dp-jumlah').forEach(input => {
            totalDp += parseFloat(input.value) || 0;
        });

        const diskon = parseFloat(diskonInput.value) || 0;
        const grandTotal = (offerTotal + totalPekerjaan) - diskon;
        const sisaPembayaran = grandTotal - totalDp;

        // Update tampilan
        displayOfferTotal.textContent = formatRupiah(offerTotal);
        displayPekerjaanTotal.textContent = formatRupiah(totalPekerjaan);
        displayDiskon.textContent = '- ' + formatRupiah(diskon);
        displayGrandTotal.textContent = formatRupiah(grandTotal);
        displayDpTotal.textContent = '- ' + formatRupiah(totalDp);
        displaySisaPembayaran.textContent = formatRupiah(sisaPembayaran);
    }

    // --- Logika Tambah Baris (Pekerjaan Tambahan) ---
    const pekerjaanContainer = document.getElementById('pekerjaan-rows-container');
    const addPekerjaanBtn = document.getElementById('add-pekerjaan-btn');
    const pekerjaanTemplate = document.getElementById('pekerjaan-row-template').content.firstElementChild;

    addPekerjaanBtn.addEventListener('click', () => {
        const newRow = pekerjaanTemplate.cloneNode(true);
        // Gunakan indeks JS yang sudah diupdate
        newRow.querySelector('input[type="text"]').name = `pekerjaan[${pekerjaanRowIndex}][nama]`;
        newRow.querySelector('.pekerjaan-harga').name = `pekerjaan[${pekerjaanRowIndex}][harga]`;

        newRow.querySelector('.calc-trigger').addEventListener('input', calculateAllTotals);
        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            calculateAllTotals();
        });

        pekerjaanContainer.appendChild(newRow);
        pekerjaanRowIndex++;
        calculateAllTotals();
    });

    // --- Logika Tambah Baris (DP) ---
    const dpContainer = document.getElementById('dp-rows-container');
    const addDpBtn = document.getElementById('add-dp-btn');
    const dpTemplate = document.getElementById('dp-row-template').content.firstElementChild;

    addDpBtn.addEventListener('click', () => {
        const newRow = dpTemplate.cloneNode(true);
        // Gunakan indeks JS yang sudah diupdate
        newRow.querySelector('input[type="text"]').name = `dp[${dpRowIndex}][keterangan]`;
        newRow.querySelector('.dp-jumlah').name = `dp[${dpRowIndex}][jumlah]`;

        newRow.querySelector('.calc-trigger').addEventListener('input', calculateAllTotals);
        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            calculateAllTotals();
        });

        dpContainer.appendChild(newRow);
        dpRowIndex++;
        calculateAllTotals();
    });

    // --- UPDATE JS ---
    // Tambahkan event listener untuk baris yang sudah ada (di-render oleh PHP)
    document.querySelectorAll('#pekerjaan-rows-container .pekerjaan-row, #dp-rows-container .dp-row').forEach(row => {
        // Tambahkan event listener untuk tombol hapus
        row.querySelector('.remove-row-btn').addEventListener('click', () => {
            row.remove();
            calculateAllTotals();
        });

        // Tambahkan event listener untuk kalkulasi
        const trigger = row.querySelector('.calc-trigger');
        if (trigger) {
            trigger.addEventListener('input', calculateAllTotals);
        }
    });
    // ---------------

    // Inisialisasi kalkulasi saat halaman dimuat
    calculateAllTotals();

    // Logika anti double-submit
    const invoiceForm = document.getElementById('invoice-form');
    if (invoiceForm) {
        invoiceForm.addEventListener('submit', function() {
            const submitButton = invoiceForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';
            }
        });
    }
});
</script>
@endsection