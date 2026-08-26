@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Buat Surat Penawaran Baru</h1>

        <form action="/buat-surat" method="POST">
            @csrf

            <fieldset class="border-t pt-6 mt-6">
                <legend class="text-lg font-semibold text-gray-700 px-2">Informasi Klien</legend>
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div>
                        <label for="nama_klien" class="block text-sm font-medium text-gray-600">Nama Klien/Perusahaan</label>
                        <input type="text" name="nama_klien" id="nama_klien" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="alamat_klien" class="block text-sm font-medium text-gray-600">Alamat Klien</label>
                        <textarea name="alamat_klien" id="alamat_klien" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t pt-6 mt-8">
                <legend class="text-lg font-semibold text-gray-700 px-2">Detail Produk</legend>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div class="md:col-span-3">
                        <label for="produk" class="block text-sm font-medium text-gray-600">Nama Produk/Jasa</label>
                        <input type="text" name="produk" id="produk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-gray-600">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label for="harga" class="block text-sm font-medium text-gray-600">Harga Satuan (Rp)</label>
                        <input type="number" name="harga" id="harga" placeholder="5000000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                 </div>
            </fieldset>

            <div class="mt-8 pt-6 border-t">
                <button type="submit" class="w-full bg-black text-white font-bold py-3 px-6 rounded hover:bg-gray-800 transition text-lg">
                    Generate Surat Penawaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection