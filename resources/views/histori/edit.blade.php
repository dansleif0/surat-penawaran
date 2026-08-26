@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Penawaran Proyek</h1>
            <p class="text-sm text-gray-500">Perbarui data penawaran di bawah ini.</p>
        </div>
        <a href="{{ route('histori.index') }}" class="text-sm text-gray-600 hover:text-blue-600 transition">
            &larr; Kembali ke Histori
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

        <form id="offer-form" action="{{ route('histori.update', $offer->id) }}" method="POST" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" id="action_input" value="save">

            <div class="mb-8 border-b border-gray-100 pb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-600 p-1.5 rounded-md text-xs">1</span> Informasi Klien
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_klien" value="{{ old('nama_klien', $offer->nama_klien) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Detail Proyek (Opsional)</label>
                        <input type="text" name="client_details" value="{{ old('client_details', $offer->client_details) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Perihal</label>
                        <input type="text" name="perihal" value="{{ old('perihal', $offer->perihal ?? 'Penawaran Jasa Apply dan Supply Pengecatan') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mb-8 border-b border-gray-100 pb-6">
                <div class="flex justify-between items-end mb-4">
                    <h2 class="text-lg font-bold text-gray-700 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 p-1.5 rounded-md text-xs">2</span> Detail Produk
                    </h2>
                </div>

                <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-100">
                    <label class="block text-sm font-bold text-blue-800 mb-1">Set Produk Masal (Opsional)</label>
                    <p class="text-xs text-blue-600 mb-2">Pilih produk di sini untuk mengubah semua baris di bawah sekaligus.</p>
                    <select id="produk-all-select" class="w-full rounded-lg border-blue-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Produk untuk Semua --</option>
                        @foreach ($all_products as $product)
                            <option value="{{ $product->nama_produk }}" data-harga="{{ $product->harga }}">{{ $product->nama_produk }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="product-rows-container" class="space-y-3">
                    {{-- Loop item produk yang sudah ada --}}
                    @forelse($offer->items as $index => $item)
                    <div class="product-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-4 border border-gray-200 rounded-lg bg-gray-50 relative hover:shadow-sm transition-shadow">

                        {{-- 1. Nama Produk (4 Kolom) --}}
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Produk</label>
                            <select name="produk[{{$index}}][nama]" class="product-select w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($all_products as $product)
                                <option value="{{ $product->nama_produk }}" data-harga="{{ $product->harga }}" @if($product->nama_produk == $item->nama_produk) selected @endif>{{ $product->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Area (2 Kolom) --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Area</label>
                            <input type="text" name="produk[{{$index}}][area]" value="{{ $item->area_dinding }}" placeholder="Dinding Luar" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- 3. Volume (1.5 Kolom) --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vol (m²)</label>
                            <input type="number" step="0.01" name="produk[{{$index}}][volume]" value="{{ $item->volume }}" class="volume-input w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 text-center">
                        </div>

                        {{-- 4. Harga (2 Kolom) --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Harga/m²</label>
                            <input type="number" name="produk[{{$index}}][harga]" value="{{ $item->harga_per_m2 }}" class="harga-input w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- 5. Total (2 Kolom) --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Subtotal</label>
                            <input type="text" class="total-output w-full bg-gray-200 border-gray-300 rounded-md text-sm font-bold text-gray-700 cursor-not-allowed" readonly>
                        </div>

                        {{-- 6. Hapus --}}
                        <div class="absolute top-2 right-2 md:static md:col-span-12 md:w-auto flex justify-end">
                            <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded transition-colors" title="Hapus Baris">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-4">
                    <button type="button" id="add-product-row-btn" class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-4 py-2 rounded-lg transition-colors border border-dashed border-blue-300 w-full justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Baris Produk
                    </button>
                </div>
            </div>

            <div class="mb-8 border-b border-gray-100 pb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-600 p-1.5 rounded-md text-xs">3</span> Jasa / Pengerjaan Tambahan
                </h2>

                <div id="jasa-rows-container" class="space-y-3">
                    @forelse($offer->jasaItems as $index => $jasa)
                    <div class="jasa-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-4 border border-gray-200 rounded-lg bg-gray-50 relative hover:shadow-sm transition-shadow">

                        {{-- 1. Nama Pengerjaan --}}
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Pengerjaan</label>
                            <input type="text" name="jasa[{{$index}}][nama]" value="{{ $jasa->nama_jasa }}" class="w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- 2. Volume --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Volume</label>
                            <input type="number" step="0.01" name="jasa[{{$index}}][volume]" value="{{ $jasa->volume ?? 1 }}" class="jasa-volume w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 text-center">
                        </div>

                        {{-- 3. Satuan --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Satuan</label>
                            <select name="jasa[{{$index}}][satuan]" class="jasa-satuan w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="Ls" {{ ($jasa->satuan ?? '') == 'Ls' ? 'selected' : '' }}>Ls (Lumpsum)</option>
                                <option value="Lot" {{ ($jasa->satuan ?? '') == 'Lot' ? 'selected' : '' }}>Lot</option>
                                <option value="M2" {{ ($jasa->satuan ?? '') == 'M2' ? 'selected' : '' }}>M²</option>
                                <option value="M1" {{ ($jasa->satuan ?? '') == 'M1' ? 'selected' : '' }}>M¹</option>
                                <option value="Unit" {{ ($jasa->satuan ?? '') == 'Unit' ? 'selected' : '' }}>Unit</option>
                                <option value="Pkt" {{ ($jasa->satuan ?? '') == 'Pkt' ? 'selected' : '' }}>Pkt (Paket)</option>
                                <option value="Liter" {{ ($jasa->satuan ?? '') == 'Liter' ? 'selected' : '' }}>Liter</option>
                                <option value="Titik" {{ ($jasa->satuan ?? '') == 'Titik' ? 'selected' : '' }}>Titik</option>
                            </select>
                        </div>

                        {{-- 4. Harga Satuan --}}
                        {{-- Note: Jika kolom 'harga_satuan' belum ada di DB, Anda mungkin perlu migrasi. Jika tidak, gunakan 'harga_jasa' tapi logikanya harus disesuaikan --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Harga Satuan</label>
                            {{-- Jika di DB Anda kolomnya 'harga_jasa' menyimpan total, maka disini harus dibagi volume. Jika ada kolom 'harga_satuan', pakai itu --}}
                            <input type="number" name="jasa[{{$index}}][harga]" value="{{ $jasa->harga_satuan ?? $jasa->harga_jasa }}" class="jasa-harga w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- 5. Total (Readonly) --}}
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Total</label>
                            <input type="text" class="jasa-total w-full bg-gray-200 border-gray-300 rounded-md text-sm font-bold text-gray-700 cursor-not-allowed" readonly>
                        </div>

                        {{-- 6. Hapus --}}
                        <div class="absolute top-2 right-2 md:static md:col-span-1 md:flex md:justify-end">
                            <button type="button" class="remove-jasa-row-btn text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-4">
                    <button type="button" id="add-jasa-row-btn" class="flex items-center gap-2 text-sm font-bold text-green-600 hover:text-green-800 hover:bg-green-50 px-4 py-2 rounded-lg transition-colors border border-dashed border-green-300 w-full justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Jasa Tambahan
                    </button>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start gap-8">

                <div class="w-full md:w-1/2 space-y-3 bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-bold text-gray-700 text-sm mb-2">Pengaturan Dokumen</h3>
                    <div class="flex items-center">
                        <input id="pisah_kriteria_total" name="pisah_kriteria_total" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $offer->pisah_kriteria_total ? 'checked' : '' }}>
                        <label for="pisah_kriteria_total" class="ml-2 block text-sm text-gray-700">
                            Pisahkan Total (Interior/Exterior)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="hilangkan_grand_total" name="hilangkan_grand_total" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $offer->hilangkan_grand_total ? 'checked' : '' }}>
                        <label for="hilangkan_grand_total" class="ml-2 block text-sm text-gray-700">
                            Sembunyikan Grand Total
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="opsi_paket" name="opsi_paket" type="checkbox" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer" {{ $offer->opsi_paket ? 'checked' : '' }}>
                        <label for="opsi_paket" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            Buat Opsi Paket
                        </label>
                    </div>
                </div>

                <div class="w-full md:w-1/2 text-right">
                    <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Total Estimasi Biaya</p>
                    <div id="total_keseluruhan" class="text-4xl font-extrabold text-gray-800 mt-1">Rp 0</div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                <button type="submit"
                    value="save"
                    onclick="document.getElementById('action_input').value = 'save';"
                    class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all">
                    Update (Simpan Perubahan)
                </button>
                <button type="submit"
                    value="save_and_copy"
                    onclick="document.getElementById('action_input').value = 'save_and_copy';"
                    class="w-full bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all">
                    Update & Copy (Buat Baru)
                </button>
            </div>

        </form>
    </div>
</div>

<template id="product-row-template">
    <div class="product-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-4 border border-gray-200 rounded-lg bg-gray-50 relative hover:shadow-sm transition-shadow">
        <div class="md:col-span-4">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Produk</label>
            <select class="product-select w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih --</option>
                @foreach ($all_products as $product)
                    <option value="{{ $product->nama_produk }}" data-harga="{{ $product->harga }}">{{ $product->nama_produk }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Area</label>
            <input type="text" placeholder="Dinding Luar" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vol (m²)</label>
            <input type="number" step="0.01" value="1" class="volume-input w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 text-center">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Harga/m²</label>
            <input type="number" class="harga-input w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Subtotal</label>
            <input type="text" class="total-output w-full bg-gray-200 border-gray-300 rounded-md text-sm font-bold text-gray-700 cursor-not-allowed" readonly>
        </div>
        <div class="absolute top-2 right-2 md:static md:col-span-12 md:w-auto flex justify-end">
             <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded transition-colors" title="Hapus Baris">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<template id="jasa-row-template">
    <div class="jasa-row grid grid-cols-1 md:grid-cols-12 gap-3 items-end p-4 border border-gray-200 rounded-lg bg-gray-50 relative hover:shadow-sm transition-shadow">

        <div class="md:col-span-4">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Pengerjaan</label>
            <input type="text" class="w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500" placeholder="Contoh: Biaya Scaffolding">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Volume</label>
            <input type="number" step="0.01" value="1" class="jasa-volume w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 text-center">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Satuan</label>
            <select class="jasa-satuan w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                <option value="Ls">Ls (Lumpsum)</option>
                <option value="Lot">Lot</option>
                <option value="M2">M²</option>
                <option value="M1">M¹</option>
                <option value="Unit">Unit</option>
                <option value="Pkt">Pkt (Paket)</option>
                <option value="Liter">Liter</option>
                <option value="Titik">Titik</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Harga Satuan</label>
            <input type="number" class="jasa-harga w-full rounded-md border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
        </div>

        <div class="md:col-span-1">
            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Total</label>
            <input type="text" class="jasa-total w-full bg-gray-200 border-gray-300 rounded-md text-sm font-bold text-gray-700 cursor-not-allowed" readonly>
        </div>

        <div class="absolute top-2 right-2 md:static md:col-span-1 md:flex md:justify-end">
            <button type="button" class="remove-jasa-row-btn text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect === 'undefined') {
            console.error('TomSelect library not loaded.');
        }

        const totalKeseluruhanDisplay = document.getElementById('total_keseluruhan');

        // Perbaikan Syntax Blade di JS
        let productRowIndex = {{ $offer->items->count() }};
        let jasaRowIndex = {{ $offer->jasaItems->count() }};

        function formatRupiah(angka) {
            return 'Rp ' + (angka || 0).toLocaleString('id-ID');
        }

        const tomSelectSettings = {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: "Cari Produk...",
            plugins: ['dropdown_input'],
        };

        // --- 1. LOGIKA PRODUK ALL ---
        const produkAllSelect = document.getElementById('produk-all-select');
        if(produkAllSelect){
            new TomSelect(produkAllSelect, tomSelectSettings);

            produkAllSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                if (!selectedValue) return;

                const selectedOption = Array.from(this.options).find(opt => opt.value === selectedValue);
                const masterHarga = selectedOption ? selectedOption.getAttribute('data-harga') : 0;

                document.querySelectorAll('.product-row').forEach(row => {
                    const rowSelect = row.querySelector('.product-select');
                    const rowHargaInput = row.querySelector('.harga-input');

                    rowHargaInput.value = masterHarga;
                    if (rowSelect.tomselect) {
                        rowSelect.tomselect.setValue(selectedValue, true);
                    } else {
                        rowSelect.value = selectedValue;
                    }
                });
                calculateAllTotals();
            });
        }

        // --- 2. LOGIKA PRODUK ROW (EDIT PAGE) ---
        const productContainer = document.getElementById('product-rows-container');
        const addProductRowBtn = document.getElementById('add-product-row-btn');
        const productTemplate = document.getElementById('product-row-template');

        // Fungsi Helper untuk Init Event pada Row yang Sudah Ada
        function setupProductRowEvents(row) {
            const productSelect = row.querySelector('.product-select');
            const hargaInput = row.querySelector('.harga-input');
            const volumeInput = row.querySelector('.volume-input');
            const removeBtn = row.querySelector('.remove-row-btn');

            if(typeof TomSelect !== 'undefined' && !productSelect.tomselect) {
                new TomSelect(productSelect, tomSelectSettings);
            }

            // Event Ganti Produk
            productSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                // Note: TomSelect hides original select, we must check options array
                // If using TomSelect, 'this' refers to original select
                const originalOption = Array.from(this.options).find(opt => opt.value === selectedValue);
                const hargaDefault = originalOption ? originalOption.getAttribute('data-harga') : 0;

                hargaInput.value = hargaDefault;
                calculateAllTotals();
            });

            hargaInput.addEventListener('input', calculateAllTotals);
            volumeInput.addEventListener('input', calculateAllTotals);

            removeBtn.addEventListener('click', function() {
                if (productSelect.tomselect) productSelect.tomselect.destroy();
                row.remove();
                calculateAllTotals();
            });
        }

        // Init Events untuk Row yang sudah ada dari Database
        document.querySelectorAll('.product-row').forEach(row => {
            setupProductRowEvents(row);
        });

        // Tambah Row Baru
        if(addProductRowBtn) {
            addProductRowBtn.addEventListener('click', function() {
                const clone = productTemplate.content.firstElementChild.cloneNode(true);

                // Update Name Attributes
                clone.querySelector('.product-select').name = `produk[${productRowIndex}][nama]`;
                const areaInput = clone.querySelector('input[placeholder="Dinding Luar"]');
                if(areaInput) areaInput.name = `produk[${productRowIndex}][area]`;
                clone.querySelector('.volume-input').name = `produk[${productRowIndex}][volume]`;
                clone.querySelector('.harga-input').name = `produk[${productRowIndex}][harga]`;

                productContainer.appendChild(clone);
                setupProductRowEvents(clone);
                productRowIndex++;
                calculateAllTotals();
            });
        }

        // --- 3. LOGIKA JASA ROW (EDIT PAGE) ---
        const jasaContainer = document.getElementById('jasa-rows-container');
        const addJasaRowBtn = document.getElementById('add-jasa-row-btn');
        const jasaTemplate = document.getElementById('jasa-row-template');

        function setupJasaRowEvents(row) {
            const volInput = row.querySelector('.jasa-volume');
            const hrgInput = row.querySelector('.jasa-harga');
            const removeBtn = row.querySelector('.remove-jasa-row-btn');

            volInput.addEventListener('input', calculateAllTotals);
            hrgInput.addEventListener('input', calculateAllTotals);

            removeBtn.addEventListener('click', function() {
                row.remove();
                calculateAllTotals();
            });
        }

        // Init Events untuk Row Jasa yang sudah ada
        document.querySelectorAll('.jasa-row').forEach(row => {
            setupJasaRowEvents(row);
        });

        if(addJasaRowBtn) {
            addJasaRowBtn.addEventListener('click', function() {
                const clone = jasaTemplate.content.firstElementChild.cloneNode(true);

                clone.querySelector('input[placeholder="Contoh: Biaya Scaffolding"]').name = `jasa[${jasaRowIndex}][nama]`;
                clone.querySelector('.jasa-volume').name = `jasa[${jasaRowIndex}][volume]`;
                clone.querySelector('.jasa-satuan').name = `jasa[${jasaRowIndex}][satuan]`;
                clone.querySelector('.jasa-harga').name = `jasa[${jasaRowIndex}][harga]`;

                jasaContainer.appendChild(clone);
                setupJasaRowEvents(clone);
                jasaRowIndex++;
            });
        }

        // --- 4. FUNGSI HITUNG TOTAL ---
        function calculateAllTotals() {
            let total = 0;

            // Hitung Produk
            document.querySelectorAll('.product-row').forEach(row => {
                const vol = parseFloat(row.querySelector('.volume-input').value) || 0;
                const hrg = parseFloat(row.querySelector('.harga-input').value) || 0;
                const subtotal = vol * hrg;
                row.querySelector('.total-output').value = formatRupiah(subtotal);
                total += subtotal;
            });

            // Hitung Jasa (Updated Formula: Volume * Harga)
            document.querySelectorAll('.jasa-row').forEach(row => {
                const vol = parseFloat(row.querySelector('.jasa-volume').value) || 0;
                const hrg = parseFloat(row.querySelector('.jasa-harga').value) || 0;
                const subtotal = vol * hrg;

                row.querySelector('.jasa-total').value = formatRupiah(subtotal);
                total += subtotal;
            });

            // Update Grand Total
            totalKeseluruhanDisplay.textContent = formatRupiah(total);
        }

        // Hitung total saat pertama kali load halaman edit
        calculateAllTotals();

        // --- 5. Anti Double-Submit Logic ---
        const offerForm = document.getElementById('offer-form');
        if (offerForm) {
            offerForm.addEventListener('submit', function() {
                const submitButtons = offerForm.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    if (btn.value === 'save') btn.innerHTML = 'Menyimpan...';
                    else if (btn.value === 'save_and_copy') btn.innerHTML = 'Menduplikasi...';
                });
            });
        }

    });
</script>
@endsection