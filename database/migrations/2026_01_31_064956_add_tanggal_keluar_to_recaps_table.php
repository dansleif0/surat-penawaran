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
        Schema::table('recaps', function (Blueprint $table) {
            // Menambahkan kolom tanggal (opsional) setelah kolom offer_id
            $table->date('tanggal_keluar')->nullable()->after('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recaps', function (Blueprint $table) {
            //
        });
    }
};
