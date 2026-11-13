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
            if (!Schema::hasColumn('offer_items', 'diskon_tipe')) {
                $table->string('diskon_tipe')->default('persen')->after('harga_per_m2');
            }
            if (!Schema::hasColumn('offer_items', 'diskon_nilai')) {
                $table->decimal('diskon_nilai', 15, 2)->default(0)->after('diskon_tipe');
            }
            if (!Schema::hasColumn('offer_items', 'diskon')) {
                $table->decimal('diskon', 15, 2)->default(0)->after('diskon_nilai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_items', function (Blueprint $table) {
            $table->dropColumn(['diskon_tipe', 'diskon_nilai', 'diskon']);
        });
    }
};

