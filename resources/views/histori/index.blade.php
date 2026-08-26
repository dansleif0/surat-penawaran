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

        {{-- Form Pencarian --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <form action="{{ route('histori.index') }}" method="GET" class="w-full md:w-1/2">
                <div class="flex">
                    <input type="text" name="search" placeholder="Cari Nama Klien / No. Surat..." class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ $search ?? '' }}">
                    <button type="submit" class="bg-gray-800 text-white font-bold py-2 px-4 rounded-r-md hover:bg-gray-700 transition">
                        Cari
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
            {{ $offers->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>
@endsection