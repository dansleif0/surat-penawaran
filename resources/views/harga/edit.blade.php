@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Edit Data Harga</h1>

        <form action="{{ route('harga.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk / Jasa <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_produk" id="nama_produk" value="{{ $product->nama_produk }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                </div>

                <div>
                    <label for="performa" class="block text-sm font-medium text-gray-700">Nama Brand <span class="text-red-500">*</span></label>
                    <input type="text" name="performa" id="performa" value="{{ $product->performa }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                </div>

                {{-- === TAMBAHAN KRITERIA === --}}
                <div>
                    <label for="kriteria" class="block text-sm font-medium text-gray-700">Kriteria <span class="text-red-500">*</span></label>
                    <select name="kriteria" id="kriteria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                        <option value="">-- Pilih Kriteria --</option>
                        <option value="Exterior" {{ $product->kriteria == 'Exterior' ? 'selected' : '' }}>Exterior</option>
                        <option value="Interior" {{ $product->kriteria == 'Interior' ? 'selected' : '' }}>Interior</option>
                    </select>
                </div>

                <div>
                    <label for="hasil_akhir" class="block text-sm font-medium text-gray-700">Hasil Akhir <span class="text-red-500">*</span></label>
                    <select name="hasil_akhir" id="hasil_akhir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                        <option value="">-- Pilih Hasil Akhir --</option>
                        <option value="Gloss/Kilap" {{ $product->hasil_akhir == 'Gloss/Kilap' ? 'selected' : '' }}>Gloss/Kilap</option>
                        <option value="Matt/Doff" {{ $product->hasil_akhir == 'Matt/Doff' ? 'selected' : '' }}>Matt/Doff</option>
                        <option value="SemiGloss/SemiKilap" {{ $product->hasil_akhir == 'SemiGloss/SemiKilap' ? 'selected' : '' }}>SemiGloss/SemiKilap</option>
                    </select>
                </div>

                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga" id="harga" value="{{ $product->harga }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800" required>
                    <p class="text-xs text-gray-500 mt-1">Harga acuan utama atau harga per m².</p>
                </div>

                {{-- === PILIHAN UKURAN KEMASAN & INFORMASI TOTAL STOK === --}}
                <div class="border-t border-gray-200 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <label class="block text-sm font-bold text-gray-800">Pilihan Ukuran Kemasan</label>
                            <p class="text-xs text-gray-500 mt-0.5">Kelola varian ukuran kemasan produk. Stok tidak diinput di sini karena dikelola melalui bagian gudang.</p>
                        </div>
                        <span class="text-xs text-indigo-600 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full font-medium">Bisa Lebih dari 1</span>
                    </div>

                    {{-- Banner Total Stok Produk (Read-Only) --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3.5 mb-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <span class="text-xs text-blue-700 uppercase tracking-wider font-semibold block">Total Stok Produk Saat Ini</span>
                                <span class="text-xl font-extrabold text-blue-900">{{ $variants->sum('stok') }} Unit</span>
                            </div>
                        </div>
                        <span class="text-xs text-blue-700 bg-white border border-blue-200 px-3 py-1 rounded-full font-medium shadow-2xs">
                            Tersedia di Gudang
                        </span>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <div class="hidden md:grid md:grid-cols-12 gap-2 text-xs font-semibold text-gray-600 uppercase mb-2 px-1">
                            <div class="md:col-span-5">Ukuran Kemasan <span class="text-red-500">*</span></div>
                            <div class="md:col-span-4">Harga Khusus (Rp) <span class="text-gray-400 font-normal">(opsional)</span></div>
                            <div class="md:col-span-2 text-center">Stok Unit</div>
                            <div class="md:col-span-1 text-center">Aksi</div>
                        </div>

                        @php
                            $sizeVariants = isset($variants) ? $variants->filter(fn($v) => !empty($v->ukuran))->values() : collect();
                        @endphp

                        <div id="variants-container" class="space-y-2.5">
                            @forelse($sizeVariants as $idx => $v)
                            <div class="variant-row bg-white p-2.5 rounded-md border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                                <div class="md:col-span-5">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Ukuran Kemasan</label>
                                    <input type="text" name="variants[{{ $idx }}][ukuran]" value="{{ $v->ukuran }}" placeholder="Contoh: 3.5L atau 18L" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Harga Khusus (Opsional)</label>
                                    <input type="number" name="variants[{{ $idx }}][harga]" value="{{ $v->harga }}" placeholder="Sama dg harga dasar" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                                </div>
                                <div class="md:col-span-2 text-center">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Stok Unit</label>
                                    <span class="bg-blue-50 text-blue-800 border border-blue-200 text-xs font-bold px-2.5 py-1.5 rounded-md inline-block w-full" title="Stok saat ini di gudang">
                                        {{ $v->stok ?? 0 }} Unit
                                    </span>
                                </div>
                                <div class="md:col-span-1 text-right md:text-center">
                                    <button type="button" class="btn-remove-variant px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded text-xs font-semibold transition" title="Hapus varian ini">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="variant-row bg-white p-2.5 rounded-md border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                                <div class="md:col-span-5">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Ukuran Kemasan</label>
                                    <input type="text" name="variants[0][ukuran]" placeholder="Contoh: 3.5L atau 18L" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Harga Khusus (Opsional)</label>
                                    <input type="number" name="variants[0][harga]" placeholder="Sama dg harga dasar" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                                </div>
                                <div class="md:col-span-2 text-center">
                                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Stok Unit</label>
                                    <span class="bg-gray-100 text-gray-600 border border-gray-200 text-xs font-medium px-2.5 py-1.5 rounded-md inline-block w-full">
                                        {{ $product->stok ?? 0 }} Unit
                                    </span>
                                </div>
                                <div class="md:col-span-1 text-right md:text-center">
                                    <button type="button" class="btn-remove-variant px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded text-xs font-semibold transition" title="Hapus varian ini">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-200">
                            <button type="button" id="btn-add-variant" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-800 rounded-md text-xs font-bold transition border border-gray-300 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                + Tambah Ukuran Lain
                            </button>
                            <span class="text-xs text-gray-500 italic">* Kosongkan jika produk tidak memiliki variasi kemasan.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 px-6 rounded hover:bg-gray-700 transition ease-in-out duration-150">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('variants-container');
        const addBtn = document.getElementById('btn-add-variant');
        let variantIndex = {{ isset($sizeVariants) ? max($sizeVariants->count(), 1) : 1 }};

        addBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'variant-row bg-white p-2.5 rounded-md border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-2 items-center';
            row.innerHTML = `
                <div class="md:col-span-5">
                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Ukuran Kemasan</label>
                    <input type="text" name="variants[${variantIndex}][ukuran]" placeholder="Contoh: 18L atau 20L" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                </div>
                <div class="md:col-span-4">
                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Harga Khusus (Opsional)</label>
                    <input type="number" name="variants[${variantIndex}][harga]" placeholder="Sama dg harga dasar" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-800 focus:ring-gray-800 text-sm">
                </div>
                <div class="md:col-span-2 text-center">
                    <label class="block md:hidden text-xs font-medium text-gray-600 mb-1">Stok Unit</label>
                    <span class="bg-gray-100 text-gray-600 border border-gray-200 text-xs font-medium px-2.5 py-1.5 rounded-md inline-block w-full">
                        0 Unit
                    </span>
                </div>
                <div class="md:col-span-1 text-right md:text-center">
                    <button type="button" class="btn-remove-variant px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded text-xs font-semibold transition" title="Hapus varian ini">
                        Hapus
                    </button>
                </div>
            `;
            container.appendChild(row);
            row.querySelector('input').focus();
            variantIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-variant')) {
                const rows = container.querySelectorAll('.variant-row');
                if (rows.length > 1) {
                    e.target.closest('.variant-row').remove();
                } else {
                    const inputs = rows[0].querySelectorAll('input');
                    inputs.forEach(inp => inp.value = '');
                }
            }
        });
    });
</script>
@endsection
