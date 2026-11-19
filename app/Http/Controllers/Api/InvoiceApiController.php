<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    /**
     * Menampilkan daftar histori invoice.
     * Mendukung filter: search, jenis (produk/proyek), status (single/merge), persiapan (semua/belum/selesai).
     */
    public function index(Request $request)
    {
        $search          = $request->input('search');
        $jenis           = $request->input('jenis') ?? $request->input('tipe_invoice');
        $statusGabungan  = $request->input('status_gabungan') ?? $request->input('status');
        $persiapanStatus = $request->input('persiapan_status'); // 'waiting', 'confirmed', 'none'
        $perPage         = (int) $request->input('per_page', 15);

        $query = Invoice::with(['offer.items']);

        // 1. Filter Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_klien', 'like', '%' . $search . '%')
                  ->orWhere('no_invoice', 'like', '%' . $search . '%')
                  ->orWhereHas('offer', function ($o) use ($search) {
                      $o->where('nama_klien', 'like', '%' . $search . '%')
                        ->orWhere('id', $search);
                  });
            });
        }

        // 2. Filter Jenis Invoice (produk / proyek)
        if ($jenis) {
            if ($jenis === 'produk') {
                $query->whereHas('offer', function ($q) {
                    $q->where('jenis_penawaran', 'produk');
                });
            } elseif ($jenis === 'proyek') {
                $query->whereHas('offer', function ($q) {
                    $q->where('jenis_penawaran', 'proyek')
                      ->orWhere('jenis_penawaran', 'jasa')
                      ->orWhereNull('jenis_penawaran');
                });
            }
        }

        // 3. Filter Status Gabungan (single / merge)
        if ($statusGabungan) {
            if ($statusGabungan === 'merge') {
                $query->where('status', 'merge');
            } elseif ($statusGabungan === 'single') {
                $query->where(function ($q) {
                    $q->where('status', 'single')
                      ->orWhereNull('status');
                });
            }
        }

        // 4. Filter Status Persiapan Gudang
        if ($persiapanStatus) {
            if ($persiapanStatus === 'waiting') {
                $query->where('is_persiapan', true)->whereNull('persiapan_confirmed_at');
            } elseif ($persiapanStatus === 'confirmed') {
                $query->whereNotNull('persiapan_confirmed_at');
            } elseif ($persiapanStatus === 'none') {
                $query->where('is_persiapan', false)->whereNull('persiapan_confirmed_at');
            }
        }

        $invoices = $query->latest()->paginate($perPage);

        $formattedData = collect($invoices->items())->map(function ($inv) {
            $statusPersiapan = 'Belum Dipersiapkan';
            if ($inv->is_persiapan && $inv->persiapan_confirmed_at === null) {
                $statusPersiapan = 'Sedang Dipersiapkan Gudang';
            } elseif ($inv->persiapan_confirmed_at !== null) {
                $statusPersiapan = 'Selesai Dipersiapkan';
            }

            return [
                'id'                    => $inv->id,
                'no_invoice'            => $inv->no_invoice,
                'tgl_invoice'           => $inv->tgl_invoice,
                'nama_klien'            => $inv->nama_klien,
                'client_details'        => $inv->client_details,
                'perihal'               => $inv->perihal,
                'jenis_invoice'         => $inv->jenis, // 'produk' atau 'proyek'
                'status_gabungan'       => $inv->status ?? 'single',
                'is_persiapan'          => (bool) $inv->is_persiapan,
                'persiapan_at'          => $inv->persiapan_at,
                'persiapan_confirmed_at'=> $inv->persiapan_confirmed_at,
                'status_persiapan'      => $statusPersiapan,
                'subtotal'              => (float) ($inv->subtotal ?? 0),
                'grand_total'           => (float) ($inv->grand_total ?? 0),
                'items_count'           => $inv->offer && $inv->offer->items ? $inv->offer->items->count() : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Histori invoice berhasil dimuat.',
            'data'    => $formattedData,
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'per_page'     => $invoices->perPage(),
                'total'        => $invoices->total(),
            ],
        ]);
    }

    /**
     * Menampilkan detail lengkap satu invoice.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['offer.items', 'additions', 'payments'])->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Data invoice tidak ditemukan.',
            ], 404);
        }

        $items = $invoice->offer && $invoice->offer->items ? $invoice->offer->items->map(function ($item) {
            return [
                'id'            => $item->id,
                'nama_produk'   => $item->nama_produk,
                'ukuran'        => $item->area_dinding ?? '-',
                'volume_qty'    => (float) $item->volume,
                'harga_satuan'  => (float) $item->harga_per_m2,
                'warna'         => $item->warna ?? '-',
                'keterangan'    => $item->keterangan ?? '-',
                'diskon'        => (float) ($item->diskon ?? 0),
                'subtotal'      => ((float) $item->volume * (float) $item->harga_per_m2) - (float) ($item->diskon ?? 0),
            ];
        }) : [];

        $statusPersiapan = 'Belum Dipersiapkan';
        if ($invoice->is_persiapan && $invoice->persiapan_confirmed_at === null) {
            $statusPersiapan = 'Sedang Dipersiapkan Gudang';
        } elseif ($invoice->persiapan_confirmed_at !== null) {
            $statusPersiapan = 'Selesai Dipersiapkan';
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail invoice berhasil dimuat.',
            'data'    => [
                'id'                    => $invoice->id,
                'no_invoice'            => $invoice->no_invoice,
                'tgl_invoice'           => $invoice->tgl_invoice,
                'nama_klien'            => $invoice->nama_klien,
                'client_details'        => $invoice->client_details,
                'perihal'               => $invoice->perihal,
                'jenis_invoice'         => $invoice->jenis,
                'status_gabungan'       => $invoice->status ?? 'single',
                'is_persiapan'          => (bool) $invoice->is_persiapan,
                'persiapan_at'          => $invoice->persiapan_at,
                'persiapan_confirmed_at'=> $invoice->persiapan_confirmed_at,
                'status_persiapan'      => $statusPersiapan,
                'subtotal'              => (float) ($invoice->subtotal ?? 0),
                'grand_total'           => (float) ($invoice->grand_total ?? 0),
                'offer'                 => [
                    'id'                => $invoice->offer ? $invoice->offer->id : null,
                    'perihal'           => $invoice->offer ? $invoice->offer->perihal : null,
                    'jenis_penawaran'   => $invoice->offer ? $invoice->offer->jenis_penawaran : null,
                ],
                'items'                 => $items,
                'payments'              => $invoice->payments ?? [],
                'additions'             => $invoice->additions ?? [],
            ],
        ]);
    }
}

