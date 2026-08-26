<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferJasa extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit jika berbeda dari konvensi
    protected $table = 'offer_jasa';

    // Izinkan semua kolom untuk diisi secara massal
    protected $guarded = [];

    /**
     * Mendefinisikan relasi bahwa satu item Jasa dimiliki oleh satu Penawaran.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}