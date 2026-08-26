<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skp extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Cast kolom JSON agar otomatis jadi Array saat diambil
    protected $casts = [
        'termin_pembayaran' => 'array',
        'diskon',
        'tanggal_surat' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}