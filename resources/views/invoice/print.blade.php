<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* 1. STANDAR PRINT A4 - MARGIN NARROW (12.7mm / 0.5 in) */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12.7mm;
            }

            html, body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11pt;
            }

            .no-print {
                display: none !important;
            }

            /* Container mengikuti A4 Murni dengan Margin Narrow */
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
            }

            /* LOGIKA PRINT TANPA KOP */
            .hide-header-on-print .invoice-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 38mm !important;
            }

            /* HINDARI TERPOTONG KE HALAMAN KEDUA */
            table {
                page-break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }

            .signature-section {
                page-break-inside: avoid;
            }
        }

        /* 2. TAMPILAN LAYAR (PREVIEW A4) */
        body {
            background-color: #cbd5e1;
            font-family: Arial, Helvetica, sans-serif;
        }

        #main-container {
            background-color: white;
            width: 21.0cm;
            min-height: 29.7cm;
            margin: 20px auto;
            padding: 12.7mm 15mm;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .sans {
            font-family: Arial, Helvetica, sans-serif;
        }

        .nav-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body class="text-black">

    {{-- Tombol Navigasi Terapung (Hanya Muncul di Layar) --}}
    <div class="nav-floating no-print">
        <a href="{{ route('invoice.histori') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded shadow-lg transition text-sm flex items-center gap-1.5">
            &larr; Kembali ke Histori
        </a>
        <button onclick="printWithHeader()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition text-sm">
            <span>🖨️</span> Cetak Normal
        </button>
        <button onclick="printWithoutHeader()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition text-sm">
            <span>📄</span> Tanpa Kop Surat
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg transition text-sm">
            Tutup
        </button>
    </div>

    <div id="main-container">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-3 invoice-header">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat" class="w-full h-auto">
            </div>
        </header>

        <section class="mt-4 flex justify-between text-sm sans">
            <div class="w-1/2">
                <p class="font-bold mb-0.5 text-xs text-gray-500">TO:</p>
                <p class="font-bold text-base uppercase leading-tight">{{ $invoice->nama_klien }}</p>

                @if($invoice->purchaseOrder && $invoice->purchaseOrder->alamat_detail)
                <p class="text-gray-700 text-xs mt-1 leading-snug">{{ $invoice->purchaseOrder->alamat_detail }}</p>
                @elseif($invoice->offer && $invoice->offer->client_details)
                <p class="text-gray-700 text-xs mt-1 leading-snug">{{ $invoice->offer->client_details }}</p>
                @endif

                <p class="mt-3 font-bold text-xs text-gray-500">Attn:</p>
                <p class="text-sm font-medium">{{ $invoice->nama_klien }}</p>
            </div>
            <div class="w-1/2 text-right">
                <div class="flex justify-end mb-1 text-xs">
                    <span class="w-24 text-left font-bold">Tanggal</span>
                    <span class="text-left">: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</span>
                </div>

                {{-- NO INVOICE SESUAI DATABASE (KONSISTEN DENGAN HISTORI) --}}
                <div class="flex justify-end text-xs">
                    <span class="w-24 text-left font-bold">Invoice No.</span>
                    <span class="text-left font-semibold">: {{ $invoice->no_invoice }}</span>
                </div>
            </div>
        </section>

        <section class="mt-5 text-sm">
            <p class="mb-1.5 text-xs">Bersama ini kami sampaikan tagihan untuk:</p>
            <div class="text-xs space-y-1">
                <p><span class="font-medium w-16 inline-block">Project</span>: {{ optional($invoice->offer)->perihal ?? 'Pengecatan dan Supply Cat Jotun Paints' }}</p>
                @if($invoice->purchaseOrder && $invoice->purchaseOrder->alamat_detail)
                <p><span class="font-medium w-16 inline-block">Alamat</span>: {{ $invoice->purchaseOrder->alamat_detail }}</p>
                @elseif($invoice->offer && $invoice->offer->client_details)
                <p><span class="font-medium w-16 inline-block">Alamat</span>: {{ $invoice->offer->client_details }}</p>
                @endif
                @if(!empty($invoice->catatan_tambahan))
                <p><span class="font-medium w-16 inline-block">Catatan</span>: {!! nl2br(e($invoice->catatan_tambahan)) !!}</p>
                @endif
            </div>

            <table class="w-full mt-3.5 border-collapse border border-black text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black py-1.5 px-2 text-center font-semibold w-12">No.</th>
                        <th class="border border-black py-1.5 px-2 text-left font-semibold">Keterangan</th>
                        <th class="border border-black py-1.5 px-2 text-right font-semibold w-44">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black py-1.5 px-2 text-center font-medium">1</td>
                        <td class="border border-black py-1.5 px-2">Total Tagihan (sesuai Penawaran)</td>
                        <td class="border border-black py-1.5 px-2 text-right font-medium">Rp {{ number_format($invoice->total_penawaran, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->additions as $index => $addition)
                    <tr>
                        <td class="border border-black py-1.5 px-2 text-center font-medium">{{ $index + 2 }}</td>
                        <td class="border border-black py-1.5 px-2">{{ $addition->nama_pekerjaan }}</td>
                        <td class="border border-black py-1.5 px-2 text-right font-medium">Rp {{ number_format($addition->harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td class="border border-black py-1 px-2 h-4"></td>
                        <td class="border border-black py-1 px-2"></td>
                        <td class="border border-black py-1 px-2"></td>
                    </tr>

                    <tr class="font-medium">
                        <td colspan="2" class="border border-black py-1.5 px-2 text-right font-bold">TOTAL</td>
                        <td class="border border-black py-1.5 px-2 text-right font-bold">Rp {{ number_format($invoice->total_penawaran + $invoice->total_tambahan, 0, ',', '.') }}</td>
                    </tr>

                    @if($invoice->diskon > 0)
                    <tr class="font-medium">
                        <td colspan="2" class="border border-black py-1.5 px-2 text-right">Diskon</td>
                        <td class="border border-black py-1.5 px-2 text-right text-red-600 font-semibold">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr class="font-bold bg-gray-100">
                        <td colspan="2" class="border border-black py-1.5 px-2 text-right">TOTAL TAGIHAN</td>
                        <td class="border border-black py-1.5 px-2 text-right">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->payments as $payment)
                    <tr class="font-medium text-gray-600">
                        <td colspan="2" class="border border-black py-1.5 px-2 text-right">{{ $payment->keterangan }}</td>
                        <td class="border border-black py-1.5 px-2 text-right text-green-600 font-semibold">- Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-bold text-sm bg-gray-200">
                        <td colspan="2" class="border border-black py-2 px-2 text-right tracking-wide">SISA PEMBAYARAN</td>
                        <td class="border border-black py-2 px-2 text-right text-sm">Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="mt-8 flex justify-between text-xs signature-section">
            <div class="text-center">
                <p>Hormat kami,</p>
                <p class="font-semibold">PT. Tasniem Gerai Inspirasi</p>
                <div class="h-24 w-44 relative my-1 mx-auto">
                    <img src="{{ asset('images/ttd.png') }}" alt="Logo & Tanda Tangan" class="h-24 opacity-100 mx-auto object-contain">
                </div>
                <p class="font-bold text-gray-900 uppercase">SAMSU RIZAL</p>
                <p class="text-gray-600 text-[11px]">General Manager</p>
            </div>
            <div class="text-left pt-2">
                <p class="font-semibold text-xs">Pembayaran melalui Bank:</p>
                <p class="text-gray-800 text-xs">a/n PT. Tasniem Gerai Inspirasi</p>
                <p class="text-gray-800 text-xs">Bank BRI Cab. Nagoya</p>
                <p class="font-bold text-gray-900 text-xs">Rek. No. 0331 - 0100 1817 306</p>
            </div>
        </section>
    </div>

    <script>
        // Fungsi Cetak Normal
        function printWithHeader() {
            document.body.classList.remove('hide-header-on-print');
            window.print();
        }

        // Fungsi Cetak Tanpa Kop
        function printWithoutHeader() {
            document.body.classList.add('hide-header-on-print');
            window.print();
        }
    </script>
</body>

</html>