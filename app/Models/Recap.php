<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recap extends Model {
    protected $guarded = [];
    protected $fillable = ['offer_id', 'total_penawaran_klien','tanggal_keluar', 'total_pengeluaran', 'margin'];

    public function offer() {
        return $this->belongsTo(Offer::class);
    }

    public function items() {
        return $this->hasMany(RecapItem::class);
    }
}