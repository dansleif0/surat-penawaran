@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-7xl mx-auto">

        {{-- ===================================================================
             HEADER: Judul + Tombol Aksi (hanya Admin)
             =================================================================== --}}
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-800">Histori Invoice</h1>

                {{-- Notifikasi Orange di Tombol Centang dengan Balon Text "Persiapkan" untuk Driver & Staff Gudang --}}
                @if(Auth::check() && Auth::user()->role !== 'admin' && $persiapanCount > 0)
                    <div class="flex items-center gap-2">
                        {{-- Tombol Centang Orange --}}
                        <div class="relative flex items-center justify-center w-11 h-11 bg-orange-500 rounded-full shadow-lg shadow-orange-200 ring-4 ring-orange-200 animate-pulse-once" title="Status Persiapkan">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            {{-- Angka badge merah --}}
                            <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-white shadow">
                                {{ $persiapanCount }}
                            </span>
                        </div>

                        {{-- Balon Text "Persiapkan" (Selalu terlihat dengan ekor balon) --}}
                        <div class="relative bg-orange-500 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl shadow-md flex items-center gap-1.5 animate-bounce-short">
                            {{-- Ekor / Panah Balon mengarah ke tombol centang --}}
                            <div class="absolute -left-1.5 top-1/2 -translate-y-1/2 w-0 h-0 border-t-[5px] border-b-[5px] border-r-[6px] border-t-transparent border-b-transparent border-r-orange-500"></div>
                            <span>Persiapkan</span>
                            <span class="bg-white/25 text-white px-1.5 py-0.5 rounded-full text-[10px] font-extrabold">{{ $persiapanCount }}</span>
                        </div>

                    </div>
                @endif
            </div>

            @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="flex items-center gap-3">
                {{-- Tombol Persiapkan (orange) --}}
                <button type="button" id="btn-persiapkan"
                    class="bg-orange-500 text-white font-bold py-2 px-4 rounded hover:bg-orange-600 transition shadow-sm flex items-center gap-2 opacity-50 cursor-not-allowed"
                    disabled title="Pilih invoice produk terlebih dahulu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Persiapkan
                    <span id="persiapkan-counter" class="hidden bg-white text-orange-600 text-xs font-black px-1.5 py-0.5 rounded-full leading-none"></span>
                </button>

                {{-- Tombol Batalkan Persiapkan (rose/merah) untuk Super User / Admin --}}
                <button type="button" id="btn-batal-persiapkan"
                    class="text-white font-bold py-2 px-4 rounded transition shadow-sm flex items-center gap-2 opacity-50 cursor-not-allowed"
                    style="background-color: #e11d48; color: #ffffff;"
                    disabled title="Pilih invoice yang sedang dalam status persiapan terlebih dahulu">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Batalkan Persiapkan
                    <span id="batal-persiapkan-counter" class="hidden bg-white text-rose-600 text-xs font-black px-1.5 py-0.5 rounded-full leading-none"></span>
                </button>

                {{-- Tombol Gabung Invoice (indigo) --}}
                <button type="button" id="btn-gabung-invoice" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Gabung Invoice
                </button>

                <a href="{{ route('invoice.create') }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition shadow-sm">
                    + Buat Invoice Baru
                </a>
            </div>
            @endif
        </div>

        {{-- ===================================================================
             BANNER: Informasi Persiapkan (hanya untuk non-admin jika ada)
             =================================================================== --}}
        @if(Auth::check() && Auth::user()->role !== 'admin' && $persiapanCount > 0)
        <div class="mb-6 bg-gradient-to-r from-orange-50 to-amber-50 border-l-4 border-orange-500 rounded-xl p-4 shadow-sm flex items-center justify-between gap-4 animate-slide-down">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-orange-800 text-sm">Perhatian — Invoice Siap Dipersiapkan</p>
                    <p class="text-orange-700 text-xs mt-1">Admin telah menandai <strong>{{ $persiapanCount }} invoice</strong> yang perlu dipersiapkan. Invoice tersebut ditampilkan paling atas dengan label <span class="bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">PERSIAPKAN</span>.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Kartu Pencarian & Filter Invoice --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-6">
            <form action="{{ route('invoice.histori') }}" method="GET" class="flex flex-col md:flex-row items-end justify-between gap-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 w-full md:w-5/6">
                    {{-- Input Pencarian --}}
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" placeholder="Cari No. Invoice, Nama Klien, atau Surat Penawaran..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors" value="{{ $search ?? '' }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Filter Tipe / Jenis Invoice --}}
                    <div>
                        <label for="jenis" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Tipe Invoice</label>
                        <select name="jenis" id="jenis" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Tipe</option>
                            <option value="produk" {{ ($jenis ?? '') == 'produk' ? 'selected' : '' }}>Invoice Produk</option>
                            <option value="proyek" {{ ($jenis ?? '') == 'proyek' ? 'selected' : '' }}>Invoice Proyek</option>
                        </select>
                    </div>

                    {{-- Dropdown Filter Format / Status Gabungan --}}
                    <div>
                        <label for="status_gabungan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Format Invoice</label>
                        <select name="status_gabungan" id="status_gabungan" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Format</option>
                            <option value="single" {{ ($status_gabungan ?? '') == 'single' ? 'selected' : '' }}>Invoice Biasa (Single)</option>
                            <option value="merge" {{ ($status_gabungan ?? '') == 'merge' ? 'selected' : '' }}>Invoice Gabungan (Merge)</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                    @if(request('search') || request('jenis') || request('status_gabungan'))
                        <a href="{{ route('invoice.histori') }}" class="px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Cari / Apply
                    </button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- ===================================================================
             TABEL INVOICE
             =================================================================== --}}
        <div class="bg-white shadow-md rounded-lg border border-gray-200 relative overflow-visible">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <th scope="col" class="px-4 py-3 rounded-tl-lg text-center w-12">
                            <input type="checkbox" id="select-all-invoices" title="Pilih Semua" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th scope="col" class="px-6 py-3">Tanggal Invoice</th>
                        @else
                        <th scope="col" class="px-6 py-3 rounded-tl-lg">Tanggal Invoice</th>
                        @endif
                        <th scope="col" class="px-6 py-3">No. Invoice</th>
                        <th scope="col" class="px-6 py-3 text-center">Jenis</th>
                        <th scope="col" class="px-6 py-3">Nama Klien</th>
                        <th scope="col" class="px-6 py-3">No. Surat Penawaran</th>
                        <th scope="col" class="px-6 py-3 text-right">Total Tagihan</th>
                        <th scope="col" class="px-6 py-3 text-center rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $index => $invoice)

                    {{-- Warna baris khusus jika persiapan & belum dikonfirmasi --}}
                    @php
                        $isPersiapan = $invoice->is_persiapan && !$invoice->persiapan_confirmed_at;
                        $rowClass = $isPersiapan ? 'bg-orange-50 hover:bg-orange-100' : 'bg-white hover:bg-gray-50';
                    @endphp

                    <tr class="{{ $rowClass }} transition duration-150 ease-in-out {{ $isPersiapan ? 'border-l-4 border-orange-400' : '' }}" id="invoice-row-{{ $invoice->id }}">

                        {{-- Checkbox (Admin saja) --}}
                        @if(Auth::check() && Auth::user()->role === 'admin')
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <input type="checkbox" value="{{ $invoice->id }}"
                                data-jenis="{{ $invoice->jenis }}"
                                data-persiapan="{{ ($invoice->is_persiapan || $invoice->persiapan_confirmed_at) ? '1' : '0' }}"
                                class="invoice-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </td>
                        @endif

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <span>{{ $invoice->created_at->format('d M Y') }}</span>
                                {{-- Badge PERSIAPKAN untuk non-admin --}}
                                @if($isPersiapan && Auth::check() && Auth::user()->role !== 'admin')
                                    <span class="inline-flex items-center gap-1 bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full w-fit animate-bounce-short">
                                        🚚 PERSIAPKAN
                                    </span>
                                @endif
                                {{-- Label sudah dikonfirmasi (Staff Gudang) --}}
                                @if($invoice->persiapan_confirmed_at && Auth::check() && Auth::user()->role !== 'admin')
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full w-fit border border-emerald-300">
                                        ✓ Dikonfirmasi
                                    </span>
                                @endif
                                {{-- Label admin: sudah ditandai persiapkan --}}
                                @if($invoice->is_persiapan && Auth::check() && Auth::user()->role === 'admin')
                                    <span class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 text-[10px] font-bold px-2 py-0.5 rounded-full w-fit border border-orange-300">
                                        📋 Persiapkan aktif
                                    </span>
                                @elseif($invoice->persiapan_confirmed_at && Auth::check() && Auth::user()->role === 'admin')
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full w-fit border border-emerald-300">
                                        ✓ Dikonfirmasi SG
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- No Invoice + Badge Merge --}}
                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span>{{ $invoice->no_invoice }}</span>
                                @if($invoice->status === 'merge')
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded border border-purple-300 tracking-wider">
                                        MERGE
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Jenis Invoice --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($invoice->jenis == 'produk')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded border border-blue-400">
                                    Produk
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-500">
                                    Proyek
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $invoice->nama_klien }}
                        </td>

                        {{-- No Surat Penawaran --}}
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-xs">
                            @if($invoice->offer)
                                @php
                                    $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
                                    $tglOffer = $invoice->offer->created_at;
                                    $romawi = $bulanRomawi[$tglOffer->format('n')];
                                    $tahun = $tglOffer->format('Y');
                                @endphp
                                <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded-full border border-gray-300">
                                    @if($invoice->offer->jenis_penawaran === 'produk')
                                        {{ str_pad($invoice->offer->id, 3, '0', STR_PAD_LEFT) }}/SP-PRODUK/TGI/{{ $romawi }}/{{ $tahun }}
                                    @else
                                        00{{ $invoice->offer->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}
                                    @endif
                                </span>
                            @else
                                <span class="text-red-500 italic">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold text-green-600">
                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                        </td>

                        {{-- Action Dropdown --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
                                        Options
                                        <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50
                                                {{ $index >= count($invoices) - 2 ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right' }}">

                                        <div class="py-1 text-left" role="menu">
                                            <a href="{{ route('invoice.show', $invoice->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700" role="menuitem">
                                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat Detail
                                            </a>

                                            <a href="{{ route('invoice.print', $invoice->id) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700" role="menuitem">
                                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                Print Invoice
                                            </a>

                                            @if(Auth::check() && Auth::user()->role === 'admin')
                                                {{-- Batalkan Persiapkan (Super User / Admin) --}}
                                                @if($invoice->is_persiapan || $invoice->persiapan_confirmed_at)
                                                    <form action="{{ route('invoice.batal_persiapan', $invoice->id) }}" method="POST" onsubmit="return confirm('Batalkan status persiapan untuk invoice {{ $invoice->no_invoice }}?');">
                                                        @csrf
                                                        <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 hover:text-rose-800" role="menuitem">
                                                            <svg class="mr-3 h-5 w-5 text-rose-500 group-hover:text-rose-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            Batalkan Persiapkan
                                                        </button>
                                                    </form>
                                                    <div class="border-t border-gray-100 my-1"></div>
                                                @endif

                                                <a href="{{ route('invoice.edit', $invoice->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700" role="menuitem">
                                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>

                                                <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus invoice ini? Data tidak bisa dikembalikan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-800" role="menuitem">
                                                        <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <span class="flex items-center px-4 py-2 text-xs text-gray-400 cursor-not-allowed bg-gray-50/50" title="Fitur Edit dinonaktifkan untuk {{ Auth::user()->role_label }}">
                                                    <svg class="mr-3 h-4 w-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Edit (Nonaktif)
                                                </span>
                                                <span class="flex items-center px-4 py-2 text-xs text-gray-400 cursor-not-allowed bg-gray-50/50" title="Fitur Hapus dinonaktifkan untuk {{ Auth::user()->role_label }}">
                                                    <svg class="mr-3 h-4 w-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Hapus (Nonaktif)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Batal Persiapkan: Super User / Admin --}}
                                @if(Auth::check() && Auth::user()->role === 'admin' && ($invoice->is_persiapan || $invoice->persiapan_confirmed_at))
                                    <form action="{{ route('invoice.batal_persiapan', $invoice->id) }}" method="POST"
                                          onsubmit="return confirm('Batalkan status persiapan untuk invoice {{ $invoice->no_invoice }}?');" class="w-full mt-1.5">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-white text-xs font-bold rounded-lg shadow-sm transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer"
                                            style="background-color: #e11d48; color: #ffffff; border: 1px solid #be123c;"
                                            onmouseover="this.style.backgroundColor='#be123c'"
                                            onmouseout="this.style.backgroundColor='#e11d48'"
                                            title="Batalkan status persiapan invoice ini">
                                            <svg class="w-3.5 h-3.5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span class="whitespace-nowrap">Batal Persiapkan</span>
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol Konfirmasi Persiapan: hanya Staff Gudang & invoice masih persiapan --}}
                                @if(Auth::check() && Auth::user()->role === 'staff_gudang' && $isPersiapan)
                                    <form action="{{ route('invoice.konfirmasi_persiapan', $invoice->id) }}" method="POST"
                                          onsubmit="return confirm('Konfirmasi bahwa invoice {{ $invoice->no_invoice }} sudah dipersiapkan?');" class="w-full mt-1.5">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-white text-xs font-bold rounded-lg shadow-md transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer"
                                            style="background-color: #059669; color: #ffffff; border: 1px solid #047857; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.4);"
                                            onmouseover="this.style.backgroundColor='#047857'"
                                            onmouseout="this.style.backgroundColor='#059669'"
                                            title="Klik untuk konfirmasi bahwa pesanan ini sudah dipersiapkan">
                                            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="whitespace-nowrap">Konfirmasi</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (Auth::check() && Auth::user()->role === 'admin') ? 8 : 7 }}" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">Belum ada data invoice.</p>
                                <p class="text-sm">Silakan buat invoice baru dari menu di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FORM TERSEMBUNYI UNTUK SUBMIT GABUNG INVOICE --}}
        <form id="combine-invoice-form" action="{{ route('invoice.combine_form') }}" method="POST" class="hidden">
            @csrf
            <div id="hidden-inputs-container"></div>
        </form>

        {{-- FORM TERSEMBUNYI UNTUK SUBMIT PERSIAPKAN --}}
        <form id="persiapkan-form" action="{{ route('invoice.persiapkan') }}" method="POST" class="hidden">
            @csrf
            <div id="hidden-persiapkan-container"></div>
        </form>

        {{-- FORM TERSEMBUNYI UNTUK SUBMIT BATAL PERSIAPKAN (BULK) --}}
        <form id="batal-persiapkan-form" action="{{ route('invoice.batal_persiapan_bulk') }}" method="POST" class="hidden">
            @csrf
            <div id="hidden-batal-persiapkan-container"></div>
        </form>

        <div class="mt-6">
            {{ $invoices->appends(request()->query())->links() }}
        </div>

    </div>
