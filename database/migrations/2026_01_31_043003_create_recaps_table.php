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
        Schema::create('recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->decimal('total_penawaran_klien', 15, 2); // Harga yang disepakati
            $table->decimal('total_pengeluaran', 15, 2);    // Total modal/biaya
            $table->decimal('margin', 15, 2);               // Laba kotor (selisih)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recaps');
    }
};
