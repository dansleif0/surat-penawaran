@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Tambah Data Harga Baru</h1>

        {{-- Action diarahkan ke route 'harga.store' --}}
        <form action="{{ route('harga.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk / Jasa</label>
                    <input type="text" name="nama_produk" id="nama_produk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                </div>

                <div>
                    <label for="performa" class="block text-sm font-medium text-gray-700">Nama Brand</label>
                    <input type="text" name="performa" id="performa" placeholder="Contoh: Jotun, Propan, dll." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                </div>

                {{-- === TAMBAHAN FIELD KRITERIA === --}}
                <div>
                    <label for="kriteria" class="block text-sm font-medium text-gray-700">Kriteria</label>
                    <select name="kriteria" id="kriteria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                        <option value="">-- Pilih Kriteria --</option>
                        <option value="Exterior">Exterior</option>
                        <option value="Interior">Interior</option>
                    </select>
                </div>
                {{-- === AKHIR TAMBAHAN FIELD KRITERIA === --}}

                <div>
                    <label for="hasil_akhir" class="block text-sm font-medium text-gray-700">Hasil Akhir</label>
                    <select name="hasil_akhir" id="hasil_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                        <option value="">-- Pilih Hasil Akhir --</option>
                        <option value="Gloss/Kilap">Gloss/Kilap</option>
                        <option value="Matt/Doff">Matt/Doff</option>
                        <option value="SemiGloss/SemiKilap">SemiGloss/SemiKilap</option>
                    </select>
                </div>

                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                    <input type="number" name="harga" id="harga" placeholder="15000000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 px-6 rounded hover:bg-gray-700 transition ease-in-out duration-150">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection