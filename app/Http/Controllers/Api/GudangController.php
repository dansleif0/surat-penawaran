<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    /**
     * Cari produk di tabel products berdasarkan nama_produk dan ukuran kemasan (jika ada).
     */
    private function findProductVariant($namaProduk, $ukuran = null)
    {
        $namaProduk = trim($namaProduk);
        $ukuran = !empty($ukuran) && $ukuran !== '-' ? trim($ukuran) : null;

        if ($ukuran) {
            $product = Product::where('nama_produk', $namaProduk)
                ->where('ukuran', $ukuran)
                ->first();
            if (!$product) {
                $product = Product::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])
                    ->whereRaw('LOWER(TRIM(ukuran)) = ?', [strtolower($ukuran)])
                    ->first();
            }
            if (!$product) {
                $cleanUkuran = preg_replace('/\s+/', '', strtolower($ukuran));
                $product = Product::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])
                    ->whereRaw("REPLACE(LOWER(TRIM(COALESCE(ukuran, ''))), ' ', '') = ?", [$cleanUkuran])
                    ->first();
            }
            if ($product) {
                return $product;
            }
        }

        // Fallback: cari berdasarkan nama_produk saja
        $product = Product::where('nama_produk', $namaProduk)->first();
        if (!$product) {
            $product = Product::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])->first();
        }
        return $product;
    }

    /**
     * Daftar invoice yang perlu dipersiapkan oleh staf gudang.
     */
    public function indexPersiapan(Request $request)
    {
        $invoices = Invoice::where('is_persiapan', true)
            ->whereNull('persiapan_confirmed_at')
            ->with(['offer.items'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'no_invoice' => $invoice->no_invoice,
                    'tgl_invoice' => $invoice->tgl_invoice,
                    'nama_client' => $invoice->offer->nama_client ?? '-',
                    'nama_proyek' => $invoice->offer->nama_proyek ?? '-',
                    'is_persiapan' => (bool) $invoice->is_persiapan,
                    'persiapan_at' => $invoice->persiapan_at,
                    'persiapan_confirmed_at' => $invoice->persiapan_confirmed_at,
                    'items_count' => $invoice->offer && $invoice->offer->items ? $invoice->offer->items->count() : 0,
                    'items' => $invoice->offer && $invoice->offer->items ? $invoice->offer->items->map(function ($item) {
                        $prod = $this->findProductVariant($item->nama_produk, $item->area_dinding);
                        return [
                            'id' => $item->id,
                            'nama_produk' => $item->nama_produk,
                            'ukuran' => $item->area_dinding ?? '-',
                            'volume' => (float) $item->volume,
                            'satuan' => $item->satuan,
                            'warna' => $item->warna,
                            'keterangan' => $item->keterangan,
                            'stok_tersedia' => $prod ? $prod->stok : null,
                            'terdaftar_di_master' => $prod !== null,
                        ];
                    }) : [],
                ];
            });

        return response()->json([
            'success' => true,
            'total' => $invoices->count(),
            'data' => $invoices,
        ]);
    }

    /**
     * Tandai invoice ke status "Persiapkan" (Bisa via API oleh Admin).
     */
    public function buatPersiapan(Request $request, Invoice $invoice)
    {
        if (!in_array($request->user()->role, ['admin', 'staff_gudang'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Admin atau Staff Gudang yang dapat menandai invoice untuk dipersiapkan.',
            ], 403);
        }

        if ($invoice->is_persiapan && $invoice->persiapan_confirmed_at === null) {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} sudah dalam status persiapan.",
            ], 400);
        }

        if ($invoice->persiapan_confirmed_at !== null) {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} sudah pernah dikonfirmasi sebelumnya.",
            ], 400);
        }

        if ($invoice->jenis !== 'produk') {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} bukan merupakan invoice produk. Hanya invoice produk yang dapat dipersiapkan untuk gudang.",
            ], 422);
        }

        // 1. Validasi produk terdaftar di data master harga produk
        $invoice->load(['offer.items']);
        if (!$invoice->offer || !$invoice->offer->items || $invoice->offer->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} tidak memiliki item produk untuk dipersiapkan.",
            ], 422);
        }

        $produkTidakTerdaftar = [];
        $hasValidProduct = false;

        foreach ($invoice->offer->items as $item) {
            if (empty($item->nama_produk)) continue;
            $qty = (int) round((float) ($item->volume ?? 0));
            if ($qty <= 0) continue;

            $hasValidProduct = true;
            $namaProduk = trim($item->nama_produk);
            $product = $this->findProductVariant($namaProduk, $item->area_dinding);

            if (!$product) {
                $produkTidakTerdaftar[] = "Produk '{$namaProduk}'" . (!empty($item->area_dinding) && $item->area_dinding !== '-' ? " (Ukuran: {$item->area_dinding})" : '') . " tidak terdaftar di Data Master Harga Produk.";
            }
        }

        if (!$hasValidProduct) {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} tidak memiliki item produk valid untuk dipersiapkan.",
            ], 422);
        }

        if (!empty($produkTidakTerdaftar)) {
            return response()->json([
                'success' => false,
                'message' => "Invoice {$invoice->no_invoice} tidak dapat dipersiapkan karena terdapat produk yang tidak terdaftar di Data Master Harga Produk.",
                'errors' => array_values(array_unique($produkTidakTerdaftar)),
            ], 422);
        }

        // 2. Validasi stok produk mencukupi sebelum status dibuat
        $stokKurangErrors = [];
        $kebutuhan = [];

        foreach ($invoice->offer->items as $item) {
            if (empty($item->nama_produk)) continue;
            $qty = (int) round((float) ($item->volume ?? 0));
            if ($qty <= 0) continue;

            $product = $this->findProductVariant($item->nama_produk, $item->area_dinding);
            if ($product) {
                $pid = $product->id;
                if (!isset($kebutuhan[$pid])) {
                    $label = $product->nama_produk . (!empty($product->ukuran) ? " ({$product->ukuran})" : '');
                    $kebutuhan[$pid] = [
                        'product' => $product,
                        'label'   => $label,
                        'qty'     => 0,
                    ];
                }
                $kebutuhan[$pid]['qty'] += $qty;
            }
        }

        foreach ($kebutuhan as $info) {
            if ($info['product']->stok < $info['qty']) {
                $stokKurangErrors[] = "Stok '{$info['label']}' tidak mencukupi. Butuh: {$info['qty']}, Tersedia: {$info['product']->stok}.";
            }
        }

        if (!empty($stokKurangErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak dapat dipersiapkan karena stok produk kurang.',
                'errors' => $stokKurangErrors,
            ], 422);
        }

        $invoice->update([
            'is_persiapan' => true,
            'persiapan_at' => now(),
            'persiapan_confirmed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Invoice {$invoice->no_invoice} berhasil ditandai sebagai 'Persiapkan'.",
            'data' => [
                'id' => $invoice->id,
                'no_invoice' => $invoice->no_invoice,
                'is_persiapan' => true,
                'persiapan_at' => $invoice->persiapan_at,
            ],
        ]);
    }

    /**
     * Detail satu invoice persiapan.
     */
    public function showPersiapan(Invoice $invoice)
    {
        $invoice->load(['offer.items']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $invoice->id,
                'no_invoice' => $invoice->no_invoice,
                'tgl_invoice' => $invoice->tgl_invoice,
                'nama_client' => $invoice->offer->nama_client ?? '-',
                'nama_proyek' => $invoice->offer->nama_proyek ?? '-',
                'is_persiapan' => (bool) $invoice->is_persiapan,
                'persiapan_at' => $invoice->persiapan_at,
                'persiapan_confirmed_at' => $invoice->persiapan_confirmed_at,
                'items' => $invoice->offer && $invoice->offer->items ? $invoice->offer->items->map(function ($item) {
                    $prod = $this->findProductVariant($item->nama_produk, $item->area_dinding);
                    return [
                        'id' => $item->id,
                        'nama_produk' => $item->nama_produk,
                        'ukuran' => $item->area_dinding ?? '-',
                        'volume' => (float) $item->volume,
                        'satuan' => $item->satuan,
                        'warna' => $item->warna,
                        'keterangan' => $item->keterangan,
                        'stok_gudang_saat_ini' => $prod ? $prod->stok : null,
                        'terdaftar_di_master' => $prod !== null,
                    ];
                }) : [],
            ],
        ]);
    }

    /**
     * Konfirmasi persiapan barang oleh Staff Gudang (potong stok produk).
     */
    public function konfirmasiPersiapan(Request $request, Invoice $invoice)
    {
        // Hanya staff gudang atau admin
        if (!in_array($request->user()->role, ['staff_gudang', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Staff Gudang yang berhak mengonfirmasi persiapan barang.',
            ], 403);
        }

        if ($invoice->persiapan_confirmed_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice ini sudah pernah dikonfirmasi sebelumnya.',
            ], 400);
        }

        if (!$invoice->is_persiapan) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice ini tidak dalam status persiapan.',
            ], 400);
        }

        $perubahanStok = [];

        DB::transaction(function () use ($invoice, &$perubahanStok) {
            if (!$invoice->relationLoaded('offer')) {
                $invoice->load(['offer.items']);
            }

            if ($invoice->offer && $invoice->offer->items) {
                foreach ($invoice->offer->items as $item) {
                    if (empty($item->nama_produk)) continue;
                    $qty = (int) round((float) ($item->volume ?? 0));
                    if ($qty <= 0) continue;

                    $product = $this->findProductVariant($item->nama_produk, $item->area_dinding);

                    if ($product) {
                        $stokAwal = $product->stok;
                        $product->decrement('stok', $qty);
                        $product->refresh();

                        $perubahanStok[] = [
                            'product_id' => $product->id,
                            'nama_produk' => $product->nama_produk,
                            'ukuran' => $product->ukuran ?? '-',
                            'qty_dipotong' => $qty,
                            'stok_awal' => $stokAwal,
                            'stok_akhir' => $product->stok,
                            'status' => 'berhasil_dipotong',
                        ];
                    } else {
                        $perubahanStok[] = [
                            'product_id' => null,
                            'nama_produk' => trim($item->nama_produk),
                            'ukuran' => $item->area_dinding ?? '-',
                            'qty_dipotong' => 0,
                            'stok_awal' => null,
                            'stok_akhir' => null,
                            'status' => 'produk_tidak_terdaftar_di_master',
                        ];
                    }
                }
            }

            $invoice->update([
                'is_persiapan' => false,
                'persiapan_confirmed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Persiapan invoice {$invoice->no_invoice} berhasil dikonfirmasi dan stok produk telah otomatis dipotong.",
            'data' => [
                'invoice_id' => $invoice->id,
                'no_invoice' => $invoice->no_invoice,
                'persiapan_confirmed_at' => $invoice->persiapan_confirmed_at,
                'perubahan_stok' => $perubahanStok,
            ],
        ]);
    }

    /**
     * Daftar stok produk di gudang.
     */
    public function produk(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('nama_produk', 'asc')->get();

        return response()->json([
            'success' => true,
            'total' => $products->count(),
            'data' => $products,
        ]);
    }
}
