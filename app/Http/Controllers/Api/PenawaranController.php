<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class PenawaranController extends Controller
{
    /**
     * Menampilkan daftar histori penawaran.
     * Mendukung filter: search, jenis (produk/proyek), status (finished/in_progress/expired).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $jenis  = $request->input('jenis');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 15);

        $query = Offer::query();

        // 1. Filter Pencarian (nama klien, detail, id)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_klien', 'like', '%' . $search . '%')
                  ->orWhere('client_details', 'like', '%' . $search . '%')
                  ->orWhere('id', $search);
            });
        }

        // 2. Filter Jenis Penawaran
        if ($jenis) {
            if ($jenis === 'produk') {
                $query->where('jenis_penawaran', 'produk');
            } elseif ($jenis === 'proyek') {
                $query->where(function ($q) {
                    $q->where('jenis_penawaran', 'proyek')
                      ->orWhere('jenis_penawaran', 'jasa')
                      ->orWhereNull('jenis_penawaran');
                });
            }
        }

        // 3. Filter Status (selesai / berjalan / expired)
        if ($status) {
            if ($status === 'finished') {
                $query->whereHas('invoices');
            } elseif ($status === 'expired') {
                $query->doesntHave('invoices')->where('created_at', '<', now()->subMonth());
            } elseif ($status === 'in_progress') {
                $query->doesntHave('invoices')->where('created_at', '>=', now()->subMonth());
            }
        }

        $offers = $query->with(['items', 'jasaItems', 'invoices'])
            ->latest()
            ->paginate($perPage);

        // Format response agar rapi dan mudah dikonsumsi Flutter
        $formattedData = collect($offers->items())->map(function ($offer) {
            $hasInvoice = $offer->invoices->isNotEmpty();
            $statusLabel = 'Menunggu';
            if ($hasInvoice) {
                $statusLabel = 'Selesai (Ada Invoice)';
            } elseif ($offer->created_at < now()->subMonth()) {
                $statusLabel = 'Kedaluwarsa';
            }

            return [
                'id'                 => $offer->id,
                'no_penawaran'       => $offer->id . '/SP/' . ($offer->jenis_penawaran === 'produk' ? 'PRODUK' : 'PROYEK'),
                'nama_klien'         => $offer->nama_klien,
                'client_details'     => $offer->client_details,
                'perihal'            => $offer->perihal,
                'jenis_penawaran'    => $offer->jenis_penawaran ?? 'proyek',
                'tanggal_penawaran'  => $offer->created_at ? $offer->created_at->format('Y-m-d') : null,
                'total_keseluruhan'  => (float) $offer->total_keseluruhan,
                'diskon_global'      => (float) ($offer->diskon_global ?? 0),
                'status'             => $statusLabel,
                'has_invoice'        => $hasInvoice,
                'invoices_count'     => $offer->invoices->count(),
                'total_items'        => ($offer->items ? $offer->items->count() : 0) + ($offer->jasaItems ? $offer->jasaItems->count() : 0),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Histori penawaran berhasil dimuat.',
            'data'    => $formattedData,
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'last_page'    => $offers->lastPage(),
                'per_page'     => $offers->perPage(),
                'total'        => $offers->total(),
            ],
        ]);
    }

    /**
     * Menampilkan detail lengkap satu penawaran.
     */
    public function show($id)
    {
        $offer = Offer::with(['items', 'jasaItems', 'invoices'])->find($id);

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Data penawaran tidak ditemukan.',
            ], 404);
        }

        $items = $offer->items ? $offer->items->map(function ($item) {
            return [
                'id'            => $item->id,
                'nama_produk'   => $item->nama_produk,
                'ukuran'        => $item->area_dinding ?? '-',
                'volume_qty'    => (float) $item->volume,
                'harga_satuan'  => (float) $item->harga_per_m2,
                'warna'         => $item->warna ?? '-',
                'keterangan'    => $item->keterangan ?? '-',
                'diskon'        => (float) ($item->diskon ?? ($item->diskon_nilai ?? 0)),
                'subtotal'      => ((float) $item->volume * (float) $item->harga_per_m2) - (float) ($item->diskon ?? 0),
            ];
        }) : [];

        $jasaItems = $offer->jasaItems ? $offer->jasaItems->map(function ($jasa) {
            return [
                'id'            => $jasa->id,
                'jasa_nama'     => $jasa->jasa_nama,
                'volume'        => (float) $jasa->volume,
                'satuan'        => $jasa->satuan,
                'harga_satuan'  => (float) $jasa->harga_satuan,
                'harga_jasa'    => (float) $jasa->harga_jasa,
            ];
        }) : [];

        $invoices = $offer->invoices ? $offer->invoices->map(function ($inv) {
            return [
                'id'            => $inv->id,
                'no_invoice'    => $inv->no_invoice,
                'tgl_invoice'   => $inv->tgl_invoice,
                'is_persiapan'  => (bool) $inv->is_persiapan,
                'persiapan_confirmed_at' => $inv->persiapan_confirmed_at,
            ];
        }) : [];

        return response()->json([
            'success' => true,
            'message' => 'Detail penawaran berhasil dimuat.',
            'data'    => [
                'id'                => $offer->id,
                'no_penawaran'      => $offer->id . '/SP/' . ($offer->jenis_penawaran === 'produk' ? 'PRODUK' : 'PROYEK'),
                'nama_klien'        => $offer->nama_klien,
                'client_details'    => $offer->client_details,
                'perihal'           => $offer->perihal,
                'jenis_penawaran'   => $offer->jenis_penawaran ?? 'proyek',
                'tanggal_penawaran' => $offer->created_at ? $offer->created_at->format('Y-m-d') : null,
                'total_keseluruhan' => (float) $offer->total_keseluruhan,
                'diskon_global'     => (float) ($offer->diskon_global ?? 0),
                'items'             => $items,
                'jasa_items'        => $jasaItems,
                'invoices'          => $invoices,
            ],
        ]);
    }
}

