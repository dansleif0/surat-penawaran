<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');

            $table->string('no_surat');
            $table->date('tanggal_surat');

            // Pihak I (Klien - Pemberi Kerja)
            $table->string('pihak_satu_nama');
            $table->string('pihak_satu_jabatan')->nullable();
            $table->string('pihak_satu_perusahaan');
            $table->text('pihak_satu_alamat');

            // Pihak II (Kita - Penerima Kerja)
            $table->string('pihak_dua_nama');
            $table->string('pihak_dua_jabatan');
            $table->string('pihak_dua_perusahaan');
            $table->text('pihak_dua_alamat');

            // Detail Pekerjaan
            $table->string('judul_pekerjaan');
            $table->string('lokasi_pekerjaan');
            $table->string('durasi_hari'); // Misal: "60 (enam puluh)"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('nilai_pekerjaan', 15, 2);

            // Sistem Pembayaran (Disimpan sebagai JSON Array)
            // Contoh: [{"tahap": "DP", "persen": "30%"}, {"tahap": "Pelunasan", "persen": "70%"}]
            $table->json('termin_pembayaran');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skps');
    }
};