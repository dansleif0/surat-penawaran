<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }

            body {
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
                max-width: none !important;
                margin: 0 auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            table {
                page-break-inside: auto;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        body {
            background-color: #cbd5e1;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        #main-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 12mm 15mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
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

<body class="text-black antialiased">

    @php
        // Helper fungsi terbilang angka ke teks Bahasa Indonesia
        if (!function_exists('penyebut_indo')) {
            function penyebut_indo($nilai) {
                $nilai = abs($nilai);
                $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
                $temp = "";
                if ($nilai < 12) {
                    $temp = " " . $huruf[$nilai];
                } else if ($nilai < 20) {
                    $temp = penyebut_indo($nilai - 10) . " belas";
                } else if ($nilai < 100) {
                    $temp = penyebut_indo((int)($nilai/10)) . " puluh" . penyebut_indo($nilai % 10);
                } else if ($nilai < 200) {
                    $temp = " seratus" . penyebut_indo($nilai - 100);
                } else if ($nilai < 1000) {
                    $temp = penyebut_indo((int)($nilai/100)) . " ratus" . penyebut_indo($nilai % 100);
                } else if ($nilai < 2000) {
                    $temp = " seribu" . penyebut_indo($nilai - 1000);
                } else if ($nilai < 1000000) {
                    $temp = penyebut_indo((int)($nilai/1000)) . " ribu" . penyebut_indo($nilai % 1000);
                } else if ($nilai < 1000000000) {
                    $temp = penyebut_indo((int)($nilai/1000000)) . " juta" . penyebut_indo($nilai % 1000000);
                } else if ($nilai < 1000000000000) {
                    $temp = penyebut_indo((int)($nilai/1000000000)) . " milyar" . penyebut_indo(fmod($nilai,1000000000));
                } else if ($nilai < 1000000000000000) {
                    $temp = penyebut_indo((int)($nilai/1000000000000)) . " trilyun" . penyebut_indo(fmod($nilai,1000000000000));
                }
                return $temp;
            }
        }

        if (!function_exists('terbilang_indo')) {
            function terbilang_indo($nilai) {
                if($nilai <= 0) return "-";
                $hasil = trim(penyebut_indo($nilai));
                return ucwords($hasil) . " rupiah";
            }
        }
    @endphp

    {{-- Tombol Navigasi Terapung --}}
    <div class="nav-floating no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition text-sm">
            <span>🖨️</span> Cetak Invoice
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg transition text-sm">
            Tutup
        </button>
    </div>

    <div id="main-container">

        {{-- Header Invoice --}}
        <div class="grid grid-cols-12 items-start gap-1 mb-4">
            {{-- Top Left (KOP SURAT INVOICE) --}}
            <div class="col-span-6 text-xs leading-tight">
                <div class="flex items-center gap-3 mb-1">
                    @if(file_exists(public_path('images/logo-tasniem.png')))
                        <img src="{{ asset('images/logo-tasniem.png') }}" style="height: 65px !important; width: auto !important;" class="object-contain shrink-0">
                    @elseif(file_exists(public_path('images/logo-app.png')))
                        <img src="{{ asset('images/logo-app.png') }}" style="height: 65px !important; width: auto !important;" class="object-contain shrink-0">
                    @endif
                    <div class="text-xs leading-tight font-sans">
                        <h1 class="font-bold text-sm uppercase text-black tracking-tight mb-0.5">PT. TASNIEM GERAI INSPIRASI</h1>
                        <p>Komp. Ruko KDA Junction Blok C no 8-9 Batam Center</p>
                        <p>Phone / Whatsapp : +62 853-6114-9597</p>
                        <p>Website : https://tasniemgroup.com</p>
                    </div>
                </div>
                <div class="mt-2 text-xs">
                    <p><span class="font-bold inline-block w-24">INVOICE NO</span> : {{ $invoice->no_invoice }}</p>
                    <p><span class="font-bold inline-block w-24">TANGGAL</span> : {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</p>
                </div>
            </div>

            {{-- Top Center --}}
            <div class="col-span-2 text-center pt-2">
                <h2 class="font-extrabold text-lg border-b border-black inline-block tracking-wider uppercase">
                    INVOICE
                </h2>
            </div>

            {{-- Top Right (LOGO JOTUN EXPLICIT HIGH HEIGHT 70px) --}}
            <div class="col-span-4 text-xs leading-tight pl-2">
                <div class="flex justify-end mb-2">
                    @if(file_exists(public_path('images/logo-jotun.png')))
                        <img src="{{ asset('images/logo-jotun.png') }}" style="height: 70px !important; max-height: 90px !important; width: auto !important;" class="object-contain">
                    @endif
                </div>
                <div class="flex items-start">
                    <span class="w-16 font-semibold shrink-0">Kepada</span>
                    <span class="mr-1">:</span>
                    <div class="font-bold uppercase">
                        {{ $invoice->nama_klien }}
                        @if($invoice->offer && $invoice->offer->client_details)
                            <div class="font-normal normal-case text-xs text-gray-800 mt-0.5">
                                {{ $invoice->offer->client_details }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center mt-1">
                    <span class="w-16 font-semibold shrink-0">Telepon</span>
                    <span class="mr-1">:</span>
                    <span>{{ optional($invoice->offer)->phone ?? '' }}</span>
                </div>
                <div class="flex items-center mt-1">
                    <span class="w-16 font-semibold shrink-0">Sales</span>
                    <span class="mr-1">:</span>
                    <span class="font-semibold uppercase">{{ optional($invoice->offer)->sales ?? 'YASRI' }}</span>
                </div>
            </div>
        </div>

        {{-- Tabel Invoice --}}
        <div class="mt-4">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="border-t border-b border-black uppercase text-left font-bold">
                        <th class="py-1.5 px-1 w-[4%] text-center">NO</th>
                        <th class="py-1.5 px-2 w-[38%]">NAMA BARANG</th>
                        <th class="py-1.5 px-2 w-[12%] text-center">JUMLAH</th>
                        <th class="py-1.5 px-2 w-[6%] text-center">BONUS</th>
                        <th class="py-1.5 px-2 w-[11%] text-right">@HARGA</th>
                        <th class="py-1.5 px-2 w-[11%] text-right">HARGA</th>
                        <th class="py-1.5 px-2 w-[7%] text-center">DISCOUNT</th>
                        <th class="py-1.5 px-2 w-[11%] text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y-0">
                    @php $invRowNo = 1; @endphp

                    @if($invoice->offer && $invoice->offer->items && $invoice->offer->items->count() > 0)
                        @foreach($invoice->offer->items as $item)
                            @php
                                $hargaSatuan = $item->harga_per_m2;
                                $qty = $item->volume;
                                $subHarga = $hargaSatuan * $qty;
                                $discVal = 0;
                                $totalBaris = $subHarga - $discVal;
                            @endphp
                            <tr class="align-top font-sans">
                                <td class="py-1 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                <td class="py-1 px-2 font-medium italic">
                                    <div class="flex justify-between items-center pr-4">
                                        <span>{{ $item->nama_produk }}</span>
                                        @if(!empty($item->area_dinding) && $item->area_dinding !== '-')
                                            <span class="not-italic font-normal ml-2 text-xs">{{ $item->area_dinding }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-1 px-2 text-center whitespace-nowrap font-medium">
                                    {{ $qty + 0 }} {{ $item->satuan ?? 'CAN' }}
                                </td>
                                <td class="py-1 px-2 text-center"></td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">
                                    {{ number_format($hargaSatuan, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">
                                    {{ number_format($subHarga, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-2 text-center">- &nbsp;0.00%</td>
                                <td class="py-1 px-2 text-right whitespace-nowrap font-medium">
                                    {{ number_format($totalBaris, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        @foreach($invoice->offer->jasaItems as $jasa)
                            <tr class="align-top font-sans">
                                <td class="py-1 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                <td class="py-1 px-2 font-medium italic">
                                    {{ $jasa->nama_jasa }}
                                </td>
                                <td class="py-1 px-2 text-center whitespace-nowrap font-medium">
                                    {{ $jasa->volume + 0 }} {{ $jasa->satuan ?? 'Ls' }}
                                </td>
                                <td class="py-1 px-2 text-center"></td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">
                                    {{ number_format($jasa->harga_satuan ?? $jasa->harga_jasa, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">
                                    {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-2 text-center">- &nbsp;0.00%</td>
                                <td class="py-1 px-2 text-right whitespace-nowrap font-medium">
                                    {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                    @elseif($invoice->status === 'merge' && $invoice->mergedItems && $invoice->mergedItems->count() > 0)
                        @foreach($invoice->mergedItems as $mItem)
                            <tr class="align-top font-sans">
                                <td class="py-1 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                <td class="py-1 px-2 font-medium italic">
                                    Invoice {{ $mItem->invoice_no }} - {{ $mItem->keterangan ?? 'Pembayaran' }}
                                </td>
                                <td class="py-1 px-2 text-center whitespace-nowrap font-medium">1 LS</td>
                                <td class="py-1 px-2 text-center"></td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                                <td class="py-1 px-2 text-right whitespace-nowrap">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                                <td class="py-1 px-2 text-center">- &nbsp;0.00%</td>
                                <td class="py-1 px-2 text-right whitespace-nowrap font-medium">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="py-4 text-center text-gray-500 italic">Belum ada rincian barang invoice.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="border-b border-black w-full"></div>
        </div>

        {{-- Bottom Invoice Summary & Notes (RAPAT LANGSUNG DI BAWAH TABEL) --}}
        <div class="grid grid-cols-12 gap-4 mt-2 text-xs font-sans">
            {{-- Left Info --}}
            <div class="col-span-7 space-y-1">
                <p><span class="font-bold">CREDIT TERM</span> : 0 hari &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="font-bold">JATUH TEMPO</span> : {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</p>
                <p><span class="font-bold">Catatan</span> : {{ $invoice->offer->client_details ?? ($invoice->nama_klien . ', ' . ($invoice->offer->perihal ?? '')) }}</p>
                @if($invoice->offer && $invoice->offer->no_po)
                    <p class="pl-16"><span class="font-bold">NO PO</span> : {{ $invoice->offer->no_po }}</p>
                @endif
                <p><span class="font-bold">Terbilang</span> : {{ terbilang_indo($invoice->grand_total) }}</p>
                <p><span class="font-bold">Printed By</span> : {{ auth()->user()->name ?? 'Admin' }}, {{ now()->format('H:i:s, l, d F Y') }}</p>
            </div>

            {{-- Right Summary --}}
            <div class="col-span-5 text-xs">
                <div class="flex justify-between py-0.5">
                    <span class="font-bold uppercase">TOTAL</span>
                    <span>: IDR &nbsp;&nbsp;{{ number_format($invoice->total_penawaran + $invoice->total_tambahan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="font-bold uppercase">DISCOUNT</span>
                    <span>: IDR &nbsp;&nbsp;{{ $invoice->diskon > 0 ? number_format($invoice->diskon, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex justify-between py-0.5">
                    <span class="font-bold uppercase">PPN</span>
                    <span>: IDR &nbsp;&nbsp;-</span>
                </div>
                <div class="flex justify-between py-1 border-t border-black font-extrabold text-sm">
                    <span class="uppercase">GRAND TOTAL</span>
                    <span>: IDR &nbsp;&nbsp;{{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Invoice Signatures (TANPA TTD & CAP OTOMATIS) --}}
        <div class="mt-12 grid grid-cols-4 gap-6 text-xs text-center font-sans">
            <div class="flex flex-col items-center justify-between h-20">
                <p>Yang Menerima,</p>
                <div class="w-full border-b border-black"></div>
            </div>
            <div class="flex flex-col items-center justify-between h-20">
                <p>Kepala Gudang,</p>
                <div class="w-full border-b border-black"></div>
            </div>
            <div class="flex flex-col items-center justify-between h-20">
                <p>Supir/Helper,</p>
                <div class="w-full border-b border-black"></div>
            </div>
            <div class="flex flex-col items-center justify-between h-20">
                <p>Hormat kami,</p>
                <div class="w-full border-b border-black mt-auto"></div>
            </div>
        </div>

    </div>

</body>

</html>