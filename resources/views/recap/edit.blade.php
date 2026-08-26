@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] ml-0 md:ml-64 p-6 lg:p-10 transition-all duration-300">
    <div class="max-w-[1440px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
            <div class="space-y-2">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Analisis Margin</h1>
                <p class="text-slate-500 font-medium text-sm">Proyek: <span class="text-blue-600 font-bold underline">{{ $recap->offer->nama_klien }}</span></p>
            </div>
            <a href="{{ route('recap.index') }}" class="px-5 py-3 rounded-2xl border border-slate-200 bg-white text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</a>
        </div>

        {{-- Gunakan ID form yang benar untuk trigger JS --}}
        <form action="{{ route('recap.update', $recap->id) }}" method="POST" id="recap-form">
            @csrf
            @method('PUT')

            <input type="hidden" name="offer_id" value="{{ $recap->offer_id }}">
            <input type="hidden" name="total_penawaran_klien" value="{{ $recap->total_penawaran_klien }}">
            <input type="hidden" name="total_pengeluaran" id="hidden-total-pengeluaran" value="{{ $recap->total_pengeluaran }}">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
                <div class="xl:col-span-9">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Daftar Pengeluaran</h3>
                            <button type="button" id="add-row-btn" class="text-xs font-bold px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                                Tambah Baris
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50/50 text-slate-400 uppercase text-[9px] font-black tracking-[0.2em] border-b">
                                    <tr>
                                        <th class="py-5 px-8 text-left w-48">Tgl Keluar (Ops)</th>
                                        <th class="py-5 px-4 text-left">Deskripsi Barang</th>
                                        <th class="py-5 px-4 text-center w-40">Harga Satuan</th>
                                        <th class="py-5 px-4 text-center w-20">Qty</th>
                                        <th class="py-5 px-8 text-right w-44">Subtotal</th>
                                        <th class="py-5 px-6 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody id="recap-rows" class="divide-y divide-slate-50">
                                    @foreach($recap->items as $index => $item)
                                    <tr class="recap-row group transition-all duration-300">
                                        <td class="py-6 px-8 text-center">
                                            <input type="date" name="items[{{ $index }}][tanggal_item]" value="{{ $item->tanggal_item ? $item->tanggal_item->format('Y-m-d') : '' }}" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-100">
                                        </td>
                                        <td class="py-6 px-4">
                                            <input type="text" name="items[{{ $index }}][material]" value="{{ $item->material }}" class="w-full border-none focus:ring-0 p-0 text-sm font-bold text-slate-800 bg-transparent" required>
                                            <input type="text" name="items[{{ $index }}][detail]" value="{{ $item->detail }}" placeholder="Spesifikasi" class="w-full border-none focus:ring-0 p-0 text-[10px] text-slate-400 bg-transparent">
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="flex items-center bg-slate-50 rounded-2xl px-4 py-2 border border-slate-100 group-hover:bg-white transition-all">
                                                <input type="number" name="items[{{ $index }}][harga]" value="{{ (int)$item->harga }}" class="input-harga w-full border-none focus:ring-0 p-0 text-sm text-right font-black bg-transparent" required>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" step="0.01" class="input-qty w-full border border-slate-100 bg-slate-50 rounded-xl py-2 text-sm text-center font-black">
                                        </td>
                                        <td class="py-6 px-8 text-right text-sm font-black text-slate-900"><span class="input-total-text">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span></td>
                                        <td class="py-6 px-6 text-center">
                                            <button type="button" class="remove-row-btn text-slate-300 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Widget Kanan --}}
                <div class="xl:col-span-3">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200/60 p-8 sticky top-10">
                        <div class="space-y-6">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400 font-bold uppercase">Modal</span>
                                <span id="display-total-pengeluaran" class="font-black text-slate-900">Rp 0</span>
                            </div>
                            <div id="margin-card" class="p-6 rounded-[2rem] border transition-all duration-500 bg-slate-50">
                                <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Profit</span>
                                <p id="display-margin" class="text-3xl font-black text-slate-900 tracking-tighter">0</p>
                                <span id="margin-status" class="inline-block mt-3 text-[8px] font-black uppercase py-1 px-2.5 rounded-lg"></span>
                            </div>
                            <button type="submit" class="w-full bg-slate-900 hover:bg-blue-600 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200 transition-all">
                                Perbarui Rekapan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Template Baris Baru --}}
