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
        Schema::table('products', function (Blueprint $table) {
            // Menambahkan kolom 'kriteria' setelah kolom 'performa' (Nama Brand)
            // Menggunakan nullable() untuk antisipasi data lama yang belum punya kriteria
            $table->string('kriteria')->nullable()->after('performa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('kriteria');
        });
    }
};