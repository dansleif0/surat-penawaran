@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4 flex-grow">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Histori Surat Perintah Kerja
                </h1>
                <p class="text-gray-500 mt-1">Kelola dan pantau semua dokumen SPK yang telah diterbitkan.</p>
            </div>
            <a href="{{ route('histori.index') }}" class="inline-flex items-center bg-indigo-600 text-white font-bold py-2.5 px-6 rounded-lg hover:bg-indigo-700 transition shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat SKP Baru
            </a>
        </div>

        {{-- Search & Filter Card --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('skp.index') }}" method="GET">
                <div class="relative flex flex-col md:flex-row gap-3">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text"
                            name="search"
                            placeholder="Cari No. Surat, Nama Klien, atau Jenis Pekerjaan..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            value="{{ $search ?? '' }}">
                    </div>
                    <button type="submit" class="bg-gray-800 text-white font-bold px-8 py-2.5 rounded-lg hover:bg-gray-900 transition shadow-sm">
                        Cari Data
                    </button>
                    @if($search)
                        <a href="{{ route('skp.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-bold">Info Surat</th>
                            <th class="px-6 py-4 font-bold">Pihak I (Klien)</th>
                            <th class="px-6 py-4 font-bold">Detail Pekerjaan</th>
                            <th class="px-6 py-4 font-bold text-right">Nilai Proyek</th>
                            <th class="px-6 py-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($skps as $skp)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="text-indigo-600 font-bold mb-0.5">{{ $skp->no_surat }}</div>
                                <div class="text-xs text-gray-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $skp->tanggal_surat->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $skp->pihak_satu_nama }}</div>
                                <div class="text-xs text-indigo-500 font-medium">{{ $skp->pihak_satu_perusahaan ?: 'Personal/Umum' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold inline-block mb-1">
                                    {{ Str::limit($skp->judul_pekerjaan, 30) }}
                                </span>
                                <div class="text-[11px] text-gray-400 italic">
                                    Tenure: {{ $skp->durasi_hari }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-gray-900 font-extrabold text-base">
                                    Rp{{ number_format($skp->nilai_pekerjaan, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" class="inline-flex items-center px-3 py-1.5 border border-gray-200 rounded-lg bg-white text-gray-600 hover:bg-gray-50 hover:border-indigo-300 transition focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                        <span class="text-xs font-bold mr-1">Menu</span>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-[100] border border-gray-100"
                                         style="display: none;">
                                        <div class="p-1">
                                            <a href="{{ route('skp.show', $skp->id) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Lihat / Cetak
                                            </a>
                                            <a href="{{ route('skp.edit', $skp->id) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit Data
                                            </a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            <form action="{{ route('skp.destroy', $skp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg font-medium">
                                                    <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
                            <td colspan="5" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-500">Tidak ada data ditemukan</p>
                                <p class="text-sm">Coba kata kunci pencarian yang lain.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $skps->appends(['search' => $search])->links() }}
        </div>

    </div>
</div>

{{-- Tambahkan Style CSS untuk Custom Pagination agar sesuai tema --}}
<style>
    .pagination { @apply flex items-center space-x-1; }
    /* Pastikan Tailwind menangani links() dengan benar atau pakai vendor publish */
</style>
@endsection