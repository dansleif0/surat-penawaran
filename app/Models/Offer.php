<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_klien',
        'client_details',
        'perihal',
        'produk_nama',
        'area_dinding',
        'volume',
        'harga_per_m2',
        'jasa_nama',
        'jasa_harga',
        'volume',       // TAMBAHAN BARU
        'satuan',       // TAMBAHAN BARU
        'harga_satuan', // TAMBAHAN BARU
        'harga_jasa',   // Ini untuk TOTAL HARGA (Volume x Harga Satuan)
        'diskon_global',
        'pisah_kriteria_total',
        'hilangkan_grand_total',
        'opsi_paket',
        'jenis_penawaran',
        'total_keseluruhan',
    ];

    public function items()
{
    return $this->hasMany(OfferItem::class);
}

public function jasaItems() {
    return $this->hasMany(OfferJasa::class);
}
}