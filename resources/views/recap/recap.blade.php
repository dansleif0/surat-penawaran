@extends('layouts.app')

@section('content')
<div class="container mx-auto my-8 px-4 print:my-0 print:px-0">
    {{-- Tombol Navigasi --}}
    <div class="flex justify-between items-center mb-6 print:hidden">
        <a href="{{ route('histori.index') }}" class="text-gray-600 hover:text-black flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Histori
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold shadow-sm hover:bg-blue-700 transition">
            🖨️ Cetak Rekapan
        </button>
    </div>

    {{-- Kertas Rekapan --}}
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg border border-gray-200 print:shadow-none print:border-none">
        <div class="text-center mb-10 border-b pb-6">
            <h1 class="text-2xl font-bold uppercase tracking-widest text-gray-800">Rekapan Biaya Pengerjaan</h1>
            <p class="text-gray-500 text-sm mt-1">No. Penawaran: SP-{{ $offer->created_at->format('Y') }}/{{ str_pad($offer->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10 text-sm">
            <div>
                <h4 class="font-bold text-gray-400 uppercase mb-2">Informasi Klien</h4>
                <p class="text-lg font-bold text-gray-800">{{ $offer->nama_klien }}</p>
                <p class="text-gray-600 mt-1">{{ $offer->client_details }}</p>
            </div>
            <div class="text-right">
                <h4 class="font-bold text-gray-400 uppercase mb-2">Tanggal Rekap</h4>
                <p class="text-gray-800 font-medium">{{ now()->format('d F Y') }}</p>
                <p class="text-gray-500 mt-1 italic">Dibuat Berdasarkan Penawaran Tgl: {{ $offer->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Tabel Produk --}}
        @if($offer->items->count() > 0)
        <div class="mb-8">
            <h3 class="font-bold text-gray-800 border-l-4 border-blue-600 pl-3 mb-4 uppercase text-sm tracking-wider">A. Rincian Produk & Material</h3>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                        <th class="py-3 px-2 text-left">Nama Produk</th>
                        <th class="py-3 px-2 text-left">Area</th>
                        <th class="py-3 px-2 text-center">Volume</th>
                        <th class="py-3 px-2 text-right">Harga Satuan</th>
                        <th class="py-3 px-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $subProduk = 0; @endphp
                    @foreach($offer->items as $item)
                        @php $lineTotal = $item->volume * $item->harga_per_m2; $subProduk += $lineTotal; @endphp
                        <tr>
                            <td class="py-3 px-2 font-medium">{{ $item->nama_produk }}</td>
                            <td class="py-3 px-2 text-gray-500 italic">{{ $item->area_dinding }}</td>
                            <td class="py-3 px-2 text-center">{{ number_format($item->volume, 2) }} M²</td>
                            <td class="py-3 px-2 text-right">Rp {{ number_format($item->harga_per_m2, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right font-semibold">Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                        <td colspan="4" class="py-3 px-2 text-right">Subtotal Produk</td>
                        <td class="py-3 px-2 text-right text-blue-700">Rp {{ number_format($subProduk, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        {{-- Tabel Jasa --}}
        @if($offer->jasaItems->count() > 0)
        <div class="mb-10">
            <h3 class="font-bold text-gray-800 border-l-4 border-green-600 pl-3 mb-4 uppercase text-sm tracking-wider">B. Rincian Jasa & Pengerjaan Tambahan</h3>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs border-b">
                        <th class="py-3 px-2 text-left w-2/3">Keterangan Pengerjaan</th>
                        <th class="py-3 px-2 text-right">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $subJasa = 0; @endphp
                    @foreach($offer->jasaItems as $jasa)
                        @php $subJasa += $jasa->harga_jasa; @endphp
                        <tr>
                            <td class="py-3 px-2 text-gray-700">{{ $jasa->nama_jasa }}</td>
                            <td class="py-3 px-2 text-right font-semibold">Rp {{ number_format($jasa->harga_jasa, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                        <td class="py-3 px-2 text-right">Subtotal Jasa</td>
                        <td class="py-3 px-2 text-right text-green-700">Rp {{ number_format($subJasa, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        {{-- Grand Total --}}
        <div class="border-t-4 border-gray-800 pt-6">
            <div class="flex justify-between items-center bg-gray-900 text-white p-6 rounded-lg shadow-inner">
                <div class="uppercase tracking-widest font-bold">Total Rekapan Biaya</div>
                <div class="text-3xl font-extrabold">
                    Rp {{ number_format(($subProduk ?? 0) + ($subJasa ?? 0), 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="mt-12 grid grid-cols-2 text-center text-xs text-gray-400 uppercase tracking-tighter">
            <div>
                <p class="mb-16">Admin Operasional</p>
                <p class="border-t border-gray-200 pt-2 inline-block px-8">( ____________________ )</p>
            </div>
            <div>
                <p class="mb-16">Mengetahui, Manager</p>
                <p class="border-t border-gray-200 pt-2 inline-block px-8">( ____________________ )</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white; }
    header, footer, nav, .print\:hidden { display: none !important; }
    .container { width: 100%; max-width: none; margin: 0; padding: 0; }
}
</style>
@endsection