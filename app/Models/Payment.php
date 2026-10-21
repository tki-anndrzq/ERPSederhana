<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['invoice_id', 'reference', 'method', 'amount', 'status', 'paid_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime:Y-m-d H:i',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
