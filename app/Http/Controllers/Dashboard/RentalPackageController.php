<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RentalPackage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentalPackageController extends Controller
{
    public function index()
    {
        $package = RentalPackage::orderBy('id', 'desc')->get();

        return Inertia::render('Dashboard/Package/index', [
            'package' => $package,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Package/create');
    }

    public function edit($id)
    {
        $package = RentalPackage::findOrFail($id);

        return Inertia::render('Dashboard/Package/edit', [
            'package' => $package,
        ]);
    }

    // Menyimpan paket baru.
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'daily_price' => ['required', 'numeric', 'min:1', 'max:9999999999.99', 'decimal:0,2'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'destination.required' => 'Destinasi wajib diisi.',
            'daily_price.required' => 'Harga per hari wajib diisi.',
            'daily_price.numeric' => 'Harga harus berupa angka.',
            'daily_price.min' => 'Harga minimal Rp1.',
            'daily_price.max' => 'Harga terlalu besar.',
            'daily_price.decimal' => 'Harga maksimal memiliki dua angka desimal.',
        ]);

        RentalPackage::create($data);

        return redirect()->route('packages.dashboard')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    // Mengubah paket berdasarkan ID yang dipilih.
    public function update(Request $request, $id)
    {
        $package = RentalPackage::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'daily_price' => ['required', 'numeric', 'min:1', 'max:9999999999.99', 'decimal:0,2'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'destination.required' => 'Destinasi wajib diisi.',
            'daily_price.required' => 'Harga per hari wajib diisi.',
            'daily_price.numeric' => 'Harga harus berupa angka.',
            'daily_price.min' => 'Harga minimal Rp1.',
            'daily_price.max' => 'Harga terlalu besar.',
            'daily_price.decimal' => 'Harga maksimal memiliki dua angka desimal.',
        ]);

        $package->update($data);

        return redirect()->route('packages.dashboard')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    // Menghapus paket yang belum digunakan data lain.
    public function destroy($id)
    {
        $package = RentalPackage::findOrFail($id);

        try {
            $package->delete();
        } catch (QueryException $exception) {
            // Kode ini menandakan ada data lain yang masih memakai paket.
            if ($exception->getCode() === '23503') {
                return redirect()->route('packages.dashboard')
                    ->with('error', 'Paket masih digunakan data lain. Nonaktifkan paket melalui Edit.');
            }

            throw $exception;
        }

        return redirect()->route('packages.dashboard')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
