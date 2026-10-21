<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Devices;
use App\Models\Invoice;
use App\Models\RentalPackage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        // with() mengambil relasi sekaligus supaya tidak terjadi N+1.
        $bookings = Booking::with(['customer', 'rentalPackage', 'device'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Dashboard/Bookings/index', [
            'bookings' => $bookings,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        $packages = RentalPackage::where('is_active', true)->orderBy('name')->get();
        $devices = Devices::where('status', 'available')->orderBy('code')->get();

        return Inertia::render('Dashboard/Bookings/create', [
            'packages' => $packages,
            'devices' => $devices,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'rental_package_id' => ['required', 'integer', 'exists:rental_packages,id'],
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ], [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'destination.required' => 'Tujuan perjalanan wajib diisi.',
            'rental_package_id.required' => 'Pilih paket sewa.',
            'rental_package_id.exists' => 'Paket tidak ditemukan. Muat ulang halaman.',
            'device_id.required' => 'Pilih perangkat WiFi.',
            'device_id.exists' => 'Perangkat tidak ditemukan. Muat ulang halaman.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // Hari mulai dan hari selesai sama-sama dihitung. Sewa hari yang sama = 1 hari.
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $durationDays = $startDate->diffInDays($endDate) + 1;

        if ($durationDays > 365) {
            throw ValidationException::withMessages([
                'end_date' => 'Lama sewa maksimal 365 hari.',
            ]);
        }

        // Semua disimpan bersama. Jika salah satu gagal, tidak ada data setengah jadi.
        $booking = DB::transaction(function () use ($data, $durationDays) {
            // Kunci perangkat agar dua admin tidak memesan perangkat yang sama bersamaan.
            $device = Devices::whereKey($data['device_id'])->lockForUpdate()->firstOrFail();

            if ($device->status !== 'available') {
                throw ValidationException::withMessages([
                    'device_id' => 'Perangkat sudah tidak tersedia. Pilih perangkat lain.',
                ]);
            }

            $package = RentalPackage::whereKey($data['rental_package_id'])->lockForUpdate()->firstOrFail();

            if (! $package->is_active) {
                throw ValidationException::withMessages([
                    'rental_package_id' => 'Paket sudah tidak aktif. Pilih paket lain.',
                ]);
            }

            $customer = Customer::create([
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
                'email' => $data['customer_email'] ?? null,
            ]);

            // Harga dari database, bukan dari input harga di browser.
            $totalAmount = round($package->daily_price * $durationDays, 2);

            $booking = Booking::create([
                'booking_number' => 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'rental_package_id' => $package->id,
                'device_id' => $device->id,
                'destination' => $data['destination'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration_days' => $durationDays,
                'daily_rate' => $package->daily_price,
                'total_amount' => $totalAmount,
                'status' => 'pending_payment',
            ]);

            $device->update(['status' => 'reserved']);

            Invoice::buatUntukBooking($booking);

            return $booking;
        });

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Pemesanan berhasil dibuat. Perangkat sudah dipesan.');
    }

    public function show($id)
    {
        $booking = Booking::with(['customer', 'rentalPackage', 'device', 'invoice.payment'])->findOrFail($id);

        return Inertia::render('Dashboard/Bookings/show', [
            'booking' => $booking,
            'invoiceUrl' => $booking->invoice ? route('invoices.show', $booking->invoice->public_token) : null,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== 'pending_payment') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Hanya pemesanan Menunggu Pembayaran yang bisa dibatalkan.');
            }

            $device = Devices::whereKey($booking->device_id)->lockForUpdate()->firstOrFail();

            $booking->update(['status' => 'cancelled']);

            Invoice::where('booking_id', $booking->id)->where('status', 'unpaid')
                ->update(['status' => 'void']);

            if ($device->status === 'reserved') {
                $device->update(['status' => 'available']);
            }

            return redirect()->route('bookings.show', $id)
                ->with('success', 'Pemesanan dibatalkan. Perangkat kembali tersedia.');
        });
    }

    public function handover($id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::whereKey($id)->lockForUpdate()->firstOrFail();
            $invoice = Invoice::where('booking_id', $booking->id)->first();

            if ($booking->status !== 'confirmed' || ! $invoice || $invoice->status !== 'paid') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Perangkat hanya bisa diserahkan setelah pembayaran lunas.');
            }

            $device = Devices::whereKey($booking->device_id)->lockForUpdate()->firstOrFail();

            if ($device->status !== 'reserved') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Status perangkat tidak sesuai. Periksa data perangkat.');
            }

            $booking->update(['status' => 'rented', 'handed_over_at' => now()]);
            $device->update(['status' => 'rented']);

            return redirect()->route('bookings.show', $id)
                ->with('success', 'Perangkat sudah diserahkan kepada pelanggan.');
        });
    }

    public function returnDevice(Request $request, $id)
    {
        $data = $request->validate([
            'condition' => ['required', 'in:available,maintenance'],
            'return_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'condition.required' => 'Pilih kondisi perangkat.',
            'condition.in' => 'Kondisi perangkat tidak valid.',
            'return_notes.max' => 'Catatan maksimal 2000 karakter.',
        ]);

        return DB::transaction(function () use ($id, $data) {
            $booking = Booking::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== 'rented') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Hanya perangkat yang sedang disewa yang bisa dikembalikan.');
            }

            $device = Devices::whereKey($booking->device_id)->lockForUpdate()->firstOrFail();

            if ($device->status !== 'rented') {
                return redirect()->route('bookings.show', $id)
                    ->with('error', 'Status perangkat tidak sesuai. Periksa data perangkat.');
            }

            $booking->update([
                'status' => 'completed',
                'returned_at' => now(),
                'return_notes' => $data['return_notes'] ?? null,
            ]);
            $device->update(['status' => $data['condition']]);

            return redirect()->route('bookings.show', $id)
                ->with('success', 'Pengembalian dicatat. Pemesanan selesai.');
        });
    }
}
