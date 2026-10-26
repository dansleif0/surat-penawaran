@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">

    <div class="max-w-4xl mx-auto mb-4 flex justify-between items-center gap-2 print:hidden">
        <div class="flex items-center gap-3">
            <a href="{{ route('histori.index') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                &larr; Kembali
            </a>
            @if($offer->status === 'Finished')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-300 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Status: Finished
                </span>
            @elseif($offer->status === 'Expired')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    Status: Expired
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    Status: In Progress
                </span>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('histori.print', $offer->id) }}" target="_blank" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center gap-2">
                🖨️ Print Surat (PDF)
            </a>
            <a href="{{ route('invoice.create_from_offer', $offer->id) }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition-colors shadow-sm">
                Buat Invoice &rarr;
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg" id="surat-penawaran">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6">
            <div class="w-full">
                {{-- Menggunakan class w-full agar gambar memenuhi lebar kontainer --}}
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
            {{-- Garis merah di bawah tetap dipertahankan atau dihapus sesuai keinginan --}}
            <div class="w-full border-b-[4px] border-[#d32f2f] mt-1"></div>
        </header>

        <section class="mb-6 text-sm sans flex justify-between items-start">
            @php
            $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
            $romawi = $bulanRomawi[$offer->created_at->format('n')];
            $tahun = $offer->created_at->format('Y');
            @endphp
            <div>
                <p>Perihal : {{ $offer->perihal ?? 'Penawaran Jasa Apply dan Supply Pengecatan' }}</p>
                <p>Nomor : 00{{ $offer->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}</p>
            </div>
            <div class="text-right">

                <p>Batam, {{ $offer->created_at->format('d F Y') }}</p>
            </div>
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
            <p>Kami PT. TASNIEM GERAI INSPIRASI adalah dealer resmi PT. JOTUN INDONESIA dan toko pertama Jotun Flagship terbesar di Kota Batam yang merupakan reatil Supply Cat Jotun Dekoratif, Selain sebagai Supply kami juga bergerak di bidang Sipil lainnya antara lain sebagai berikut:</a>.</p>
            <div>
                <p>Kami PT Tasniem Gerai Inspirasi begerak di bidang Painting Dan Pekerjaan Sipil lainnya :</p>
                <ol class="list-decimal list-inside ml-4">
                    <li>Jasa Apply dan Supply pengecatan Property</li>
                    <li>Jasa Apply dan Supply Pengecatan lantai Epoxy</li>
                    <li>Jasa Pemasangan Partisi, Plafon Gypsum dan Sudah plafon</li>
                    <li>Supply Cat jotun Marine</li>
                    <li>Supply Cat jotun Protective</li>
                    <li>Desain dan Pabrikasi Interior</li>
                </ol>
            </div>
            <p>Dengan ini kami sampaikan penawaran Jasa Apply dan Supply pengecatan Sebagai berikut:</p>
        </section>

        {{-- PHP LOGIC --}}
        @php
        $isSplit = $offer->pisah_kriteria_total;
        $showTotal = !$offer->hilangkan_grand_total;
        $showDetail = (bool) $offer->tampilkan_detail;

        // Hitung Total Jasa
        $totalJasa = $offer->jasaItems->sum('harga_jasa');

        $exteriorItems = collect();
        $interiorItems = collect();
        $totalExterior = 0;
        $totalInterior = 0;

        if ($isSplit) {
            $exteriorItems = $offer->items->filter(function($item) {
                $prod = \App\Models\Product::where('nama_produk', $item->nama_produk)->first();
                return $prod && $prod->kriteria == 'Exterior';
            });
            $totalExterior = $exteriorItems->sum(function($item) { return $item->volume * $item->harga_per_m2; });

            $interiorItems = $offer->items->filter(function($item) {
                $prod = \App\Models\Product::where('nama_produk', $item->nama_produk)->first();
                return !$prod || $prod->kriteria != 'Exterior';
            });
            $totalInterior = $interiorItems->sum(function($item) { return $item->volume * $item->harga_per_m2; });
        }
        $globalIndex = 0;
        @endphp

        <section class="mt-8">
            <div class="w-full overflow-x-auto">

                {{-- JIKA TAMPILKAN DETAIL = FALSE (FORMAT RANGKUMAN / NON-DETAIL) --}}
                @if(!$showDetail)

                    @if($isSplit)
                        {{-- 1. EXTERIOR RANGKUMAN --}}
                        @if($exteriorItems->isNotEmpty())
                        <div class="mb-6 page-break-inside-avoid">
                            <div class="flex items-center gap-2 mb-2.5">
                                <span class="w-1.5 h-3.5 bg-slate-800 rounded-xs inline-block"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Pekerjaan Exterior</h4>
                            </div>
                            <div class="overflow-hidden rounded-lg border border-slate-300 shadow-xs">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-800 text-white">
                                        <tr>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider w-[70%] align-middle">Area Pekerjaan</th>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider text-right w-[30%] whitespace-nowrap align-middle">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-3.5 px-4 align-middle">
                                                <div class="font-semibold text-slate-800 text-xs tracking-wide">Total Pekerjaan Exterior</div>
                                                <div class="text-[11px] text-slate-500 font-normal mt-0.5">Jasa Apply & Supply Pengecatan</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right align-middle whitespace-nowrap">
                                                <div class="flex justify-end items-baseline gap-1.5">
                                                    <span class="text-xs font-semibold text-slate-500">Rp</span>
                                                    <span class="text-sm font-extrabold text-slate-900">{{ number_format($totalExterior, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        {{-- 2. INTERIOR RANGKUMAN --}}
                        @if($interiorItems->isNotEmpty())
                        <div class="mb-6 page-break-inside-avoid">
                            <div class="flex items-center gap-2 mb-2.5">
                                <span class="w-1.5 h-3.5 bg-slate-800 rounded-xs inline-block"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Pekerjaan Interior</h4>
                            </div>
                            <div class="overflow-hidden rounded-lg border border-slate-300 shadow-xs">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-800 text-white">
                                        <tr>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider w-[70%] align-middle">Area Pekerjaan</th>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider text-right w-[30%] whitespace-nowrap align-middle">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-3.5 px-4 align-middle">
                                                <div class="font-semibold text-slate-800 text-xs tracking-wide">Total Pekerjaan Interior</div>
                                                <div class="text-[11px] text-slate-500 font-normal mt-0.5">Jasa Apply & Supply Pengecatan</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right align-middle whitespace-nowrap">
                                                <div class="flex justify-end items-baseline gap-1.5">
                                                    <span class="text-xs font-semibold text-slate-500">Rp</span>
                                                    <span class="text-sm font-extrabold text-slate-900">{{ number_format($totalInterior, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                    @else
                        {{-- 3. NON-SPLIT RANGKUMAN --}}
                        @if($offer->items->isNotEmpty())
                        @php
                            $allTotal = $offer->items->sum(function($item) { return $item->volume * $item->harga_per_m2; });
                        @endphp
                        <div class="mb-6 page-break-inside-avoid">
                            <div class="overflow-hidden rounded-lg border border-slate-300 shadow-xs">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-800 text-white">
                                        <tr>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider w-[70%] align-middle">Area Pekerjaan</th>
                                            <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider text-right w-[30%] whitespace-nowrap align-middle">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-3.5 px-4 align-middle">
                                                <div class="font-semibold text-slate-800 text-xs tracking-wide">Total Pekerjaan</div>
                                                <div class="text-[11px] text-slate-500 font-normal mt-0.5">Jasa Apply & Supply Pengecatan</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-right align-middle whitespace-nowrap">
                                                <div class="flex justify-end items-baseline gap-1.5">
                                                    <span class="text-xs font-semibold text-slate-500">Rp</span>
                                                    <span class="text-sm font-extrabold text-slate-900">{{ number_format($allTotal, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    @endif

                @else
                    {{-- JIKA TAMPILKAN DETAIL = TRUE (FORMAT RINCIAN AREA PEKERJAAN) --}}

                    {{-- KASUS 1: SPLIT --}}
                    @if($isSplit)

                    {{-- EXTERIOR --}}
                    @if($exteriorItems->isNotEmpty())
                    <div class="mb-8 page-break-inside-avoid">
                        <h4 class="font-bold text-gray-800 mb-2 uppercase border-b-2 border-gray-800 inline-block text-sm">Pekerjaan Exterior</h4>
                        <table class="w-full text-left border-collapse mb-4">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    @if($offer->opsi_paket)
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Opsi Paket</th>
                                    @endif
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[28%] whitespace-nowrap align-middle">Area Pekerjaan</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Nama Brand</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[20%] whitespace-nowrap align-middle">Produk</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[10%] whitespace-nowrap align-middle">Volume/M²</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Harga Satuan</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exteriorItems as $item)
                                <tr class="border-b border-gray-500">
                                    @if($offer->opsi_paket)
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">Paket {{ chr(65 + $globalIndex) }}</td>
                                    @php $globalIndex++; @endphp
                                    @endif
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->area_dinding }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                        @php $p = \App\Models\Product::where('nama_produk', $item->nama_produk)->first(); @endphp
                                        {{ $p->performa ?? '-' }}
                                    </td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->nama_produk }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none text-right whitespace-nowrap align-middle">{{ $item->volume }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->harga_per_m2, 0, ',', '.') }}</span></div>
                                    </td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap font-medium align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->volume * $item->harga_per_m2, 0, ',', '.') }}</span></div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($showTotal)
                            <tfoot>
                                <tr class="bg-gray-100 font-bold text-gray-800">
                                    <td colspan="{{ $offer->opsi_paket ? 4 : 3 }}" class="py-1 px-1 text-xs text-right uppercase align-middle">Total Exterior</td>
                                    <td class="py-1 px-1 text-xs text-right align-middle">{{ $exteriorItems->sum('volume') + 0 }}</td>
                                    <td class="py-1 px-1 text-xs align-middle"></td>
                                    <td class="py-1 px-1 text-xs text-right whitespace-nowrap align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($totalExterior, 0, ',', '.') }}</span></div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- INTERIOR --}}
                    @if($interiorItems->isNotEmpty())
                    <div class="mb-8 page-break-inside-avoid">
                        <h4 class="font-bold text-gray-800 mb-2 uppercase border-b-2 border-gray-800 inline-block text-sm">Pekerjaan Interior</h4>
                        <table class="w-full text-left border-collapse mb-4">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    @if($offer->opsi_paket)
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Opsi Paket</th>
                                    @endif
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[28%] whitespace-nowrap align-middle">Area Pekerjaan</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Nama Brand</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs w-[20%] whitespace-nowrap align-middle">Produk</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[10%] whitespace-nowrap align-middle">Volume/M²</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Harga Satuan</th>
                                    <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interiorItems as $item)
                                <tr class="border-b border-gray-500">
                                    @if($offer->opsi_paket)
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">Paket {{ chr(65 + $globalIndex) }}</td>
                                    @php $globalIndex++; @endphp
                                    @endif
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->area_dinding }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                        @php $p = \App\Models\Product::where('nama_produk', $item->nama_produk)->first(); @endphp
                                        {{ $p->performa ?? '-' }}
                                    </td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->nama_produk }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none text-right whitespace-nowrap align-middle">{{ $item->volume }}</td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->harga_per_m2, 0, ',', '.') }}</span></div>
                                    </td>
                                    <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap font-medium align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->volume * $item->harga_per_m2, 0, ',', '.') }}</span></div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($showTotal)
                            <tfoot>
                                <tr class="bg-gray-100 font-bold text-gray-800">
                                    <td colspan="{{ $offer->opsi_paket ? 4 : 3 }}" class="py-1 px-1 text-xs text-right uppercase align-middle">Total Interior</td>
                                    <td class="py-1 px-1 text-xs text-right align-middle">{{ $interiorItems->sum('volume') + 0 }}</td>
                                    <td class="py-1 px-1 text-xs align-middle"></td>
                                    <td class="py-1 px-1 text-xs text-right whitespace-nowrap align-middle">
                                        <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($totalInterior, 0, ',', '.') }}</span></div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- KASUS 2: DEFAULT --}}
                    @else
                    <table class="w-full text-left border-collapse page-break-inside-avoid">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                @if($offer->opsi_paket)
                                <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Opsi Paket</th>
                                @endif
                                <th class="py-2 px-2 font-semibold uppercase text-xs w-[28%] whitespace-nowrap align-middle">Area Pekerjaan</th>
                                <th class="py-2 px-2 font-semibold uppercase text-xs w-[12%] whitespace-nowrap align-middle">Nama Brand</th>
                                <th class="py-2 px-2 font-semibold uppercase text-xs w-[20%] whitespace-nowrap align-middle">Produk</th>
                                <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[10%] whitespace-nowrap align-middle">Volume/M²</th>
                                <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Harga Satuan</th>
                                <th class="py-2 px-2 font-semibold uppercase text-xs text-right w-[15%] whitespace-nowrap align-middle">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offer->items as $index => $item)
                            <tr class="border-b border-gray-500">
                                @if($offer->opsi_paket)
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">Paket {{ chr(65 + $index) }}</td>
                                @endif
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->area_dinding }}</td>
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                    @php $p = \App\Models\Product::where('nama_produk', $item->nama_produk)->first(); @endphp
                                    {{ $p->performa ?? '-' }}
                                </td>
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">{{ $item->nama_produk }}</td>
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none text-right whitespace-nowrap align-middle">{{ $item->volume }}</td>
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap align-middle">
                                    <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->harga_per_m2, 0, ',', '.') }}</span></div>
                                </td>
                                <td class="py-1 px-2 text-xs text-gray-700 leading-none whitespace-nowrap font-medium align-middle">
                                    <div class="flex justify-end gap-1 w-full"><span>Rp</span><span>{{ number_format($item->volume * $item->harga_per_m2, 0, ',', '.') }}</span></div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                @endif

                {{-- TABEL JASA (DENGAN TOTAL) --}}
                @if($offer->jasaItems->isNotEmpty())
                <div class="mb-6 page-break-inside-avoid">
                    <div class="flex items-center gap-2 mb-2.5">
                        <span class="w-1.5 h-3.5 bg-slate-800 rounded-xs inline-block"></span>
                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Pengerjaan Tambahan</h4>
                    </div>
                    <div class="overflow-hidden rounded-lg border border-slate-300 shadow-xs">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-800 text-white">
                                <tr>
                                    <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider align-middle w-[50%]" colspan="3">Deskripsi Pengerjaan</th>
                                    <th class="py-2.5 px-3 font-semibold uppercase text-xs tracking-wider text-right align-middle w-[15%]">Vol / Sat</th>
                                    <th class="py-2.5 px-3 font-semibold uppercase text-xs tracking-wider text-right align-middle w-[15%]">Harga Satuan</th>
                                    <th class="py-2.5 px-4 font-semibold uppercase text-xs tracking-wider text-right align-middle w-[20%]">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach($offer->jasaItems as $jasa)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-2.5 px-4 font-medium text-slate-800 text-xs align-middle" colspan="3">{{ $jasa->nama_jasa }}</td>
                                    <td class="py-2.5 px-3 text-right text-xs text-slate-600 align-middle whitespace-nowrap">{{ $jasa->volume + 0 }} {{ $jasa->satuan }}</td>
                                    <td class="py-2.5 px-3 text-xs align-middle whitespace-nowrap">
                                        <div class="flex justify-end items-baseline gap-1 w-full">
                                            <span class="text-[10px] text-slate-400">Rp</span>
                                            <span class="text-slate-700 font-medium">{{ number_format($jasa->harga_satuan ?? ($jasa->harga_jasa / ($jasa->volume ?: 1)), 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-4 text-xs font-bold text-slate-900 align-middle whitespace-nowrap">
                                        <div class="flex justify-end items-baseline gap-1 w-full">
                                            <span class="text-[10px] text-slate-400">Rp</span>
                                            <span>{{ number_format($jasa->harga_jasa, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($showTotal)
                            <tfoot>
                                <tr class="bg-slate-100 font-bold text-slate-800 border-t border-slate-300">
                                    <td colspan="5" class="py-2.5 px-4 text-xs text-right uppercase tracking-wider align-middle">Total Pengerjaan Tambahan</td>
                                    <td class="py-2.5 px-4 text-xs font-extrabold text-slate-900 text-right whitespace-nowrap align-middle">
                                        <div class="flex justify-end items-baseline gap-1 w-full">
                                            <span class="text-xs font-semibold text-slate-500">Rp</span>
                                            <span class="text-sm font-extrabold text-slate-900">{{ number_format($totalJasa, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </section>

        @if($showTotal)
        <section class="mt-4 flex justify-end" id="grand-total-block">
            <div class="w-full md:w-6/12">
                <div class="flex justify-between items-center bg-slate-800 text-white p-3.5 rounded-lg shadow-sm">
                    <span class="text-base font-bold uppercase tracking-wider">Grand Total</span>
                    <span class="text-xl font-black whitespace-nowrap flex items-baseline gap-1.5">
                        <span class="text-sm font-semibold text-slate-400">Rp</span>
                        <span>{{ number_format($offer->total_keseluruhan, 0, ',', '.') }}</span>
                    </span>
                </div>
            </div>
        </section>
        @endif

        <section class="mt-8 text-sm text-gray-700 leading-relaxed">
            <h4 class="font-semibold text-gray-800">Teknis pengerjaan:</h4>
            <ul class="list-disc list-inside ml-4 mt-2">
                <li>Semua peralatan pekerjaan akan disiapkan oleh pihak PT. Tasniem Gerai Inspirasi</li>
                <li>Perbaikan dan Dempul retakan tembok area pengerjaan</li>
                <li>Cleaning area sebelum melakukan pekerjaan</li>
                <li>Pengaplikasikan Cat Dasar (Sealer)</li>
                <li>Pengaplikasikan Topcoat minimal 2 kali lapis</li>
                <li>Finish.</li>
            </ul>
        </section>

        <section class="mt-8 text-sm text-gray-700 leading-relaxed">
            <h4 class="font-semibold text-gray-800">Beberapa Hal yang perlu kami sampaikan sebelum pengerjaan :</h4>
            <ul class="list-disc list-inside ml-4 mt-2">
                <li>Permohonan untuk Air, Listrik dan Gudang peyimpanan alat-alat kerja di siapkan oleh Pemberi Kerja</li>
                <li>Down Payment minimal 30% dibayarkan sebelum pekerjaan di mulai</li>
                <li>Payment kedua sebesar 30 %dibayarkan pada saat pengerjaan berlangsung</li>
                <li>Pelunasan sebesar 40% di bayarkan setelah Pengerjaan selesai dan telah di lakukan pengecekan Bersama</li>
                <li>Harga penawaran diatas berlaku 30 hari sejak tanggal surat penawaran di tebitkan</li>
            </ul>
        </section>


        <section class="text-md font-bold text-gray-800">
            <p>NB : Surat Ini Berlaku Sampai dengan Tanggal {{ $offer->created_at->copy()->addDays(30)->format('d F Y') }}.</p>
        </section>

        <section class="mt-6 text-sm text-gray-700">
            <p>Demikianlah surat penawaran ini kami sampaikan, semoga dapat disetujui.</p>
        </section>

        <section class="mt-10 flex justify-end page-break-inside-avoid">
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

<style>
    @media print {
        @page {
            size: A4;
            margin: 12.7mm;
        }

        /* Hilangkan elemen yang tidak perlu */
        .print\:hidden,
        nav,
        header.bg-white.shadow-sm,
        aside,
        .bg-gray-200 {
            display: none !important;
        }

        /* Atur Body */
        body {
            background-color: white;
            margin: 0;
            padding: 0;
            overflow: visible !important;
            /* PENTING: Agar bisa scroll/print halaman selanjutnya */
        }

        /* Atur Kontainer Utama */
        .container {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Atur Kertas Surat */
        #surat-penawaran {
            width: 100% !important;
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            position: relative !important;
            /* PENTING: Gunakan relative/static, BUKAN absolute */
            left: auto !important;
            top: auto !important;
        }

        /* Warna Hitam untuk Teks */
        #grand-total-block div,
        #grand-total-block span {
            color: #000 !important;
            background-color: transparent !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Page Break Management */
        .page-break-inside-avoid {
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }
    }
</style>
@endsection