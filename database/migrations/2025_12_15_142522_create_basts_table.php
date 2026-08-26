<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basts', function (Blueprint $table) {
            $table->id();
            // BAST terhubung dengan Offer (Penawaran)
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');

            $table->string('no_surat');
            $table->date('tanggal_bast');

            // PIHAK PERTAMA (Kontraktor/Kita) - Bisa diubah jika penanggung jawab beda
            $table->string('pihak_pertama_nama');
            $table->string('pihak_pertama_jabatan');

            // PIHAK KEDUA (Klien)
            $table->string('pihak_kedua_nama');
            $table->string('pihak_kedua_jabatan');
            $table->string('pihak_kedua_perusahaan');
            $table->text('pihak_kedua_alamat');

            // Detail Pekerjaan
            $table->text('deskripsi_pekerjaan');

            // Path Gambar Dokumentasi
            $table->string('before_image_path')->nullable();
            $table->string('after_image_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basts');
    }
};