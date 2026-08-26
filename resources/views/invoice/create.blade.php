@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Buat Invoice Baru</h1>

        <form id="invoice-form" action="#" method="POST">
            @csrf

            <fieldset class="border-t pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_klien" class="block text-sm font-medium text-gray-700">Nama Klien</label>
                        <input type="text" name="nama_klien" id="nama_klien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                    </div>
                    <div>
                        <label for="no_invoice" class="block text-sm font-medium text-gray-700">Nomor Invoice</label>
                        <input type="text" name="no_invoice" id="no_invoice" value="INV/2025/10/001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800">
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t pt-6 mt-8">
                <legend class="text-lg font-semibold text-gray-700 px-2 mb-4">Rincian Barang / Jasa</legend>
                <div id="invoice-rows-container" class="space-y-4">
                    </div>
                <div class="mt-4">
                    <button type="button" id="add-item-row-btn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition"> + Tambah Barang </button>
                </div>
            </fieldset>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="w-full md:w-2/3 lg:w-1/2 ml-auto space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-md font-medium text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="text-md font-medium text-gray-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-md font-medium text-gray-600">Total Diskon:</span>
                        <span id="total_diskon" class="text-md font-medium text-red-600">- Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-3 mt-3">
                        <span class="text-xl font-bold text-gray-800">Grand Total:</span>
                        <span id="grand_total" class="text-2xl font-bold text-gray-800">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="mt-8"><button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 px-6 rounded hover:bg-gray-700 transition"> Simpan Invoice </button></div>
        </form>

        <template id="item-row-template">
            <div class="invoice-row grid grid-cols-12 gap-x-4 items-end p-3 border rounded-lg bg-gray-50">
                <div class="col-span-12 md:col-span-4"><label class="block text-sm font-medium text-gray-600">Nama Barang</label><input type="text" placeholder="Cat Tembok" class="item-nama mt-1 block w-full rounded-md border-gray-300"></div>
                <div class="col-span-4 md:col-span-2"><label class="block text-sm font-medium text-gray-600">Jumlah</label><input type="number" value="1" class="item-jumlah mt-1 block w-full rounded-md border-gray-300"></div>
                <div class="col-span-8 md:col-span-2"><label class="block text-sm font-medium text-gray-600">Harga Satuan</label><input type="number" placeholder="150000" class="item-harga mt-1 block w-full rounded-md border-gray-300"></div>
                <div class="col-span-4 md:col-span-2"><label class="block text-sm font-medium text-gray-600">Diskon (Rp)</label><input type="number" placeholder="0" class="item-diskon mt-1 block w-full rounded-md border-gray-300"></div>
                <div class="col-span-8 md:col-span-2 flex items-end gap-2">
                    <div class="w-full"><label class="block text-sm font-medium text-gray-600">Total</label><input type="text" class="item-total mt-1 block w-full rounded-md border-gray-300 bg-gray-100" readonly></div>
                    <button type="button" class="remove-item-row-btn bg-red-500 text-white p-2 rounded w-10 h-10 flex-shrink-0 flex items-center justify-center">-</button>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemRowIndex = 0;
        const container = document.getElementById('invoice-rows-container');
        const addRowBtn = document.getElementById('add-item-row-btn');
        const template = document.getElementById('item-row-template').content.firstElementChild;
        const subtotalDisplay = document.getElementById('subtotal');
        const totalDiskonDisplay = document.getElementById('total_diskon');
        const grandTotalDisplay = document.getElementById('grand_total');

        function formatRupiah(angka) { return 'Rp ' + (angka || 0).toLocaleString('id-ID'); }

        function calculateAllTotals() {
            let subtotal = 0; let totalDiskon = 0;
            document.querySelectorAll('.invoice-row').forEach(row => {
                const jumlah = parseFloat(row.querySelector('.item-jumlah').value) || 0;
                const harga = parseFloat(row.querySelector('.item-harga').value) || 0;
                const diskon = parseFloat(row.querySelector('.item-diskon').value) || 0;
                const total = (jumlah * harga) - diskon;
                row.querySelector('.item-total').value = formatRupiah(total);
                subtotal += jumlah * harga;
                totalDiskon += diskon;
            });
            const grandTotal = subtotal - totalDiskon;
            subtotalDisplay.textContent = formatRupiah(subtotal);
            totalDiskonDisplay.textContent = '- ' + formatRupiah(totalDiskon);
            grandTotalDisplay.textContent = formatRupiah(grandTotal);
        }

        function addEventListeners(row) {
            row.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', calculateAllTotals);
            });
            row.querySelector('.remove-item-row-btn').addEventListener('click', () => {
                row.remove();
                calculateAllTotals();
            });
        }

        function addNewRow() {
            const newRow = template.cloneNode(true);
            newRow.querySelector('.item-nama').name = `items[${itemRowIndex}][nama]`;
            newRow.querySelector('.item-jumlah').name = `items[${itemRowIndex}][jumlah]`;
            newRow.querySelector('.item-harga').name = `items[${itemRowIndex}][harga]`;
            newRow.querySelector('.item-diskon').name = `items[${itemRowIndex}][diskon]`;
            container.appendChild(newRow);
            addEventListeners(newRow);
            itemRowIndex++;
        }

        addRowBtn.addEventListener('click', addNewRow);
        addNewRow();
        calculateAllTotals();

        const invoiceForm = document.getElementById('invoice-form');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', function() {
                const submitButton = invoiceForm.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Menyimpan...';
                }
            });
        }
    });
</script>
@endsection