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
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // Kolom ID otomatis (primary key)
        $table->string('nama_produk'); // Untuk kolom "nama produk"
        $table->string('hasil_akhir'); // Untuk kolom "hasil akhir"
        $table->string('performa'); // Untuk kolom "peforma"
        $table->bigInteger('harga'); // Untuk kolom "harga", bigInteger aman untuk angka besar
        $table->timestamps(); // Kolom created_at dan updated_at otomatis
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
