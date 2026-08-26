<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceMergedItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    public function mergedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'merged_invoice_id');
    }

    public function refInvoice()
    {
        return $this->belongsTo(Invoice::class, 'ref_invoice_id');
    }
}
