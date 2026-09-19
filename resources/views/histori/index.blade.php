@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 flex-grow">
    <div class="max-w-7xl mx-auto">

        {{-- Header & Tombol --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-800">Histori Penawaran</h1>
            <div class="flex gap-2">
                <a href="{{ route('penawaran.create_product') }}" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition shadow-sm text-sm">
                    + Penawaran Produk
                </a>
                <a href="{{ route('penawaran.create_combined') }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition shadow-sm text-sm">
                    + Penawaran Proyek
                </a>
            </div>
        </div>

        {{-- Kartu Pencarian & Filter Jenis --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-6">
            <form action="{{ route('histori.index') }}" method="GET" class="flex flex-col md:flex-row items-end justify-between gap-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full md:w-3/4">
                    {{-- Input Pencarian --}}
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" placeholder="Cari Nama Klien, No. Surat, atau Detail..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors" value="{{ $search ?? '' }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Filter Jenis --}}
                    <div>
                        <label for="jenis" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Jenis</label>
                        <select name="jenis" id="jenis" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Jenis</option>
                            <option value="produk" {{ ($jenis ?? '') == 'produk' ? 'selected' : '' }}>Penawaran Produk</option>
                            <option value="proyek" {{ ($jenis ?? '') == 'proyek' ? 'selected' : '' }}>Penawaran Proyek</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                    @if(request('search') || request('jenis'))
                        <a href="{{ route('histori.index') }}" class="px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
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
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        {{-- TABEL HISTORI --}}
        <div class="bg-white shadow-md rounded-lg overflow-x-auto min-h-[400px]">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-tl-lg text-center w-24">Action</th>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">No. Surat</th>
                        <th scope="col" class="px-6 py-3 text-center">Jenis</th>
                        <th scope="col" class="px-6 py-3">Nama Klien</th>
                        <th scope="col" class="px-6 py-3">Detail</th>
                        <th scope="col" class="px-6 py-3 text-right rounded-tr-lg">Total Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($offers as $offer)
                    <tr class="bg-white hover:bg-gray-50 transition-colors align-top">

                        {{-- 1. ACTION --}}
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-1.5 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 focus:outline-none">
                                    Options
                                    <svg class="-mr-1 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition class="origin-top-left absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
                                    <div class="py-1" role="menu">
                                        <a href="{{ route('histori.show', ['offer' => $offer->id]) }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">👁️ Lihat / Print</a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('invoice.create_from_offer', ['offer' => $offer->id]) }}" class="text-green-700 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">💰 Buat Invoice</a>
                                        <a href="{{ route('skp.create', ['offer' => $offer->id]) }}" class="text-indigo-700 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">📝 Buat SPK</a>
                                        <a href="{{ route('bast.create', ['offer' => $offer->id]) }}" class="text-teal-700 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">🤝 Buat BAST</a>
                                        <a href="{{ route('histori.recap', ['offer' => $offer->id]) }}" class="text-blue-700 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">📋 Buat Rekapan</a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        @if($offer->jenis_penawaran == 'produk')
                                            <a href="{{ route('penawaran.edit_product', ['offer' => $offer->id]) }}" class="text-yellow-600 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">✏️ Edit Produk</a>
                                        @else
                                            <a href="{{ route('histori.edit', ['offer' => $offer->id]) }}" class="text-yellow-600 block px-4 py-2 text-sm hover:bg-gray-100 font-medium">✏️ Edit Proyek</a>
                                        @endif
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form action="{{ route('histori.destroy', ['offer' => $offer->id]) }}" method="POST" onsubmit="return confirm('Hapus penawaran ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full text-left text-red-700 block px-4 py-2 text-sm hover:bg-gray-100">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- TANGGAL --}}
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $offer->created_at->format('d M Y') }}</td>

                        {{-- NO SURAT --}}
                        <td class="px-6 py-4 font-medium whitespace-nowrap">
                            SP-{{ $offer->created_at->format('Y') }}/{{ str_pad($offer->id, 4, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- JENIS --}}
                        <td class="px-6 py-4 text-center">
                            @if($offer->jenis_penawaran == 'produk')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded border border-blue-400">Produk</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-500">Proyek</span>
                            @endif
                        </td>

                        {{-- NAMA KLIEN --}}
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-normal min-w-[200px] max-w-[300px] leading-snug">
                            {{ $offer->nama_klien }}
                        </td>

                        {{-- DETAIL --}}
                        <td class="px-6 py-4 text-sm text-gray-600 min-w-[200px] whitespace-normal">
                            {{ Str::limit($offer->client_details, 50) }}
                        </td>

                        {{-- TOTAL HARGA (DIPERBAIKI: HITUNG ULANG MANUAL) --}}
                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold text-gray-800">
                            @php
                                // Hitung Total Produk (Volume * Harga)
                                $totalProduk = $offer->items->sum(function($item) {
                                    return $item->volume * $item->harga_per_m2;
                                });

                                // Hitung Total Jasa (Harga Jasa di DB sudah Total)
                                $totalJasa = $offer->jasaItems->sum('harga_jasa');

                                // Grand Total
                                $grandTotal = $totalProduk + $totalJasa;
                            @endphp
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <span class="text-lg font-medium">Belum ada histori penawaran.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 pb-12">
            {{ $offers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection