<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Piutang Statement - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* RESET STANDAR UNTUK PRINT MARGIN NARROW */
        @media print {
            @page {
                size: A4;
                margin: 12.7mm;
            }

            body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            #main-container {
                width: 210mm;
                margin: 0 auto !important;
                padding: 10mm 12.7mm !important;
                box-shadow: none !important;
                border: none !important;
                float: none !important;
            }

            .hide-header-on-print .statement-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 55mm !important;
            }

            table {
                page-break-inside: auto;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .signature-section {
                page-break-inside: avoid;
            }
        }

        body {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900">

    {{-- BAR TOMBOL UNTUK OPSI PRINT (Hanya Tampil di Layar Monitor) --}}
    <div class="no-print bg-slate-800 text-white p-4 sticky top-0 z-50 shadow-md">
        <div class="max-w-4xl mx-auto flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="font-bold text-lg">Cetak Piutang Statement</h1>
                <p class="text-xs text-slate-300">Pilih opsi pencetakan di bawah ini</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="printWithHeader()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Lengkap (Dengan Kop)
                </button>

                <button onclick="printWithoutHeader()" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cetak di Kertas Kop Fisik
                </button>

                <a href="{{ route('invoice.show', $invoice->id) }}" class="bg-slate-600 hover:bg-slate-700 text-white text-sm font-bold py-2 px-4 rounded shadow transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA A4 --}}
    <div id="main-container" class="max-w-[21cm] mx-auto my-8 p-12 bg-white shadow-xl rounded border border-gray-200">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-4 statement-header">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- SUB HEADER & CLIENT BOX --}}
        <section class="mt-4 flex justify-between items-start text-sm font-sans">
            <div class="w-1/2 pr-4">
                <h2 class="text-xl font-extrabold underline tracking-wide text-black">PIUTANG STATEMENT</h2>
                <p class="font-bold text-sm mt-2 text-black">
                    ON : {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y') }}
                </p>
                <p class="text-[11px] text-gray-600 tracking-wider mt-1">CR, RTC, DN, CN, DP</p>
            </div>

            <div class="w-1/2 flex justify-end">
                <div class="border border-black p-3 rounded-none w-full max-w-sm text-xs leading-relaxed">
                    <p class="font-semibold text-black mb-1">To :</p>
                    <p class="font-bold text-sm uppercase text-black">{{ $invoice->nama_klien }}</p>
                    @if($invoice->offer && $invoice->offer->client_details)
                        <p class="text-black">{{ $invoice->offer->client_details }}</p>
                    @endif
                    <p class="text-black mt-1 font-medium">Phone : {{ optional($invoice->offer)->client_details ? '' : '-' }}</p>
                </div>
            </div>
        </section>

        {{-- TABEL PIUTANG STATEMENT --}}
        <section class="mt-6">
            <table class="w-full text-xs border-collapse border border-black text-black">
                <thead>
                    <tr class="border-b border-black text-center font-bold uppercase bg-gray-50">
                        <th class="border border-black p-2 w-[14%]">DATE</th>
                        <th class="border border-black p-2 w-[14%]">DUE DATE</th>
                        <th class="border border-black p-2 w-[22%]">INVOICE</th>
                        <th class="border border-black p-2 w-[10%]">KET</th>
                        <th class="border border-black p-2 w-[20%] text-right">DEBIT</th>
                        <th class="border border-black p-2 w-[20%] text-right">CREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningCredit = 0; @endphp
                    @forelse ($invoice->mergedItems as $item)
                        @php $runningCredit += $item->debit; @endphp
                        <tr class="text-center font-medium">
                            <td class="border border-black p-2 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                            </td>
                            <td class="border border-black p-2 whitespace-nowrap">
                                {{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="border border-black p-2 font-semibold">
                                {{ $item->invoice_no }}
                            </td>
                            <td class="border border-black p-2">
                                {{ $item->keterangan ?? $item->currency ?? '-' }}
                            </td>
                            <td class="border border-black p-2 text-right font-semibold">
                                {{ number_format($item->debit, 0, ',', '.') }}
                            </td>
                            <td class="border border-black p-2 text-right font-semibold">
                                {{ number_format($runningCredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-black p-4 text-center text-gray-500 italic">
                                Belum ada item data invoice yang digabungkan.
                            </td>
                        </tr>
                    @endforelse

                    {{-- ROW GRAND TOTAL --}}
                    <tr class="font-bold border-t-2 border-black">
                        <td colspan="5" class="border border-black p-2 text-right text-sm">
                            GRAND TOTAL
                        </td>
                        <td class="border border-black p-2 text-right text-sm">
                            {{ number_format($runningCredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        {{-- FOOTER SIGNATURE & BANK INFO --}}
        <section class="mt-8 flex justify-between items-end text-xs font-sans text-black signature-section">
            {{-- Bank & Received By --}}
            <div class="w-1/2">
                <p class="font-bold tracking-wide">BANKERS PT. TASNIEM GERAI INSPIRASI</p>
                <p class="font-semibold text-gray-800">BANK BRI (IDR) : 033101001817306</p>
                <p class="font-semibold text-gray-800">BANK MANDIRI (IDR) : 1090080002223</p>

                <div class="mt-12">
                    <p class="font-medium">Received By,</p>
                    <div class="h-16"></div>
                    <div class="w-56 border-b border-black"></div>
                </div>
            </div>

            {{-- Signature & Stamp --}}
            <div class="w-1/2 text-center">
                <p class="font-medium">Yours Faithfully,</p>
                <div class="my-1 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan & Stempel" class="h-20 mx-auto object-contain">
                </div>
                <p class="font-bold text-sm">Samsu Rizal</p>
                <div class="w-56 border-b border-black mx-auto my-0.5"></div>
                <p class="font-bold">PT. TASNIEM GERAI INSPIRASI</p>
            </div>
        </section>

    </div>

    <script>
        function printWithHeader() {
            document.body.classList.remove('hide-header-on-print');
            window.print();
        }

        function printWithoutHeader() {
            document.body.classList.add('hide-header-on-print');
            window.print();
        }
    </script>
</body>

</html>
