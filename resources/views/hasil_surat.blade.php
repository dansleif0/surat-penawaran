@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">

    <!-- Tombol Aksi -->
    <div class="max-w-4xl mx-auto mb-4 flex justify-between gap-2 print:hidden">
        <a href="{{ route('histori.index') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300 transition-colors">
            &larr; Kembali
        </a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                🖨️ Print
            </button>
            {{-- Tombol Download PDF sudah dihapus --}}
            <a href="{{ route('invoice.create_from_offer', $offer->id) }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition-colors">
                Buat Invoice &rarr;
            </a>
        </div>
    </div>

    <!-- Konten Surat -->
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg" id="surat-penawaran">

        <header class="border-b-2 border-gray-300 pb-4">
            <div class="flex justify-between items-center">
                <div class="w-1/4">
                    <img src="{{ asset('images/logo-tasniem.png') }}" alt="Logo Tasniem" class="w-33">
                </div>
                <div class="w-1/2 text-center">
                    <h1 class="text-xl font-bold text-gray-800">PT. TASNIEM GERAI INSPIRASI</h1>
                    <p class="text-sm font-semibold text-gray-600">(The First Inspiration Center of Jotun Indonesia)</p>
                    <p class="text-xs text-gray-500 mt-2">Komp. Ruko KDA Junction Blok C 8 - 9 Batam Centre, Batam, Kepri - Indonesia</p>
                    <p class="text-xs text-gray-500">Telp : +62 778-7485 999, 7080 549 Fax : +62 778-7485 789</p>
                    <p class="text-xs text-gray-500">E-mail : tgi_team040210@yahoo.com Website : www.jotun.com/ap</p>
                </div>
                <div class="w-1/4 flex justify-end">
                    <img src="{{ asset('images/logo-jotun.png') }}" alt="Logo Jotun" class="w-40">
                </div>
            </div>
        </header>

        <section class="mt-8">
            <p class="text-gray-700">Batam, {{ $offer->created_at->format('d F Y') }}</p>
            <p class="text-gray-700">Nomor. : 00{{ $offer->id }}/SP/TGI-1/IX/2025</p>
        </section>

        <section class="mt-8">
            <p class="text-gray-600">Kepada Yth,</p>
            <h3 class="text-md font-bold text-gray-800">{{ $offer->nama_klien }}</h3>
            @if($offer->client_details)
            <p class="text-sm text-gray-700">{{ $offer->client_details }}</p>
            @endif
            <p class="text-gray-700 mt-2">Dengan Hormat,</p>
        </section>

        <section class="mt-4 space-y-4 text-sm text-gray-700 leading-relaxed">
            <p>Kami PT. TASNIEM GERAI INSPIRASI adalah dealer resmi PT. JOTUN INDONESIA, didirikan pada tanggal 4 Februari 2010, Konsep Inspirasi Centre pertama di kota Batam dan pertama di Indonesia, website <a href="https://tasniemgroup.com" class="text-blue-600 underline">https://tasniemgroup.com</a>.</p>
            <div>
                <p>Kami PT Tasniem Gerai Inspirasi begerak di bidang Painting Dan Pekerjaan Sipil lainnya :</p>
                <ol class="list-decimal list-inside ml-4">
                    <li>Pekerjaan pengecatan dan perawatan gedung</li>
                    <li>Pemasangan partisi dan plafon Finising gypsum dan plafon sunda Plafon</li>
                    <li>Pekerjaan Pengecatan Lantai epoxy</li>
                </ol>
            </div>
            <p>Dengan ini kami sampaikan penawaran Upah Jasa pengecatan :</p>
        </section>

        <!-- Tabel Item -->
        <section class="mt-8">
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="p-3 font-semibold uppercase whitespace-nowrap w-[28%]">Area Pekerjaan</th>
                            {{-- KOLOM BARU --}}
                            <th class="p-3 font-semibold uppercase whitespace-nowrap w-[12%]">Nama Brand</th>
                            <th class="p-3 font-semibold uppercase whitespace-nowrap w-[20%]">Produk</th>
                            <th class="p-3 font-semibold uppercase text-right whitespace-nowrap w-[10%]">Volume</th>
                            <th class="p-3 font-semibold uppercase text-right whitespace-nowrap w-[15%]">Harga Satuan</th>
                            <th class="p-3 font-semibold uppercase text-right whitespace-nowrap w-[15%]">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offer->items as $item)
                        <tr class="border-b border-gray-500">
                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $item->area_dinding }}</td>

                            {{-- DATA KOLOM BARU --}}
                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">
                                @php
                                    // Mencari data produk asli untuk mendapatkan brand (performa)
                                    $productData = \App\Models\Product::where('nama_produk', $item->nama_produk)->first();
                                @endphp
                                {{ $productData->performa ?? '-' }}
                            </td>

                            <td class="p-3 text-sm text-gray-700 whitespace-nowrap">{{ $item->nama_produk }}</td>
                            <td class="p-3 text-sm text-gray-700 text-right whitespace-nowrap">{{ $item->volume }} M²</td>
                            <td class="p-3 text-sm text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($item->harga_per_m2, 0, ',', '.') }}</td>
                            <td class="p-3 text-sm text-gray-700 text-right whitespace-nowrap">Rp {{ number_format($item->volume * $item->harga_per_m2, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach

                        @foreach($offer->jasaItems as $jasa)
                        <tr class="border-b border-gray-200">
                            {{-- COLSPAN DIUBAH DARI 2 MENJADI 3 UNTUK MENYESUAIKAN KOLOM BARU --}}
                            <td class="p-3 font-medium text-gray-800" colspan="3">
                                {{ $jasa->nama_jasa }}
                            </td>
                            <td class="p-3 text-center">1 Lot</td>
                            <td class="p-3 text-right">Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</td>
                            <td class="p-3 text-right">Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- GRAND TOTAL DIPINDAH KE LUAR TABEL -->
        <section class="mt-4 flex justify-end" id="grand-total-block">
            <div class="w-full md:w-6/12">
                <div class="flex justify-between items-center bg-gray-800 text-white p-4 rounded-lg">
                    <span class="text-xl font-bold uppercase">Grand Total</span>
                    <span class="text-xl font-bold whitespace-nowrap">
                        Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </section>


        <section class="mt-10 text-sm text-gray-700 leading-relaxed">
            <h4 class="font-semibold text-gray-800">Teknis pengerjaan:</h4>
            <ul class="list-disc list-inside ml-4 mt-2">
                <li>Semua peralatan pekerjaan akan disiapkan oleh pihak PT. Tasniem Gerai Inspirasi</li>
                <li>Meliputi : Cat, rol, kuas, dempul, plamir, scaffolding dll.</li>
                <li>Air dan Listrik serta gudang penyimpanan disediakan oleh pemberi kerja yaitu pihak {{ $offer->nama_klien }}</li>
                <li>Pengukuran final luas area akan dihitung bersama dan dijadikan patokan untuk nilai pekerjaan yang disepakati nantinya.</li>
                <li>Aplikasi Sealer ( cat dasar ) dilakukan pada area dinding yang akan di Cat.</li>
                <li>Pengecatan warna dua lapis.</li>
                <li>Finish.</li>
            </ul>
        </section>

        <section class="mt-8 text-sm text-gray-700">
            <p>Demikianlah surat penawaran ini kami sampaikan, semoga dapat disetujui.</p>
        </section>

        <section class="mt-12 flex justify-end">
            <div class="text-center">
                <p>Hormat kami,</p>
                <div class="h-28 w-48 relative">
                    <img src="{{ asset('images/ttd.png') }}" alt="Logo & Tanda Tangan" class="h-28 opacity-100 mx-auto">
                </div>
                <p class="font-bold text-gray-800">SAMSU RIZAL</p>
                <p class="text-gray-600">General Manager</p>
            </div>
        </section>

    </div>
</div>

<!-- CSS DAN SCRIPT -->
<style>
    @media print {
        .print\:hidden {
            display: none;
        }

        body * {
            visibility: hidden;
        }

        #surat-penawaran,
        #surat-penawaran * {
            visibility: visible;
        }

        #surat-penawaran {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 0.5in;
        }

        #grand-total-block {
            page-break-inside: avoid !important;
        }

        /* PERBAIKAN WARNA PRINT ADA DI SINI */
        #grand-total-block div,
        #grand-total-block span {
            color: #000 !important;
            background-color: transparent !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

{{-- SCRIPT UNTUK html2pdf.js SUDAH DIHAPUS --}}
@endsection