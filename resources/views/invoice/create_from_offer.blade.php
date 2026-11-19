@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Buat Invoice dari Penawaran</h1>

        @php
            $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
            $romawi = $bulanRomawi[$offer->created_at->format('n')] ?? 'I';
            $tahun = $offer->created_at->format('Y');
            $noSuratLengkap = "00{$offer->id}/SP/TGI-1/{$romawi}/{$tahun}";
            $hasInvoice = $offer->invoices && $offer->invoices->isNotEmpty();
            $invList = $hasInvoice ? $offer->invoices->pluck('no_invoice')->implode(', ') : '';
        @endphp

        {{-- Banner Peringatan jika surat penawaran sudah pernah diubah menjadi invoice --}}
        @if($hasInvoice)
            <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="text-amber-500 mt-0.5 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 text-xs text-amber-800">
                        <div class="font-bold text-amber-900 text-sm">Peringatan: Surat Penawaran Ini Sudah Pernah Diubah Menjadi Invoice</div>
                        <p class="mt-1 text-amber-700">
                            Invoice terkait sebelumnya: <strong class="font-mono text-amber-900 bg-amber-100/80 px-1.5 py-0.5 rounded">{{ $invList }}</strong>.
                        </p>
                        <p class="mt-0.5 text-amber-700">
                            Anda saat ini sedang membuat invoice tambahan / baru untuk penawaran ini.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Nanti kita arahkan ke route 'invoice.store' -->
        <form id="invoice-form" action="{{ route('invoice.store_from_offer') }}" method="POST">
            @csrf
            <!-- Simpan ID penawaran asli untuk referensi -->
            <input type="hidden" name="offer_id" value="{{ $offer->id }}">

            <!-- ====================================================== -->
            <!-- 1. DATA YANG SUDAH TERISI DARI SURAT PENAWARAN -->
            <!-- ====================================================== -->
            <fieldset class="border-t pt-6">
                <legend class="text-lg font-semibold text-gray-700 px-2">Data Penawaran (Read-Only)</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nama Klien</label>
                        <input type="text" value="{{ $offer->nama_klien }}" class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm" readonly>
                    </div>
                    <div>
    <label class="block text-sm font-medium text-gray-600">Nomor Surat Penawaran</label>

    {{-- LOGIKA NOMOR SURAT REALTIME --}}
    @php
        $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        // Ambil bulan & tahun dari data penawaran
        $romawi = $bulanRomawi[$offer->created_at->format('n')];
        $tahun = $offer->created_at->format('Y');
    @endphp

    <input type="text" value="00{{ $offer->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}" class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm text-gray-700" readonly>
