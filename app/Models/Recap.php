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

    /**
     * Cek apakah rekapan ini memuat setidaknya satu produk yang terdaftar di master produk.
     */
    public function hasRegisteredProducts(): bool {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        return $this->items->contains(function ($item) {
            return $item->isRegisteredProduct();
        });
    }

    /**
     * Cek apakah rekapan terkonfirmasi atau terverifikasi.
     * Jika rekapan hanya memuat selain produk terdaftar (non-stok), otomatis dianggap terverifikasi.
     */
    public function isConfirmed(): bool {
        if (!$this->hasRegisteredProducts()) {
            return true;
        }
        return $this->status === 'terkonfirmasi' || $this->confirmed_at !== null;
    }

    /**
     * Cek apakah status rekapan ini diverifikasi secara otomatis karena tidak memuat produk terdaftar.
     */
    public function isAutoVerified(): bool {
        return !$this->hasRegisteredProducts();
    }

    /**
     * Cek apakah ada produk terdaftar yang stoknya tidak mencukupi untuk rekapan ini.
     */
    public function hasInsufficientStock(): bool {
        if (!$this->hasRegisteredProducts()) {
            return false;
        }

        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }

        $aggregated = [];
        foreach ($this->items as $item) {
            $product = $item->matched_product;
            if ($product) {
                $qty = (int) round((float) ($item->qty ?? 0));
                if ($qty > 0) {
                    if (!isset($aggregated[$product->id])) {
                        $aggregated[$product->id] = [
                            'product' => $product,
                            'qty' => 0,
                        ];
                    }
                    $aggregated[$product->id]['qty'] += $qty;
                }
            }
        }

        foreach ($aggregated as $entry) {
            if ((int) $entry['product']->stok < $entry['qty']) {
                return true;
            }
        }

        return false;
    }
}