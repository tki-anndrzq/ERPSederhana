<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Devices;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DevicesController extends Controller
{
    public function index()
    {
        $devices = Devices::orderBy('code', 'desc')->get();
        return Inertia::render('Dashboard/Devices/index', [
            'devices' => $devices,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Devices/create');
    }

    public function edit($id)
    {
        $device = Devices::findOrFail($id);

        if (in_array($device->status, ['reserved', 'rented'])) {
            return redirect()->route('devices.dashboard')
                ->with('error', 'Perangkat sedang dipesan atau disewa dan belum bisa diedit.');
        }

        return Inertia::render('Dashboard/Devices/edit', [
            'device' => $device,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:devices,code'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'status' => ['required', 'in:available,maintenance'],
            'note' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode perangkat wajib diisi.',
            'code.unique' => 'Kode perangkat sudah digunakan.',
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah digunakan.',
            'status.required' => 'Status perangkat wajib diisi.',
            'status.in' => 'Pilih status Tersedia atau Perawatan.',
        ]);

        Devices::create($data);

        return redirect()->route('devices.dashboard')
            ->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $device = Devices::whereKey($id)->lockForUpdate()->firstOrFail();

            // Perangkat yang sedang digunakan diatur melalui proses pemesanan.
            if (in_array($device->status, ['reserved', 'rented'])) {
                throw ValidationException::withMessages([
                    'status' => 'Perangkat sedang dipesan atau disewa dan belum bisa diedit.',
                ]);
            }

            $data = $request->validate([
                'code' => ['required', 'string', 'max:255', 'unique:devices,code,' . $device->id],
                'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number,' . $device->id],
                'status' => ['required', 'in:available,maintenance'],
                'note' => ['nullable', 'string'],
            ], [
                'code.required' => 'Kode perangkat wajib diisi.',
                'code.unique' => 'Kode perangkat sudah digunakan.',
                'serial_number.required' => 'Nomor seri wajib diisi.',
                'serial_number.unique' => 'Nomor seri sudah digunakan.',
                'status.required' => 'Status perangkat wajib diisi.',
                'status.in' => 'Pilih status Tersedia atau Perawatan.',
            ]);

            $device->update($data);

            return redirect()->route('devices.dashboard')
                ->with('success', 'Perangkat berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        $device = Devices::findOrFail($id);

        if (in_array($device->status, ['reserved', 'rented'])) {
            return redirect()->route('devices.dashboard')
                ->with('error', 'Perangkat yang sedang dipesan atau disewa tidak bisa dihapus.');
        }

        try {
            $device->delete();
        } catch (QueryException $exception) {
            // PostgreSQL menolak hapus jika perangkat masih digunakan data lain.
            if ($exception->getCode() === '23503') {
                return redirect()->route('devices.dashboard')
                    ->with('error', 'Perangkat masih digunakan oleh data lain dan tidak bisa dihapus.');
            }

            throw $exception;
        }

        return redirect()->route('devices.dashboard')
            ->with('success', 'Perangkat berhasil dihapus.');
    }
}
