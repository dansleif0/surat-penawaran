@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] ml-0 md:ml-64 p-4 lg:p-8 transition-all duration-300">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    <a href="{{ route('recap.index') }}" class="hover:text-indigo-600 transition">Histori Rekapan</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-slate-600">Edit Analisis Margin</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Analisis Margin</h1>
                <p class="text-slate-500 mt-1">Proyek: <span class="font-bold text-indigo-600">{{ $recap->offer->nama_klien }}</span></p>
            </div>

            <a href="{{ route('recap.index') }}" class="btn-cancel-nav inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-2xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Batal & Kembali
            </a>
        </div>

        <form action="{{ route('recap.update', $recap->id) }}" method="POST" id="recap-form" novalidate>
            @csrf
            @method('PUT')

            <input type="hidden" name="offer_id" value="{{ $recap->offer_id }}">
            <input type="hidden" name="total_penawaran_klien" value="{{ $recap->total_penawaran_klien }}">
            <input type="hidden" name="total_pengeluaran" id="hidden-total-pengeluaran" value="{{ $recap->total_pengeluaran }}">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                {{-- Left: Main Table --}}
                <div class="xl:col-span-8 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                DAFTAR PENGELUARAN
                            </h3>
                            <button type="button" id="add-row-btn" class="text-xs font-bold px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-200 flex items-center gap-2 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                Tambah Baris
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50/80 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                                    <tr>
                                        <th class="py-4 px-4 w-40">Tgl Keluar</th>
                                        <th class="py-4 px-4">Item / Deskripsi</th>
                                        <th class="py-4 px-4 w-44">Kategori</th>
                                        <th class="py-4 px-4 w-36 text-right">Harga Satuan</th>
                                        <th class="py-4 px-4 w-20 text-center">Qty</th>
                                        <th class="py-4 px-4 w-36 text-right">Subtotal</th>
                                        <th class="py-4 px-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="recap-rows" class="divide-y divide-slate-100">
                                    @foreach($recap->items as $index => $item)
                                    @php
                                        $catVal = strtolower(trim($item->kategori ?? ''));
                                    @endphp
                                    <tr class="recap-row group hover:bg-slate-50/60 transition-colors">
                                        <td class="py-4 px-4 align-top">
                                            <div class="input-field-container">
                                                <input type="date" name="items[{{ $index }}][tanggal_item]" value="{{ $item->tanggal_item ? $item->tanggal_item->format('Y-m-d') : '' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 align-top">
                                            <div class="flex flex-col gap-1.5 input-field-container">
                                                <input type="text" name="items[{{ $index }}][material]" value="{{ $item->material }}" placeholder="Nama item / pengeluaran..." class="input-material w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                                <input type="text" name="items[{{ $index }}][detail]" value="{{ $item->detail }}" placeholder="Spesifikasi / catatan (opsional)" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] font-medium text-slate-600 placeholder-slate-300 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 align-top">
                                            <div class="input-field-container">
                                                <select name="items[{{ $index }}][kategori]" class="input-kategori w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                                                    <option value="material bangunan" {{ $catVal === 'material bangunan' ? 'selected' : '' }}>Material Bangunan</option>
                                                    <option value="material cat" {{ $catVal === 'material cat' ? 'selected' : '' }}>Material Cat</option>
                                                    <option value="upah" {{ $catVal === 'upah' ? 'selected' : '' }}>Upah</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 align-top">
                                            <div class="input-field-container">
                                                <div class="input-harga-wrapper flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 group-hover:bg-white focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition-all">
                                                    <span class="text-slate-400 text-[10px] font-bold mr-1.5">RP</span>
                                                    <input type="number" name="items[{{ $index }}][harga]" value="{{ (int)$item->harga }}" placeholder="0" class="input-harga w-full border-none focus:ring-0 p-0 text-xs text-right font-bold text-slate-800 bg-transparent outline-none">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 align-top">
                                            <div class="input-field-container">
                                                <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" step="0.01" class="input-qty w-full border border-slate-200 bg-slate-50 group-hover:bg-white rounded-xl py-2 px-2 text-xs text-center font-bold text-slate-800 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right align-top pt-6">
                                            <span class="input-total-text text-xs font-black text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-4 px-3 text-center align-top pt-5">
                                            <button type="button" class="remove-row-btn p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Baris">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right: Summary Sidebar --}}
                <div class="xl:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        {{-- Calculation Card --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6">Ringkasan Finansial</h4>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-xs text-slate-500 font-semibold">Total Penawaran</span>
                                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <span class="text-xs text-slate-500 font-semibold">Total Pengeluaran (Modal)</span>
                                    <span id="display-total-pengeluaran" class="text-sm font-bold text-red-600">Rp 0</span>
                                </div>

                                <div id="margin-card" class="p-5 rounded-2xl bg-slate-50 transition-all duration-500">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Profit</span>
                                        <span id="margin-status" class="px-2 py-0.5 rounded text-[9px] font-black uppercase"></span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-slate-400">Rp</span>
                                        <span id="display-margin" class="text-3xl font-black tracking-tight text-slate-900">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-100">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-black font-extrabold text-base py-4 px-6 rounded-2xl shadow-xl shadow-emerald-600/30 hover:shadow-emerald-600/50 active:scale-[0.98] transition-all flex items-center justify-center gap-3 cursor-pointer border-2 border-emerald-500">
                                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="tracking-wide">PERBARUI REKAPAN BIAYA</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Template Baris Baru --}}
