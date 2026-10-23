@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Penawaran Produk</h1>
            <a href="{{ route('histori.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali</a>
        </div>

        {{-- FORM UPDATE --}}
        <form id="product-offer-form" action="{{ route('penawaran.update_product', $offer->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

            <fieldset class="border-t pt-6">
                <legend class="text-lg font-semibold text-gray-700 px-2">1. Informasi Klien</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Nama Klien/Perusahaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_klien" id="nama_klien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $offer->nama_klien }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Detail / Alamat</label>
                        <input type="text" name="client_details" id="client_details" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $offer->client_details }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tanggal Penawaran <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ $offer->created_at->format('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Perihal <span class="text-red-500">*</span></label>
                        <input type="text" name="perihal" id="perihal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('perihal', $offer->perihal ?? 'Penawaran supply produk cat Jotun') }}" required>
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t pt-6 mt-8">
                <legend class="text-lg font-semibold text-gray-700 px-2">2. Daftar Produk</legend>

                {{-- Datalist untuk Autocomplete --}}
                <datalist id="list-produk-db">
                    @foreach ($products as $product)
                        <option value="{{ $product->nama_produk }}" data-harga="{{ $product->harga }}" data-kemasan="{{ $product->kemasan ?? '-' }}">
                    @endforeach
                </datalist>

                <div id="items-container" class="space-y-4 mt-4">
                    {{-- Item akan di-generate oleh JS --}}
                </div>

                <div class="mt-4">
                    <button type="button" id="add-row-btn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition flex items-center gap-2">
                        + Tambah Baris
                    </button>
                </div>
            </fieldset>

            <div class="mt-8 pt-6 border-t grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 p-4 rounded-lg h-fit">
                    <h4 class="font-semibold text-gray-700 mb-3">Opsi Dokumen</h4>

                    <div class="flex items-start mb-3">
                        <div class="flex items-center h-5">
                            <input name="pisah_kriteria_total" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $offer->pisah_kriteria_total ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm"><label class="font-medium text-gray-700">Pisahkan Kriteria Total</label></div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input name="hilangkan_grand_total" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" {{ $offer->hilangkan_grand_total ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 text-sm"><label class="font-medium text-gray-700">Hilangkan Grand Total</label></div>
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-lg flex flex-col gap-3">
                    <div class="flex justify-between text-gray-600 text-sm"><span>Total QTY:</span><span id="display-total-qty" class="font-semibold">0</span></div>
                    <div class="flex justify-between text-gray-700 font-medium"><span>Total Harga Produk:</span><span id="display-subtotal">Rp 0</span></div>

                    <div class="flex justify-between items-center text-red-600">
                        <span class="text-sm font-semibold">Diskon Global:</span>
                        <div class="flex items-center gap-1 w-1/2 justify-end">
                            <span class="text-xs">- Rp</span>
                            <input type="number" name="diskon_global" id="global-discount-input"
                                   class="w-full text-right text-sm border-gray-300 rounded p-1"
                                   value="{{ $offer->diskon_global }}" min="0">
                        </div>
                    </div>

                    <hr class="border-gray-300 my-1">
                    <div class="flex justify-between text-gray-900 text-xl"><span class="font-bold">Grand Total:</span><span id="display-grand-total" class="font-bold text-blue-700">Rp 0</span></div>

                    <div class="mt-4">
                        <button type="submit" class="w-full bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-yellow-700 transition shadow-lg">
                            Update Penawaran
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TEMPLATE BARIS (Sama dengan Create) --}}
<template id="row-template">
    <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm relative grid grid-cols-1 md:grid-cols-12 gap-3 items-end mb-4">
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Produk <span class="text-red-500">*</span></label>
            <input type="text" class="nama-produk-input w-full rounded-md border-gray-300 text-sm" list="list-produk-db" placeholder="Ketik..." autocomplete="off">
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kode Warna</label>
            <input type="text" class="kode-warna-input w-full rounded-md border-gray-300 text-sm" placeholder="Warna">
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Keterangan</label>
            <input type="text" class="keterangan-input w-full rounded-md border-gray-300 text-sm" placeholder="Ket">
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ukuran</label>
            <input type="text" class="ukuran-input w-full rounded-md border-gray-300 text-sm" placeholder="Size">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Harga (@) <span class="text-red-500">*</span></label>
            <input type="number" class="harga-input w-full rounded-md border-gray-300 text-sm text-right" placeholder="0">
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">QTY <span class="text-red-500">*</span></label>
            <input type="number" class="qty-input w-full rounded-md border-gray-300 text-sm text-center font-bold" value="1">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total</label>
            <input type="text" class="subtotal-input w-full rounded-md border-gray-300 bg-gray-200 text-sm text-right font-bold" readonly>
        </div>
        <div class="absolute -top-2 right-2 flex items-center gap-1">
            <div class="drag-handle cursor-grab active:cursor-grabbing bg-gray-600 text-white rounded-full p-1 shadow hover:bg-gray-700 transition flex items-center justify-center" title="Geser untuk memindahkan posisi">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 2a2 2 0 100 4 2 2 0 000-4zM13 2a2 2 0 100 4 2 2 0 000-4zM7 8a2 2 0 100 4 2 2 0 000-4zM13 8a2 2 0 100 4 2 2 0 000-4zM7 14a2 2 0 100 4 2 2 0 000-4zM13 14a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
            </div>
            <button type="button" class="insert-btn bg-blue-500 text-white rounded-full p-1 shadow hover:bg-blue-600 transition" title="Sisip Baris di Bawah">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </button>
            <button type="button" class="remove-btn bg-red-500 text-white rounded-full p-1 shadow hover:bg-red-600 transition" title="Hapus Baris">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </button>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DATA LAMA DARI DATABASE (Inject PHP ke JS) ---
        // Kita parsing data item yang ada agar bisa di-looping
        const existingItems = @json($offer->items);

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

        // Fungsi Tambah / Sisip Baris
        function addRow(data = null, targetRow = null) {
            const newRow = template.cloneNode(true);

            // Setup Names
            newRow.querySelector('.nama-produk-input').name = `items[${rowIndex}][nama_produk]`;
            newRow.querySelector('.kode-warna-input').name = `items[${rowIndex}][kode_warna]`;
            newRow.querySelector('.keterangan-input').name = `items[${rowIndex}][keterangan]`;
            newRow.querySelector('.ukuran-input').name = `items[${rowIndex}][ukuran]`;
            newRow.querySelector('.harga-input').name = `items[${rowIndex}][harga_satuan]`;
            newRow.querySelector('.qty-input').name = `items[${rowIndex}][qty]`;

            // --- ISI DATA LAMA JIKA ADA ---
            if(data) {
                newRow.querySelector('.nama-produk-input').value = data.nama_produk;
                newRow.querySelector('.ukuran-input').value = data.area_dinding; // Ukuran disimpan di area_dinding
                newRow.querySelector('.harga-input').value = data.harga_per_m2;
                newRow.querySelector('.qty-input').value = data.volume;

                // Parsing Warna
                let warna = data.warna ?? '';
                let keterangan = data.keterangan ?? '';
                if(!warna && data.deskripsi_tambahan) {
                    if(data.deskripsi_tambahan.includes('Warna:')) {
                        let match = data.deskripsi_tambahan.match(/Warna:\s*([^|]+)/);
                        if(match) warna = match[1].trim();
                    } else {
                        warna = data.deskripsi_tambahan;
                    }
                }
                newRow.querySelector('.kode-warna-input').value = warna;
                newRow.querySelector('.keterangan-input').value = keterangan;
            }

            if (targetRow && targetRow instanceof Element) {
                targetRow.after(newRow);
            } else {
                container.appendChild(newRow);
            }

            initRowEvents(newRow);
            rowIndex++;
        }

        function initRowEvents(row) {
            const inputs = row.querySelectorAll('input');
            const removeBtn = row.querySelector('.remove-btn');
            const insertBtn = row.querySelector('.insert-btn');
            const nameInput = row.querySelector('.nama-produk-input');
            const hargaInput = row.querySelector('.harga-input');
            const ukuranInput = row.querySelector('.ukuran-input');

            // Autocomplete Logic
            nameInput.addEventListener('input', function() {
                const val = this.value;
                const options = document.getElementById('list-produk-db').options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === val) {
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

            inputs.forEach(input => input.addEventListener('input', calculateAll));

            if (insertBtn) {
                insertBtn.addEventListener('click', function() {
                    addRow(null, row);
                });
            }

            removeBtn.addEventListener('click', function() { row.remove(); calculateAll(); });
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

        function reindexItems() {
            document.querySelectorAll('#items-container .item-row').forEach((row, index) => {
                const nama = row.querySelector('.nama-produk-input');
                const warna = row.querySelector('.kode-warna-input');
                const ket = row.querySelector('.keterangan-input');
                const ukur = row.querySelector('.ukuran-input');
                const harga = row.querySelector('.harga-input');
                const qty = row.querySelector('.qty-input');

                if (nama) nama.name = `items[${index}][nama_produk]`;
                if (warna) warna.name = `items[${index}][kode_warna]`;
                if (ket) ket.name = `items[${index}][keterangan]`;
                if (ukur) ukur.name = `items[${index}][ukuran]`;
                if (harga) harga.name = `items[${index}][harga_satuan]`;
                if (qty) qty.name = `items[${index}][qty]`;
            });
        }

        if (typeof Sortable !== 'undefined' && container) {
            new Sortable(container, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'opacity-50',
                onEnd: function() {
                    reindexItems();
                    calculateAll();
                    isFormDirty = true;
                }
            });
        }

        globalDiscountInput.addEventListener('input', calculateAll);
        document.getElementById('add-row-btn').addEventListener('click', () => addRow());

        // --- INIT DATA SAAT LOAD ---
        if (existingItems && existingItems.length > 0) {
            // Loop data dari database dan buat baris
            existingItems.forEach(item => {
                addRow(item);
            });
        } else {
            // Jika kosong, tambah 1 baris kosong
            addRow();
        }

        // Hitung total awal setelah data diload
        setTimeout(calculateAll, 100);

        // --- Toast Notifikasi Validasi ---
        function showValidationToast(message) {
            let toast = document.getElementById('validation-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'validation-toast';
                toast.className = 'fixed top-5 right-5 z-50 flex items-center gap-3 bg-red-600 text-white px-5 py-3.5 rounded-xl shadow-2xl transition-all duration-300 transform translate-y-0 text-sm font-semibold max-w-md';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">${message}</div>
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

        // --- Fungsi Validasi Form Penawaran Produk ---
        function clearAllErrors() {
            document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));
            document.querySelectorAll('.field-error-text').forEach(el => el.remove());
        }

        function markFieldError(err) {
            const el = err.element;
            if (!el) return;

            el.classList.add('field-error');

            const container = el.parentElement;
            if (container && !container.querySelector('.field-error-text')) {
                const msgEl = document.createElement('p');
                msgEl.className = 'field-error-text text-xs text-red-500 font-semibold mt-1';
                msgEl.textContent = err.message;
                container.appendChild(msgEl);
            }

            const clearFn = function() {
                el.classList.remove('field-error');
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

        function validateProductOfferForm() {
            clearAllErrors();
            const errors = [];

            // 1. Validasi Nama Klien
            const namaKlien = document.getElementById('nama_klien');
            if (!namaKlien || !namaKlien.value.trim()) {
                errors.push({
                    element: namaKlien,
                    scrollTarget: namaKlien,
                    message: 'Nama Klien / Perusahaan wajib diisi.'
                });
            }

            // 2. Validasi Tanggal
            const tanggal = document.getElementById('tanggal');
            if (!tanggal || !tanggal.value.trim()) {
                errors.push({
                    element: tanggal,
                    scrollTarget: tanggal,
                    message: 'Tanggal Penawaran wajib diisi.'
                });
            }

            // 3. Validasi Perihal
            const perihal = document.getElementById('perihal');
            if (!perihal || !perihal.value.trim()) {
                errors.push({
                    element: perihal,
                    scrollTarget: perihal,
                    message: 'Perihal penawaran wajib diisi.'
                });
            }

            // 4. Validasi Baris Produk
            const itemRows = document.querySelectorAll('#items-container .item-row');
            if (itemRows.length === 0) {
                const addBtn = document.getElementById('add-row-btn');
                errors.push({
                    element: addBtn,
                    scrollTarget: addBtn,
                    message: 'Tambahkan minimal 1 baris produk.'
                });
            } else {
                itemRows.forEach((row, index) => {
                    const namaInput = row.querySelector('.nama-produk-input');
                    const hargaInput = row.querySelector('.harga-input');
                    const qtyInput = row.querySelector('.qty-input');

                    if (!namaInput || !namaInput.value.trim()) {
                        errors.push({
                            element: namaInput,
                            scrollTarget: row,
                            message: `Nama Produk baris #${index + 1} wajib diisi.`
                        });
                    }

                    if (!hargaInput || hargaInput.value === '' || parseFloat(hargaInput.value) < 0 || isNaN(parseFloat(hargaInput.value))) {
                        errors.push({
                            element: hargaInput,
                            scrollTarget: row,
                            message: `Harga baris #${index + 1} wajib diisi.`
                        });
                    }

                    if (!qtyInput || qtyInput.value === '' || parseFloat(qtyInput.value) <= 0 || isNaN(parseFloat(qtyInput.value))) {
                        errors.push({
                            element: qtyInput,
                            scrollTarget: row,
                            message: `QTY baris #${index + 1} minimal 1.`
                        });
                    }
                });
            }

            return errors;
        }

        // --- Dirty Form Protection & Submit Validation ---
        let isFormDirty = false;
        let isSubmitting = false;

        const productOfferForm = document.getElementById('product-offer-form');
        if (productOfferForm) {
            productOfferForm.addEventListener('input', function() { isFormDirty = true; });
            productOfferForm.addEventListener('change', function() { isFormDirty = true; });

            productOfferForm.addEventListener('submit', function(e) {
                const errors = validateProductOfferForm();

                if (errors.length > 0) {
                    e.preventDefault();
                    isSubmitting = false;

                    // Tandai semua field yang error
                    errors.forEach(err => markFieldError(err));

                    // Tampilkan notifikasi toast
                    showValidationToast('Ada form yang belum diisi! Halaman secara otomatis diarahkan ke bagian yang belum lengkap.');

                    // Scroll otomatis ke field pertama yang belum diisi
                    const firstErr = errors[0];
                    const scrollTarget = firstErr.scrollTarget || firstErr.element;

                    if (scrollTarget) {
                        scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        scrollTarget.classList.add('field-error-shake');
                        setTimeout(() => scrollTarget.classList.remove('field-error-shake'), 600);
                    }

                    // Fokus pada input pertama yang error
                    setTimeout(() => {
                        if (firstErr.element && typeof firstErr.element.focus === 'function') {
                            firstErr.element.focus();
                        }
                    }, 350);

                    return false;
                }

                isSubmitting = true;
                const submitBtn = productOfferForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    `;
                }
            });
        }

        window.addEventListener('beforeunload', function(e) {
            if (isFormDirty && !isSubmitting) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
</script>

<style>
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