<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecapItem extends Model {
    // 1. Gunakan salah satu saja: fillable atau guarded.
    // Jika menggunakan fillable, pastikan 'tanggal_item' ada di dalam list.
    protected $fillable = [
        'recap_id',
        'tanggal_item', // Tambahkan ini agar tanggal bisa disimpan ke DB
        'material',
        'detail',
        'harga',
        'qty',
        'subtotal'
    ];

    // 2. Tambahkan Casts agar tanggal_item otomatis menjadi objek Carbon
    // Ini memudahkan pemanggilan format('d/m/Y') di view
    protected $casts = [
        'tanggal_item' => 'date',
        'harga' => 'integer',
        'qty' => 'integer',
        'subtotal' => 'integer',
    ];

    public function recap() {
        return $this->belongsTo(Recap::class);
    }
}