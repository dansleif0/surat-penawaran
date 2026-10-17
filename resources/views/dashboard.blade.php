@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 font-sans">

    <div class="relative bg-gray-900 pb-40 pt-16 px-6 rounded-b-[50px] shadow-2xl overflow-hidden">

        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/toko-jotun.jpg') }}" alt="Background Toko" class="w-full h-full object-cover opacity-50 blur-[2px]">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-900/90 via-gray-900/70 to-gray-900/95"></div>
        </div>

        <div class="relative z-10 container mx-auto max-w-6xl flex flex-col md:flex-row justify-between items-center text-white">

            <div class="flex items-center space-x-6 mb-6 md:mb-0">
                <div class="h-20 w-20 rounded-full border-4 border-white/20 p-1 shadow-xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-blue-300 font-bold uppercase tracking-widest text-xs mb-1">{{ Auth::user()->role_label ?? 'Administrator' }} Dashboard</p>
                    <h1 class="text-4xl font-extrabold tracking-tight">
                        Halo, {{ Auth::user()->name ?? 'Admin Tasniem' }}
                    </h1>
                    <p class="text-gray-400 text-sm mt-1">Pantau performa bisnis Anda hari ini.</p>
                </div>
            </div>

            <div class="text-right bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                <p class="text-gray-300 text-sm font-medium uppercase tracking-wider">Hari ini</p>
                <p class="text-3xl font-bold font-mono text-white">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="text-emerald-400 text-sm font-bold mt-1">
                    Lokasi: Batam, Indonesia
                </p>
            </div>
        </div>
    </div>

    <div class="container mx-auto max-w-6xl px-4 -mt-28 relative z-20">

        @php
            $persiapanActiveCount = \App\Models\Invoice::where('is_persiapan', true)->whereNull('persiapan_confirmed_at')->count();
        @endphp

        {{-- Banner Siap Dipersiapkan untuk Driver & Staff Gudang --}}
        @if(Auth::check() && Auth::user()->role !== 'admin' && $persiapanActiveCount > 0)
        <div class="mb-8 bg-gradient-to-r from-orange-500 to-amber-500 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 animate-slide-down">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white flex-shrink-0 shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="bg-white text-orange-600 text-xs font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider">Persiapkan</span>
                        <h3 class="text-xl font-black">{{ $persiapanActiveCount }} Invoice Perlu Dipersiapkan!</h3>
                    </div>
                    <p class="text-orange-100 text-sm mt-1">Super Admin telah menandai invoice produk yang siap diambil/dikirim.</p>
                </div>
            </div>
            <a href="{{ route('invoice.histori') }}" class="px-5 py-2.5 bg-white text-orange-600 hover:bg-orange-50 font-bold rounded-xl shadow-md transition-all duration-200 hover:scale-105 whitespace-nowrap text-sm flex items-center gap-2">
                Buka Histori Invoice
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
        @endif

        {{-- KARTU STATISTIK --}}
        @if(Auth::check() && Auth::user()->role === 'admin')
            {{-- Statistik Khusus Admin --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-8 shadow-xl border-l-8 border-blue-500 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-wider mb-2">Total Nilai Penawaran</p>
                        <h2 class="text-3xl font-extrabold text-gray-800">
                            Rp {{ number_format(\App\Models\Offer::sum('total_keseluruhan'), 0, ',', '.') }}
                        </h2>
                        <p class="text-blue-500 text-sm mt-2 font-medium">Potensi Pendapatan</p>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-32 bg-gradient-to-l from-blue-50 to-transparent opacity-50"></div>
                    <div class="h-16 w-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-xl border-l-8 border-emerald-500 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-wider mb-2">Total Nilai Tagihan</p>
                        <h2 class="text-3xl font-extrabold text-gray-800">
                            Rp {{ number_format(\App\Models\Invoice::sum('grand_total'), 0, ',', '.') }}
                        </h2>
                        <p class="text-emerald-500 text-sm mt-2 font-medium">Tagihan Dikeluarkan</p>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-32 bg-gradient-to-l from-emerald-50 to-transparent opacity-50"></div>
                    <div class="h-16 w-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow">
                    <span class="text-4xl font-black text-gray-800 mb-1">{{ \App\Models\Offer::count() }}</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Penawaran</span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow">
                    <span class="text-4xl font-black text-gray-800 mb-1">{{ \App\Models\Invoice::count() }}</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Invoice</span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow">
                    <span class="text-4xl font-black text-gray-800 mb-1">{{ \App\Models\Bast::count() }}</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">BAST</span>
                </div>
                {{-- Sementara disembunyikan: SPK Statistik --}}
                {{-- <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow">
                    <span class="text-4xl font-black text-gray-800 mb-1">{{ \App\Models\Skp::count() }}</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SPK</span>
                </div> --}}
            </div>
        @else
            {{-- Statistik Khusus Staff Gudang & Driver --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 shadow-xl border-l-8 border-orange-500 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-wider mb-1">Perlu Dipersiapkan</p>
                        <h2 class="text-3xl font-extrabold text-orange-600">
                            {{ $persiapanActiveCount }}
                        </h2>
                        <p class="text-orange-500 text-xs mt-1 font-semibold">Invoice Produk Aktif</p>
                    </div>
                    <div class="h-14 w-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-xl border-l-8 border-emerald-500 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-wider mb-1">Sudah Dikonfirmasi</p>
                        <h2 class="text-3xl font-extrabold text-emerald-600">
                            {{ \App\Models\Invoice::whereNotNull('persiapan_confirmed_at')->count() }}
                        </h2>
                        <p class="text-emerald-500 text-xs mt-1 font-semibold">Telah Diproses</p>
                    </div>
                    <div class="h-14 w-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-xl border-l-8 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 font-bold uppercase text-xs tracking-wider mb-1">Total Semua Invoice</p>
                        <h2 class="text-3xl font-extrabold text-blue-600">
                            {{ \App\Models\Invoice::count() }}
                        </h2>
                        <p class="text-blue-500 text-xs mt-1 font-semibold">Histori Invoice</p>
                    </div>
                    <div class="h-14 w-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Penawaran Terakhir Masuk</h3>
                    <span class="text-xs text-blue-500 font-semibold bg-blue-100 px-2 py-1 rounded-md">Live Update</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach(\App\Models\Offer::latest()->take(4)->get() as $offer)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs mr-3">
                                SP
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $offer->nama_klien }}</p>
                                <p class="text-xs text-gray-500">{{ $offer->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-700">Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach

                    @if(\App\Models\Offer::count() == 0)
                        <div class="p-6 text-center text-gray-400 text-sm">Belum ada data.</div>
                    @endif
                </div>
            </div>
            @else
            {{-- Untuk Driver & Staff Gudang: Daftar Invoice Perlu Dipersiapkan --}}
            <div class="bg-white rounded-2xl shadow-lg border border-orange-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-orange-100 bg-orange-50 flex justify-between items-center">
                    <h3 class="font-bold text-orange-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-ping"></span>
                        Invoice Siap Dipersiapkan
                    </h3>
                    <span class="text-xs text-orange-700 font-bold bg-orange-200 px-2.5 py-0.5 rounded-full">
                        {{ $persiapanActiveCount }} Antrian
                    </span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse(\App\Models\Invoice::where('is_persiapan', true)->latest('persiapan_at')->take(4)->get() as $pInv)
                    <div class="px-6 py-4 flex items-center justify-between bg-orange-50/40 hover:bg-orange-50 transition">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xs mr-3">
                                🚚
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $pInv->nama_klien }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $pInv->no_invoice }}</p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <span class="bg-orange-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full">
                                PERSIAPKAN
                            </span>
                            <a href="{{ route('invoice.histori') }}" class="text-xs text-orange-600 hover:text-orange-800 font-semibold underline">
                                Lihat
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-400 text-sm">Tidak ada invoice yang perlu dipersiapkan saat ini.</div>
                    @endforelse
                </div>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Invoice Terakhir Dibuat</h3>
                    <span class="text-xs text-emerald-500 font-semibold bg-emerald-100 px-2 py-1 rounded-md">Live Update</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach(\App\Models\Invoice::latest()->take(4)->get() as $invoice)
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs mr-3">
                                INV
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $invoice->nama_klien }}</p>
                                <p class="text-xs text-gray-500">{{ $invoice->no_invoice }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-700">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                            @if($invoice->sisa_pembayaran > 0)
                                <p class="text-xs text-red-500 font-semibold">Belum Lunas</p>
                            @else
                                <p class="text-xs text-emerald-500 font-semibold">Lunas</p>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if(\App\Models\Invoice::count() == 0)
                        <div class="p-6 text-center text-gray-400 text-sm">Belum ada data.</div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
@endsection