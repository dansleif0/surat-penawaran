<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    // 1. Tambah kolom 'jenis_penawaran' di tabel offers
    if (!Schema::hasColumn('offers', 'jenis_penawaran')) {
        Schema::table('offers', function (Blueprint $table) {
            // HAPUS bagian ->after('status')
            // Cukup seperti ini:
            $table->string('jenis_penawaran')->default('proyek');
        });
    }

    // 2. Tambah kolom 'deskripsi_tambahan' di tabel offer_items
    if (!Schema::hasColumn('offer_items', 'deskripsi_tambahan')) {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->text('deskripsi_tambahan')->nullable()->after('nama_produk');
        });
    }
}

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('jenis_penawaran');
        });
        Schema::table('offer_items', function (Blueprint $table) {
            $table->dropColumn('deskripsi_tambahan');
        });
    }
};