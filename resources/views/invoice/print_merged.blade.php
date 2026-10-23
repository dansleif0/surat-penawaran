<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Piutang Statement - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* 1. STANDAR PRINT A4 - 1 HALAMAN */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12.7mm;
            }

            html, body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-family: Arial, Helvetica, sans-serif;
            }

            .no-print {
                display: none !important;
            }

            #main-container {
                width: 100% !important;
                max-width: 100% !important;
                min-height: 0 !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                display: block !important;
                box-sizing: border-box !important;
                page-break-after: avoid;
                break-after: avoid;
            }

            .hide-header-on-print .statement-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 38mm !important;
            }

            table {
                page-break-inside: avoid;
                break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            .signature-section {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }

        /* 2. TAMPILAN LAYAR (PREVIEW A4) */
        body {
            background-color: #cbd5e1;
            font-family: Arial, Helvetica, sans-serif;
        }

        #main-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 12.7mm 15mm;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
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
    <div id="main-container">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-3 statement-header">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- SUB HEADER & CLIENT BOX --}}
        <section class="mt-2 flex justify-between items-start text-xs sm:text-sm">
            <div class="w-1/2 pr-4">
                <h2 class="text-xl font-extrabold underline tracking-wide text-black">PIUTANG STATEMENT</h2>
                <p class="font-bold text-sm mt-1 text-black">
                    ON : {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y') }}
                </p>
                <p class="text-[11px] text-gray-600 tracking-wider mt-0.5">CR, RTC, DN, CN, DP</p>
            </div>

            <div class="w-1/2 flex justify-end">
                <div class="border border-black p-2.5 rounded-none w-full max-w-sm text-xs leading-relaxed">
                    <p class="font-semibold text-black mb-0.5">To :</p>
                    <p class="font-bold text-sm uppercase text-black">{{ $invoice->nama_klien }}</p>
                    @if($invoice->offer && $invoice->offer->client_details)
                        <p class="text-black">{{ $invoice->offer->client_details }}</p>
                    @endif
                    @if(!empty($invoice->catatan_tambahan))
                        <p class="text-black mt-0.5">Catatan: {!! nl2br(e($invoice->catatan_tambahan)) !!}</p>
                    @endif
                    <p class="text-black mt-0.5 font-medium">Phone : {{ optional($invoice->offer)->client_details ? '' : '-' }}</p>
                </div>
            </div>
        </section>

        {{-- TABEL PIUTANG STATEMENT --}}
        <section class="mt-4">
            <table class="w-full text-xs border-collapse border border-black text-black">
                <thead>
                    <tr class="border-b border-black text-center font-bold uppercase bg-gray-50">
                        <th class="border border-black px-2 py-1.5 w-[14%]">DATE</th>
                        <th class="border border-black px-2 py-1.5 w-[14%]">DUE DATE</th>
                        <th class="border border-black px-2 py-1.5 w-[22%]">INVOICE</th>
                        <th class="border border-black px-2 py-1.5 w-[10%]">KET</th>
                        <th class="border border-black px-2 py-1.5 w-[20%] text-right">DEBIT</th>
                        <th class="border border-black px-2 py-1.5 w-[20%] text-right">CREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningCredit = 0; @endphp
                    @forelse ($invoice->mergedItems as $item)
                        @php $runningCredit += $item->debit; @endphp
                        <tr class="text-center font-medium">
                            <td class="border border-black px-2 py-1.5 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                            </td>
                            <td class="border border-black px-2 py-1.5 whitespace-nowrap">
                                {{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="border border-black px-2 py-1.5 font-semibold">
                                {{ $item->invoice_no }}
                            </td>
                            <td class="border border-black px-2 py-1.5">
                                {{ $item->keterangan ?? $item->currency ?? '-' }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-right font-semibold">
                                {{ number_format($item->debit, 0, ',', '.') }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-right font-semibold">
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
                        <td colspan="5" class="border border-black px-2 py-1.5 text-right text-xs">
                            GRAND TOTAL
                        </td>
                        <td class="border border-black px-2 py-1.5 text-right text-xs">
                            {{ number_format($runningCredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        {{-- FOOTER SIGNATURE & BANK INFO --}}
        <section class="mt-6 flex justify-between items-end text-xs text-black signature-section">
            {{-- Bank & Received By --}}
            <div class="w-1/2">
                <p class="font-bold tracking-wide">BANKERS PT. TASNIEM GERAI INSPIRASI</p>
                <p class="font-semibold text-gray-800">BANK BRI (IDR) : 033101001817306</p>
                <p class="font-semibold text-gray-800">BANK MANDIRI (IDR) : 1090080002223</p>

                <div class="mt-8">
                    <p class="font-medium">Received By,</p>
                    <div class="h-16"></div>
                    <div class="w-52 border-b border-black"></div>
                </div>
            </div>

            {{-- Signature & Stamp --}}
            <div class="w-1/2 text-center">
                <p class="font-medium">Yours Faithfully,</p>
                <div class="my-1 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan & Stempel" class="h-20 mx-auto object-contain">
                </div>
                <p class="font-bold text-sm">Samsu Rizal</p>
                <div class="w-52 border-b border-black mx-auto my-0.5"></div>
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
