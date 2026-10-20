<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPackage extends Model
{
    protected $fillable = [
        'name',
        'destination',
        'daily_price',
        'is_active',
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

}
