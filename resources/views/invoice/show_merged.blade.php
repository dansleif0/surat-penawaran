@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">

    <div class="max-w-4xl mx-auto mb-4 flex justify-end gap-2 print:hidden">
        <a href="{{ route('invoice.histori') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300 transition">
            &larr; Kembali ke Histori
        </a>
        <a href="{{ route('invoice.print', $invoice->id) }}" target="_blank" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition shadow-sm inline-flex items-center gap-2">
            🖨️ Print Piutang Statement (PDF)
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg border border-gray-200" id="statement-print-area">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-4">
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- SUB HEADER & CLIENT BOX --}}
        <section class="mt-6 flex justify-between items-start text-sm font-sans">
            <div class="w-1/2 pr-4">
                <h2 class="text-xl font-extrabold underline tracking-wide text-black">PIUTANG STATEMENT</h2>
                <p class="font-bold text-sm mt-2 text-black">
                    ON : {{ \Carbon\Carbon::parse($invoice->created_at)->translatedFormat('d F Y') }}
                </p>
                <p class="text-[11px] text-gray-600 tracking-wider mt-1">CR, RTC, DN, CN, DP</p>
            </div>

            <div class="w-1/2 flex justify-end">
                <div class="border border-black p-3 rounded-none w-full max-w-sm text-xs leading-relaxed">
                    <p class="font-semibold text-black mb-1">To :</p>
                    <p class="font-bold text-sm uppercase text-black">{{ $invoice->nama_klien }}</p>
                    @if($invoice->offer && $invoice->offer->client_details)
                        <p class="text-black">{{ $invoice->offer->client_details }}</p>
                    @endif
                    <p class="text-black mt-1 font-medium">Phone : {{ optional($invoice->offer)->client_details ? '' : '-' }}</p>
                </div>
            </div>
        </section>

        {{-- TABEL PIUTANG STATEMENT --}}
        <section class="mt-6">
            <table class="w-full text-xs border-collapse border border-black text-black">
                <thead>
                    <tr class="border-b border-black text-center font-bold uppercase bg-gray-50">
                        <th class="border border-black p-2 w-[14%]">DATE</th>
                        <th class="border border-black p-2 w-[14%]">DUE DATE</th>
                        <th class="border border-black p-2 w-[22%]">INVOICE</th>
                        <th class="border border-black p-2 w-[10%]">KET</th>
                        <th class="border border-black p-2 w-[20%] text-right">DEBIT</th>
                        <th class="border border-black p-2 w-[20%] text-right">CREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningCredit = 0; @endphp
                    @forelse ($invoice->mergedItems as $item)
                        @php $runningCredit += $item->debit; @endphp
                        <tr class="text-center font-medium hover:bg-gray-50">
                            <td class="border border-black p-2 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                            </td>
                            <td class="border border-black p-2 whitespace-nowrap">
                                {{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="border border-black p-2 font-semibold">
                                {{ $item->invoice_no }}
                            </td>
                            <td class="border border-black p-2">
                                {{ $item->keterangan ?? $item->currency ?? '-' }}
                            </td>
                            <td class="border border-black p-2 text-right font-semibold">
                                {{ number_format($item->debit, 0, ',', '.') }}
                            </td>
                            <td class="border border-black p-2 text-right font-semibold">
                                {{ number_format($runningCredit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border border-black p-4 text-center text-gray-500 italic">
                                Belum ada item data invoice yang digabungkan.
                            </td>
                        </tr>
                    @endforelse

                    {{-- ROW GRAND TOTAL --}}
                    <tr class="font-bold border-t-2 border-black">
                        <td colspan="5" class="border border-black p-2 text-right text-sm">
                            GRAND TOTAL
                        </td>
                        <td class="border border-black p-2 text-right text-sm">
                            {{ number_format($runningCredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        {{-- FOOTER SIGNATURE & BANK INFO --}}
        <section class="mt-8 flex justify-between items-end text-xs font-sans text-black">
            {{-- Bank & Received By --}}
            <div class="w-1/2">
                <p class="font-bold tracking-wide">BANKERS PT. TASNIEM GERAI INSPIRASI</p>
                <p class="font-semibold text-gray-800">BANK BRI (IDR) : 033101001817306</p>
                <p class="font-semibold text-gray-800">BANK MANDIRI (IDR) : 1090080002223</p>

                <div class="mt-12">
                    <p class="font-medium">Received By,</p>
                    <div class="h-16"></div>
                    <div class="w-56 border-b border-black"></div>
                </div>
            </div>

            {{-- Signature & Stamp --}}
            <div class="w-1/2 text-center">
                <p class="font-medium">Yours Faithfully,</p>
                <div class="my-1 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan & Stempel" class="h-20 mx-auto object-contain">
                </div>
                <p class="font-bold text-sm">Samsu Rizal</p>
                <div class="w-56 border-b border-black mx-auto my-0.5"></div>
                <p class="font-bold">PT. TASNIEM GERAI INSPIRASI</p>
            </div>
        </section>

    </div>
</div>
@endsection
