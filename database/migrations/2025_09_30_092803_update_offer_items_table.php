<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('offer_items', function (Blueprint $table) {
        // Mengganti nama kolom 'nama_produk_area' menjadi 'nama_produk'
        $table->renameColumn('nama_produk_area', 'nama_produk');

        // Menambahkan kolom baru untuk 'area_dinding' setelah 'nama_produk'
        $table->string('area_dinding')->after('nama_produk');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            //
        });
    }
};
