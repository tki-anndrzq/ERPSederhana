<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_number',
        'customer_id',
        'rental_package_id',
        'device_id',
        'destination',
        'start_date',
        'end_date',
        'duration_days',
        'daily_rate',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'daily_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rentalPackage()
    {
        return $this->belongsTo(RentalPackage::class);
    }

    public function device()
    {
        return $this->belongsTo(Devices::class, 'device_id');
    }
}
