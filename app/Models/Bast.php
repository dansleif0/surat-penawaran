<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Offer
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}