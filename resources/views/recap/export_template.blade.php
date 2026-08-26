<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Standar Font agar rapi saat dibuka di Excel & Word */
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; vertical-align: top; }

        .header { text-align: center; margin-bottom: 20px; }
        .total { font-weight: bold; background-color: #f2f2f2; }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-gray { background-color: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h2>RINCIAN REKAP BIAYA</h2>
        <p>Klien: {{ $recap->offer->nama_klien }} | Tanggal Laporan: {{ $recap->created_at->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr class="bg-gray">
                <th style="width: 120px; text-align: center;">Tgl Keluar</th>
                <th>Deskripsi</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="text-align: right;">Harga Satuan</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recap->items as $item)
            <tr>
                <td class="text-center">
                    @if($item->tanggal_item)
                        {{ \Carbon\Carbon::parse($item->tanggal_item)->format('d/m/Y') }}
                    @elseif($item->created_at)
                        {{ $item->created_at->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    <strong>{{ $item->material }}</strong>
                    @if($item->detail)
                        <br><small style="color: #666;">{{ $item->detail }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="4" class="text-right">TOTAL PENAWARAN</td>
                <td class="text-right">{{ number_format($recap->total_penawaran_klien, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td colspan="4" class="text-right">TOTAL MODAL</td>
                <td class="text-right">{{ number_format($recap->total_pengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td colspan="4" class="text-right">NET PROFIT (LABA)</td>
                <td class="text-right" style="color: {{ $recap->margin >= 0 ? '#008000' : '#FF0000' }}">
                    {{ number_format($recap->margin, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>