<template id="row-template">
    <tr class="recap-row group hover:bg-slate-50/60 transition-colors">
        <td class="py-4 px-4 align-top">
            <div class="input-field-container">
                <input type="date" name="items[INDEX][tanggal_item]" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
            </div>
        </td>
        <td class="py-4 px-4 align-top">
            <div class="flex flex-col gap-1.5 input-field-container">
                <input type="text" name="items[INDEX][material]" placeholder="Nama item / pengeluaran..." class="input-material w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 placeholder-slate-400 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
                <input type="text" name="items[INDEX][detail]" placeholder="Spesifikasi / catatan (opsional)" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3 py-1.5 text-[11px] font-medium text-slate-600 placeholder-slate-300 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
            </div>
        </td>
        <td class="py-4 px-4 align-top">
            <div class="input-field-container">
                <select name="items[INDEX][kategori]" class="input-kategori w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all cursor-pointer">
                    <option value="material bangunan">Material Bangunan</option>
                    <option value="material cat">Material Cat</option>
                    <option value="upah">Upah</option>
                </select>
            </div>
        </td>
        <td class="py-4 px-4 align-top">
            <div class="input-field-container">
                <div class="input-harga-wrapper flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 group-hover:bg-white focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-500 transition-all">
                    <span class="text-slate-400 text-[10px] font-bold mr-1.5">RP</span>
                    <input type="number" name="items[INDEX][harga]" placeholder="0" class="input-harga w-full border-none focus:ring-0 p-0 text-xs text-right font-bold text-slate-800 bg-transparent outline-none">
                </div>
            </div>
        </td>
        <td class="py-4 px-4 align-top">
            <div class="input-field-container">
                <input type="number" name="items[INDEX][qty]" value="1" step="0.01" class="input-qty w-full border border-slate-200 bg-slate-50 group-hover:bg-white rounded-xl py-2 px-2 text-xs text-center font-bold text-slate-800 outline-none focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all">
            </div>
        </td>
        <td class="py-4 px-4 text-right align-top pt-6">
            <span class="input-total-text text-xs font-black text-slate-900">Rp 0</span>
        </td>
        <td class="py-4 px-3 text-center align-top pt-5">
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

    function formatRupiah(angka) { return Math.floor(angka).toLocaleString('id-ID'); }

    function calculateTotals() {
        let totalRecap = 0;
        document.querySelectorAll('.recap-row').forEach(row => {
            const harga = parseFloat(row.querySelector('.input-harga')?.value) || 0;
            const qty = parseFloat(row.querySelector('.input-qty')?.value) || 0;
            const subtotal = harga * qty;
            const textEl = row.querySelector('.input-total-text');
            if (textEl) textEl.textContent = 'Rp ' + formatRupiah(subtotal);
            totalRecap += subtotal;
        });
        const margin = grandTotalOffer - totalRecap;
        document.getElementById('display-total-pengeluaran').textContent = 'Rp ' + formatRupiah(totalRecap);
        document.getElementById('hidden-total-pengeluaran').value = totalRecap;
        document.getElementById('display-margin').textContent = formatRupiah(margin);

        const card = document.getElementById('margin-card');
        const status = document.getElementById('margin-status');
        if (margin < 0) {
            status.textContent = 'DEFISIT'; status.className = 'inline-block mt-3 text-[8px] font-black uppercase py-1 px-2.5 rounded-lg bg-red-100 text-red-600';
            card.className = 'p-5 rounded-2xl bg-red-50 border border-red-100 transition-all duration-500';
        } else {
            status.textContent = 'SURPLUS'; status.className = 'inline-block mt-3 text-[8px] font-black uppercase py-1 px-2.5 rounded-lg bg-emerald-100 text-emerald-600';
            card.className = 'p-5 rounded-2xl bg-emerald-50 border border-emerald-100 transition-all duration-500';
        }
    }

    function bindRowEvents(row) {
        row.querySelector('.remove-row-btn').addEventListener('click', () => {
            isFormDirty = true;
            row.remove();
            calculateTotals();
        });
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

    document.querySelectorAll('.recap-row').forEach(row => bindRowEvents(row));

    addRowBtn.addEventListener('click', () => {
        isFormDirty = true;
        const html = template.replace(/INDEX/g, rowIndex++);
        rowsContainer.insertAdjacentHTML('beforeend', html);
        bindRowEvents(rowsContainer.lastElementChild);
        calculateTotals();
    });

    calculateTotals();

    // --- Validasi Rekapan Proyek ---
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

            // Bersihkan baris yang 100% kosong
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
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan Perubahan Rekapan...
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
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
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