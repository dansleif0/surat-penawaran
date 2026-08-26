@extends('layouts.app')

@section('content')
<div class="container mx-auto my-8 px-4">
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">Histori Rekapan Biaya</h1>
                <p class="text-sm text-gray-500">Pantau performa laba rugi dari setiap penawaran.</p>
            </div>
            
            <div class="flex items-center gap-4 w-full md:w-auto">
                <form action="{{ route('recap.index') }}" method="GET" class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Klien atau No Rekap..." class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 transition-all font-medium text-gray-700 placeholder-gray-400">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
                
                <div class="bg-blue-50 px-4 py-2.5 rounded-xl border border-blue-100 whitespace-nowrap">
                    <span class="text-xs font-bold text-blue-600 uppercase">Total: {{ $recaps->total() }}</span>
                </div>
            </div>
        </div>

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Omzet</p>
                <p class="text-2xl font-black text-gray-800">Rp {{ number_format($recaps->sum('total_penawaran_klien'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pengeluaran</p>
                <p class="text-2xl font-black text-red-500">Rp {{ number_format($recaps->sum('total_pengeluaran'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Laba Bersih</p>
                <p class="text-2xl font-black text-emerald-500">Rp {{ number_format($recaps->sum('margin'), 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase">Tanggal</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase">Klien / Project</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase text-right">Penawaran</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase text-right">Modal</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase text-center">Margin</th>
                        <th class="py-4 px-6 text-[10px] font-bold text-gray-400 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recaps as $recap)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6 text-sm text-gray-600">
                            {{ $recap->created_at->format('d M Y') }}
                        </td>
                        <td class="py-4 px-6">
                            <p class="text-sm font-bold text-gray-800">{{ $recap->offer->nama_klien }}</p>
                            <p class="text-xs text-gray-400">ID: #{{ $recap->offer->id }}</p>
                        </td>
                        <td class="py-4 px-6 text-sm text-right font-medium text-gray-700">
                            Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-sm text-right font-medium text-red-400">
                            Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            @php $percent = ($recap->margin / $recap->total_penawaran_klien) * 100; @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black {{ $recap->margin >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                {{ number_format($percent, 1) }}%
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('recap.show', $recap->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" />
                                    </svg>
                                </a>
                                <a href="{{ route('recap.edit', $recap->id) }}" class="p-2 text-gray-400 hover:text-yellow-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" />
                                    </svg>
                                </a>
                                <form action="{{ route('recap.destroy', $recap->id) }}" method="POST" onsubmit="return confirm('Hapus rekapan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400 text-sm">Belum ada data rekapan biaya.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection