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

        {{-- CARD STATUS KONFIRMASI GUDANG & NOTIFIKASI PRODUK TERDAFTAR --}}
        <div class="mb-6 bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden print:hidden">
            {{-- Header Status --}}
            <div class="p-5 md:p-6 {{ $recap->isAutoVerified() ? 'bg-gradient-to-r from-blue-50 via-indigo-50 to-white border-b border-blue-100' : ($recap->isConfirmed() ? 'bg-gradient-to-r from-emerald-50 via-teal-50 to-white border-b border-emerald-100' : 'bg-gradient-to-r from-amber-50 via-orange-50 to-white border-b border-amber-100') }}">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        @if($recap->isAutoVerified())
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-blue-600/20 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-blue-800">Status Rekapan:</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-600 text-white shadow-xs">
                                        Terverifikasi Otomatis
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 font-medium">
                                    Rekapan ini hanya memuat pengeluaran selain produk terdaftar (seperti material umum atau upah), sehingga diverifikasi otomatis tanpa pengurangan stok gudang.
                                </p>
                            </div>
                        @elseif($recap->isConfirmed())
                            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-600/20 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-emerald-800">Status Rekapan:</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-600 text-white shadow-xs">
                                        Terkonfirmasi
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 font-medium">
                                    Telah dikonfirmasi pada <strong class="text-slate-800">{{ \Carbon\Carbon::parse($recap->confirmed_at)->translatedFormat('d F Y, H:i') }} WIB</strong>
                                    @if($recap->confirmer)
                                        oleh <strong class="text-slate-800">{{ $recap->confirmer->name }}</strong> ({{ $recap->confirmer->role_label }})
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shadow-md shadow-amber-500/20 flex-shrink-0 animate-pulse">
                                📦
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-amber-800">Status Rekapan:</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-500 text-white shadow-xs">
                                        Belum Dikonfirmasi
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 font-medium">
                                    Menunggu konfirmasi pengeluaran barang oleh Staff Gudang.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Konfirmasi / Badge Status --}}
                    @if($recap->isAutoVerified())
                        <div class="px-4 py-2 bg-blue-100/80 border border-blue-300 text-blue-800 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span>✓ Otomatis Terverifikasi (Non-Stok)</span>
                        </div>
                    @elseif($recap->isConfirmed())
                        <div class="px-4 py-2 bg-emerald-100/70 border border-emerald-300 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span>✓ Stok Terpotong</span>
                        </div>
                    @else
                        @if($hasInsufficientStock)
                            <div class="flex flex-col items-end gap-1">
                                <button type="button" disabled
                                        class="px-5 py-3 rounded-xl text-xs font-black text-slate-400 bg-slate-100 border border-slate-300 flex items-center gap-2 cursor-not-allowed opacity-90 shadow-none">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>Konfirmasi Dinonaktifkan (Stok Kurang)</span>
                                </button>
                                <span class="text-[10px] text-red-600 font-bold flex items-center gap-1">
                                    ⚠️ Harap restock produk terlebih dahulu
                                </span>
                            </div>
                        @else
                            @if(Auth::check() && in_array(Auth::user()->role, ['staff_gudang', 'admin']))
                                <form action="{{ route('recap.konfirmasi', $recap->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin mengonfirmasi rekapan proyek ini?\n\n• Stok produk terdaftar akan otomatis dipotong sesuai kuantitas rekapan.\n• Status rekapan akan berubah menjadi \'Terkonfirmasi\'.')">
                                    @csrf
                                    <button type="submit" 
                                            style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff !important;"
                                            class="px-5 py-3 rounded-xl text-xs font-black text-white hover:brightness-110 active:scale-95 transition-all shadow-md shadow-emerald-700/20 flex items-center gap-2 whitespace-nowrap cursor-pointer">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Konfirmasi & Potong Stok</span>
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            {{-- Notifikasi Produk Terdaftar Saja --}}
            <div class="p-5 md:p-6 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">🔔</span>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">
                            Notifikasi Produk Terdaftar (Pengeluaran Inventori Gudang)
                        </h3>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500">
                        Total Terdaftar: <strong class="text-indigo-600">{{ count($registeredItems) }}</strong> produk
                    </span>
                </div>

                @if(count($registeredItems) > 0)
                    @if($hasInsufficientStock && !$recap->isConfirmed())
                        <div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3 text-red-800 text-xs">
                            <span class="text-base flex-shrink-0">⚠️</span>
                            <div>
                                <p class="font-bold text-red-900">Perhatian: Stok Gudang Tidak Mencukupi!</p>
                                <p class="mt-0.5 text-red-700 leading-relaxed">
                                    Rekapan ini tidak dapat dikonfirmasi oleh Staff Gudang karena terdapat produk yang stoknya di bawah kuantitas kebutuhan proyek. Silakan lakukan penambahan / restock produk di master data terlebih dahulu.
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                            Item berikut merupakan produk yang telah terdaftar di sistem master data gudang. Hanya item ini yang akan memotong kuantitas stok inventori.
                        </p>
                    @endif

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-3">Nama Produk</th>
                                    <th class="py-2.5 px-3 text-center">Ukuran / Varian</th>
                                    <th class="py-2.5 px-3 text-center">QTY Dibutuhkan</th>
                                    <th class="py-2.5 px-3 text-center">Sisa Stok Gudang</th>
                                    <th class="py-2.5 px-3 text-center">Status Stok</th>
                                    <th class="py-2.5 px-3 text-center">Status Pemotongan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($registeredItems as $reg)
                                    <tr class="{{ !$reg['stok_cukup'] && !$recap->isConfirmed() ? 'bg-red-50/70 hover:bg-red-50' : 'hover:bg-slate-50/50' }}">
                                        <td class="py-2.5 px-3 font-bold text-slate-900">
                                            {{ $reg['product']->nama_produk }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                            {{ $reg['product']->ukuran ?: ($reg['item']->ukuran ?: '-') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-extrabold text-blue-700">
                                            {{ $reg['qty_butuh'] }}
                                            @if(isset($reg['total_dibutuhkan']) && $reg['total_dibutuhkan'] > $reg['qty_butuh'])
                                                <span class="block text-[10px] text-slate-500 font-normal">
                                                    (Total rekapan: {{ $reg['total_dibutuhkan'] }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-bold text-slate-700">
                                            {{ $reg['stok_sekarang'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            @if($recap->isConfirmed())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    Tersedia: {{ $reg['stok_sekarang'] }}
                                                </span>
                                            @elseif($reg['stok_cukup'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    ✓ Cukup ({{ $reg['stok_sekarang'] }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800 border border-red-200 animate-pulse">
                                                    ⚠️ Kurang (Sisa: {{ $reg['stok_sekarang'] }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            @if($recap->isConfirmed())
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Telah Dipotong
                                                </span>
                                            @elseif(!$reg['stok_cukup'])
                                                <span class="inline-flex items-center text-[10px] font-bold text-red-700 bg-red-100/70 px-2 py-0.5 rounded border border-red-200">
                                                    Tertahan (Stok Kurang)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                                    Siap Dikonfirmasi
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-3 text-slate-600 text-xs">
                        <span class="text-base">ℹ️</span>
                        <span>Rekapan ini tidak memuat produk cat/material yang terdaftar di master data gudang. Konfirmasi tidak akan memotong stok inventori.</span>
                    </div>
                @endif
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
                            <th colspan="7" class="border border-black p-2.5 text-center font-bold text-base bg-white">
                                Audit Material & Pekerjaan
                            </th>
                        </tr>
                        {{-- HEADER KOLOM --}}
                        <tr class="bg-[#d9e1f2] text-black font-bold">
                            <th class="border border-black p-2 text-left w-[13%]">TGL Keluar</th>
                            <th class="border border-black p-2 text-left w-[24%]">Item/Deskripsi</th>
                            <th class="border border-black p-2 text-left w-[15%]">Kategori</th>
                            <th class="border border-black p-2 text-center w-[12%]">Ukuran</th>
                            <th class="border border-black p-2 text-center w-[8%]">QTY</th>
                            <th class="border border-black p-2 text-right w-[14%]">Harga satuan</th>
                            <th class="border border-black p-2 text-right w-[14%]">Subtotal</th>
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
                                @if($item->isRegisteredProduct())
                                    <span class="inline-block text-[9px] bg-blue-100 text-blue-800 font-bold px-1.5 py-0.2 rounded print:hidden ml-1">Produk Terdaftar</span>
                                @endif
                                @if($item->detail)
                                    <br><span class="text-xs text-gray-600">{{ $item->detail }}</span>
                                @endif
                            </td>
                            <td class="border border-black p-2">
                                {{ $item->kategori ? ucwords($item->kategori) : '-' }}
                            </td>
                            <td class="border border-black p-2 text-center font-medium">
                                {{ $item->ukuran ?: '-' }}
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
                            <td colspan="7" class="border border-black p-4 text-center text-gray-500 italic">
                                Belum ada rincian item pengeluaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="border border-black p-2 font-bold text-right text-base">
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