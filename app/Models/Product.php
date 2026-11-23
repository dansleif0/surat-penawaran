<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_produk',
        'hasil_akhir',
        'kriteria', // <-- Tanda koma ditambahkan di sini
        'performa',
        'harga',
        'stok',
        'ukuran',
    ];

    /**
     * Cari produk terdaftar berdasarkan nama produk dan varian ukuran.
     */
    public static function findVariant($namaProduk, $ukuran = null)
    {
        if (empty($namaProduk)) {
            return null;
        }

        $namaProduk = trim($namaProduk);
        $ukuran = !empty($ukuran) && $ukuran !== '-' ? trim($ukuran) : null;

        if ($ukuran) {
            $product = self::where('nama_produk', $namaProduk)
                ->where('ukuran', $ukuran)
                ->first();
            if (!$product) {
                $product = self::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])
                    ->whereRaw('LOWER(TRIM(ukuran)) = ?', [strtolower($ukuran)])
                    ->first();
            }
            if (!$product) {
                $cleanUkuran = preg_replace('/\s+/', '', strtolower($ukuran));
                $product = self::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])
                    ->whereRaw("REPLACE(LOWER(TRIM(COALESCE(ukuran, ''))), ' ', '') = ?", [$cleanUkuran])
                    ->first();
            }
            if ($product) {
                return $product;
            }
        }

        // Fallback: cari berdasarkan nama_produk saja
        $product = self::where('nama_produk', $namaProduk)->first();
        if (!$product) {
            $product = self::whereRaw('LOWER(TRIM(nama_produk)) = ?', [strtolower($namaProduk)])->first();
        }
        return $product;
    }
}