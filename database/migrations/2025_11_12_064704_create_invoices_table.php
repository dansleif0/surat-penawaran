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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade'); // Link ke Surat Penawaran asli
            $table->string('no_invoice');
            $table->string('nama_klien');

            $table->bigInteger('total_penawaran'); // Total asli dari $offer->total_keseluruhan
            $table->bigInteger('total_tambahan')->default(0);
            $table->bigInteger('diskon')->default(0);
            $table->bigInteger('grand_total');
            $table->bigInteger('total_dp')->default(0);
            $table->bigInteger('sisa_pembayaran');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
