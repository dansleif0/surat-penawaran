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
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('pisah_kriteria_total')->default(false)->after('total_keseluruhan');
            $table->boolean('hilangkan_grand_total')->default(false)->after('pisah_kriteria_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['pisah_kriteria_total', 'hilangkan_grand_total']);
        });
    }
};