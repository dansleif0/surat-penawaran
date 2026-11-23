@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] ml-0 md:ml-64 p-4 lg:p-8 transition-all duration-300">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                    <a href="{{ route('recap.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        Histori Rekapan
                    </a>
                    <span>/</span>
                    <span class="text-slate-600">Edit Analisis Margin</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Edit Analisis Margin</h1>
                    <span class="px-2.5 py-0.5 text-xs font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200/60">
                        Mode Edit
                    </span>
                </div>
                <p class="text-slate-500 text-xs md:text-sm mt-1">
                    Proyek / Klien: <span class="font-bold text-slate-800 bg-white px-2.5 py-0.5 rounded-lg border border-slate-200 shadow-xs">{{ $recap->offer->nama_klien ?? '-' }}</span>
                </p>
            </div>

            <a href="{{ route('recap.index') }}" class="btn-cancel-nav inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-2xl text-xs font-bold hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Batal & Kembali
            </a>
        </div>

        {{-- Datalist Produk Cat Terdaftar untuk Autocomplete --}}
        <datalist id="registered-paint-products">
            @if(isset($products) && $products->isNotEmpty())
                @foreach($products->unique(fn($p) => strtolower(trim($p->nama_produk))) as $prod)
                    <option value="{{ $prod->nama_produk }}">
                        {{ $prod->performa ? $prod->performa . ' | ' : '' }}Produk Cat Terdaftar
                    </option>
                @endforeach
            @endif
        </datalist>

        <form action="{{ route('recap.update', $recap->id) }}" method="POST" id="recap-form" novalidate>
            @csrf
            @method('PUT')

            <input type="hidden" name="offer_id" value="{{ $recap->offer_id }}">
            <input type="hidden" name="total_penawaran_klien" value="{{ $recap->total_penawaran_klien }}">
            <input type="hidden" name="total_pengeluaran" id="hidden-total-pengeluaran" value="{{ $recap->total_pengeluaran }}">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                {{-- Left: Main Input Table --}}
                <div class="xl:col-span-8 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden">
                        {{-- Table Header Bar --}}
                        <div class="px-6 py-4.5 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 bg-gradient-to-r from-slate-50 to-white">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-sm tracking-tight">DAFTAR PENGELUARAN (MODAL)</h3>
                                    <p class="text-[11px] text-slate-400 font-medium">Perbarui rincian belanja material atau upah pekerja proyek</p>
                                </div>
                            </div>
                            <button type="button" id="add-row-btn" class="text-xs font-bold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all shadow-sm shadow-indigo-200 flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                Tambah Baris
                            </button>
                        </div>

                        {{-- Table Container --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50/90 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200/80">
                                    <tr>
                                        <th class="py-3 px-3 w-32 whitespace-nowrap">Tgl Keluar</th>
                                        <th class="py-3 px-3 w-44 whitespace-nowrap">Kategori</th>
                                        <th class="py-3 px-3 min-w-[220px]">Item / Deskripsi Pengeluaran</th>
                                        <th class="py-3 px-3 w-36 whitespace-nowrap">Ukuran Cat</th>
                                        <th class="py-3 px-3 w-36 text-right whitespace-nowrap">Harga Satuan</th>
                                        <th class="py-3 px-3 w-20 text-center whitespace-nowrap">Qty</th>
                                        <th class="py-3 px-3 w-32 text-right whitespace-nowrap">Subtotal</th>
                                        <th class="py-3 px-2 w-10 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="recap-rows" class="divide-y divide-slate-100">
                                    @foreach($recap->items as $index => $item)
                                    @php
                                        $catVal = strtolower(trim($item->kategori ?? 'material cat'));
                                    @endphp
                                    <tr class="recap-row group hover:bg-slate-50/70 transition-colors" data-initial-ukuran="{{ $item->ukuran }}">
                                        {{-- 1. Tgl Keluar --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="input-field-container">
                                                <input type="date"
                                                       name="items[{{ $index }}][tanggal_item]"
                                                       value="{{ $item->tanggal_item ? $item->tanggal_item->format('Y-m-d') : '' }}"
                                                       class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-medium text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                            </div>
                                        </td>

                                        {{-- 2. Kategori (Mendahului Item / Deskripsi) --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="flex flex-col gap-1.5 input-field-container">
                                                <select name="items[{{ $index }}][kategori]" class="input-kategori w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                                    <option value="material cat" {{ $catVal === 'material cat' ? 'selected' : '' }}>Material Cat</option>
                                                    <option value="material bangunan" {{ $catVal === 'material bangunan' ? 'selected' : '' }}>Material Bangunan</option>
                                                    <option value="upah" {{ $catVal === 'upah' ? 'selected' : '' }}>Upah</option>
                                                </select>
                                                <span class="cat-badge text-[10px] font-bold px-2 py-0.5 rounded-md inline-flex items-center gap-1 w-fit
                                                    {{ $catVal === 'material cat' ? 'text-blue-700 bg-blue-50/90 border border-blue-200/60' : ($catVal === 'material bangunan' ? 'text-amber-700 bg-amber-50/90 border border-amber-200/60' : 'text-emerald-700 bg-emerald-50/90 border border-emerald-200/60') }}">
                                                    {{ $catVal === 'material cat' ? '🎨 List Cat Aktif' : ($catVal === 'material bangunan' ? '🧱 Material Bangunan' : '👷 Upah Tenaga') }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- 3. Item / Deskripsi --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="flex flex-col gap-1.5 input-field-container">
                                                <input type="text"
                                                       name="items[{{ $index }}][material]"
                                                       value="{{ $item->material }}"
                                                       {{ $catVal === 'material cat' ? 'list=registered-paint-products' : '' }}
                                                       autocomplete="off"
                                                       placeholder="{{ $catVal === 'material cat' ? 'Ketik atau pilih nama cat terdaftar...' : 'Nama item / pengeluaran...' }}"
                                                       class="input-material w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">

                                                <input type="text"
                                                       name="items[{{ $index }}][detail]"
                                                       value="{{ $item->detail }}"
                                                       placeholder="Spesifikasi / catatan / merk (opsional)"
                                                       class="input-detail w-full bg-slate-50/70 hover:bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] font-medium text-slate-600 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                            </div>
                                        </td>

                                        {{-- 4. Ukuran Cat --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="ukuran-field-wrapper input-field-container">
                                                <select data-name="items[{{ $index }}][ukuran]"
                                                        class="input-ukuran-select w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer hidden">
                                                    <option value="">-- Ukuran --</option>
                                                </select>

                                                <div class="ukuran-manual-box flex items-center gap-1">
                                                    <input type="text"
                                                           data-name="items[{{ $index }}][ukuran]"
                                                           name="items[{{ $index }}][ukuran]"
                                                           value="{{ $item->ukuran }}"
                                                           placeholder="cth: 2.5 L"
                                                           class="input-ukuran-manual w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                                    <button type="button" 
                                                            class="btn-switch-to-select hidden p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg text-xs font-bold transition flex-shrink-0" 
                                                            title="Pilih kembali dari opsi ukuran terdaftar">
                                                        ▼
                                                    </button>
                                                </div>
                                                <span class="ukuran-notif-label text-[10px] text-slate-400 mt-0.5 block font-medium leading-tight"></span>
                                            </div>
                                        </td>

                                        {{-- 5. Harga Satuan --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="input-field-container">
                                                <div class="input-harga-wrapper flex items-center bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition-all">
                                                    <span class="text-slate-400 text-[10px] font-extrabold mr-1">Rp</span>
                                                    <input type="number"
                                                           name="items[{{ $index }}][harga]"
                                                           value="{{ (int)$item->harga }}"
                                                           placeholder="0"
                                                           class="input-harga w-full border-none focus:ring-0 p-0 text-xs text-right font-bold text-slate-800 bg-transparent outline-none">
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 6. Qty --}}
                                        <td class="py-3 px-3 align-top">
                                            <div class="input-field-container">
                                                <input type="number"
                                                       name="items[{{ $index }}][qty]"
                                                       value="{{ $item->qty }}"
                                                       step="0.01"
                                                       class="input-qty w-full border border-slate-200 bg-slate-50 hover:bg-white rounded-xl py-2 px-1 text-xs text-center font-bold text-slate-800 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                            </div>
                                        </td>

                                        {{-- 7. Subtotal --}}
                                        <td class="py-3 px-3 text-right align-top pt-4">
                                            <span class="input-total-text text-xs font-black text-slate-900 block tracking-tight">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </td>

                                        {{-- 8. Aksi (Hapus) --}}
                                        <td class="py-3 px-2 text-center align-top pt-3">
                                            <button type="button" class="remove-row-btn p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Baris">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Footer Hint --}}
                        <div class="px-6 py-3 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Kolom <strong>Kategori</strong> &rarr; Pilih <em>Material Cat</em> untuk memunculkan rekomendasi katalog produk cat.
                            </span>
                            <span id="row-count-badge" class="font-semibold text-slate-400">{{ $recap->items->count() }} baris</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Summary Sidebar --}}
                <div class="xl:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        {{-- Calculation Card --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Ringkasan Finansial</h4>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-slate-100 text-slate-600">Real-time</span>
                            </div>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                                    <span class="text-xs text-slate-500 font-semibold">Total Nilai Penawaran</span>
                                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                                    <span class="text-xs text-slate-500 font-semibold">Total Pengeluaran (Modal)</span>
                                    <span id="display-total-pengeluaran" class="text-sm font-black text-red-600">Rp 0</span>
                                </div>

                                {{-- Margin Profit Card --}}
                                <div id="margin-card" class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 transition-all duration-500">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Profit</span>
                                        <div class="flex items-center gap-1.5">
                                            <span id="margin-percentage" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100/80 px-1.5 py-0.5 rounded">0%</span>
                                            <span id="margin-status" class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-100 text-emerald-700">Surplus</span>
                                        </div>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-slate-400">Rp</span>
                                        <span id="display-margin" class="text-3xl font-black tracking-tight text-slate-900">0</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Simpan Perubahan --}}
                            <div class="mt-6 pt-5 border-t border-slate-100">
                                <button type="submit"
                                        style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff !important; box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.45);"
                                        class="w-full py-4 px-6 rounded-2xl font-black text-white hover:brightness-105 active:scale-[0.98] transition-all flex flex-col items-center justify-center gap-1 cursor-pointer border border-sky-400">
                                    <div class="flex items-center gap-2 text-base font-black tracking-wide">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>PERBARUI REKAPAN BIAYA</span>
                                    </div>
                                    <span class="text-[11px] font-medium text-sky-100 opacity-95">
                                        Simpan pembaruan rincian pengeluaran modal proyek
                                    </span>
                                </button>
                            </div>
                        </div>

                        {{-- Info Box Panduan Pengisian --}}
                        <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #2563eb;" class="rounded-2xl p-5 shadow-xs">
                            <div class="flex items-center gap-2.5 mb-3.5">
                                <div style="background-color: #dbeafe; color: #1d4ed8;" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs flex-shrink-0">
                                    💡
                                </div>
                                <div>
                                    <h5 style="color: #0f172a;" class="font-extrabold text-xs uppercase tracking-wider">
                                        PANDUAN / TIPS PENGISIAN
                                    </h5>
                                    <p style="color: #64748b;" class="text-[11px] font-medium">
                                        Petunjuk pengisian rekapan biaya proyek
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs" style="color: #334155;">
                                <div class="flex items-start gap-2.5 p-3 rounded-xl" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                    <span style="background-color: #eff6ff; color: #2563eb; font-weight: 800;" class="w-5 h-5 rounded-md flex items-center justify-center text-[11px] flex-shrink-0 mt-0.5">1</span>
                                    <div class="leading-relaxed">
                                        <strong style="color: #1e40af;">Kategori Material Cat & Ukuran:</strong><br>
                                        Pilih kategori <em>Material Cat</em>, lalu ketik nama cat untuk memilih <strong>katalog produk terdaftar</strong>. Kolom <strong>Ukuran Cat</strong> akan otomatis menampilkan varian ukuran terdaftar (atau bisa pilih "Isi Manual" / ketik manual jika belum terdaftar).
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5 p-3 rounded-xl" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                    <span style="background-color: #fef3c7; color: #b45309; font-weight: 800;" class="w-5 h-5 rounded-md flex items-center justify-center text-[11px] flex-shrink-0 mt-0.5">2</span>
                                    <div class="leading-relaxed">
                                        <strong style="color: #92400e;">Kategori Material Bangunan & Upah:</strong><br>
                                        Gunakan untuk mencatat bahan non-cat (semen, pasir, kuas, dll.) atau upah tenaga kerja (tukang, mandor) secara bebas.
                                    </div>
                                </div>

                                <div class="flex items-start gap-2.5 p-3 rounded-xl" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                    <span style="background-color: #dcfce7; color: #15803d; font-weight: 800;" class="w-5 h-5 rounded-md flex items-center justify-center text-[11px] flex-shrink-0 mt-0.5">3</span>
                                    <div class="leading-relaxed">
                                        <strong style="color: #166534;">Perhitungan Laba Bersih Otomatis:</strong><br>
                                        Subtotal otomatis dihitung dari $(\text{Harga Satuan} \times \text{Qty})$. Kotak <strong>Net Profit</strong> langsung mengalkulasi surplus / defisit margin proyek secara real-time.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Template Baris Baru Pengeluaran --}}
<template id="row-template">
    <tr class="recap-row group hover:bg-slate-50/70 transition-colors">
        {{-- 1. Tgl Keluar --}}
        <td class="py-3 px-3 align-top">
            <div class="input-field-container">
                <input type="date" name="items[INDEX][tanggal_item]" class="w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-medium text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
            </div>
        </td>

        {{-- 2. Kategori --}}
        <td class="py-3 px-3 align-top">
            <div class="flex flex-col gap-1.5 input-field-container">
                <select name="items[INDEX][kategori]" class="input-kategori w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                    <option value="material cat" selected>Material Cat</option>
                    <option value="material bangunan">Material Bangunan</option>
                    <option value="upah">Upah</option>
                </select>
                <span class="cat-badge text-[10px] font-bold text-blue-700 bg-blue-50/90 border border-blue-200/60 px-2 py-0.5 rounded-md inline-flex items-center gap-1 w-fit">
                    🎨 List Cat Aktif
                </span>
            </div>
        </td>

        {{-- 3. Item / Deskripsi --}}
        <td class="py-3 px-3 align-top">
            <div class="flex flex-col gap-1.5 input-field-container">
                <input type="text"
                       name="items[INDEX][material]"
                       list="registered-paint-products"
                       autocomplete="off"
                       placeholder="Ketik atau pilih nama cat terdaftar..."
                       class="input-material w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">

                <input type="text"
                       name="items[INDEX][detail]"
                       placeholder="Spesifikasi / catatan / merk (opsional)"
                       class="input-detail w-full bg-slate-50/70 hover:bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] font-medium text-slate-600 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
            </div>
        </td>

        {{-- 4. Ukuran Cat --}}
        <td class="py-3 px-3 align-top">
            <div class="ukuran-field-wrapper input-field-container">
                <select data-name="items[INDEX][ukuran]"
                        class="input-ukuran-select w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer hidden">
                    <option value="">-- Ukuran --</option>
                </select>

                <div class="ukuran-manual-box flex items-center gap-1">
                    <input type="text"
                           data-name="items[INDEX][ukuran]"
                           name="items[INDEX][ukuran]"
                           placeholder="cth: 2.5 L"
                           class="input-ukuran-manual w-full bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                    <button type="button" 
                            class="btn-switch-to-select hidden p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg text-xs font-bold transition flex-shrink-0" 
                            title="Pilih kembali dari opsi ukuran terdaftar">
                        ▼
                    </button>
                </div>
                <span class="ukuran-notif-label text-[10px] text-slate-400 mt-0.5 block font-medium leading-tight"></span>
            </div>
        </td>

        {{-- 5. Harga Satuan --}}
        <td class="py-3 px-3 align-top">
            <div class="input-field-container">
                <div class="input-harga-wrapper flex items-center bg-slate-50 hover:bg-white border border-slate-200 rounded-xl px-2.5 py-2 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition-all">
                    <span class="text-slate-400 text-[10px] font-extrabold mr-1">Rp</span>
                    <input type="number" name="items[INDEX][harga]" placeholder="0" class="input-harga w-full border-none focus:ring-0 p-0 text-xs text-right font-bold text-slate-800 bg-transparent outline-none">
                </div>
            </div>
        </td>

        {{-- 6. Qty --}}
        <td class="py-3 px-3 align-top">
            <div class="input-field-container">
                <input type="number" name="items[INDEX][qty]" value="1" step="0.01" class="input-qty w-full border border-slate-200 bg-slate-50 hover:bg-white rounded-xl py-2 px-1 text-xs text-center font-bold text-slate-800 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
            </div>
        </td>

        {{-- 7. Subtotal --}}
        <td class="py-3 px-3 text-right align-top pt-4">
            <span class="input-total-text text-xs font-black text-slate-900 block tracking-tight">Rp 0</span>
        </td>

        {{-- 8. Aksi (Hapus) --}}
        <td class="py-3 px-2 text-center align-top pt-3">
            <button type="button" class="remove-row-btn p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Baris">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recapForm = document.getElementById('recap-form');
    const rowsContainer = document.getElementById('recap-rows');
    const addRowBtn = document.getElementById('add-row-btn');
    const template = document.getElementById('row-template').innerHTML;
    const grandTotalOffer = parseFloat("{{ $recap->total_penawaran_klien }}") || 0;
    const rowCountBadge = document.getElementById('row-count-badge');

    // Mapping data produk dan varian ukuran dari database
    const productsDbMap = {
        @foreach ($products->groupBy(fn($p) => strtolower(trim($p->nama_produk))) as $key => $variants)
            @php
                $first = $variants->first();
                $sizes = $variants->filter(fn($v) => !empty($v->ukuran))->map(function($v) {
                    return [
                        'ukuran' => $v->ukuran,
                        'harga' => (int) $v->harga,
                        'stok' => (int) ($v->stok ?? 0),
                        'performa' => $v->performa ?? '',
                        'hasil_akhir' => $v->hasil_akhir ?? '',
                    ];
                })->values();
            @endphp
            "{{ addslashes($key) }}": {
                nama_produk: "{{ addslashes($first->nama_produk) }}",
                performa: "{{ addslashes($first->performa ?? '') }}",
                hasil_akhir: "{{ addslashes($first->hasil_akhir ?? '') }}",
                harga: {{ (int) $first->harga }},
                variants: @json($sizes),
                ukuran: @json($sizes->pluck('ukuran'))
            },
        @endforeach
    };

    let rowIndex = {{ $recap->items->count() }} + 100;
    let isFormDirty = false;
    let isSubmitting = false;

    // --- Notifikasi Toast Kustom ---
    function showValidationToast(message) {
        let toast = document.getElementById('validation-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'validation-toast';
            toast.className = 'fixed top-5 right-5 z-[9999] flex items-center gap-3 bg-red-600 text-white px-5 py-3.5 rounded-2xl shadow-2xl transition-all duration-300 transform translate-y-0 text-sm font-semibold max-w-md border border-red-500';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `
            <div class="flex items-center justify-center w-8 h-8 bg-red-500 rounded-full flex-shrink-0 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="flex-1 text-xs leading-snug">${message}</div>
            <button type="button" class="text-white hover:text-red-200 text-lg font-bold ml-2 leading-none" onclick="this.parentElement.remove()">&times;</button>
        `;
        toast.classList.remove('opacity-0', '-translate-y-4');
        toast.classList.add('opacity-100', 'translate-y-0');

        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            if (toast && toast.parentElement) {
                toast.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => toast.remove(), 300);
            }
        }, 4500);
    }

    function clearAllErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));
        document.querySelectorAll('.field-error-text').forEach(el => el.remove());
    }

    function markFieldError(err) {
        const el = err.element;
        if (!el) return;

        const targetBorder = el.classList.contains('input-harga') ? (el.closest('.input-harga-wrapper') || el) : el;
        targetBorder.classList.add('field-error');

        const container = el.closest('.input-field-container') || el.parentElement;
        if (container && !container.querySelector('.field-error-text')) {
            const msgEl = document.createElement('p');
            msgEl.className = 'field-error-text text-[11px] text-red-500 font-semibold mt-1';
            msgEl.textContent = err.message;
            container.appendChild(msgEl);
        }

        const clearFn = function() {
            targetBorder.classList.remove('field-error');
            if (container) {
                const msgEl = container.querySelector('.field-error-text');
                if (msgEl) msgEl.remove();
            }
            el.removeEventListener('input', clearFn);
            el.removeEventListener('change', clearFn);
        };

        el.addEventListener('input', clearFn);
        el.addEventListener('change', clearFn);
    }

    function formatRupiah(angka) {
        const val = Math.floor(Math.abs(Number(angka) || 0));
        return val.toLocaleString('id-ID');
    }

    // --- Pengaturan Tampilan Kategori ---
    function updateCategoryState(row) {
        const katSelect = row.querySelector('.input-kategori');
        const matInput = row.querySelector('.input-material');
        const badge = row.querySelector('.cat-badge');
        if (!katSelect || !matInput) return;

        const val = katSelect.value.toLowerCase().trim();

        if (val === 'material cat') {
            matInput.setAttribute('list', 'registered-paint-products');
            matInput.placeholder = 'Ketik atau pilih nama cat terdaftar...';
            if (badge) {
                badge.className = 'cat-badge text-[10px] font-bold text-blue-700 bg-blue-50/90 border border-blue-200/60 px-2 py-0.5 rounded-md inline-flex items-center gap-1 w-fit';
                badge.innerHTML = '🎨 List Cat Aktif';
                badge.style.display = 'inline-flex';
            }
        } else if (val === 'material bangunan') {
            matInput.removeAttribute('list');
            matInput.placeholder = 'Nama material / bahan bangunan...';
            if (badge) {
                badge.className = 'cat-badge text-[10px] font-bold text-amber-700 bg-amber-50/90 border border-amber-200/60 px-2 py-0.5 rounded-md inline-flex items-center gap-1 w-fit';
                badge.innerHTML = '🧱 Material Bangunan';
                badge.style.display = 'inline-flex';
            }
        } else if (val === 'upah') {
            matInput.removeAttribute('list');
            matInput.placeholder = 'Nama pekerjaan / upah tukang...';
            if (badge) {
                badge.className = 'cat-badge text-[10px] font-bold text-emerald-700 bg-emerald-50/90 border border-emerald-200/60 px-2 py-0.5 rounded-md inline-flex items-center gap-1 w-fit';
                badge.innerHTML = '👷 Upah Tenaga';
                badge.style.display = 'inline-flex';
            }
        } else {
            matInput.removeAttribute('list');
            matInput.placeholder = 'Nama item / pengeluaran...';
            if (badge) badge.style.display = 'none';
        }
    }

    // --- Sinkronisasi Kolom Ukuran Cat & Auto-Fill Produk ---
    function syncUkuranAndProduct(row, initialUkuran = null) {
        const katSelect = row.querySelector('.input-kategori');
        const matInput = row.querySelector('.input-material');
        const detailInput = row.querySelector('.input-detail');
        const hargaInput = row.querySelector('.input-harga');
        const ukuranSelect = row.querySelector('.input-ukuran-select');
        const ukuranInput = row.querySelector('.input-ukuran-manual');
        const manualBox = row.querySelector('.ukuran-manual-box');
        const switchBtn = row.querySelector('.btn-switch-to-select');
        const notifLabel = row.querySelector('.ukuran-notif-label');

        if (!katSelect || !matInput || !ukuranSelect || !ukuranInput) return;

        const katVal = katSelect.value.toLowerCase().trim();
        const nameAttr = ukuranSelect.dataset.name || ukuranInput.dataset.name || 'items[0][ukuran]';

        if (katVal !== 'material cat') {
            ukuranSelect.classList.add('hidden');
            ukuranSelect.removeAttribute('name');
            manualBox.classList.remove('hidden');
            switchBtn.classList.add('hidden');
            ukuranInput.name = nameAttr;
            ukuranInput.disabled = true;
            ukuranInput.value = '';
            ukuranInput.placeholder = '— (Bukan Cat)';
            if (notifLabel) {
                notifLabel.textContent = '';
                notifLabel.className = 'ukuran-notif-label text-[10px] text-slate-400 mt-0.5 block font-medium leading-tight';
            }
            return;
        }

        // Kategori = Material Cat
        ukuranInput.disabled = false;
        const matVal = matInput.value.trim().toLowerCase();
        const prodData = productsDbMap[matVal];
        const hasSizes = prodData && Array.isArray(prodData.variants) && prodData.variants.length > 0;

        if (hasSizes) {
            ukuranSelect.innerHTML = '<option value="">-- Pilih Ukuran --</option>';
            prodData.variants.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.ukuran;
                opt.textContent = `${v.ukuran} (${v.harga ? 'Rp ' + formatRupiah(v.harga) : 'Tanpa Harga'})`;
                opt.dataset.harga = v.harga;
                opt.dataset.performa = v.performa || '';
                opt.dataset.hasil = v.hasil_akhir || '';
                ukuranSelect.appendChild(opt);
            });

            const optManual = document.createElement('option');
            optManual.value = '__manual__';
            optManual.textContent = '✏️ Isi Manual...';
            ukuranSelect.appendChild(optManual);

            const curVal = initialUkuran !== null ? initialUkuran : (ukuranSelect.value && ukuranSelect.value !== '__manual__' ? ukuranSelect.value : ukuranInput.value.trim());

            if (curVal && prodData.ukuran.includes(curVal)) {
                ukuranSelect.value = curVal;
                ukuranSelect.classList.remove('hidden');
                manualBox.classList.add('hidden');
                ukuranSelect.name = nameAttr;
                ukuranInput.removeAttribute('name');
                if (notifLabel) {
                    notifLabel.textContent = '✓ Ukuran produk terdaftar';
                    notifLabel.className = 'ukuran-notif-label text-[10px] text-blue-600 mt-0.5 block font-medium leading-tight';
                }
            } else if (curVal && curVal !== '') {
                ukuranSelect.value = '__manual__';
                ukuranSelect.classList.add('hidden');
                manualBox.classList.remove('hidden');
                switchBtn.classList.remove('hidden');
                ukuranInput.value = curVal;
                ukuranInput.name = nameAttr;
                ukuranSelect.removeAttribute('name');
                if (notifLabel) {
                    notifLabel.textContent = '✏️ Ukuran manual / custom';
                    notifLabel.className = 'ukuran-notif-label text-[10px] text-amber-600 mt-0.5 block font-medium leading-tight';
                }
            } else {
                ukuranSelect.selectedIndex = 1;
                ukuranSelect.classList.remove('hidden');
                manualBox.classList.add('hidden');
                ukuranSelect.name = nameAttr;
                ukuranInput.value = ukuranSelect.value;
                ukuranInput.removeAttribute('name');
                if (notifLabel) {
                    notifLabel.textContent = '✓ Ukuran produk terdaftar';
                    notifLabel.className = 'ukuran-notif-label text-[10px] text-blue-600 mt-0.5 block font-medium leading-tight';
                }

                const optFirst = ukuranSelect.options[1];
                if (optFirst && optFirst.dataset.harga && (!hargaInput.value || parseFloat(hargaInput.value) === 0)) {
                    hargaInput.value = Math.floor(optFirst.dataset.harga);
                    calculateTotals();
                }
            }

            // Isi info tambahan spesifikasi jika masih kosong
            if (detailInput && !detailInput.value.trim()) {
                const parts = [];
                if (prodData.performa) parts.push('Brand: ' + prodData.performa);
                if (prodData.hasil_akhir) parts.push('Hasil: ' + prodData.hasil_akhir);
                if (parts.length > 0) detailInput.value = parts.join(' | ');
            }
        } else {
            // Produk belum terdaftar atau tidak memiliki varian ukuran di database
            ukuranSelect.classList.add('hidden');
            ukuranSelect.removeAttribute('name');
            manualBox.classList.remove('hidden');
            switchBtn.classList.add('hidden');
            ukuranInput.name = nameAttr;
            ukuranInput.placeholder = 'cth: 2.5 L / 20 kg';
            if (initialUkuran !== null) {
                ukuranInput.value = initialUkuran;
            }
            if (notifLabel) {
                notifLabel.textContent = matVal ? '✏️ Produk kustom (bisa isi ukuran manual)' : 'Isi ukuran kemasan cat';
                notifLabel.className = 'ukuran-notif-label text-[10px] text-slate-400 mt-0.5 block font-medium leading-tight';
            }
        }
    }

    // --- Kalkulasi Total & Margin Real-Time ---
    function calculateTotals() {
        let totalRecap = 0;
        const rows = document.querySelectorAll('.recap-row');

        rows.forEach(row => {
            const harga = parseFloat(row.querySelector('.input-harga')?.value) || 0;
            const qty = parseFloat(row.querySelector('.input-qty')?.value) || 0;
            const subtotal = Math.max(0, harga * qty);

            const textEl = row.querySelector('.input-total-text');
            if (textEl) textEl.textContent = 'Rp ' + formatRupiah(subtotal);
            totalRecap += subtotal;
        });

        const safeTotalRecap = Math.round(totalRecap);
        const safeMargin = Math.round(grandTotalOffer - safeTotalRecap);

        const totalPengeluaranEl = document.getElementById('display-total-pengeluaran');
        if (totalPengeluaranEl) totalPengeluaranEl.textContent = 'Rp ' + formatRupiah(safeTotalRecap);

        const hiddenTotalPengeluaran = document.getElementById('hidden-total-pengeluaran');
        if (hiddenTotalPengeluaran) hiddenTotalPengeluaran.value = safeTotalRecap;

        const displayMargin = document.getElementById('display-margin');
        if (displayMargin) {
            displayMargin.textContent = (safeMargin < 0 ? '- ' : '') + formatRupiah(safeMargin);
            displayMargin.className = safeMargin < 0
                ? 'text-3xl font-black tracking-tight text-red-600'
                : 'text-3xl font-black tracking-tight text-slate-900';
        }

        const marginCard = document.getElementById('margin-card');
        const statusEl = document.getElementById('margin-status');
        const percentageEl = document.getElementById('margin-percentage');

        // Hitung persentase margin terhadap total penawaran
        const pct = grandTotalOffer > 0 ? ((safeMargin / grandTotalOffer) * 100).toFixed(1) : (safeTotalRecap > 0 ? -100 : 0);
        if (percentageEl) {
            percentageEl.textContent = (pct >= 0 ? '+' : '') + pct + '%';
            percentageEl.className = safeMargin < 0
                ? 'text-[10px] font-extrabold text-red-700 bg-red-100/80 px-1.5 py-0.5 rounded'
                : 'text-[10px] font-extrabold text-emerald-700 bg-emerald-100/80 px-1.5 py-0.5 rounded';
        }

        if (safeMargin < 0) {
            if (statusEl) {
                statusEl.textContent = 'Defisit';
                statusEl.className = 'px-2 py-0.5 rounded text-[9px] font-black uppercase bg-red-100 text-red-700';
            }
            if (marginCard) {
                marginCard.className = 'p-5 rounded-2xl bg-red-50 border border-red-200 transition-all duration-500';
            }
        } else {
            if (statusEl) {
                statusEl.textContent = 'Surplus';
                statusEl.className = 'px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-100 text-emerald-700';
            }
            if (marginCard) {
                marginCard.className = 'p-5 rounded-2xl bg-emerald-50 border border-emerald-100 transition-all duration-500';
            }
        }

        if (rowCountBadge) {
            rowCountBadge.textContent = rows.length + ' baris';
        }
    }

    // --- Inisialisasi Event Baris ---
    function initRowEvents(row) {
        const removeBtn = row.querySelector('.remove-row-btn');
        const katSelect = row.querySelector('.input-kategori');
        const matInput = row.querySelector('.input-material');
        const ukuranSelect = row.querySelector('.input-ukuran-select');
        const ukuranInput = row.querySelector('.input-ukuran-manual');
        const manualBox = row.querySelector('.ukuran-manual-box');
        const switchBtn = row.querySelector('.btn-switch-to-select');
        const hargaInput = row.querySelector('.input-harga');
        const notifLabel = row.querySelector('.ukuran-notif-label');
        const nameAttr = ukuranSelect?.dataset.name || ukuranInput?.dataset.name;

        // Hapus baris event
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                isFormDirty = true;
                row.remove();
                calculateTotals();
            });
        }

        // Event change kategori
        if (katSelect) {
            katSelect.addEventListener('change', () => {
                isFormDirty = true;
                updateCategoryState(row);
                syncUkuranAndProduct(row);
                calculateTotals();
            });
        }

        // Event input material (autocomplete & ukuran matching)
        if (matInput) {
            matInput.addEventListener('input', () => {
                isFormDirty = true;
                syncUkuranAndProduct(row);
                calculateTotals();
            });
            matInput.addEventListener('change', () => {
                isFormDirty = true;
                syncUkuranAndProduct(row);
                calculateTotals();
            });
        }

        // Event change ukuran dropdown
        if (ukuranSelect) {
            ukuranSelect.addEventListener('change', function() {
                isFormDirty = true;
                if (this.value === '__manual__') {
                    ukuranSelect.classList.add('hidden');
                    ukuranSelect.removeAttribute('name');
                    manualBox.classList.remove('hidden');
                    switchBtn.classList.remove('hidden');
                    ukuranInput.name = nameAttr;
                    ukuranInput.focus();
                    if (notifLabel) {
                        notifLabel.textContent = '✏️ Ukuran manual / custom';
                        notifLabel.className = 'ukuran-notif-label text-[10px] text-amber-600 mt-0.5 block font-medium leading-tight';
                    }
                } else {
                    ukuranSelect.name = nameAttr;
                    ukuranInput.removeAttribute('name');
                    ukuranInput.value = this.value;
                    const opt = this.options[this.selectedIndex];
                    if (opt && opt.dataset.harga && parseFloat(opt.dataset.harga) > 0) {
                        hargaInput.value = Math.floor(opt.dataset.harga);
                    }
                    if (notifLabel) {
                        notifLabel.textContent = '✓ Ukuran produk terdaftar';
                        notifLabel.className = 'ukuran-notif-label text-[10px] text-blue-600 mt-0.5 block font-medium leading-tight';
                    }
                    calculateTotals();
                }
            });
        }

        // Event tombol beralih kembali ke pilihan ukuran dropdown
        if (switchBtn) {
            switchBtn.addEventListener('click', () => {
                isFormDirty = true;
                ukuranSelect.classList.remove('hidden');
                manualBox.classList.add('hidden');
                ukuranSelect.name = nameAttr;
                ukuranInput.removeAttribute('name');
                if (ukuranSelect.selectedIndex <= 0 || ukuranSelect.value === '__manual__') {
                    ukuranSelect.selectedIndex = 1;
                }
                ukuranSelect.dispatchEvent(new Event('change'));
            });
        }

        // Kalkulasi trigger semua input
        row.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                isFormDirty = true;
                calculateTotals();
            });
            input.addEventListener('change', () => {
                isFormDirty = true;
                calculateTotals();
            });
        });
    }

    // Inisialisasi baris yang sudah ada dari database
    document.querySelectorAll('.recap-row').forEach(row => {
        updateCategoryState(row);
        initRowEvents(row);
        const initUkuran = row.dataset.initialUkuran || row.querySelector('.input-ukuran-manual')?.value || null;
        syncUkuranAndProduct(row, initUkuran);
    });

    // --- Tambah Baris Baru ---
    function addNewRow() {
        const html = template.replace(/INDEX/g, rowIndex);
        rowsContainer.insertAdjacentHTML('beforeend', html);
        const newRow = rowsContainer.lastElementChild;

        updateCategoryState(newRow);
        initRowEvents(newRow);
        syncUkuranAndProduct(newRow);

        rowIndex++;
        calculateTotals();
    }

    if (addRowBtn) {
        addRowBtn.addEventListener('click', (e) => {
            e.preventDefault();
            isFormDirty = true;
            addNewRow();
        });
    }

    calculateTotals();

    // --- Validasi Form Rekapan Proyek ---
    function validateRecapForm() {
        clearAllErrors();
        const errors = [];
        const rows = document.querySelectorAll('.recap-row');

        if (rows.length === 0) {
            errors.push({
                element: addRowBtn,
                scrollTarget: addRowBtn,
                message: 'Silakan tambahkan minimal 1 baris pengeluaran rekapan.'
            });
            return errors;
        }

        let anyRowFilled = false;
        rows.forEach(row => {
            const material = row.querySelector('.input-material');
            const harga = row.querySelector('.input-harga');
            const matVal = material ? material.value.trim() : '';
            const hrgVal = harga ? harga.value.trim() : '';
            if (matVal !== '' || (hrgVal !== '' && parseFloat(hrgVal) > 0)) {
                anyRowFilled = true;
            }
        });

        if (!anyRowFilled) {
            const firstRow = rows[0];
            const material = firstRow.querySelector('.input-material');
            const harga = firstRow.querySelector('.input-harga');
            const qty = firstRow.querySelector('.input-qty');

            errors.push({
                element: material,
                scrollTarget: firstRow,
                message: 'Nama item / pengeluaran wajib diisi.'
            });

            if (!harga.value || parseFloat(harga.value) <= 0) {
                errors.push({
                    element: harga,
                    scrollTarget: firstRow,
                    message: 'Harga satuan wajib diisi.'
                });
            }

            if (!qty.value || parseFloat(qty.value) <= 0) {
                errors.push({
                    element: qty,
                    scrollTarget: firstRow,
                    message: 'Qty minimal 1.'
                });
            }

            return errors;
        }

        rows.forEach((row, index) => {
            const material = row.querySelector('.input-material');
            const harga = row.querySelector('.input-harga');
            const qty = row.querySelector('.input-qty');

            const matVal = material ? material.value.trim() : '';
            const hrgVal = harga ? harga.value.trim() : '';
            const qtyVal = qty ? qty.value.trim() : '';

            const isRowEmpty = matVal === '' && (hrgVal === '' || parseFloat(hrgVal) === 0);

            if (!isRowEmpty) {
                if (!matVal) {
                    errors.push({
                        element: material,
                        scrollTarget: row,
                        message: `Nama item baris #${index + 1} wajib diisi.`
                    });
                }

                if (!hrgVal || isNaN(parseFloat(hrgVal)) || parseFloat(hrgVal) <= 0) {
                    errors.push({
                        element: harga,
                        scrollTarget: row,
                        message: `Harga satuan baris #${index + 1} wajib diisi (minimal Rp 1).`
                    });
                }

                if (!qtyVal || isNaN(parseFloat(qtyVal)) || parseFloat(qtyVal) <= 0) {
                    errors.push({
                        element: qty,
                        scrollTarget: row,
                        message: `Qty baris #${index + 1} minimal 1.`
                    });
                }
            }
        });

        return errors;
    }

    // --- Submit Handler dengan Auto-Scroll & Shake ---
    if (recapForm) {
        recapForm.addEventListener('input', () => { isFormDirty = true; });
        recapForm.addEventListener('change', () => { isFormDirty = true; });

        recapForm.addEventListener('submit', function(e) {
            const errors = validateRecapForm();

            if (errors.length > 0) {
                e.preventDefault();
                isSubmitting = false;

                errors.forEach(err => markFieldError(err));
                showValidationToast('Ada form yang belum diisi! Halaman secara otomatis diarahkan ke bagian yang belum lengkap.');

                const firstErr = errors[0];
                const scrollTarget = firstErr.scrollTarget || firstErr.element;

                if (scrollTarget) {
                    scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    scrollTarget.classList.add('field-error-shake');
                    setTimeout(() => scrollTarget.classList.remove('field-error-shake'), 600);
                }

                setTimeout(() => {
                    if (firstErr.element && typeof firstErr.element.focus === 'function') {
                        firstErr.element.focus();
                    }
                }, 350);

                return false;
            }

            const rows = document.querySelectorAll('.recap-row');
            rows.forEach(row => {
                const matVal = row.querySelector('.input-material')?.value.trim() || '';
                const hrgVal = row.querySelector('.input-harga')?.value.trim() || '';
                if (matVal === '' && (hrgVal === '' || parseFloat(hrgVal) === 0)) {
                    row.remove();
                }
            });
            calculateTotals();

            isSubmitting = true;
            const submitBtn = recapForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memperbarui Analisis Rekapan...
                `;
            }
        });
    }

    // --- Proteksi Beforeunload & Konfirmasi Navigasi ---
    window.addEventListener('beforeunload', function(e) {
        if (isFormDirty && !isSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    document.querySelectorAll('.btn-cancel-nav, a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (isFormDirty && !isSubmitting) {
                if (!confirm('Anda memiliki perubahan yang belum disimpan. Yakin ingin keluar tanpa menyimpan rekapan?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
});
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }

    @keyframes fieldShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }
    .field-error {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25) !important;
    }
    .field-error-shake {
        animation: fieldShake 0.4s ease-in-out;
    }
</style>
@endsection
