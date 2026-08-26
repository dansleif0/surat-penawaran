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
    Schema::create('offer_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('offer_id')->constrained()->onDelete('cascade'); // Kunci penghubung
        $table->string('nama_produk_area');
        $table->decimal('volume', 8, 2);
        $table->bigInteger('harga_per_m2');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_items');
    }
};
