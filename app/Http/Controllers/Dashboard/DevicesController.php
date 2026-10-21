<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Devices;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:devices,code'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'status' => ['required', 'boolean'],
            'note' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode perangkat wajib diisi.',
            'code.unique' => 'Kode perangkat sudah digunakan.',
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah digunakan.',
            'status.required' => 'Status perangkat wajib diisi.',
            'status.boolean' => 'Status perangkat tidak valid.',
        ]);

        Devices::create($data);

        return redirect()->route('devices.dashboard')
            ->with('success', 'Perangkat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $device = Devices::findOrFail($id);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:devices,code,' . $device->id],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number,' . $device->id],
            'status' => ['required', 'boolean'],
            'note' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode perangkat wajib diisi.',
            'code.unique' => 'Kode perangkat sudah digunakan.',
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah digunakan.',
            'status.required' => 'Status perangkat wajib diisi.',
            'status.boolean' => 'Status perangkat tidak valid.',
        ]);

        $device->update($data);

        return redirect()->route('devices.dashboard')
            ->with('success', 'Perangkat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $device = Devices::findOrFail($id);

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