</div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Detail Pengerjaan</label>
                        <input type="text" value="{{ $offer->client_details }}" class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm" readonly>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-600">Rincian Area/Pekerjaan (dari Penawaran)</label>
                    <div class="border rounded-md mt-1 divide-y divide-gray-200 bg-gray-50">
                        @foreach($offer->items as $index => $item)
                        @php
                            $subtotalKotor = $item->volume * $item->harga_per_m2;
                            $discNominal = (float)($item->diskon ?? 0);
                            $subtotalBaris = max(0, $subtotalKotor - $discNominal);
                        @endphp
                        <div class="p-3 flex justify-between items-center text-sm">
                            <span class="text-gray-700">
                                @if($offer->opsi_paket)
                                <span class="font-bold text-blue-600">Paket {{ chr(65 + $index) }}</span> - 
                                @endif
                                {{ $item->area_dinding }} ({{ $item->nama_produk }})
                                @if($discNominal > 0)
                                    <span class="text-xs text-red-500 font-medium">
                                        (Diskon: -Rp {{ number_format($discNominal, 0, ',', '.') }})
                                    </span>
                                @endif
                            </span>
                            <span class="font-medium">Rp {{ number_format($subtotalBaris, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @foreach($offer->jasaItems as $jasa)
                         <div class="p-3 flex justify-between items-center text-sm">
                            <span class="text-gray-700">{{ $jasa->nama_jasa }}</span>
                            <span class="font-medium">Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="p-3 flex justify-between items-center bg-gray-200 font-bold">
                            <span>Total Penawaran Asli</span>
                            <input type="hidden" id="offer_total" value="{{ $offer->total_keseluruhan }}">
                            <span>Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}</span>
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

                        {{-- LOGIKA NOMOR INVOICE REALTIME --}}
                        @php
                            $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];

                            // Ambil Tanggal Hari Ini (Realtime saat form dibuka)
                            $currentMonth = date('n');
                            $currentYear = date('Y');

                            $romawi = $bulanRomawi[$currentMonth];
                        @endphp

                        {{-- Format: 00[ID_OFFER]/INV/TGI/[ROMAWI]/[TAHUN] --}}
                        {{-- Catatan: ID menggunakan Offer ID karena Invoice ID belum terbentuk --}}
                        <input type="text"
                               name="no_invoice"
                               id="no_invoice"
                               value="00{{ $offer->id }}/INV/TGI/{{ $romawi }}/{{ $currentYear }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800">
                    </div>
                    <div>
                         <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (Rp)</label>
                         <input type="number" name="diskon" id="diskon" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm calc-trigger">
                    </div>
                    <div class="md:col-span-2">
                        <label for="catatan_tambahan" class="block text-sm font-medium text-gray-700">Catatan Tambahan (opsional, tampil di bawah Catatan)</label>
                        <textarea name="catatan_tambahan" id="catatan_tambahan" rows="2" placeholder="Contoh: NO PO : PO/LIVE/2609/00953" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">{{ old('catatan_tambahan') }}</textarea>
                    </div>
                </div>

                <!-- Pekerjaan Tambahan -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Pekerjaan Tambahan (optional)</label>
                    <div id="pekerjaan-rows-container" class="space-y-3 mt-2"></div>
                    <button type="button" id="add-pekerjaan-btn" class="text-sm bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 mt-2">+ Tambah Pekerjaan</button>
                </div>

                <!-- Down Payment (DP) -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Down Payment (DP) (optional)</label>
                    <div id="dp-rows-container" class="space-y-3 mt-2"></div>
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

            <div class="mt-8"><button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 px-6 rounded"> Simpan Invoice </button></div>
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
    let pekerjaanRowIndex = 0;
    let dpRowIndex = 0;

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
        newRow.querySelector('input[type="text"]').name = `pekerjaan[${pekerjaanRowIndex}][nama]`;
        newRow.querySelector('.pekerjaan-harga').name = `pekerjaan[${pekerjaanRowIndex}][harga]`;

        newRow.querySelector('.calc-trigger').addEventListener('input', calculateAllTotals);
        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            calculateAllTotals();
        });

        pekerjaanContainer.appendChild(newRow);
        pekerjaanRowIndex++;
        calculateAllTotals(); // Hitung ulang saat baris baru ditambah
    });

    // --- Logika Tambah Baris (DP) ---
    const dpContainer = document.getElementById('dp-rows-container');
    const addDpBtn = document.getElementById('add-dp-btn');
    const dpTemplate = document.getElementById('dp-row-template').content.firstElementChild;

    addDpBtn.addEventListener('click', () => {
        const newRow = dpTemplate.cloneNode(true);
        newRow.querySelector('input[type="text"]').name = `dp[${dpRowIndex}][keterangan]`;
        newRow.querySelector('.dp-jumlah').name = `dp[${dpRowIndex}][jumlah]`;

        newRow.querySelector('.calc-trigger').addEventListener('input', calculateAllTotals);
        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            calculateAllTotals();
        });

        dpContainer.appendChild(newRow);
        dpRowIndex++;
        calculateAllTotals(); // Hitung ulang saat baris baru ditambah
    });

    // Inisialisasi kalkulasi saat halaman dimuat
    calculateAllTotals();

    // Peringatan otomatis jika penawaran sudah pernah diubah menjadi invoice
    @if($hasInvoice && !request('confirmed'))
        if (typeof window.showInvoiceWarningModal === 'function') {
            window.showInvoiceWarningModal({
                url: null, // Cukup tutup modal agar pengguna bisa melanjutkan form
                cancelUrl: '{{ route('histori.index') }}', // Kembalikan ke halaman histori jika dibatalkan
                noSurat: '{{ $noSuratLengkap }}',
                namaKlien: '{{ addslashes($offer->nama_klien) }}',
                existingInvoices: '{{ addslashes($invList) }}'
            });
        }
    @endif

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