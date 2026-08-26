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
                    <p class="text-blue-300 font-bold uppercase tracking-widest text-xs mb-1">Administrator Dashboard</p>
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

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
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
            <div class="bg-white p-6 rounded-2xl shadow-md flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow">
                <span class="text-4xl font-black text-gray-800 mb-1">{{ \App\Models\Skp::count() }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SPK</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

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