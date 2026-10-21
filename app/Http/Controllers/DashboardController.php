<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Devices;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $returns = Booking::with(['customer', 'device'])
            ->where('status', 'rented')->orderBy('end_date')->limit(20)->get();

        return Inertia::render('Dashboard', [
            'availableDevices' => Devices::where('status', 'available')->count(),
            'activeRentals' => Booking::where('status', 'rented')->count(),
            'pendingPayments' => Booking::where('status', 'pending_payment')->count(),
            'returnsToday' => Booking::where('status', 'rented')->whereDate('end_date', today())->count(),
            'returns' => $returns,
        ]);
    }
}
