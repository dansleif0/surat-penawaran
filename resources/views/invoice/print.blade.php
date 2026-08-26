<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* 1. RESET STANDAR UNTUK PRINT */
        @media print {
            @page {
                size: A4;
                margin: 0;
                /* Margin nol agar kita kontrol penuh lewat CSS */
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

            /* KONTROL PRESISI A4 */
            #main-container {
                width: 210mm;
                /* Jangan gunakan min-height: 297mm agar jika konten sedikit tidak memaksa 2 halaman */
                margin: 0 auto !important;
                padding: 15mm 20mm !important;
                /* Margin konten standar surat */
                box-shadow: none !important;
                border: none !important;
                float: none !important;
            }

            /* LOGIKA PRINT TANPA KOP */
            .hide-header-on-print .invoice-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 60mm !important;
                /* Sesuaikan dengan tinggi Kop fisik Anda */
            }

            /* ANTI TERPOTONG */
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

            /* Mencegah bagian tanda tangan terpisah sendirian di halaman baru */
            .signature-section {
                page-break-inside: avoid;
            }
        }

        /* 2. TAMPILAN LAYAR (PREVIEW) */
        body {

            background-color: #f3f4f6;
            /* Abu-abu netral */
        }

        #main-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            /* Simulasi kertas A4 di layar */
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

<body class="bg-gray-100 text-black">

    {{-- Tombol Navigasi Terapung (Hanya Muncul di Layar) --}}
    <div class="nav-floating no-print">
        <button onclick="printWithHeader()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition">
            <span>🖨️</span> Cetak Normal
        </button>
        <button onclick="printWithoutHeader()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition">
            <span>📄</span> Tanpa Kop Surat
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg transition">
            Tutup
        </button>
    </div>

    <div id="main-container" class="max-w-[21cm] mx-auto bg-white shadow-xl my-10 p-10 print:shadow-none print:my-0">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6 invoice-header">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat" class="w-full h-auto">
            </div>
        </header>

        <section class="mt-8 flex justify-between text-sm sans">
            <div class="w-1/2">
                <p class="font-bold mb-1">TO:</p>
                <p class="font-bold text-lg uppercase">{{ $invoice->nama_klien }}</p>

                @if($invoice->offer && $invoice->offer->client_details)
                <p class="text-gray-700">{{ $invoice->offer->client_details }}</p>
                @endif

                <p class="mt-4 font-bold">Attn:</p>
                <p>{{ $invoice->nama_klien }}</p>
            </div>
            <div class="w-1/2 text-right">
                <div class="flex justify-end mb-1">
                    <span class="w-24 text-left font-bold">Tanggal</span>
                    <span class="text-left">: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</span>
                </div>

                {{-- NO INVOICE SESUAI DATABASE (KONSISTEN DENGAN HISTORI) --}}
                <div class="flex justify-end">
                    <span class="w-24 text-left font-bold">Invoice No.</span>
                    <span class="text-left">: {{ $invoice->no_invoice }}</span>
                </div>
            </div>
        </section>

        <section class="mt-8 text-sm">
            <p class="mb-2">Bersama ini kami sampaikan tagihan untuk:</p>
            <p><span class="font-medium w-20 inline-block">Project</span>: {{ optional($invoice->offer)->perihal ?? 'Pengecatan dan Supply Cat Jotun Paints' }}</p>
            @if($invoice->offer && $invoice->offer->client_details)
            <p><span class="font-medium w-20 inline-block">Alamat</span>: {{ $invoice->offer->client_details }}</p>
            @endif

            <table class="w-full mt-4 border-collapse border border-black">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black p-2 text-left font-semibold">No.</th>
                        <th class="border border-black p-2 text-left font-semibold">Keterangan</th>
                        <th class="border border-black p-2 text-right font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-2 text-center">1</td>
                        <td class="border border-black p-2">Total Pengecatan (sesuai Penawaran)</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->total_penawaran, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->additions as $index => $addition)
                    <tr>
                        <td class="border border-black p-2 text-center">{{ $index + 2 }}</td>
                        <td class="border border-black p-2">{{ $addition->nama_pekerjaan }}</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($addition->harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td class="border border-black p-2 h-8"></td>
                        <td class="border border-black p-2"></td>
                        <td class="border border-black p-2"></td>
                    </tr>

                    <tr class="font-medium">
                        <td colspan="2" class="border border-black p-2 text-right">TOTAL</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->total_penawaran + $invoice->total_tambahan, 0, ',', '.') }}</td>
                    </tr>

                    @if($invoice->diskon > 0)
                    <tr class="font-medium">
                        <td colspan="2" class="border border-black p-2 text-right">Diskon</td>
                        <td class="border border-black p-2 text-right text-red-600">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr class="font-bold bg-gray-100">
                        <td colspan="2" class="border border-black p-2 text-right">TOTAL TAGIHAN</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->payments as $payment)
                    <tr class="font-medium text-gray-600">
                        <td colspan="2" class="border border-black p-2 text-right">{{ $payment->keterangan }}</td>
                        <td class="border border-black p-2 text-right text-green-600">- Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-bold text-xl bg-gray-200">
                        <td colspan="2" class="border border-black p-2 text-right">SISA PEMBAYARAN</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="mt-12 flex justify-between text-sm">
            <div class="text-center">
                <p>Hormat kami,</p>
                <p>PT. Tasniem Gerai Inspirasi</p>
                <div class="h-28 w-48 relative">
                    <img src="{{ asset('images/ttd.png') }}" alt="Logo & Tanda Tangan" class="h-28 opacity-100 mx-auto">
                </div>
                <p class="font-bold text-gray-800">SAMSU RIZAL</p>
                <p class="text-gray-600">General Manager</p>
            </div>
            <div class="text-left">
                <p class="font-medium">Pembayaran melalui Bank:</p>
                <p>a/n PT. Tasniem Gerai Inspirasi</p>
                <p>Bank BRI Cab. Nagoya</p>
                <p>Rek. No. 0331 - 0100 1817 306</p>
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