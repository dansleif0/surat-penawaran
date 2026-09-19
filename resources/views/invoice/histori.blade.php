@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">
    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                Histori Invoice
            </h1>
            <div class="flex items-center gap-3">
                <button type="button" id="btn-gabung-invoice" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Gabung Invoice
                </button>
                <a href="{{ route('invoice.create') }}" class="bg-gray-800 text-white font-bold py-2 px-4 rounded hover:bg-gray-700 transition shadow-sm">
                    + Buat Invoice Baru
                </a>
            </div>
        </div>

        {{-- Kartu Pencarian & Filter Invoice Gabungan --}}
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4 mb-6">
            <form action="{{ route('invoice.histori') }}" method="GET" class="flex flex-col md:flex-row items-end justify-between gap-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full md:w-3/4">
                    {{-- Input Pencarian --}}
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Pencarian</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" placeholder="Cari No. Invoice, Nama Klien, atau Surat Penawaran..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors" value="{{ $search ?? '' }}">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Filter Invoice Gabungan --}}
                    <div>
                        <label for="status_gabungan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Filter Tipe Invoice</label>
                        <select name="status_gabungan" id="status_gabungan" onchange="this.form.submit()" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 transition-colors bg-white">
                            <option value="">Semua Invoice</option>
                            <option value="single" {{ ($status_gabungan ?? '') == 'single' ? 'selected' : '' }}>Invoice Biasa (Single)</option>
                            <option value="merge" {{ ($status_gabungan ?? '') == 'merge' ? 'selected' : '' }}>Invoice Gabungan (Merge)</option>
                        </select>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                    @if(request('search') || request('status_gabungan'))
                        <a href="{{ route('invoice.histori') }}" class="px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Cari / Apply
                    </button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg border border-gray-200 relative overflow-visible">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-gray-800">
                    <tr>
                        <th scope="col" class="px-4 py-3 rounded-tl-lg text-center w-12">
                            <input type="checkbox" id="select-all-invoices" title="Pilih Semua" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th scope="col" class="px-6 py-3">Tanggal Invoice</th>
                        <th scope="col" class="px-6 py-3">No. Invoice</th>
                        <th scope="col" class="px-6 py-3">Nama Klien</th>
                        <th scope="col" class="px-6 py-3">No. Surat Penawaran</th>
                        <th scope="col" class="px-6 py-3 text-right">Total Tagihan</th>
                        <th scope="col" class="px-6 py-3 text-center rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $index => $invoice)
                    <tr class="bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <input type="checkbox" value="{{ $invoice->id }}" class="invoice-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $invoice->created_at->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span>{{ $invoice->no_invoice }}</span>
                                @if($invoice->status === 'merge')
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-0.5 rounded border border-purple-300 tracking-wider">
                                        MERGE
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $invoice->nama_klien }}
                        </td>

                        {{-- No Surat Penawaran --}}
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-xs">
                            @if($invoice->offer)
                                @php
                                    $bulanRomawi = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
                                    $tglOffer = $invoice->offer->created_at;
                                    $romawi = $bulanRomawi[$tglOffer->format('n')];
                                    $tahun = $tglOffer->format('Y');
                                @endphp
                                <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded-full border border-gray-300">
                                    00{{ $invoice->offer->id }}/SP/TGI-1/{{ $romawi }}/{{ $tahun }}
                                </span>
                            @else
                                <span class="text-red-500 italic">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap font-bold text-green-600">
                            Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                        </td>

                        {{-- Action Dropdown --}}
                        <td class="px-6 py-4 text-center">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button type="button" @click="open = !open" @click.away="open = false" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
                                    Options
                                    <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open"
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50
                                            {{ $index >= count($invoices) - 2 ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right' }}">

                                    <div class="py-1 text-left" role="menu">
                                        <a href="{{ route('invoice.show', $invoice->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700" role="menuitem">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat Detail
                                        </a>

                                        <a href="{{ route('invoice.print', $invoice->id) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700" role="menuitem">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Print Invoice
                                        </a>

                                        <a href="{{ route('invoice.print_surat_jalan', $invoice->id) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700" role="menuitem">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Print Surat Jalan
                                        </a>

                                        <a href="{{ route('invoice.edit', $invoice->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700" role="menuitem">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>

                                        <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus invoice ini? Data tidak bisa dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-800" role="menuitem">
                                                <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">Belum ada data invoice.</p>
                                <p class="text-sm">Silakan buat invoice baru dari menu di atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FORM TERSEMBUNYI UNTUK SUBMIT GABUNG INVOICE -->
        <form id="combine-invoice-form" action="{{ route('invoice.combine_form') }}" method="POST" class="hidden">
            @csrf
            <div id="hidden-inputs-container"></div>
        </form>

        <div class="mt-6">
            {{ $invoices->appends(request()->query())->links() }}
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-invoices');
    const invoiceCheckboxes = document.querySelectorAll('.invoice-checkbox');
    const btnGabung = document.getElementById('btn-gabung-invoice');
    const combineForm = document.getElementById('combine-invoice-form');
    const hiddenContainer = document.getElementById('hidden-inputs-container');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            invoiceCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    if (btnGabung && combineForm) {
        btnGabung.addEventListener('click', function(e) {
            const selectedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked');
            if (selectedCheckboxes.length < 2) {
                alert('Pilih minimal 2 invoice yang ingin digabungkan!');
                return;
            }

            hiddenContainer.innerHTML = '';
            selectedCheckboxes.forEach(cb => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_invoices[]';
                hiddenInput.value = cb.value;
                hiddenContainer.appendChild(hiddenInput);
            });

            combineForm.submit();
        });
    }
});
</script>
@endsection