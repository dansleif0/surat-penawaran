@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] ml-0 md:ml-64 p-4 lg:p-8 transition-all duration-300">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-600 font-bold">Histori Rekapan Biaya Proyek</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Histori Rekapan Proyek</h1>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200/60">
                        Analisis Margin
                    </span>
                </div>
                <p class="text-slate-500 text-xs md:text-sm mt-1">
                    Pantau perbandingan total omzet penawaran, modal pengeluaran, dan net profit (laba/rugi) proyek secara transparan.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <a href="{{ route('histori.index', ['jenis' => 'proyek']) }}" 
                   style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: #ffffff !important;"
                   class="px-4 py-2.5 text-xs font-bold text-white rounded-xl shadow-sm hover:brightness-105 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Rekap dari Penawaran Proyek</span>
                </a>
            </div>
        </div>

        {{-- Kartu Statistik Ringkasan Finansial --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Card 1: Total Omzet --}}
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/90 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Nilai Penawaran</span>
                    <div style="background-color: #eff6ff; color: #2563eb;" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm">
                        💼
                    </div>
                </div>
                <p class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">
                    Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-500 mt-1 font-medium">
                    Dari <strong class="text-slate-700">{{ $totalProyek ?? 0 }}</strong> proyek yang direkap
                </p>
            </div>

            {{-- Card 2: Total Pengeluaran (Modal) --}}
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/90 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Modal Pengeluaran</span>
                    <div style="background-color: #fef2f2; color: #dc2626;" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm">
                        📉
                    </div>
                </div>
                <p class="text-xl md:text-2xl font-black text-red-600 tracking-tight">
                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-500 mt-1 font-medium">
                    Total belanja material & upah tukang
                </p>
            </div>

            {{-- Card 3: Total Net Profit --}}
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/90 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Net Profit</span>
                    <div style="background-color: {{ ($totalMargin ?? 0) >= 0 ? '#ecfdf5' : '#fef2f2' }}; color: {{ ($totalMargin ?? 0) >= 0 ? '#059669' : '#dc2626' }};" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm">
                        {{ ($totalMargin ?? 0) >= 0 ? '💰' : '⚠️' }}
                    </div>
                </div>
                <p class="text-xl md:text-2xl font-black {{ ($totalMargin ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }} tracking-tight">
                    Rp {{ number_format($totalMargin ?? 0, 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded {{ ($totalMargin ?? 0) >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ ($totalMargin ?? 0) >= 0 ? 'Surplus Finansial' : 'Defisit Pengeluaran' }}
                    </span>
                </div>
            </div>

            {{-- Card 4: Rata-Rata Margin --}}
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/90 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Rata-Rata Margin Laba</span>
                    <div style="background-color: #f5f3ff; color: #7c3aed;" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm">
                        📊
                    </div>
                </div>
                @php
                    $avgPct = ($totalOmzet ?? 0) > 0 ? ((($totalMargin ?? 0) / $totalOmzet) * 100) : 0;
                @endphp
                <p class="text-xl md:text-2xl font-black {{ $avgPct >= 0 ? 'text-indigo-600' : 'text-red-600' }} tracking-tight">
                    {{ ($avgPct >= 0 ? '+' : '') . number_format($avgPct, 1) }}%
                </p>
                <p class="text-[11px] text-slate-500 mt-1 font-medium">
                    Persentase laba dari total omzet
                </p>
            </div>
        </div>

        {{-- Bar Pencarian & Filter --}}
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200/90 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="{{ route('recap.index') }}" method="GET" class="relative w-full md:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari Nama Klien, Proyek, atau ID..."
                           class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs font-semibold text-slate-700 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition shadow-xs whitespace-nowrap cursor-pointer">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('recap.index') }}" class="px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-50 rounded-xl transition border border-red-200 whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </form>

            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium w-full md:w-auto justify-end">
                <span>Menampilkan <strong class="text-slate-800">{{ $recaps->total() }}</strong> rekapan biaya</span>
                @if(request('search'))
                    <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md">(Filter Aktif)</span>
                @endif
            </div>
        </div>

        {{-- Tabel Data Rekapan --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-visible">
            <div class="overflow-x-auto min-h-[380px] pb-16">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/90 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th class="py-4 px-4 w-32 whitespace-nowrap">Tanggal</th>
                            <th class="py-4 px-4 min-w-[220px]">Proyek & Klien</th>
                            <th class="py-4 px-4 w-40 text-right whitespace-nowrap">Nilai Penawaran (Omzet)</th>
                            <th class="py-4 px-4 w-40 text-right whitespace-nowrap">Modal (Pengeluaran)</th>
                            <th class="py-4 px-4 w-44 text-right whitespace-nowrap">Net Profit (Laba)</th>
                            <th class="py-4 px-4 w-32 text-center whitespace-nowrap">Status Margin</th>
                            <th class="py-4 px-4 w-36 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recaps as $recap)
                        @php
                            $omzet = (float)($recap->total_penawaran_klien ?? 0);
                            $pengeluaran = (float)($recap->total_pengeluaran ?? 0);
                            $laba = (float)($recap->margin ?? ($omzet - $pengeluaran));
                            $pct = $omzet > 0 ? (($laba / $omzet) * 100) : 0;
                            $tglTampil = $recap->tanggal_keluar ? \Carbon\Carbon::parse($recap->tanggal_keluar)->format('d M Y') : $recap->created_at->format('d M Y');
                            $noSurat = $recap->offer ? ('SP-' . $recap->offer->created_at->format('Y') . '/' . str_pad($recap->offer->id, 4, '0', STR_PAD_LEFT)) : ('#REC-' . $recap->id);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- 1. Tanggal --}}
                            <td class="py-4 px-4 align-top">
                                <span class="text-xs font-bold text-slate-800 block whitespace-nowrap">{{ $tglTampil }}</span>
                                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">ID Rekap #{{ $recap->id }}</span>
                            </td>

                            {{-- 2. Proyek & Klien --}}
                            <td class="py-4 px-4 align-top">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition">
                                        {{ $recap->offer->nama_klien ?? 'Tanpa Nama Klien' }}
                                    </span>
                                    <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                        <span class="text-[11px] font-mono font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                            {{ $noSurat }}
                                        </span>
                                        <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                            📦 {{ $recap->items->count() }} item belanja
                                        </span>
                                    </div>
                                    @if($recap->offer && $recap->offer->client_details)
                                        <p class="text-[11px] text-slate-400 truncate max-w-xs mt-1" title="{{ $recap->offer->client_details }}">
                                            {{ $recap->offer->client_details }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- 3. Nilai Penawaran (Omzet) --}}
                            <td class="py-4 px-4 align-top text-right">
                                <span class="text-sm font-black text-slate-800 block whitespace-nowrap">
                                    Rp {{ number_format($omzet, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium block">
                                    Harga penawaran klien
                                </span>
                            </td>

                            {{-- 4. Modal (Pengeluaran) --}}
                            <td class="py-4 px-4 align-top text-right">
                                <span class="text-sm font-black text-red-600 block whitespace-nowrap">
                                    Rp {{ number_format($pengeluaran, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium block">
                                    Material & upah tukang
                                </span>
                            </td>

                            {{-- 5. Net Profit (Laba Bersih) --}}
                            <td class="py-4 px-4 align-top text-right">
                                <span class="text-sm font-black {{ $laba >= 0 ? 'text-emerald-700' : 'text-red-600' }} block whitespace-nowrap">
                                    {{ $laba >= 0 ? '+' : '' }}Rp {{ number_format($laba, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] font-extrabold {{ $laba >= 0 ? 'text-emerald-600' : 'text-red-500' }} block">
                                    {{ ($pct >= 0 ? '+' : '') . number_format($pct, 1) }}% dari omzet
                                </span>
                            </td>

                            {{-- 6. Status Margin --}}
                            <td class="py-4 px-4 align-top text-center">
                                @if($laba >= 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Surplus
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Defisit
                                    </span>
                                @endif
                            </td>

                            {{-- 7. Aksi Dropdown --}}
                            <td class="py-4 px-4 align-top text-center">
                                <div x-data="{ open: false }" class="relative inline-block text-left" @click.away="open = false">
                                    <button @click="open = !open" 
                                            type="button"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-xs hover:border-slate-300 transition-all cursor-pointer">
                                        <span>Aksi</span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-xl bg-white border border-slate-200/90 py-1.5 z-50 divide-y divide-slate-100 text-left"
                                         style="display: none;">
                                        
                                        <div class="py-1">
                                            <a href="{{ route('recap.show', $recap->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                                <span class="text-sm">👁️</span>
                                                <span>Lihat Detail</span>
                                            </a>
                                            <a href="{{ route('recap.print', $recap->id) }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                                <span class="text-sm">🖨️</span>
                                                <span>Cetak Dokumen PDF</span>
                                            </a>
                                            <a href="{{ route('recap.export.excel', $recap->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600 transition">
                                                <span class="text-sm">📊</span>
                                                <span>Export Excel</span>
                                            </a>
                                            <a href="{{ route('recap.export.word', $recap->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition">
                                                <span class="text-sm">📄</span>
                                                <span>Export Word</span>
                                            </a>
                                        </div>

                                        <div class="py-1">
                                            <a href="{{ route('recap.edit', $recap->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition">
                                                <span class="text-sm">✏️</span>
                                                <span>Edit Rekapan</span>
                                            </a>
                                        </div>

                                        <div class="py-1">
                                            <form action="{{ route('recap.destroy', $recap->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data rekapan proyek ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition text-left cursor-pointer">
                                                    <span class="text-sm">🗑️</span>
                                                    <span>Hapus Rekapan</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-2xl text-slate-400">
                                        📋
                                    </div>
                                    <p class="text-sm font-bold text-slate-600">Belum ada data rekapan biaya proyek.</p>
                                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                                        Anda dapat membuat analisis rekapan biaya modal proyek melalui menu <strong>Histori Penawaran</strong> &rarr; pilih penawaran proyek &rarr; klik tombol <strong>📋 Buat Rekapan</strong>.
                                    </p>
                                    <a href="{{ route('histori.index', ['jenis' => 'proyek']) }}" class="mt-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                        Buka Histori Penawaran Proyek
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($recaps->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $recaps->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
