<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferItem extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = []; // <-- GUNAKAN INI, HAPUS $fillable

    /**
     * Get the offer that owns the item.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}