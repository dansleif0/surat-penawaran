@extends('layouts.app')

@section('content')
<div class="container mx-auto my-8 px-4 ml-0 md:ml-64 transition-all duration-300">
    <div class="max-w-5xl mx-auto">

        {{-- Navigasi & Toolbar Aksi --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 print:hidden">
            <a href="{{ route('recap.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-900 flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                KEMBALI KE HISTORI
            </a>

            <div class="flex flex-wrap justify-center gap-2">

                {{-- Tombol Excel --}}
                <a href="{{ route('recap.export.excel', $recap->id) }}" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    EXCEL
                </a>

                {{-- Tombol Word --}}
                <a href="{{ route('recap.export.word', $recap->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    WORD
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden print:shadow-none print:border-none">
            {{-- Header Konten --}}
            <div class="p-10 border-b border-slate-50 bg-slate-50/30">
                <div class="flex flex-col md:flex-row justify-between items-start gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                            Laporan Finansial Resmi
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tighter italic">Rincian Analisis Rekap Biaya</h2>
                        <p class="text-slate-500 font-medium">Klien: <span class="text-slate-900 font-bold underline decoration-blue-500/30 underline-offset-4">{{ $recap->offer->nama_klien }}</span></p>
                    </div>
                    <div class="text-left md:text-right space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block">Diterbitkan Pada</span>
                        <p class="text-lg font-black text-slate-700">{{ $recap->created_at->format('d F Y') }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">REF: #REC-{{ $recap->id }}</p>
                    </div>
                </div>
            </div>

            {{-- Grid Ringkasan Performa --}}
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100 border-b border-slate-100">
                <div class="p-10 transition-colors hover:bg-slate-50/50">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Penawaran</span>
                    <p class="text-2xl font-black text-blue-600 tracking-tight">Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}</p>
                </div>
                <div class="p-10 transition-colors hover:bg-slate-50/50">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Modal (Expense)</span>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}</p>
                </div>
                <div class="p-10 @if($recap->margin >= 0) bg-emerald-50/40 @else bg-red-50/40 @endif">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-black @if($recap->margin >= 0) text-emerald-600 @else text-red-600 @endif uppercase tracking-widest block">Net Profit</span>
                        <span class="px-2 py-0.5 @if($recap->margin >= 0) bg-emerald-100 text-emerald-700 @else bg-red-100 text-red-700 @endif rounded text-[8px] font-black uppercase">
                            {{ $recap->margin >= 0 ? 'Surplus' : 'Defisit' }}
                        </span>
                    </div>
                    <p class="text-3xl font-black @if($recap->margin >= 0) text-emerald-600 @else text-red-600 @endif tracking-tighter">
                        Rp {{ number_format($recap->margin, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Tabel Detail --}}
            <div class="p-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-5 bg-slate-900 rounded-full"></div>
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Audit Material & Pekerjaan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-4 text-left font-black uppercase text-[10px] tracking-widest">Tgl Keluar</th>
                                <th class="pb-4 text-left font-black uppercase text-[10px] tracking-widest">Item / Deskripsi</th>
                                <th class="pb-4 text-center font-black uppercase text-[10px] tracking-widest w-24">Qty</th>
                                <th class="pb-4 text-right font-black uppercase text-[10px] tracking-widest">Satuan</th>
                                <th class="pb-4 text-right font-black uppercase text-[10px] tracking-widest">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recap->items as $item)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="py-5 text-slate-500 font-medium">
                                    {{-- Perbaikan: Pastikan nama kolom 'tanggal_item' sesuai dengan di database --}}
                                    @if($item->tanggal_item)
                                    {{ \Carbon\Carbon::parse($item->tanggal_item)->format('d/m/Y') }}
                                    @elseif($item->created_at)
                                    {{ $item->created_at->format('d/m/Y') }}
                                    @else
                                    <span class="text-slate-300 italic">N/A</span>
                                    @endif
                                </td>
                                <td class="py-5">
                                    <p class="font-black text-slate-800 tracking-tight">{{ $item->material }}</p>
                                    @if($item->detail)
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">{{ $item->detail }}</p>
                                    @endif
                                </td>
                                <td class="py-5 text-center font-bold text-slate-600">{{ $item->qty }}</td>
                                <td class="py-5 text-right font-bold text-slate-600 italic">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                <td class="py-5 text-right font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="pt-10 text-right font-black text-slate-400 uppercase text-[10px] tracking-[0.2em]">Total Modal Akhir</td>
                                <td class="pt-10 text-right font-black text-3xl text-slate-900 tracking-tighter italic">
                                    Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Disclaimer Footer --}}
            <div class="px-10 py-8 bg-slate-900 text-slate-500 border-t border-slate-800">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-center md:text-left">
                        DIHASILKAN SECARA OTOMATIS OLEH <span class="text-white">PENAWARAN.APP</span> PADA {{ now()->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-[9px] font-bold italic">Sistem Manajemen Inventori & Profit V2.0</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,700;0,800;1,800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    @media print {
        body {
            background: white !important;
        }

        .ml-64 {
            margin-left: 0 !important;
        }

        .max-w-5xl {
            max-width: 100% !important;
        }

        .print\:hidden {
            display: none !important;
        }

        .rounded-[2.5rem] {
            border-radius: 0 !important;
        }
    }
</style>
@endsection