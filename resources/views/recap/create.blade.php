@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F1F5F9] ml-0 md:ml-64 p-4 lg:p-8 transition-all duration-300">
    <div class="max-w-7xl mx-auto">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                    <a href="{{ route('histori.index') }}" class="hover:text-indigo-600 transition">Histori</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-slate-600">Analisis Margin</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Analisis Margin Proyek</h1>
                <p class="text-slate-500 mt-1">Klien: <span class="font-semibold text-slate-700">{{ $offer->nama_klien }}</span></p>
            </div>

            <a href="{{ route('histori.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('recap.store') }}" method="POST" id="recap-form">
            @csrf
            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            <input type="hidden" name="total_penawaran_klien" value="{{ $grandTotalOffer }}">
            <input type="hidden" name="total_pengeluaran" id="hidden-total-pengeluaran" value="0">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

                {{-- Left: Main Input --}}
                <div class="xl:col-span-8 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white/50 backdrop-blur-md">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                Rincian Pengeluaran
                            </h3>
                            <button type="button" id="add-row-btn" class="text-xs font-bold px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                Tambah Baris
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                                    <tr>
                                        <th class="py-4 px-6">Item & Detail</th>
                                        <th class="py-4 px-4 w-44 text-right">Harga Satuan</th>
                                        <th class="py-4 px-4 w-24 text-center">Qty</th>
                                        <th class="py-4 px-6 w-44 text-right">Subtotal</th>
                                        <th class="py-4 px-4 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody id="recap-rows" class="divide-y divide-slate-50">
                                    {{-- JS Content --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right: Summary Sidebar --}}
                <div class="xl:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        {{-- Calculation Card --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <h4 class="text-sm font-bold text-slate-800 mb-6">Ringkasan Finansial</h4>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-slate-50">
                                    <span class="text-sm text-slate-500 font-medium">Total Penawaran</span>
                                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($grandTotalOffer, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-4 border-b border-slate-50">
                                    <span class="text-sm text-slate-500 font-medium">Total Pengeluaran</span>
                                    <span id="display-total-pengeluaran" class="text-sm font-bold text-slate-900 text-red-500">Rp 0</span>
                                </div>

                                <div id="margin-card" class="p-5 rounded-2xl bg-slate-50 transition-all duration-500">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Estimasi Laba Bersih</span>
                                        <span id="margin-status" class="px-2 py-0.5 rounded text-[9px] font-black uppercase"></span>
                                    </div>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-slate-400">Rp</span>
                                        <span id="display-margin" class="text-3xl font-black tracking-tight text-slate-900">0</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full mt-6 bg-slate-900 text-black py-4 rounded-2xl font-bold text-sm hover:bg-indigo-600 transition-all hover:bg-indigo-700 shadow-lg shadow-slate-200 active:scale-95">
                                Simpan Analisis Proyek
                            </button>
                        </div>

                        {{-- Info Box --}}
                        <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="p-2 bg-white/20 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="font-bold text-sm italic">Tips Analisis</span>
                            </div>
                            <p class="text-xs text-indigo-100 leading-relaxed">
                                Masukkan semua biaya material, jasa vendor, dan biaya operasional lainnya untuk mendapatkan estimasi margin yang akurat.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Dynamic Row Template --}}
<template id="row-template">
    <tr class="recap-row group hover:bg-slate-50/50 transition-colors">
        <td class="py-5 px-6">
            <div class="flex flex-col gap-1">
                <input type="text" name="items[INDEX][material]" placeholder="Contoh: Pengadaan PC Core i7" class="bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-slate-800 placeholder-slate-300" required>
                <input type="text" name="items[INDEX][detail]" placeholder="Spesifikasi tambahan..." class="bg-transparent border-none focus:ring-0 p-0 text-xs font-medium text-slate-400 placeholder-slate-200">
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Tgl Opsional:</span>
                    <input type="date" name="items[INDEX][tanggal_item]" class="text-[10px] bg-slate-50 border border-slate-100 rounded-md px-2 py-0.5 font-bold text-slate-500 outline-none focus:border-indigo-300">
                </div>
            </div>
        </td>
        <td class="py-5 px-4">
            <div class="flex items-center bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 group-hover:bg-white transition-all focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-200">
                <span class="text-slate-400 text-[10px] font-bold mr-2">RP</span>
                <input type="number" name="items[INDEX][harga]" placeholder="0" class="input-harga w-full border-none focus:ring-0 p-0 text-sm text-right bg-transparent font-bold text-slate-700" required>
            </div>
        </td>
        <td class="py-5 px-4">
            <input type="number" name="items[INDEX][qty]" value="1" step="0.01" class="input-qty w-full border border-slate-100 bg-slate-50 group-hover:bg-white rounded-xl py-2 text-sm text-center font-bold text-slate-700 focus:ring-2 focus:ring-indigo-100 transition-all" required>
        </td>
        <td class="py-5 px-6 text-right">
            <span class="input-total-text text-sm font-bold text-slate-900">Rp 0</span>
        </td>
        <td class="py-5 px-4 text-center">
            <button type="button" class="remove-row-btn p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsContainer = document.getElementById('recap-rows');
    const addRowBtn = document.getElementById('add-row-btn');
    const template = document.getElementById('row-template').innerHTML;
    const grandTotalOffer = parseFloat("{{ $grandTotalOffer }}") || 0;

    let rowIndex = 0;

    function formatRupiah(angka) {
        return Math.floor(angka).toLocaleString('id-ID');
    }

    function calculateTotals() {
        let totalRecap = 0;
        document.querySelectorAll('.recap-row').forEach(row => {
            const harga = parseFloat(row.querySelector('.input-harga').value) || 0;
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const subtotal = harga * qty;

            row.querySelector('.input-total-text').textContent = 'Rp ' + formatRupiah(subtotal);
            totalRecap += subtotal;
        });

        const margin = grandTotalOffer - totalRecap;

        document.getElementById('display-total-pengeluaran').textContent = 'Rp ' + formatRupiah(totalRecap);
        document.getElementById('hidden-total-pengeluaran').value = totalRecap;
        document.getElementById('display-margin').textContent = formatRupiah(margin);

        const marginCard = document.getElementById('margin-card');
        const statusEl = document.getElementById('margin-status');

        if (margin < 0) {
            statusEl.textContent = 'Defisit';
            statusEl.className = 'px-2 py-0.5 rounded text-[9px] font-black uppercase bg-red-100 text-red-600';
            marginCard.className = 'p-5 rounded-2xl bg-red-50 border border-red-100 transition-all duration-500';
        } else {
            statusEl.textContent = 'Surplus';
            statusEl.className = 'px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-100 text-emerald-600';
            marginCard.className = 'p-5 rounded-2xl bg-emerald-50 border border-emerald-100 transition-all duration-500';
        }
    }

    function addNewRow() {
        const html = template.replace(/INDEX/g, rowIndex);
        rowsContainer.insertAdjacentHTML('beforeend', html);
        const newRow = rowsContainer.lastElementChild;

        newRow.querySelector('.remove-row-btn').addEventListener('click', () => {
            newRow.remove();
            calculateTotals();
        });

        newRow.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        rowIndex++;
    }

    // Default 3 rows
    for (let i = 0; i < 3; i++) addNewRow();
    addRowBtn.addEventListener('click', addNewRow);
    calculateTotals();
});
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.01em; }
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
</style>
@endsection