<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice & Surat Jalan - {{ $invoice->no_invoice }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* KONTROL CETAK (PRINT) PAS 1 HALAMAN 24.1cm x 27.9cm ATAU A4 */
        @media print {
            @page {
                size: 24.1cm 27.9cm;
                margin: 4mm 6mm;
            }

            html, body {
                width: 24.1cm;
                height: 27.9cm;
                margin: 0;
                padding: 0;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 9.5px;
                line-height: 1.2;
            }

            .no-print {
                display: none !important;
            }

            #main-container {
                width: 22.5cm !important;
                height: 27cm !important;
                margin: 0 auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                box-sizing: border-box !important;
            }

            .page-half {
                height: 13.1cm !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                box-sizing: border-box !important;
            }

            table {
                page-break-inside: avoid;
                width: 100%;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        /* TAMPILAN PRATINJAU MONITOR */
        body {
            background-color: #cbd5e1;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
        }

        #main-container {
            background-color: white;
            width: 24.1cm;
            height: 27.9cm;
            margin: 15px auto;
            padding: 6mm 10mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .page-half {
            height: 13.3cm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .nav-floating {
            position: fixed;
            top: 15px;
            right: 15px;
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

    {{-- Tombol Navigasi Terapung (Hanya di Layar Monitor) --}}
    <div class="nav-floating no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition text-sm">
            <span>🖨️</span> Cetak Halaman
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg transition text-sm">
            Tutup
        </button>
    </div>

    {{-- KONTEN UTAMA DENGAN UKURAN 24.1cm x 27.9cm --}}
    <div id="main-container">

        <!-- ========================================================================= -->
        <!-- PARUH ATAS: INVOICE                                                       -->
        <!-- ========================================================================= -->
        <div class="page-half invoice-section">

            <div>
                {{-- Header Invoice --}}
                <div class="grid grid-cols-12 items-start gap-1">
                    {{-- Top Left (KOP SURAT TGI UNTUK INVOICE) --}}
                    <div class="col-span-6 text-[9.5px] leading-tight">
                        <div class="flex items-center gap-3 mb-1">
                            @if(file_exists(public_path('images/logo-tasniem.png')))
                                <img src="{{ asset('images/logo-tasniem.png') }}" style="height: 65px !important; width: auto !important;" class="object-contain shrink-0">
                            @elseif(file_exists(public_path('images/logo-app.png')))
                                <img src="{{ asset('images/logo-app.png') }}" style="height: 65px !important; width: auto !important;" class="object-contain shrink-0">
                            @endif
                            <div class="text-[9.5px] leading-tight font-sans">
                                <h1 class="font-bold text-[12px] uppercase text-black tracking-tight mb-0.5">PT. TASNIEM GERAI INSPIRASI</h1>
                                <p>Komp. Ruko KDA Junction Blok C no 8-9 Batam Center</p>
                                <p>Phone / Whatsapp : +62 853-6114-9597</p>
                                <p>Website : https://tasniemgroup.com</p>
                            </div>
                        </div>
                        <div class="mt-1 text-[9.5px]">
                            <p><span class="font-bold inline-block w-20">INVOICE NO</span> : {{ $invoice->no_invoice }}</p>
                            <p><span class="font-bold inline-block w-20">TANGGAL</span> : {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Top Center --}}
                    <div class="col-span-2 text-center pt-2">
                        <h2 class="font-extrabold text-sm md:text-base border-b border-black inline-block tracking-wider uppercase">
                            INVOICE
                        </h2>
                    </div>

                    {{-- Top Right (LOGO JOTUN EXPLICIT HIGH HEIGHT 60px) --}}
                    <div class="col-span-4 text-[9.5px] leading-tight pl-2">
                        <div class="flex justify-end mb-1">
                            @if(file_exists(public_path('images/logo-jotun.png')))
                                <img src="{{ asset('images/logo-jotun.png') }}" style="height: 40px !important; max-height: 50px !important; width: auto !important;" class="object-contain">
                            @endif
                        </div>
                        <div class="flex items-start">
                            <span class="w-14 font-semibold shrink-0">Kepada</span>
                            <span class="mr-1">:</span>
                            <div class="font-bold uppercase">
                                {{ $invoice->nama_klien }}
                                @if($invoice->offer && $invoice->offer->client_details)
                                    <div class="font-normal normal-case text-[8.5px] text-gray-800 mt-0.5">
                                        {{ $invoice->offer->client_details }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center mt-0.5">
                            <span class="w-14 font-semibold shrink-0">Telepon</span>
                            <span class="mr-1">:</span>
                            <span>{{ optional($invoice->offer)->phone ?? '' }}</span>
                        </div>
                        <div class="flex items-center mt-0.5">
                            <span class="w-14 font-semibold shrink-0">Sales</span>
                            <span class="mr-1">:</span>
                            <span class="font-semibold uppercase">{{ optional($invoice->offer)->sales ?? 'YASRI' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tabel Invoice --}}
                <div class="mt-2">
                    <table class="w-full text-[9px] border-collapse">
                        <thead>
                            <tr class="border-t border-b border-black uppercase text-left font-bold">
                                <th class="py-1 px-1 w-[4%] text-center">NO</th>
                                <th class="py-1 px-1 w-[38%]">NAMA BARANG</th>
                                <th class="py-1 px-1 w-[12%] text-center">JUMLAH</th>
                                <th class="py-1 px-1 w-[6%] text-center">BONUS</th>
                                <th class="py-1 px-1 w-[11%] text-right">@HARGA</th>
                                <th class="py-1 px-1 w-[11%] text-right">HARGA</th>
                                <th class="py-1 px-1 w-[7%] text-center">DISCOUNT</th>
                                <th class="py-1 px-1 w-[11%] text-right">TOTAL</th>
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
                                        $strUkuran = str_replace(',', '.', $item->area_dinding);
                                        $ukuranVal = (float) filter_var($strUkuran, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        $satuanCetak = $ukuranVal > 5 ? 'PAIL' : 'CAN';
                                    @endphp
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                        <td class="py-0.5 px-1 font-medium italic">
                                            <div class="flex justify-between items-center pr-3">
                                                <span>{{ $item->nama_produk }}</span>
                                                @if(!empty($item->area_dinding) && $item->area_dinding !== '-')
                                                    <span class="not-italic font-normal ml-2 text-[8.5px]">{{ $item->area_dinding }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-0.5 px-1 text-center whitespace-nowrap font-medium">
                                            {{ $qty + 0 }} {{ $satuanCetak }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center"></td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">
                                            {{ number_format($hargaSatuan, 0, ',', '.') }}
                                        </td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">
                                            {{ number_format($subHarga, 0, ',', '.') }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center">- &nbsp;0.00%</td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap font-medium">
                                            {{ number_format($totalBaris, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach($invoice->offer->jasaItems as $jasa)
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                        <td class="py-0.5 px-1 font-medium italic">
                                            {{ $jasa->nama_jasa }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center whitespace-nowrap font-medium">
                                            {{ $jasa->volume + 0 }} {{ $jasa->satuan ?? 'Ls' }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center"></td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">
                                            {{ number_format($jasa->harga_satuan ?? $jasa->harga_jasa, 0, ',', '.') }}
                                        </td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">
                                            {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center">- &nbsp;0.00%</td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap font-medium">
                                            {{ number_format($jasa->harga_jasa, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                            @elseif($invoice->status === 'merge' && $invoice->mergedItems && $invoice->mergedItems->count() > 0)
                                @foreach($invoice->mergedItems as $mItem)
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $invRowNo++ }}.</td>
                                        <td class="py-0.5 px-1 font-medium italic">
                                            Invoice {{ $mItem->invoice_no }} - {{ $mItem->keterangan ?? 'Pembayaran' }}
                                        </td>
                                        <td class="py-0.5 px-1 text-center whitespace-nowrap font-medium">1 LS</td>
                                        <td class="py-0.5 px-1 text-center"></td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                                        <td class="py-0.5 px-1 text-center">- &nbsp;0.00%</td>
                                        <td class="py-0.5 px-1 text-right whitespace-nowrap font-medium">{{ number_format($mItem->debit, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="py-2 text-center text-gray-500 italic">Belum ada rincian barang invoice.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="border-b border-black w-full"></div>
                </div>

                {{-- Bottom Invoice Summary & Notes --}}
                <div class="grid grid-cols-12 gap-2 mt-1 text-[9px] font-sans">
                    {{-- Left Info --}}
                    <div class="col-span-7 space-y-0.5">
                        <p><span class="font-bold">CREDIT TERM</span> : 0 hari &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="font-bold">JATUH TEMPO</span> : {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</p>
                        <p><span class="font-bold">Catatan</span> : {{ $invoice->offer->client_details ?? ($invoice->nama_klien . ', ' . ($invoice->offer->perihal ?? '')) }}</p>
                        @if($invoice->offer && $invoice->offer->no_po)
                            <p class="pl-14"><span class="font-bold">NO PO</span> : {{ $invoice->offer->no_po }}</p>
                        @endif
                        <p><span class="font-bold">Terbilang</span> : {{ terbilang_indo($invoice->grand_total) }}</p>
                        <p><span class="font-bold">Printed By</span> : {{ auth()->user()->name ?? 'Admin' }}, {{ now()->format('H:i:s, l, d F Y') }}</p>
                    </div>

                    {{-- Right Summary --}}
                    <div class="col-span-5 text-[9px]">
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
                        <div class="flex justify-between py-0.5 border-t border-black font-extrabold text-[10px]">
                            <span class="uppercase">GRAND TOTAL</span>
                            <span>: IDR &nbsp;&nbsp;{{ number_format($invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Invoice Signatures --}}
            <div class="mt-2 grid grid-cols-4 gap-4 text-[9px] text-center font-sans">
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Yang Menerima,</p>
                    <div class="w-full border-b border-black"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Kepala Gudang,</p>
                    <div class="w-full border-b border-black"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Supir/Helper,</p>
                    <div class="w-full border-b border-black"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Hormat kami,</p>
                    <div class="w-full border-b border-black mt-auto"></div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- GARIS POTONG-POTONG (DASHED CUT LINE) ANTARA INVOICE & SURAT JALAN       -->
        <!-- ========================================================================= -->
        <div class="my-2 border-b-2 border-dashed border-black relative text-center shrink-0">
            <span class="bg-white px-3 text-[8.5px] text-gray-700 font-mono uppercase tracking-widest absolute -top-2 left-1/2 -translate-x-1/2">
                ✂ SURAT JALAN ✂
            </span>
        </div>

        <!-- ========================================================================= -->
        <!-- PARUH BAWAH: SURAT JALAN                                                  -->
        <!-- ========================================================================= -->
        <div class="page-half surat-jalan-section">

            <div>
                {{-- Header Surat Jalan --}}
                <div class="grid grid-cols-12 items-start gap-1">
                    {{-- Top Left --}}
                    <div class="col-span-5 text-[9.5px] leading-tight">
                        <h1 class="font-bold text-[10.5px] uppercase tracking-tight">PT. TASNIEM GERAI INSPIRASI</h1>
                        <p>Komp.Ruko KDA Junction Blok C 8-9</p>
                        <p>Batam Centre</p>
                        <div class="mt-1 text-[9.5px]">
                            <p><span class="font-bold inline-block w-16">NOMOR</span> : {{ $invoice->no_invoice }}</p>
                            <p><span class="font-bold inline-block w-16">TANGGAL</span> : {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Top Center --}}
                    <div class="col-span-3 text-center pt-2">
                        <h2 class="font-extrabold text-sm md:text-base border-b border-black inline-block tracking-wider uppercase">
                            SURAT JALAN
                        </h2>
                    </div>

                    {{-- Top Right --}}
                    <div class="col-span-4 text-[9.5px] leading-tight pl-2">
                        <div class="flex items-start">
                            <span class="w-14 font-semibold shrink-0">Kepada</span>
                            <span class="mr-1">:</span>
                            <div class="font-bold uppercase">
                                {{ $invoice->nama_klien }}
                                @if($invoice->offer && $invoice->offer->client_details)
                                    <div class="font-normal normal-case text-[8.5px] text-gray-800 mt-0.5">
                                        {{ $invoice->offer->client_details }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center mt-0.5">
                            <span class="w-14 font-semibold shrink-0">Telp.</span>
                            <span class="mr-1">:</span>
                            <span>{{ optional($invoice->offer)->phone ?? '' }}</span>
                        </div>
                        <div class="flex items-center mt-0.5">
                            <span class="w-14 font-semibold shrink-0">Fax</span>
                            <span class="mr-1">:</span>
                            <span></span>
                        </div>
                        <div class="flex items-center mt-0.5">
                            <span class="w-14 font-semibold shrink-0">Sales</span>
                            <span class="mr-1">:</span>
                            <span class="font-semibold uppercase">{{ optional($invoice->offer)->sales ?? 'YASRI' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tabel Surat Jalan --}}
                <div class="mt-2">
                    <table class="w-full text-[9px] border-collapse">
                        <thead>
                            <tr class="border-t border-b border-black uppercase text-left font-bold">
                                <th class="py-1 px-1 w-[4%] text-center">NO</th>
                                <th class="py-1 px-2 w-[52%]">NAMA BARANG</th>
                                <th class="py-1 px-2 w-[18%] text-center">JUMLAH</th>
                                <th class="py-1 px-2 w-[8%] text-center">BONUS</th>
                                <th class="py-1 px-2 w-[18%]">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            @php $sjRowNo = 1; @endphp

                            @if($invoice->offer && $invoice->offer->items && $invoice->offer->items->count() > 0)
                                @foreach($invoice->offer->items as $item)
                                    @php
                                        $strUkuran = str_replace(',', '.', $item->area_dinding);
                                        $ukuranVal = (float) filter_var($strUkuran, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        $satuanCetak = $ukuranVal > 5 ? 'PAIL' : 'CAN';
                                    @endphp
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $sjRowNo++ }}.</td>
                                        <td class="py-0.5 px-2 font-medium italic">
                                            <div class="flex justify-between items-center pr-3">
                                                <span>{{ $item->nama_produk }}</span>
                                                @if(!empty($item->area_dinding) && $item->area_dinding !== '-')
                                                    <span class="not-italic font-normal ml-2 text-[8.5px]">{{ $item->area_dinding }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-0.5 px-2 text-center whitespace-nowrap font-medium">
                                            {{ $item->volume + 0 }} {{ $satuanCetak }}
                                        </td>
                                        <td class="py-0.5 px-2 text-center"></td>
                                        <td class="py-0.5 px-2 leading-tight">
                                            {{ $item->keterangan ?? $item->warna ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach($invoice->offer->jasaItems as $jasa)
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $sjRowNo++ }}.</td>
                                        <td class="py-0.5 px-2 font-medium italic">
                                            {{ $jasa->nama_jasa }}
                                        </td>
                                        <td class="py-0.5 px-2 text-center whitespace-nowrap font-medium">
                                            {{ $jasa->volume + 0 }} {{ $jasa->satuan ?? 'Ls' }}
                                        </td>
                                        <td class="py-0.5 px-2 text-center"></td>
                                        <td class="py-0.5 px-2 leading-tight"></td>
                                    </tr>
                                @endforeach

                            @elseif($invoice->status === 'merge' && $invoice->mergedItems && $invoice->mergedItems->count() > 0)
                                @foreach($invoice->mergedItems as $mItem)
                                    <tr class="align-top font-sans">
                                        <td class="py-0.5 px-1 text-center font-medium">{{ $sjRowNo++ }}.</td>
                                        <td class="py-0.5 px-2 font-medium italic">
                                            Invoice {{ $mItem->invoice_no }} - {{ $mItem->keterangan ?? 'Pengiriman Cat' }}
                                        </td>
                                        <td class="py-0.5 px-2 text-center whitespace-nowrap font-medium">1 LS</td>
                                        <td class="py-0.5 px-2 text-center"></td>
                                        <td class="py-0.5 px-2 leading-tight">Tgl: {{ \Carbon\Carbon::parse($mItem->date)->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="py-2 text-center text-gray-500 italic">Belum ada rincian barang surat jalan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="border-b border-black w-full"></div>
                </div>

                {{-- Surat Jalan Notes & Printed By --}}
                <div class="mt-1 text-[9px] font-sans space-y-0.5">
                    <div class="flex items-start">
                        <span class="w-14 font-medium shrink-0">Catatan</span>
                        <span class="mr-1">:</span>
                        <div class="font-semibold uppercase leading-tight">
                            {{ $invoice->offer->client_details ?? ($invoice->nama_klien . ', ' . ($invoice->offer->perihal ?? '')) }}
                        </div>
                    </div>
                    <div class="flex items-center pt-0.5">
                        <span class="w-16 font-medium shrink-0">Printed By</span>
                        <span class="mr-1">:</span>
                        <span>
                            {{ auth()->user()->name ?? 'Admin' }}, {{ now()->format('H:i:s, l, d F Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Surat Jalan Signatures --}}
            <div class="mt-2 grid grid-cols-4 gap-4 text-[9px] text-center font-sans">
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Bag. Administrasi,</p>
                    <div class="w-full border-b border-black mt-auto"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Kepala Gudang,</p>
                    <div class="w-full border-b border-black mt-auto"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Supir/Helper,</p>
                    <div class="w-full border-b border-black mt-auto"></div>
                </div>
                <div class="flex flex-col items-center justify-between h-14">
                    <p>Yang Menerima,</p>
                    <div class="w-full border-b border-black mt-auto"></div>
                </div>
            </div>

        </div>

    </div>

</body>

</html>
