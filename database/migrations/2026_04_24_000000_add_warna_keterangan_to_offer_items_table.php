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
            if (!Schema::hasColumn('offer_items', 'warna')) {
                $table->string('warna')->nullable()->after('nama_produk');
            }
            if (!Schema::hasColumn('offer_items', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('warna');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->dropColumn(['warna', 'keterangan']);
        });
    }
};
