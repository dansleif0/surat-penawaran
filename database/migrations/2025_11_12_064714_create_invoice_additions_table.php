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
    Schema::create('invoice_additions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('invoice_id')->constrained()->onDelete('cascade'); // Link ke Invoice utama
        $table->string('nama_pekerjaan');
        $table->bigInteger('harga');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_additions');
    }
};
