@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-5xl mx-auto"> {{-- Diperlebar sedikit dari max-w-4xl agar tabel lebih lega --}}

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Daftar Harga Produk & Jasa
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data harga, kriteria, brand, dan hasil akhir produk/jasa.</p>
            </div>
            <a href="{{ url('/daftar-harga/tambah') }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-700 transition ease-in-out duration-150 shadow-sm flex items-center gap-2">
                <span>+ Tambah Baru</span>
            </a>
        </div>

        {{-- Kartu Pencarian & Filter Dropdown --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-5 mb-6">
            <form action="{{ route('harga.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Kolom Pencarian --}}
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari produk / brand..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Filter Kriteria --}}
                    <div>
                        <label for="kriteria" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Kriteria</label>
                        <select name="kriteria" id="kriteria" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Kriteria</option>
                            @foreach($kriteriaOptions as $opt)
                                <option value="{{ $opt }}" {{ request('kriteria') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown Filter Nama Brand --}}
                    <div>
                        <label for="brand" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Nama Brand</label>
                        <select name="brand" id="brand" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Brand</option>
                            @foreach($brandOptions as $opt)
                                <option value="{{ $opt }}" {{ request('brand') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown Filter Hasil Akhir --}}
                    <div>
                        <label for="hasil_akhir" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Hasil Akhir</label>
                        <select name="hasil_akhir" id="hasil_akhir" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Hasil Akhir</option>
                            @foreach($hasilAkhirOptions as $opt)
                                <option value="{{ $opt }}" {{ request('hasil_akhir') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Action Buttons & Result Counter --}}
                <div class="flex flex-col sm:flex-row items-center justify-between pt-3 border-t border-gray-100 gap-3">
                    <div class="text-xs text-gray-500 font-medium">
                        Menampilkan <span class="font-bold text-gray-800">{{ $products->count() }}</span> data produk
                        @if(request('search') || request('kriteria') || request('brand') || request('hasil_akhir'))
                            <span class="italic text-blue-600">(dengan filter aktif)</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if(request('search') || request('kriteria') || request('brand') || request('hasil_akhir'))
                            <a href="{{ route('harga.index') }}" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                                Reset Filter
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-1.5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Cari / Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Data Harga --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700 min-w-[800px]">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Nama Produk
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama Brand
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            Hasil Akhir
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Harga
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        {{-- Nama Produk --}}
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900">
                            {{ $product->nama_produk }}
                        </th>

                        {{-- Nama Brand --}}
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $product->performa }}
                        </td>

                        {{-- Kriteria --}}
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-400">
                                {{ $product->kriteria ?? '-' }}
                            </span>
                        </td>

                        {{-- Hasil Akhir --}}
                        <td class="px-6 py-4 text-center">
                            {{ $product->hasil_akhir }}
                        </td>

                        {{-- Harga --}}
                        <td class="px-6 py-4 font-semibold text-right whitespace-nowrap text-gray-900">
                            Rp {{ number_format($product->harga, 0, ',', '.') }} <span class="text-gray-500 font-normal text-xs">/m²</span>
                        </td>

                        {{-- Action --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-center items-center gap-3">
                                <a href="{{ route('harga.edit', $product->id) }}" class="font-medium text-blue-600 hover:text-blue-800 transition-colors">Edit</a>

                                <form action="{{ route('harga.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-800 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <p class="text-base font-semibold text-gray-700">Data tidak ditemukan</p>
                                <p class="text-xs text-gray-500">Coba ubah kata kunci pencarian atau sesuaikan opsi filter dropdown Anda.</p>
                                @if(request('search') || request('kriteria') || request('brand') || request('hasil_akhir'))
                                    <a href="{{ route('harga.index') }}" class="mt-2 text-xs text-blue-600 font-semibold hover:underline">
                                        Bersihkan Semua Filter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection