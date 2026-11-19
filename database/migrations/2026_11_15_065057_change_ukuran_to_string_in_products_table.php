<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah tipe kolom ukuran menjadi VARCHAR(100) NULL terlebih dahulu
        DB::statement("ALTER TABLE products MODIFY ukuran VARCHAR(100) NULL");

        // 2. Pecah data lama yang berbentuk string JSON menjadi baris produk terpisah
        $products = DB::table('products')->whereNotNull('ukuran')->get();
        foreach ($products as $p) {
            $raw = trim($p->ukuran);
            if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
                $sizes = json_decode($raw, true);
                if (is_array($sizes) && count($sizes) > 0) {
                    // Baris pertama diperbarui dengan ukuran pertama
                    DB::table('products')->where('id', $p->id)->update([
                        'ukuran' => $sizes[0],
                    ]);

                    // Buat baris produk baru untuk ukuran selanjutnya
                    for ($i = 1; $i < count($sizes); $i++) {
                        DB::table('products')->insert([
                            'nama_produk' => $p->nama_produk,
                            'hasil_akhir' => $p->hasil_akhir,
                            'performa'    => $p->performa,
                            'kriteria'    => $p->kriteria,
                            'harga'       => $p->harga,
                            'stok'        => $p->stok,
                            'ukuran'      => $sizes[$i],
                            'created_at'  => $p->created_at ?? now(),
                            'updated_at'  => $p->updated_at ?? now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY ukuran JSON NULL");
    }
};
