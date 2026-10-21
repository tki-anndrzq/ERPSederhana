<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function show($token)
    {
        // Link pelanggan memakai token acak, bukan ID berurutan.
        $invoice = Invoice::with([
            'booking.customer', 'booking.rentalPackage', 'booking.device:id,code', 'payment',
        ])->where('public_token', $token)->firstOrFail();

        return Inertia::render('Customer/Invoice', [
            'invoice' => $invoice,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    // Untuk pemesanan lama yang dibuat sebelum fitur invoice tersedia.
    public function store($id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== 'pending_payment') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Invoice baru hanya dibuat untuk pemesanan Menunggu Pembayaran.');
            }

            Invoice::buatUntukBooking($booking);

            return redirect()->route('bookings.show', $id)
                ->with('success', 'Invoice sudah tersedia.');
        });
    }
}
