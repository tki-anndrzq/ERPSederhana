<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'booking_id', 'public_token', 'amount', 'status', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        // Halaman invoice menampilkan percobaan pembayaran yang paling baru.
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Dipakai saat pemesanan baru dan saat membuat invoice untuk pemesanan lama.
    public static function buatUntukBooking($booking)
    {
        return self::firstOrCreate(['booking_id' => $booking->id], [
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8)),
            'public_token' => Str::random(64),
            'amount' => $booking->total_amount,
            'status' => 'unpaid',
        ]);
    }

}
