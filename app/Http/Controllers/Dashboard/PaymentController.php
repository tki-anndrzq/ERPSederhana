<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
        Config::$curlOptions = [
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [],
        ];
    }

    public function show($token)
    {
        // Link pembayaran lama tetap membawa pelanggan kembali ke invoice.
        return redirect()->route('invoices.show', $token);
    }

    public function store($token)
    {
        if (blank(config('services.midtrans.server_key'))) {
            return back()->with('error', 'Pembayaran belum tersedia. Server Key Midtrans belum diisi.');
        }

        $found = Invoice::where('public_token', $token)->firstOrFail();

        $result = DB::transaction(function () use ($found) {
            // Kunci booking dahulu, sama seperti pembatalan dan pemeriksaan status.
            $booking = Booking::whereKey($found->booking_id)->lockForUpdate()->firstOrFail();
            $invoice = Invoice::whereKey($found->id)->lockForUpdate()->firstOrFail();

            if ($invoice->status !== 'unpaid' || $booking->status !== 'pending_payment') {
                throw ValidationException::withMessages([
                    'payment' => 'Invoice ini sudah tidak dapat dibayar.',
                ]);
            }

            // Klik Bayar berulang memakai checkout yang sama selama masih pending.
            $payment = Payment::where('invoice_id', $invoice->id)
                ->where('status', 'pending')->latest('id')->first();

            if ($payment) {
                return ['payment' => $payment, 'baru' => false];
            }

            if ((float) $invoice->amount < 1 || floor((float) $invoice->amount) != (float) $invoice->amount) {
                throw ValidationException::withMessages([
                    'payment' => 'Total pembayaran harus berupa rupiah bulat, tanpa pecahan.',
                ]);
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'reference' => 'PAY-' . Str::uuid(),
                'method' => 'midtrans',
                'amount' => $invoice->amount,
                'status' => 'pending',
            ]);

            return ['payment' => $payment, 'baru' => true];
        });

        $payment = $result['payment'];

        if ($payment->created_at->copy()->addMinutes(31)->isPast()) {
            return back()->with('error', 'Waktu checkout sudah berakhir. Klik Cek Status Pembayaran sebelum membuat pembayaran baru.');
        }

        if ($payment->redirect_url) {
            return Inertia::location($payment->redirect_url);
        }

        if (! $result['baru']) {
            return back()->with('error', 'Checkout belum tersedia. Cek status pembayaran atau hubungi admin.');
        }

        try {
            // Nominal berasal dari database, bukan input browser.
            $transaction = Snap::createTransaction([
                'transaction_details' => [
                    'order_id' => $payment->reference,
                    'gross_amount' => (int) $payment->amount,
                ],
                'callbacks' => [
                    'finish' => route('invoices.show', $token),
                ],
                // Batasi halaman checkout dan semua metode bayar pada waktu yang sama.
                'expiry' => [
                    'start_time' => $payment->created_at->copy()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 30,
                ],
            ]);

            $payment->update(['redirect_url' => $transaction->redirect_url]);
        } catch (\Exception $exception) {
            // Penolakan autentikasi berarti transaksi belum dibuat oleh Midtrans.
            if (in_array((int) $exception->getCode(), [401, 403])) {
                Payment::whereKey($payment->id)->where('status', 'pending')->update(['status' => 'failed']);

                return back()->with('error', 'Akses Midtrans ditolak. Admin perlu memeriksa Server Key dan mode sandbox/production.');
            }

            // Tetap pending: permintaan mungkin sudah diterima sebelum koneksi putus.
            // Jangan tampilkan pesan mentah SDK karena dapat memuat informasi sensitif.
            return back()->with('error', 'Checkout belum berhasil dibuka. Admin perlu memeriksa transaksi di dashboard Midtrans.');
        }

        return Inertia::location($payment->redirect_url);
    }

    public function check($token)
    {
        $invoice = Invoice::where('public_token', $token)->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $token)->with('success', 'Invoice sudah lunas.');
        }

        if (blank(config('services.midtrans.server_key'))) {
            return back()->with('error', 'Server Key Midtrans belum diisi.');
        }

        $payment = Payment::where('invoice_id', $invoice->id)->latest('id')->first();

        if (! $payment || ! str_starts_with($payment->reference, 'PAY-')) {
            return back()->with('error', 'Belum ada transaksi Midtrans untuk invoice ini.');
        }

        try {
            $status = Transaction::status($payment->reference);
        } catch (\Exception $exception) {
            // Checkout yang ditinggal sebelum memilih metode tidak memiliki status Core API.
            // 404 baru boleh dianggap kedaluwarsa setelah batas 30 menit + jeda 1 menit.
            if ((int) $exception->getCode() === 404 && $payment->created_at->copy()->addMinutes(31)->isPast()) {
                Payment::whereKey($payment->id)->where('status', 'pending')->update(['status' => 'failed']);

                return back()->with('success', 'Checkout sudah kedaluwarsa. Kamu dapat membuat pembayaran baru atau membatalkan pemesanan.');
            }

            return back()->with('error', 'Status belum dapat diperiksa. Pilih metode di checkout Midtrans, lalu cek kembali. Jika tetap gagal, hubungi admin.');
        }

        $this->simpanStatus($payment, $status);

        if ($invoice->fresh()->status === 'paid') {
            return redirect()->route('invoices.show', $token)
                ->with('success', 'Pembayaran berhasil. Invoice sudah lunas.');
        }

        if ($payment->fresh()->status === 'failed') {
            return back()->with('error', 'Checkout sudah berakhir tanpa pembayaran. Kamu dapat membuat pembayaran baru.');
        }

        return back()->with('success', 'Pembayaran masih menunggu penyelesaian atau pemeriksaan Midtrans.');
    }

    public function notification(Request $request)
    {
        abort_if(blank(config('services.midtrans.server_key')), 503);

        $data = $request->validate([
            'order_id' => ['required', 'string', 'max:50'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string', 'size:128'],
        ]);

        $signature = hash('sha512',
            $data['order_id'] . $data['status_code'] . $data['gross_amount'] . config('services.midtrans.server_key')
        );

        abort_unless(hash_equals($signature, $data['signature_key']), 403, 'Signature tidak sesuai.');

        $payment = Payment::where('reference', $data['order_id'])->firstOrFail();

        try {
            // Ambil status langsung dari Midtrans, bukan percaya status kiriman browser.
            $status = Transaction::status($payment->reference);
        } catch (\Exception $exception) {
            // Status 503 memberi kesempatan Midtrans mengirim notifikasi kembali.
            return response()->json(['message' => 'Status belum dapat diperiksa.'], 503);
        }

        $this->simpanStatus($payment, $status);

        return response()->json(['message' => 'OK']);
    }

    // Cek manual dan webhook memakai aturan yang sama agar hasilnya konsisten.
    private function simpanStatus($payment, $status)
    {
        abort_unless(($status->order_id ?? '') === $payment->reference, 422, 'Referensi pembayaran tidak sesuai.');
        abort_unless(
            isset($status->gross_amount) && is_numeric($status->gross_amount) &&
            number_format((float) $status->gross_amount, 2, '.', '') === number_format((float) $payment->amount, 2, '.', ''),
            422, 'Nominal pembayaran tidak sesuai.'
        );
        abort_if(isset($status->currency) && $status->currency !== 'IDR', 422, 'Mata uang tidak sesuai.');

        DB::transaction(function () use ($payment, $status) {
            $found = Invoice::findOrFail($payment->invoice_id);
            $booking = Booking::whereKey($found->booking_id)->lockForUpdate()->firstOrFail();
            $invoice = Invoice::whereKey($found->id)->lockForUpdate()->firstOrFail();
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            // Notifikasi dapat datang berkali-kali. Pembayaran lunas tetap lunas.
            if ($payment->status === 'paid') {
                return;
            }

            $berhasil = $status->transaction_status === 'settlement' ||
                ($status->transaction_status === 'capture' && ($status->fraud_status ?? '') === 'accept');

            if ($berhasil) {
                abort_unless($invoice->amount === $payment->amount, 422, 'Nominal invoice tidak sesuai.');

                if ($invoice->status !== 'paid') {
                    abort_unless($invoice->status === 'unpaid' && $booking->status === 'pending_payment', 409,
                        'Status pemesanan perlu diperiksa admin.');
                }

                $payment->update([
                    'status' => 'paid',
                    'method' => $status->payment_type,
                    'paid_at' => now(),
                ]);

                if ($invoice->status !== 'paid') {
                    $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                    $booking->update(['status' => 'confirmed']);
                }

                // Perangkat tetap reserved sampai admin menyerahkannya.
                return;
            }

            // Gagal satu kali belum tentu menutup Snap: pelanggan masih bisa memilih metode lain.
            // Tunggu batas seluruh checkout agar link lama tidak masih dapat dibayar.
            if (in_array($status->transaction_status, ['deny', 'cancel', 'expire']) &&
                $payment->created_at->copy()->addMinutes(31)->isPast()) {
                $payment->update([
                    'status' => 'failed',
                    'method' => $status->payment_type ?? 'midtrans',
                ]);
            }
        });
    }
}
