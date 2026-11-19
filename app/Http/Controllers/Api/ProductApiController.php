<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Menampilkan daftar data produk dan stoknya.
     * 
     * Query Parameters:
     * - search: kata kunci pencarian (nama_produk, brand/performa, kriteria, hasil_akhir, ukuran)
     * - brand: filter nama brand
     * - kriteria: filter Exterior / Interior
     * - hasil_akhir: filter Gloss / Matt / SemiGloss
     * - grouped: boolean (default true) -> mengelompokkan produk dengan nama sama dan menampilkan rincian varian ukurannya
     * - per_page: integer (default 20, atau 'all' untuk semua data)
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // 1. Filter Pencarian Kata Kunci
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('performa', 'like', "%{$search}%")
                  ->orWhere('hasil_akhir', 'like', "%{$search}%")
                  ->orWhere('kriteria', 'like', "%{$search}%")
                  ->orWhere('ukuran', 'like', "%{$search}%");
            });
        }

        // 2. Filter Dropdown
        if ($request->filled('kriteria')) {
            $query->where('kriteria', $request->kriteria);
        }

        if ($request->filled('brand')) {
            $query->where('performa', $request->brand);
        }

        if ($request->filled('hasil_akhir')) {
            $query->where('hasil_akhir', $request->hasil_akhir);
        }

        $isGrouped = $request->boolean('grouped', true);
        $perPage   = $request->input('per_page', 20);

        // Jika mode GROUPED (default): Kelompokkan per nama_produk seperti halaman Daftar Harga web
        if ($isGrouped) {
            $rawProducts = $query->orderBy('nama_produk', 'asc')->get();

            $grouped = $rawProducts->groupBy(function ($item) {
                return strtolower(trim($item->nama_produk));
            })->map(function ($variants) {
                $first = $variants->first();
                $minHarga = $variants->min('harga') ?? $first->harga;
                $maxHarga = $variants->max('harga') ?? $first->harga;

                return [
                    'id'              => $first->id,
                    'nama_produk'     => $first->nama_produk,
                    'brand'           => $first->performa,
                    'kriteria'        => $first->kriteria,
                    'hasil_akhir'     => $first->hasil_akhir,
                    'total_stok'      => (int) $variants->sum('stok'),
                    'harga'           => (int) $first->harga,
                    'harga_terendah'  => (int) $minHarga,
                    'harga_tertinggi' => (int) $maxHarga,
                    'is_multi_harga'  => $minHarga !== $maxHarga,
                    'total_varian'    => $variants->count(),
                    'variants'        => $variants->map(function ($v) {
                        return [
                            'id'     => $v->id,
                            'ukuran' => $v->ukuran ?? 'Umum',
                            'stok'   => (int) $v->stok,
                            'harga'  => (int) $v->harga,
                        ];
                    })->values(),
                ];
            })->values();

            // Handle Pagination manual jika diperlukan
            if ($perPage !== 'all' && is_numeric($perPage) && (int)$perPage > 0) {
                $page = (int) $request->input('page', 1);
                $total = $grouped->count();
                $pagedData = $grouped->forPage($page, (int)$perPage)->values();

                return response()->json([
                    'success' => true,
                    'message' => 'Data produk berhasil dimuat (mode grup).',
                    'total'   => $total,
                    'data'    => $pagedData,
                    'pagination' => [
                        'current_page' => $page,
                        'last_page'    => (int) ceil($total / (int)$perPage),
                        'per_page'     => (int) $perPage,
                        'total'        => $total,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil dimuat (mode grup).',
                'total'   => $grouped->count(),
                'data'    => $grouped,
            ]);
        }

        // Mode FLAT: List per masing-masing baris produk / varian ukuran di database
        if ($perPage === 'all') {
            $products = $query->orderBy('nama_produk', 'asc')->get();
            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil dimuat (mode flat).',
                'total'   => $products->count(),
                'data'    => $products->map(fn($p) => self::formatFlatProduct($p)),
            ]);
        }

        $paginated = $query->orderBy('nama_produk', 'asc')->paginate((int) $perPage);
        $formattedItems = collect($paginated->items())->map(fn($p) => self::formatFlatProduct($p));

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil dimuat (mode flat).',
            'data'    => $formattedItems,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    /**
     * Menampilkan detail satu produk berdasarkan ID baris.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        // Ambil semua varian ukuran dari produk yang sama jika ada
        $allVariants = Product::where('nama_produk', $product->nama_produk)
            ->orWhereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower(trim($product->nama_produk))])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil dimuat.',
            'data'    => [
                'id'          => $product->id,
                'nama_produk' => $product->nama_produk,
                'brand'       => $product->performa,
                'kriteria'    => $product->kriteria,
                'hasil_akhir' => $product->hasil_akhir,
                'ukuran'      => $product->ukuran ?? 'Umum',
                'stok'        => (int) $product->stok,
                'harga'       => (int) $product->harga,
                'total_stok_semua_varian' => (int) $allVariants->sum('stok'),
                'semua_varian' => $allVariants->map(function ($v) {
                    return [
                        'id'     => $v->id,
                        'ukuran' => $v->ukuran ?? 'Umum',
                        'stok'   => (int) $v->stok,
                        'harga'  => (int) $v->harga,
                    ];
                })->values(),
            ],
        ]);
    }

    private static function formatFlatProduct($p)
    {
        return [
            'id'          => $p->id,
            'nama_produk' => $p->nama_produk,
            'brand'       => $p->performa,
            'kriteria'    => $p->kriteria,
            'hasil_akhir' => $p->hasil_akhir,
            'ukuran'      => $p->ukuran ?? 'Umum',
            'stok'        => (int) $p->stok,
            'harga'       => (int) $p->harga,
        ];
    }
}

