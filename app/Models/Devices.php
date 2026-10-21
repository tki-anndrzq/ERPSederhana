<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'serial_number',
        'status',
        'note',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'device_id');
    }
}
