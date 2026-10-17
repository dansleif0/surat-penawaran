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
        Schema::table('invoices', function (Blueprint $table) {
            // Flag apakah invoice ini sedang dalam status "Persiapkan"
            $table->boolean('is_persiapan')->default(false)->after('status');
            // Waktu ketika admin menandai "Persiapkan"
            $table->timestamp('persiapan_at')->nullable()->after('is_persiapan');
            // Waktu ketika Staff Gudang mengkonfirmasi
            $table->timestamp('persiapan_confirmed_at')->nullable()->after('persiapan_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['is_persiapan', 'persiapan_at', 'persiapan_confirmed_at']);
        });
    }
};
