<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('offer_jasa', function (Blueprint $table) {
            // Menambahkan kolom baru setelah nama_jasa
            $table->float('volume')->default(1)->after('nama_jasa');
            $table->string('satuan')->nullable()->after('volume'); // Contoh: Lot, M2, Unit
            $table->bigInteger('harga_satuan')->default(0)->after('satuan'); // Harga per unit
        });
    }

    public function down()
    {
        Schema::table('offer_jasas', function (Blueprint $table) {
            $table->dropColumn(['volume', 'satuan', 'harga_satuan']);
        });
    }
};