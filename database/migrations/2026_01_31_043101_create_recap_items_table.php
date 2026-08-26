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
        Schema::create('recap_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recap_id')->constrained()->onDelete('cascade');
            $table->string('material');      // Nama barang / upah
            $table->string('detail')->nullable(); // Satuan atau keterangan tambahan
            $table->decimal('harga', 15, 2); // Harga satuan modal
            $table->decimal('qty', 10, 2);   // Jumlah (support desimal)
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recap_items');
    }
};
