<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $guarded = []; // Izinkan semua kolom diisi

    // Relasi ke Penawaran (Offer) aslinya
    public function offer() {
        return $this->belongsTo(Offer::class);
    }

    // Relasi ke Pekerjaan Tambahan
    public function additions() {
        return $this->hasMany(InvoiceAddition::class);
    }

    // Relasi ke Pembayaran DP
    public function payments() {
        return $this->hasMany(InvoicePayment::class);
    }
}