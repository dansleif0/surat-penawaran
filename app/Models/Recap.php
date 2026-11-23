<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recap extends Model {
    protected $guarded = [];
    protected $fillable = [
        'offer_id',
        'total_penawaran_klien',
        'tanggal_keluar',
        'total_pengeluaran',
        'margin',
        'status',
        'confirmed_at',
        'confirmed_by',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function offer() {
        return $this->belongsTo(Offer::class);
    }

    public function items() {
        return $this->hasMany(RecapItem::class);
    }

    public function confirmer() {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function isConfirmed(): bool {
        return $this->status === 'terkonfirmasi' || $this->confirmed_at !== null;
    }
}