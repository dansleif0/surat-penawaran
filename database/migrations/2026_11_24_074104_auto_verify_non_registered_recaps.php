<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Recap;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Otomatis verifikasi semua rekapan yang hanya memuat selain produk terdaftar
        Recap::with('items')->get()->each(function ($r) {
            if (!$r->hasRegisteredProducts()) {
                $r->update([
                    'status'       => 'terkonfirmasi',
                    'confirmed_at' => $r->confirmed_at ?? ($r->created_at ?? now()),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
