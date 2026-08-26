@extends('layouts.app')

@section('content')

@php
    // Setup Variabel
    $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
    $bulanAngka = $offer->created_at->format('n');
    $tahun      = $offer->created_at->format('Y');
    $noUrut     = str_pad($offer->id, 3, '0', STR_PAD_LEFT);
    $nomorSurat = sprintf('%s/SP-PRODUK/TGI/%s/%s', $noUrut, $bulanRomawi[$bulanAngka], $tahun);

    // Cek Opsi
    $hideGrandTotal = $offer->hilangkan_grand_total;
@endphp

<div class="container mx-auto my-12 px-4">

    <div class="max-w-4xl mx-auto mb-4 flex justify-between gap-2 print:hidden">
        <a href="{{ route('histori.index') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300 transition-colors flex items-center">&larr; Kembali</a>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors flex items-center gap-2">🖨️ Print</button>
            <a href="{{ route('invoice.create_from_offer', $offer->id) }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition-colors flex items-center">Buat Invoice &rarr;</a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg print:shadow-none print:p-0" id="surat-penawaran">

        <style>
    .font-times {
        font-family: 'Times New Roman', Times, serif;
    }
</style>

{{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6">
            <div class="w-full">
                {{-- Menggunakan class w-full agar gambar memenuhi lebar kontainer --}}
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
            {{-- Garis merah di bawah tetap dipertahankan atau dihapus sesuai keinginan --}}
            <div class="w-full border-b-[4px] border-[#d32f2f] mt-1"></div>
        </header>

        <section class="text-sm">
            <div class="flex justify-between items-start mb-6 text-gray-700 text-sm">
                <div>
                    <p>Perihal : {{ $offer->perihal ?? 'Penawaran supply produk cat Jotun' }}</p>
                </div>
                <div class="text-right font-sans">
                    <p class="font-bold">Nomor : {{ $nomorSurat }}</p>
                    <p>Batam, {{ $offer->created_at->format('d F Y') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-600">Kepada Yth,</p>
                <h3 class="text-base font-bold text-gray-800 uppercase mt-1">{{ $offer->nama_klien }}</h3>
                @if($offer->client_details)
                    <p class="text-gray-700 max-w-lg mt-0.5">{{ $offer->client_details }}</p>
                @endif
            </div>

        </section>

        <section class="mb-4 text-sm text-gray-700">
            <p>Dengan Hormat,</p>
            <p class="mt-2 text-justify">Bersama surat ini, kami <strong>PT. TASNIEM GERAI INSPIRASI</strong> mengajukan penawaran harga untuk produk cat JOTUN dengan rincian sebagai berikut:</p>
        </section>

        <section class="mt-4">
            <table class="w-full text-left border-collapse border border-gray-800 text-xs">
                <thead class="bg-gray-800 text-white print-bg-black">
                    <tr>
                        <th class="py-1 px-1 border border-gray-600 text-center w-[5%]">No</th>
                        <th class="py-1 px-1 border border-gray-600 w-[26%]">Nama Produk</th>
                        <th class="py-1 px-1 border border-gray-600 w-[15%]">Warna</th>
                        <th class="py-1 px-1 border border-gray-600 w-[15%]">Keterangan</th>
                        <th class="py-1 px-1 border border-gray-600 w-[10%] text-center">Ukuran</th>
                        <th class="py-1 px-1 border border-gray-600 w-[12%] text-right">Harga Satuan</th>
                        <th class="py-1 px-1 border border-gray-600 w-[5%] text-center">Qty</th>
                        <th class="py-1 px-1 border border-gray-600 w-[12%] text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotalSemua = 0; @endphp

                    @foreach($offer->items as $index => $item)
                        @php
                            $harga = $item->harga_per_m2;
                            $qty = $item->volume;
                            $totalBaris = $harga * $qty;
                            $diskonNominal = 0;
                            
                            $warnaBaris = $item->warna ?? '';
                            $keteranganBaris = $item->keterangan ?? '';

                            // Fallback backward compatibility
                            if (empty($warnaBaris) && !empty($item->deskripsi_tambahan)) {
                                $keterangan = $item->deskripsi_tambahan;
                                if (preg_match('/Potongan: Rp ([0-9,.]+)/', $keterangan, $matches)) {
                                    $diskonNominal = (int) str_replace(['.', ','], '', $matches[1]);
                                    $totalBaris -= $diskonNominal;
                                    $keterangan = preg_replace('/ \| Potongan: Rp [0-9,.]+/', '', $keterangan);
                                    $keterangan = preg_replace('/Potongan: Rp [0-9,.]+/', '', $keterangan);
                                }
                                if (preg_match('/Warna:\s*(.*)/i', $keterangan, $m)) {
                                    $warnaBaris = trim($m[1]);
                                } else {
                                    $warnaBaris = $keterangan;
                                }
                            }

                            $subtotalSemua += $totalBaris;
                        @endphp
                        <tr class="border-b border-gray-300">
                            <td class="py-0.5 px-1 border-x border-gray-300 text-center align-middle">{{ $index + 1 }}</td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-gray-700 align-middle leading-tight">
                                {{ $item->nama_produk }}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-[10px] align-middle leading-tight">
                                {!! nl2br(e($warnaBaris ?: '-')) !!}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-[10px] align-middle leading-tight">
                                {!! nl2br(e($keteranganBaris ?: '-')) !!}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-center align-middle">
                                {{ $item->area_dinding == '-' ? '' : $item->area_dinding }}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-right whitespace-nowrap align-middle">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-center align-middle">
                                {{ $qty }}
                            </td>
                            <td class="py-0.5 px-1 border-x border-gray-300 text-right whitespace-nowrap align-middle">
                                Rp {{ number_format($totalBaris, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                @if(!$hideGrandTotal)
                    <tfoot>
                        @if($offer->diskon_global > 0)
                            <tr class="bg-gray-50 print:bg-white">
                                <td colspan="7" class="py-1 px-1 border border-gray-300 text-right text-gray-600 text-xs">Subtotal</td>
                                <td class="py-1 px-1 border border-gray-300 text-right whitespace-nowrap text-xs">
                                    Rp {{ number_format($subtotalSemua, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-gray-50 print:bg-white text-red-600">
                                <td colspan="7" class="py-1 px-1 border border-gray-300 text-right text-xs">Diskon Tambahan (Global)</td>
                                <td class="py-1 px-1 border border-gray-300 text-right whitespace-nowrap text-xs">
                                    - Rp {{ number_format($offer->diskon_global, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif

                        <tr class="bg-gray-100 print:bg-gray-200">
                            <td colspan="7" class="py-2 px-1 border border-gray-300 text-right font-bold uppercase text-gray-900 text-sm">Grand Total</td>
                            <td class="py-2 px-1 border border-gray-300 text-right font-bold text-sm text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </section>

        <section class="mt-6 text-xs text-gray-700 break-inside-avoid">
            <h4 class="font-bold underline mb-1">Keterangan:</h4>
            <ul class="list-disc ml-4 space-y-0">
                <li>Harga sudah termasuk PPN.</li>
                <li>Barang yang sudah dibeli (Tinting) tidak dapat dikembalikan.</li>
                <li>Rekening: <strong>BRI / Mandiri a.n PT. Tasniem Gerai Inspirasi</strong>.</li>
            </ul>
        </section>

        <section class="mt-8 flex justify-end break-inside-avoid">
            <div class="text-center w-64">
                <p class="mb-4 text-sm">Batam, {{ $offer->created_at->format('d F Y') }}</p>
                <p class="mb-1 text-sm font-semibold">Hormat Kami,</p>
                <div class="h-20 flex justify-center items-center my-1">
                    @if(file_exists(public_path('images/ttd.png')))
                        <img src="{{ asset('images/ttd.png') }}" class="h-20 object-contain">
                    @else <br><br> @endif
                </div>
                <p class="font-bold text-gray-800 border-b border-gray-800 pb-1 text-sm">SAMSU RIZAL</p>
                <p class="text-gray-600 text-xs mt-1">General Manager</p>
            </div>
        </section>

    </div>
</div>

<style>
    @media print {
        @page { margin: 0; size: A4; }
        body * { visibility: hidden; }
        #surat-penawaran, #surat-penawaran * { visibility: visible; }
        #surat-penawaran {
            position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 1cm;
            box-shadow: none !important; border: none !important; background-color: white !important;
        }
        .print-bg-black { background-color: #1f2937 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print\:hidden { display: none !important; }
        tr, td, th { page-break-inside: avoid; }
        .break-inside-avoid { page-break-inside: avoid; }
    }
</style>
@endsection