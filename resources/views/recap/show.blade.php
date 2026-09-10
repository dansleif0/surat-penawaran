@extends('layouts.app')

@section('content')
<div class="container mx-auto my-8 px-4 ml-0 md:ml-64 transition-all duration-300">
    <div class="max-w-4xl mx-auto">

        {{-- Navigasi & Toolbar Aksi --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 print:hidden">
            <a href="{{ route('recap.index') }}" class="text-sm font-bold text-slate-500 hover:text-slate-900 flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                &larr; KEMBALI KE HISTORI
            </a>

            <div class="flex flex-wrap justify-center gap-2">
                {{-- Tombol Print Surat --}}
                <a href="{{ route('recap.print', $recap->id) }}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <span>🖨️</span> CETAK SURAT (PDF)
                </a>

                {{-- Tombol Excel --}}
                <a href="{{ route('recap.export.excel', $recap->id) }}" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                    <span>📊</span> EXCEL
                </a>

                {{-- Tombol Word --}}
                <a href="{{ route('recap.export.word', $recap->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                    <span>📄</span> WORD
                </a>
            </div>
        </div>

        {{-- KERTAS SURAT REKAPAN BIAYA (Format Murni Sesuai Cetak) --}}
        <div class="bg-white p-8 md:p-12 shadow-xl rounded-lg border border-gray-200 text-black font-sans" id="recap-paper">

            {{-- HEADER KOP SURAT --}}
            <header class="w-full mb-6">
                <div class="w-full">
                    <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
                </div>
            </header>

            {{-- JUDUL & INFORMASI SURAT --}}
            <section class="mt-6 flex justify-between items-start text-sm">
                <div>
                    <h1 class="text-xl font-bold text-black tracking-tight mb-1">Rincian Analisis Rekap Biaya</h1>
                    <p class="font-medium text-black">Klien : <span class="font-normal">{{ $recap->offer->nama_klien }}</span></p>
                    <p class="font-medium text-black">Alamat Project : <span class="font-normal">{{ $recap->offer->client_details ?? '-' }}</span></p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-black italic">Diterbitkan pada</p>
                    <p class="text-sm font-normal text-black">{{ \Carbon\Carbon::parse($recap->created_at)->translatedFormat('d F Y') }}</p>
                </div>
            </section>

            {{-- RINGKASAN BIAYA & NET PROFIT --}}
            <section class="mt-6">
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-[#d9ead3] text-black">
                            <th class="border border-black p-2 text-left font-bold w-1/3">Total Penawaran</th>
                            <th class="border border-black p-2 text-left font-bold w-1/3">Total Modal (expense)</th>
                            <th class="border border-black p-2 text-left font-bold w-1/3">Net Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black p-3 text-lg font-bold text-blue-600 align-top">
                                Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}
                            </td>
                            <td class="border border-black p-3 text-lg font-bold text-red-600 align-top">
                                Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                            </td>
                            <td class="border border-black p-3 text-lg font-bold @if($recap->margin >= 0) text-emerald-600 @else text-red-600 @endif align-top">
                                Rp {{ number_format($recap->margin, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            {{-- RINCIAN AUDIT MATERIAL & PEKERJAAN --}}
            <section class="mt-6">
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        {{-- HEADER SUBTITEL AUDIT --}}
                        <tr>
                            <th colspan="6" class="border border-black p-2.5 text-center font-bold text-base bg-white">
                                Audit Material & Pekerjaan
                            </th>
                        </tr>
                        {{-- HEADER KOLOM --}}
                        <tr class="bg-[#d9e1f2] text-black font-bold">
                            <th class="border border-black p-2 text-left w-[15%]">TGL Keluar</th>
                            <th class="border border-black p-2 text-left w-[25%]">Item/Deskripsi</th>
                            <th class="border border-black p-2 text-left w-[20%]">Kategori</th>
                            <th class="border border-black p-2 text-center w-[10%]">QTY</th>
                            <th class="border border-black p-2 text-right w-[15%]">Harga satuan</th>
                            <th class="border border-black p-2 text-right w-[15%]">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recap->items as $item)
                        <tr>
                            <td class="border border-black p-2 whitespace-nowrap">
                                @if($item->tanggal_item)
                                    {{ \Carbon\Carbon::parse($item->tanggal_item)->format('d/m/Y') }}
                                @elseif($item->created_at)
                                    {{ $item->created_at->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="border border-black p-2">
                                <span class="font-bold">{{ $item->material }}</span>
                                @if($item->detail)
                                    <br><span class="text-xs text-gray-600">{{ $item->detail }}</span>
                                @endif
                            </td>
                            <td class="border border-black p-2">
                                {{ $item->kategori ? ucwords($item->kategori) : '-' }}
                            </td>
                            <td class="border border-black p-2 text-center font-medium">
                                {{ $item->qty + 0 }}
                            </td>
                            <td class="border border-black p-2 text-right whitespace-nowrap">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td class="border border-black p-2 text-right font-medium whitespace-nowrap">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="border border-black p-4 text-center text-gray-500 italic">
                                Belum ada rincian item pengeluaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="border border-black p-2 font-bold text-right text-base">
                                Total Modal Akhir
                            </td>
                            <td colspan="2" class="border border-black p-2 font-bold text-right text-base">
                                Rp {{ number_format($recap->total_pengeluaran, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>

        </div>
    </div>
</div>
@endsection