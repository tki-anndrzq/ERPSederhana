<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function show($token)
    {
        $invoice = Invoice::with([
            'booking.customer', 'booking.rentalPackage', 'booking.device:id,code', 'payment',
        ])->where('public_token', $token)->firstOrFail();

        return Inertia::render('Customer/Payment', [
            'invoice' => $invoice,
            'mockEnabled' => (bool) config('services.mock_payment.enabled'),
            'error' => session('error'),
        ]);
    }

    public function store(Request $request, $token)
    {
        // Ini gateway MOCK untuk technical test, bukan pemrosesan uang sungguhan.
        abort_unless(config('services.mock_payment.enabled'), 403, 'Mock payment belum diaktifkan.');

        $data = $request->validate([
            'method' => ['required', 'in:bank,qris'],
            'result' => ['required', 'in:success,failed'],
        ]);

        $found = Invoice::where('public_token', $token)->firstOrFail();

        return DB::transaction(function () use ($found, $data, $token) {
            // Urutan kunci sama dengan pembatalan: booking dahulu, lalu invoice.
            $booking = Booking::whereKey($found->booking_id)->lockForUpdate()->firstOrFail();
            $invoice = Invoice::whereKey($found->id)->lockForUpdate()->firstOrFail();

            // Klik ulang tidak mencatat pembayaran dua kali atau menurunkan status lunas.
            if ($invoice->status === 'paid') {
                return redirect()->route('invoices.show', $token)
                    ->with('success', 'Invoice ini sudah lunas.');
            }

            if ($invoice->status === 'void' || $booking->status !== 'pending_payment') {
                return redirect()->route('invoices.show', $token)
                    ->with('error', 'Pemesanan ini sudah tidak dapat dibayar.');
            }

            $payment = Payment::firstOrNew(['invoice_id' => $invoice->id]);

            if (! $payment->exists) {
                $payment->reference = 'MOCK-' . strtoupper(Str::random(12));
            }

            $payment->method = $data['method'];
            $payment->amount = $invoice->amount;
            $payment->status = $data['result'] === 'success' ? 'paid' : 'failed';
            $payment->paid_at = $data['result'] === 'success' ? now() : null;
            $payment->save();

            if ($data['result'] === 'failed') {
                return redirect()->route('payments.show', $token)
                    ->with('error', 'Simulasi pembayaran gagal. Silakan coba lagi.');
            }

            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            $booking->update(['status' => 'confirmed']);

            return redirect()->route('invoices.show', $token)
                ->with('success', 'Pembayaran mock berhasil. Invoice sudah lunas.');
        });
    }
}
