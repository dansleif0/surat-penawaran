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
     * Format unit symbol (e.g. M2 -> m², M1 -> m¹, M3 -> m³).
     */
    public static function formatSatuanValue(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        $map = [
            'M2' => 'm²',
            'm2' => 'm²',
            'M²' => 'm²',
            'M1' => 'm¹',
            'm1' => 'm¹',
            'M¹' => 'm¹',
            'M3' => 'm³',
            'm3' => 'm³',
            'M³' => 'm³',
            'LS' => 'Ls',
            'ls' => 'Ls',
            'LOT' => 'Lot',
            'lot' => 'Lot',
            'UNIT' => 'Unit',
            'unit' => 'Unit',
            'PKT' => 'Pkt',
            'pkt' => 'Pkt',
        ];

        if (isset($map[$value])) {
            return $map[$value];
        }

        return str_replace(
            ['M2', 'm2', 'M1', 'm1', 'M3', 'm3'],
            ['m²', 'm²', 'm¹', 'm¹', 'm³', 'm³'],
            $value
        );
    }

    public function getSatuanAttribute($value)
    {
        return self::formatSatuanValue($value);
    }

    public function setSatuanAttribute($value)
    {
        $this->attributes['satuan'] = self::formatSatuanValue($value);
    }

    /**
     * Mendefinisikan relasi bahwa satu item Jasa dimiliki oleh satu Penawaran.
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}