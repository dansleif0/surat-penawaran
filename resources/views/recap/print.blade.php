<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Biaya - #REC-{{ $recap->id }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
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
                padding: 15mm 20mm !important;
                box-shadow: none !important;
                border: none !important;
                float: none !important;
            }

            .hide-header-on-print .recap-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 60mm !important;
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
        }

        body {
            background-color: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
        }

        #main-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

    <div id="main-container" class="max-w-[21cm] mx-auto bg-white shadow-xl my-10 p-10 print:shadow-none print:my-0">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6 recap-header">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- JUDUL & INFORMASI SURAT --}}
        <section class="mt-6 flex justify-between items-start text-sm">
            <div>
                <h1 class="text-xl font-bold text-black tracking-tight mb-1">Rincian Analisis Rekap Biaya</h1>
                <p class="font-medium text-black">Klien : <span class="font-normal">{{ $recap->offer->nama_klien }}</span></p>
                <p class="font-medium text-black">Alamat Project : <span class="font-normal">{{ $recap->offer->client_details ?? '-' }}</span></p>
            </div>
            <div class="text-right">
                <p class="font-bold text-black italic">Diterbitkan pada</p>
                <p class="text-sm font-normal text-black">{{ \Carbon\Carbon::parse($recap->created_at)->translatedFormat('d F Y') }}</p>
            </div>
        </section>

        {{-- RINGKASAN BIAYA & NET PROFIT --}}
        <section class="mt-6">
            <table class="w-full border-collapse border border-black text-sm">
                <thead>
                    <tr class="bg-[#d9ead3] text-black">
                        <th class="border border-black p-2 text-left font-bold w-1/3">Total Penawaran</th>
                        <th class="border border-black p-2 text-left font-bold w-1/3">Total Modal (expense)</th>
                        <th class="border border-black p-2 text-left font-bold w-1/3">Net Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-3 text-lg font-bold text-blue-600 align-top">
                            Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}
                        </td>
                        <td class="border border-black p-3 text-lg font-bold text-red-600 align-top">
                            Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="border border-black p-3 text-lg font-bold @if($recap->margin >= 0) text-emerald-600 @else text-red-600 @endif align-top">
                            Rp {{ number_format($recap->margin, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        {{-- RINCIAN AUDIT MATERIAL & PEKERJAAN --}}
        <section class="mt-6">
            <table class="w-full border-collapse border border-black text-sm">
                <thead>
                    {{-- HEADER SUBTITEL AUDIT --}}
                    <tr>
                        <th colspan="6" class="border border-black p-2.5 text-center font-bold text-base bg-white">
                            Audit Material & Pekerjaan
                        </th>
                    </tr>
                    {{-- HEADER KOLOM --}}
                    <tr class="bg-[#d9e1f2] text-black font-bold">
                        <th class="border border-black p-2 text-left w-[15%]">TGL Keluar</th>
                        <th class="border border-black p-2 text-left w-[25%]">Item/Deskripsi</th>
                        <th class="border border-black p-2 text-left w-[20%]">Kategori</th>
                        <th class="border border-black p-2 text-center w-[10%]">QTY</th>
                        <th class="border border-black p-2 text-right w-[15%]">Harga satuan</th>
                        <th class="border border-black p-2 text-right w-[15%]">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recap->items as $item)
                    <tr>
                        <td class="border border-black p-2 whitespace-nowrap">
                            @if($item->tanggal_item)
                                {{ \Carbon\Carbon::parse($item->tanggal_item)->format('d/m/Y') }}
                            @elseif($item->created_at)
                                {{ $item->created_at->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="border border-black p-2">
                            <span class="font-bold">{{ $item->material }}</span>
                            @if($item->detail)
                                <br><span class="text-xs text-gray-600">{{ $item->detail }}</span>
                            @endif
                        </td>
                        <td class="border border-black p-2">
                            {{ $item->kategori ?? '-' }}
                        </td>
                        <td class="border border-black p-2 text-center font-medium">
                            {{ $item->qty + 0 }}
                        </td>
                        <td class="border border-black p-2 text-right whitespace-nowrap">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </td>
                        <td class="border border-black p-2 text-right font-medium whitespace-nowrap">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="border border-black p-4 text-center text-gray-500 italic">
                            Belum ada rincian item pengeluaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="border border-black p-2 font-bold text-right text-base" style="border-right: none;">
                            Total Modal Akhir
                        </td>
                        <td colspan="2" class="border border-black p-2 font-bold text-right text-base" style="border-left: none;">
                            Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
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
