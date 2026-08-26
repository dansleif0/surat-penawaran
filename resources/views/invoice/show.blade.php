@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4">

    <div class="max-w-4xl mx-auto mb-4 flex justify-end gap-2 print:hidden">
        <a href="{{ route('invoice.histori') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300">
            &larr; Kembali ke Histori
        </a>
        <a href="{{ route('invoice.print', $invoice->id) }}" target="_blank" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center gap-2">
            🖨️ Print Invoice (PDF)
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg border" id="invoice-print-area">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6">
            <div class="w-full">
                {{-- Menggunakan class w-full agar gambar memenuhi lebar kontainer --}}
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
            {{-- Garis merah di bawah tetap dipertahankan atau dihapus sesuai keinginan --}}
            <div class="w-full border-b-[4px] border-[#d32f2f] mt-1"></div>
        </header>

        <section class="mt-8 flex justify-between text-sm sans">
            <div class="w-1/2">
                <p class="font-bold mb-1">TO:</p>
                <p class="font-bold text-lg uppercase">{{ $invoice->nama_klien }}</p>

                @if($invoice->offer && $invoice->offer->client_details)
                <p class="text-gray-700">{{ $invoice->offer->client_details }}</p>
                @endif

                <p class="mt-4 font-bold">Attn:</p>
                <p>{{ $invoice->nama_klien }}</p>
            </div>
            <div class="w-1/2 text-right">
                <div class="flex justify-end mb-1">
                    <span class="w-24 text-left font-bold">Tanggal</span>
                    <span class="text-left">: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d F Y') }}</span>
                </div>

                {{-- NO INVOICE SESUAI DATABASE (KONSISTEN DENGAN HISTORI) --}}
                <div class="flex justify-end">
                    <span class="w-24 text-left font-bold">Invoice No.</span>
                    <span class="text-left">: {{ $invoice->no_invoice }}</span>
                </div>
            </div>
        </section>

        <section class="mt-8 text-sm">
            <p class="mb-2">Bersama ini kami sampaikan tagihan untuk:</p>
            <p><span class="font-medium w-20 inline-block">Project</span>: {{ optional($invoice->offer)->perihal ?? 'Pengecatan dan Supply Cat Jotun Paints' }}</p>
            @if($invoice->offer && $invoice->offer->client_details)
            <p><span class="font-medium w-20 inline-block">Alamat</span>: {{ $invoice->offer->client_details }}</p>
            @endif

            <table class="w-full mt-4 border-collapse border border-black">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black p-2 text-left font-semibold">No.</th>
                        <th class="border border-black p-2 text-left font-semibold">Keterangan</th>
                        <th class="border border-black p-2 text-right font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-2 text-center">1</td>
                        <td class="border border-black p-2">Total Pengecatan (sesuai Penawaran)</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->total_penawaran, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->additions as $index => $addition)
                    <tr>
                        <td class="border border-black p-2 text-center">{{ $index + 2 }}</td>
                        <td class="border border-black p-2">{{ $addition->nama_pekerjaan }}</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($addition->harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td class="border border-black p-2 h-8"></td>
                        <td class="border border-black p-2"></td>
                        <td class="border border-black p-2"></td>
                    </tr>

                    <tr class="font-medium">
                        <td colspan="2" class="border border-black p-2 text-right">TOTAL</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->total_penawaran + $invoice->total_tambahan, 0, ',', '.') }}</td>
                    </tr>

                    @if($invoice->diskon > 0)
                    <tr class="font-medium">
                        <td colspan="2" class="border border-black p-2 text-right">Diskon</td>
                        <td class="border border-black p-2 text-right text-red-600">- Rp {{ number_format($invoice->diskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr class="font-bold bg-gray-100">
                        <td colspan="2" class="border border-black p-2 text-right">TOTAL TAGIHAN</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
                    </tr>

                    @foreach($invoice->payments as $payment)
                    <tr class="font-medium text-gray-600">
                        <td colspan="2" class="border border-black p-2 text-right">{{ $payment->keterangan }}</td>
                        <td class="border border-black p-2 text-right text-green-600">- Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <tr class="font-bold text-xl bg-gray-200">
                        <td colspan="2" class="border border-black p-2 text-right">SISA PEMBAYARAN</td>
                        <td class="border border-black p-2 text-right">Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="mt-12 flex justify-between text-sm">
            <div class="text-center">
                <p>Hormat kami,</p>
                <p>PT. Tasniem Gerai Inspirasi</p>
                <div class="h-28 w-48 relative">
                    <img src="{{ asset('images/ttd.png') }}" alt="Logo & Tanda Tangan" class="h-28 opacity-100 mx-auto">
                </div>
                <p class="font-bold text-gray-800">SAMSU RIZAL</p>
                <p class="text-gray-600">General Manager</p>
            </div>
            <div class="text-left">
                <p class="font-medium">Pembayaran melalui Bank:</p>
                <p>a/n PT. Tasniem Gerai Inspirasi</p>
                <p>Bank BRI Cab. Nagoya</p>
                <p>Rek. No. 0331 - 0100 1817 306</p>
            </div>
        </section>

    </div>
</div>

<style>
    @media print {
        .print\:hidden {
            display: none;
        }

        body * {
            visibility: hidden;
        }

        #invoice-print-area,
        #invoice-print-area * {
            visibility: visible;
        }

        #invoice-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
            margin: 0;
            padding: 0.5in;
            font-size: 10pt;
        }

        /* Hindari page-break di dalam tabel */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    }
</style>
@endsection