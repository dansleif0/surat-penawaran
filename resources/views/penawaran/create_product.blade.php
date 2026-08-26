@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buat Penawaran Produk</h1>
            <a href="{{ route('histori.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke Histori</a>
        </div>

        <p class="text-gray-600 mb-6">Silakan isi detail pemesanan produk di bawah ini.</p>

        <form id="product-offer-form" action="{{ route('penawaran.store_product') }}" method="POST">
            @csrf

            <fieldset class="border-t pt-6">
                <legend class="text-lg font-semibold text-gray-700 px-2">1. Informasi Klien</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="nama_klien" class="block text-sm font-medium text-gray-600">Nama Klien/Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_klien" id="nama_klien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    </div>
                    <div>
                        <label for="client_details" class="block text-sm font-medium text-gray-600">Detail / Alamat (Optional)</label>
                        <input type="text" name="client_details" id="client_details" placeholder="Alamat atau Catatan Klien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-600">Tanggal Penawaran</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="perihal" class="block text-sm font-medium text-gray-600">Perihal</label>
                        <input type="text" name="perihal" id="perihal" value="Penawaran supply produk cat Jotun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t pt-6 mt-8">
                <legend class="text-lg font-semibold text-gray-700 px-2">2. Daftar Produk</legend>

                <datalist id="list-produk-db">
                    @foreach ($products as $product)
                        <option value="{{ $product->nama_produk }}" data-harga="{{ $product->harga }}" data-kemasan="{{ $product->kemasan ?? '-' }}">
                    @endforeach
                </datalist>

                <div id="items-container" class="space-y-4 mt-4">
                    </div>

                <div class="mt-4">
                    <button type="button" id="add-row-btn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Tambah Baris Produk
                    </button>
                </div>
            </fieldset>

            <div class="mt-8 pt-6 border-t grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="bg-gray-50 p-4 rounded-lg h-fit">
                    <h4 class="font-semibold text-gray-700 mb-3">Opsi Dokumen</h4>

                    <div class="flex items-start mb-3">
                        <div class="flex items-center h-5">
                            <input id="pisah_kriteria_total" name="pisah_kriteria_total" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="pisah_kriteria_total" class="font-medium text-gray-700 cursor-pointer">Pisahkan Kriteria Total</label>
                            <p class="text-gray-500 text-xs">Pisahkan total berdasarkan kategori produk (jika ada).</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="hilangkan_grand_total" name="hilangkan_grand_total" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="hilangkan_grand_total" class="font-medium text-gray-700 cursor-pointer">Hilangkan Grand Total</label>
                            <p class="text-gray-500 text-xs">Sembunyikan baris Grand Total di surat penawaran.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg flex flex-col gap-3">
                    <div class="flex justify-between text-gray-600 text-sm">
                        <span>Total QTY:</span>
                        <span id="display-total-qty" class="font-semibold">0</span>
                    </div>

                    <div class="flex justify-between text-gray-700 font-medium">
                        <span>Total Harga Produk:</span>
                        <span id="display-subtotal">Rp 0</span>
                    </div>

                    <div class="flex justify-between items-center text-red-600">
                        <span class="text-sm font-semibold">Diskon Tambahan (Global):</span>
                        <div class="flex items-center gap-1 w-1/2 justify-end">
                            <span class="text-xs">- Rp</span>
                            <input type="number" name="diskon_global" id="global-discount-input"
                                   class="w-full text-right text-sm border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 p-1"
                                   placeholder="0" min="0">
                        </div>
                    </div>

                    <hr class="border-gray-300 my-1">

                    <div class="flex justify-between text-gray-900 text-xl">
                        <span class="font-bold">Grand Total:</span>
                        <span id="display-grand-total" class="font-bold text-blue-700">Rp 0</span>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition shadow-lg">
                            Simpan Penawaran
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="row-template">
    <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm relative grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">

        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Produk</label>
            {{-- Gunakan Input Text Biasa agar bisa diketik bebas --}}
            <input type="text" class="nama-produk-input w-full rounded-md border-gray-300 text-sm"
                   list="list-produk-db"
                   placeholder="Ketik nama produk..."
                   autocomplete="off">
        </div>

        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kode Warna</label>
            <input type="text" class="kode-warna-input w-full rounded-md border-gray-300 text-sm" placeholder="Contoh: 9918">
        </div>

        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Keterangan</label>
            <input type="text" class="keterangan-input w-full rounded-md border-gray-300 text-sm" placeholder="Ket">
        </div>

        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ukuran</label>
            <input type="text" class="ukuran-input w-full rounded-md border-gray-300 text-sm" placeholder="2.5 L">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Harga (@)</label>
            <input type="number" class="harga-input w-full rounded-md border-gray-300 text-sm text-right font-medium" placeholder="0">
        </div>

        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">QTY</label>
            <input type="number" class="qty-input w-full rounded-md border-gray-300 text-sm text-center font-bold" value="1" min="1">
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total</label>
            <input type="text" class="subtotal-input w-full rounded-md border-gray-300 bg-gray-200 text-sm text-right font-bold text-gray-800" readonly>
        </div>

        <button type="button" class="remove-btn absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const addBtn = document.getElementById('add-row-btn');
        const template = document.getElementById('row-template').content.firstElementChild;

        const displayTotalQty = document.getElementById('display-total-qty');
        const displaySubtotal = document.getElementById('display-subtotal');
        const displayGrandTotal = document.getElementById('display-grand-total');
        const globalDiscountInput = document.getElementById('global-discount-input');

        let rowIndex = 0;

        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        };

        function addRow() {
            const newRow = template.cloneNode(true);

            // Perhatikan perubahan nama field 'nama_produk'
            // Kita ganti 'product_id' menjadi 'nama_produk' (string) karena input bebas
            newRow.querySelector('.nama-produk-input').name = `items[${rowIndex}][nama_produk]`;
            newRow.querySelector('.kode-warna-input').name = `items[${rowIndex}][kode_warna]`;
            newRow.querySelector('.keterangan-input').name = `items[${rowIndex}][keterangan]`;
            newRow.querySelector('.ukuran-input').name = `items[${rowIndex}][ukuran]`;
            newRow.querySelector('.harga-input').name = `items[${rowIndex}][harga_satuan]`;
            newRow.querySelector('.qty-input').name = `items[${rowIndex}][qty]`;

            container.appendChild(newRow);
            initRowEvents(newRow);
            rowIndex++;
        }

        function initRowEvents(row) {
            const inputs = row.querySelectorAll('input');
            const removeBtn = row.querySelector('.remove-btn');
            const nameInput = row.querySelector('.nama-produk-input');
            const hargaInput = row.querySelector('.harga-input');
            const ukuranInput = row.querySelector('.ukuran-input');

            // Logika Autocomplete: Jika user memilih dari saran datalist, isi harga otomatis (Opsional)
            // Jika user mengetik manual yang tidak ada di list, harga tidak berubah.
            nameInput.addEventListener('input', function() {
                const val = this.value;
                const options = document.getElementById('list-produk-db').options;

                // Cek apakah input cocok dengan salah satu opsi di datalist
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
                        // Jika cocok, isi harga & kemasan (Hanya jika input harga masih kosong/0 agar tidak menimpa editan user)
                        if(hargaInput.value == "" || hargaInput.value == 0) {
                            hargaInput.value = options[i].getAttribute('data-harga');
                        }
                        if(ukuranInput.value == "") {
                            ukuranInput.value = options[i].getAttribute('data-kemasan');
                        }
                        calculateAll();
                        break;
                    }
                }
            });

            inputs.forEach(input => {
                input.addEventListener('input', calculateAll);
            });

            removeBtn.addEventListener('click', function() {
                row.remove();
                calculateAll();
            });
        }

        function calculateAll() {
            let totalQty = 0;
            let totalHargaProduk = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;

                let subtotalBaris = (harga * qty);
                if(subtotalBaris < 0) subtotalBaris = 0;

                row.querySelector('.subtotal-input').value = formatRupiah(subtotalBaris);

                totalQty += qty;
                totalHargaProduk += subtotalBaris;
            });

            const diskonGlobal = parseFloat(globalDiscountInput.value) || 0;
            let grandTotal = totalHargaProduk - diskonGlobal;
            if(grandTotal < 0) grandTotal = 0;

            displayTotalQty.textContent = totalQty;
            displaySubtotal.textContent = formatRupiah(totalHargaProduk);
            displayGrandTotal.textContent = formatRupiah(grandTotal);
        }

        globalDiscountInput.addEventListener('input', calculateAll);
        document.getElementById('add-row-btn').addEventListener('click', addRow);
        addRow();
    });
</script>
@endsection