@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Histori Invoice
            </h1>
            <a href="{{ route('invoice.create') }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition shadow-sm">
                + Buat Invoice Baru
            </a>
        </div>

        <form action="{{ route('invoice.histori') }}" method="GET" class="mb-6">
            <div class="flex gap-2">
                <input type="text"
                       name="search"
                       placeholder="Cari No. Invoice, Nama Klien, atau No. Surat Penawaran..."
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800"
                       value="{{ $search ?? '' }}">
                <button type="submit" class="mt-1 bg-gray-800 text-white font-bold py-2 px-6 rounded hover:bg-gray-700 transition">
                    Cari
                </button>
            </div>
        </form>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- PERBAIKAN: Hapus overflow-hidden agar dropdown tidak terpotong --}}
        {{-- Jika tabel butuh scroll horizontal, pindahkan overflow ke div dalam --}}
        <div class="bg-white shadow-md rounded-lg border border-gray-200 relative">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-tl-lg">Tanggal Invoice</th>
                        <th scope="col" class="px-6 py-3">No. Invoice</th>
                        <th scope="col" class="px-6 py-3">Nama Klien</th>
                        <th scope="col" class="px-6 py-3">No. Surat Penawaran</th>
                        <th scope="col" class="px-6 py-3 text-right">Total Tagihan</th>
                        <th scope="col" class="px-6 py-3 text-center rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $index => $invoice)
                    <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $invoice->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                            {{ $invoice->no_invoice }}
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
                                    00{{ $invoice->offer->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}
                                </span>
                            @else
                                <span class="text-red-500 italic">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold text-green-600">
                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                        </td>

                        {{-- Action Dropdown (DIPERBAIKI) --}}
                        <td class="px-6 py-4 text-center">
                            {{-- Tambahkan 'relative' di td agar posisi absolute dropdown benar --}}
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" @click.away="open = false" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
                                    Options
                                    <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                {{-- Logika Posisi Dropdown: --}}
                                {{-- Gunakan z-50 agar tampil paling depan --}}
                                {{-- Jika ini 3 baris terakhir, dropdown muncul KE ATAS agar tidak tertutup footer --}}
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

                                    <div class="py-1" role="menu">
                                        <a href="{{ route('invoice.show', $invoice->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700" role="menuitem">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat Detail
                                        </a>

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
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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

        <div class="mt-6">
            {{ $invoices->appends(['search' => $search ?? ''])->links() }}
        </div>

    </div>
</div>
@endsection