<template id="row-template">
    <tr class="recap-row group transition-all duration-300">
        <td class="py-6 px-8"><input type="date" name="items[INDEX][tanggal_item]" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-2 text-[10px] font-bold outline-none"></td>
        <td class="py-6 px-4">
            <input type="text" name="items[INDEX][material]" placeholder="Nama item..." class="w-full border-none focus:ring-0 p-0 text-sm font-bold text-slate-800 bg-transparent" required>
            <input type="text" name="items[INDEX][detail]" placeholder="Spesifikasi" class="w-full border-none focus:ring-0 p-0 text-[10px] text-slate-400 bg-transparent">
        </td>
        <td class="py-6 px-4">
            <div class="flex items-center bg-slate-50 rounded-2xl px-4 py-2 border border-slate-100 group-hover:bg-white transition-all">
                <input type="number" name="items[INDEX][harga]" placeholder="0" class="input-harga w-full border-none focus:ring-0 p-0 text-sm text-right font-black bg-transparent" required>
            </div>
        </td>
        <td class="py-6 px-4"><input type="number" name="items[INDEX][qty]" value="1" step="0.01" class="input-qty w-full border border-slate-100 bg-slate-50 rounded-xl py-2 text-sm text-center font-black"></td>
        <td class="py-6 px-8 text-right text-sm font-black text-slate-900"><span class="input-total-text">Rp 0</span></td>
        <td class="py-6 px-6 text-center">
            <button type="button" class="remove-row-btn text-slate-300 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg></button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsContainer = document.getElementById('recap-rows');
    const addRowBtn = document.getElementById('add-row-btn');
    const template = document.getElementById('row-template').innerHTML;
    const grandTotalOffer = parseFloat("{{ $recap->total_penawaran_klien }}") || 0;

    // Gunakan timestamp atau counter yang lebih aman untuk index unik
    let rowIndex = {{ $recap->items->count() }} + 100;

    function formatRupiah(angka) { return Math.floor(angka).toLocaleString('id-ID'); }

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

        const card = document.getElementById('margin-card');
        const status = document.getElementById('margin-status');
        if (margin < 0) {
            status.textContent = 'DEFISIT'; status.className = 'inline-block mt-3 text-[8px] font-black uppercase py-1 px-2.5 rounded-lg bg-red-100 text-red-600';
            card.className = 'p-6 rounded-[2rem] bg-red-50 border-red-100 transition-all';
        } else {
            status.textContent = 'PROFIT'; status.className = 'inline-block mt-3 text-[8px] font-black uppercase py-1 px-2.5 rounded-lg bg-emerald-100 text-emerald-600';
            card.className = 'p-6 rounded-[2rem] bg-emerald-50 border-emerald-100 transition-all';
        }
    }

    // Pasang event listener untuk baris yang sudah ada
    function bindRowEvents(row) {
        row.querySelector('.remove-row-btn').addEventListener('click', () => {
            row.remove();
            calculateTotals();
        });
        row.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', calculateTotals);
        });
    }

    // Inisialisasi baris lama
    document.querySelectorAll('.recap-row').forEach(row => bindRowEvents(row));

    addRowBtn.addEventListener('click', () => {
        const html = template.replace(/INDEX/g, rowIndex++);
        rowsContainer.insertAdjacentHTML('beforeend', html);
        bindRowEvents(rowsContainer.lastElementChild);
        calculateTotals();
    });

    calculateTotals();
});
</script>
@endsection