</div>

<style>
/* Animasi bounce singkat untuk badge persiapkan */
@keyframes bounce-short {
    0%, 100% { transform: translateY(0); }
    40% { transform: translateY(-4px); }
    60% { transform: translateY(-2px); }
}
.animate-bounce-short {
    animation: bounce-short 1.5s ease-in-out infinite;
}

/* Animasi slide-down untuk banner notifikasi */
@keyframes slide-down {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-slide-down {
    animation: slide-down 0.4s ease-out forwards;
}

/* Animasi pulse satu kali untuk ikon centang */
@keyframes pulse-once {
    0%   { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); }
    70%  { box-shadow: 0 0 0 12px rgba(249, 115, 22, 0); }
    100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
}
.animate-pulse-once {
    animation: pulse-once 1.8s ease-out infinite;
}

/* Border kiri orange untuk baris persiapan */
tr.border-l-4 td:first-child {
    border-left: 4px solid #f97316;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox    = document.getElementById('select-all-invoices');
    const invoiceCheckboxes    = document.querySelectorAll('.invoice-checkbox');
    const btnGabung            = document.getElementById('btn-gabung-invoice');
    const btnPersiapkan        = document.getElementById('btn-persiapkan');
    const btnBatalPersiapkan   = document.getElementById('btn-batal-persiapkan');
    const combineForm          = document.getElementById('combine-invoice-form');
    const persiapkanForm       = document.getElementById('persiapkan-form');
    const batalPersiapkanForm  = document.getElementById('batal-persiapkan-form');
    const hiddenContainer      = document.getElementById('hidden-inputs-container');
    const hiddenPContainer     = document.getElementById('hidden-persiapkan-container');
    const hiddenBPContainer    = document.getElementById('hidden-batal-persiapkan-container');
    const persiapkanCounter    = document.getElementById('persiapkan-counter');
    const batalPersiapkanCounter = document.getElementById('batal-persiapkan-counter');

    // ─── Select All ────────────────────────────────────────────────────────────
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            invoiceCheckboxes.forEach(cb => cb.checked = this.checked);
            updateActionButtons();
        });
    }

    // ─── Perbarui tombol Persiapkan & Batal Persiapkan berdasarkan checkbox ────
    function updateActionButtons() {
        // 1. Tombol Persiapkan (Hanya invoice berjenis "produk")
        if (btnPersiapkan) {
            const produkChecked = Array.from(document.querySelectorAll('.invoice-checkbox:checked'))
                .filter(cb => cb.dataset.jenis === 'produk');

            if (produkChecked.length > 0) {
                btnPersiapkan.disabled = false;
                btnPersiapkan.classList.remove('opacity-50', 'cursor-not-allowed');
                btnPersiapkan.classList.add('hover:bg-orange-600');
                btnPersiapkan.title = `Persiapkan ${produkChecked.length} invoice produk yang dipilih`;

                persiapkanCounter.classList.remove('hidden');
                persiapkanCounter.textContent = produkChecked.length;
            } else {
                btnPersiapkan.disabled = true;
                btnPersiapkan.classList.add('opacity-50', 'cursor-not-allowed');
                btnPersiapkan.classList.remove('hover:bg-orange-600');
                btnPersiapkan.title = 'Pilih invoice produk terlebih dahulu';

                persiapkanCounter.classList.add('hidden');
            }
        }

        // 2. Tombol Batalkan Persiapkan (Invoice yang sedang/pernah dalam status persiapan)
        if (btnBatalPersiapkan) {
            const persiapanChecked = Array.from(document.querySelectorAll('.invoice-checkbox:checked'))
                .filter(cb => cb.dataset.persiapan === '1');

            if (persiapanChecked.length > 0) {
                btnBatalPersiapkan.disabled = false;
                btnBatalPersiapkan.classList.remove('opacity-50', 'cursor-not-allowed');
                btnBatalPersiapkan.title = `Batalkan status persiapan untuk ${persiapanChecked.length} invoice yang dipilih`;

                batalPersiapkanCounter.classList.remove('hidden');
                batalPersiapkanCounter.textContent = persiapanChecked.length;
            } else {
                btnBatalPersiapkan.disabled = true;
                btnBatalPersiapkan.classList.add('opacity-50', 'cursor-not-allowed');
                btnBatalPersiapkan.title = 'Pilih invoice yang sedang dalam status persiapan terlebih dahulu';

                batalPersiapkanCounter.classList.add('hidden');
            }
        }
    }

    invoiceCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateActionButtons);
    });

    // ─── Gabung Invoice ────────────────────────────────────────────────────────
    if (btnGabung && combineForm) {
        btnGabung.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked');
            if (selectedCheckboxes.length < 2) {
                alert('Pilih minimal 2 invoice yang ingin digabungkan!');
                return;
            }

            hiddenContainer.innerHTML = '';
            selectedCheckboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'selected_invoices[]';
                input.value = cb.value;
                hiddenContainer.appendChild(input);
            });

            combineForm.submit();
        });
    }

    // ─── Persiapkan Invoice ───────────────────────────────────────────────────
    if (btnPersiapkan && persiapkanForm) {
        btnPersiapkan.addEventListener('click', function() {
            const produkChecked = Array.from(document.querySelectorAll('.invoice-checkbox:checked'))
                .filter(cb => cb.dataset.jenis === 'produk');

            if (produkChecked.length === 0) {
                alert('Pilih minimal 1 invoice produk yang ingin dipersiapkan!');
                return;
            }

            if (!confirm(`Anda akan menandai ${produkChecked.length} invoice sebagai "Persiapkan".\n\nDriver dan Staff Gudang akan mendapat notifikasi. Lanjutkan?`)) {
                return;
            }

            hiddenPContainer.innerHTML = '';
            produkChecked.forEach(cb => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'selected_invoices[]';
                input.value = cb.value;
                hiddenPContainer.appendChild(input);
            });

            persiapkanForm.submit();
        });
    }

    // ─── Batalkan Persiapkan Invoice (Bulk) ────────────────────────────────────
    if (btnBatalPersiapkan && batalPersiapkanForm) {
        btnBatalPersiapkan.addEventListener('click', function() {
            const persiapanChecked = Array.from(document.querySelectorAll('.invoice-checkbox:checked'))
                .filter(cb => cb.dataset.persiapan === '1');

            if (persiapanChecked.length === 0) {
                alert('Pilih minimal 1 invoice yang sedang dalam status persiapan untuk dibatalkan!');
                return;
            }

            if (!confirm(`Batalkan status persiapan untuk ${persiapanChecked.length} invoice yang dipilih?`)) {
                return;
            }

            hiddenBPContainer.innerHTML = '';
            persiapanChecked.forEach(cb => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'selected_invoices[]';
                input.value = cb.value;
                hiddenBPContainer.appendChild(input);
            });

            batalPersiapkanForm.submit();
        });
    }
});
</script>
@endsection