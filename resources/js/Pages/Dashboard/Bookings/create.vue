<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    packages: { type: Array, required: true },
    devices: { type: Array, required: true },
});

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    destination: '',
    rental_package_id: '',
    device_id: '',
    start_date: '',
    end_date: '',
});

function simpanPemesanan() {
    form.post('/bookings', { preserveScroll: true });
}
</script>

<template>
    <Head title="Tambah Pemesanan" />

    <div class="page-title-box d-flex justify-content-between align-items-center">
        <h1 class="h4">Tambah Pemesanan</h1>
        <Link href="/bookings" class="btn btn-light">Kembali</Link>
    </div>

    <div v-if="packages.length === 0" class="alert alert-warning" role="alert">
        Belum ada paket aktif. Tambahkan atau aktifkan paket di halaman Paket Sewa.
    </div>
    <div v-if="devices.length === 0" class="alert alert-warning" role="alert">
        Belum ada perangkat tersedia. Periksa data di halaman Perangkat.
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-1">Form Pemesanan</h2>
            <p class="text-muted mb-0">Satu pemesanan untuk satu perangkat WiFi.</p>
        </div>
        <div class="card-body">
            <form @submit.prevent="simpanPemesanan">
                <fieldset :disabled="form.processing">
                    <legend class="h6 mb-3">Data Pelanggan</legend>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer-name" class="form-label">Nama Pelanggan</label>
                            <input
                                id="customer-name"
                                v-model="form.customer_name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.customer_name }"
                                maxlength="255"
                                required
                            >
                            <div v-if="form.errors.customer_name" class="invalid-feedback" role="alert">{{ form.errors.customer_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer-phone" class="form-label">Nomor HP</label>
                            <input
                                id="customer-phone"
                                v-model="form.customer_phone"
                                type="tel"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.customer_phone }"
                                maxlength="25"
                                placeholder="Contoh: 081234567890"
                                required
                            >
                            <div v-if="form.errors.customer_phone" class="invalid-feedback" role="alert">{{ form.errors.customer_phone }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer-email" class="form-label">Email (opsional)</label>
                            <input
                                id="customer-email"
                                v-model="form.customer_email"
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.customer_email }"
                                maxlength="255"
                            >
                            <div v-if="form.errors.customer_email" class="invalid-feedback" role="alert">{{ form.errors.customer_email }}</div>
                        </div>
                    </div>

                    <h3 class="h6 mt-3 mb-3">Data Sewa</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Tujuan Perjalanan</label>
                            <input
                                id="destination"
                                v-model="form.destination"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.destination }"
                                maxlength="255"
                                placeholder="Contoh: Jepang"
                                required
                            >
                            <div v-if="form.errors.destination" class="invalid-feedback" role="alert">{{ form.errors.destination }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rental-package" class="form-label">Paket Sewa</label>
                            <select
                                v-select2
                                id="rental-package"
                                data-placeholder="Pilih paket sewa"
                                v-model="form.rental_package_id"
                                class="form-select"
                                :class="{ 'is-invalid': form.errors.rental_package_id }"
                                :disabled="form.processing || packages.length === 0"
                                required
                            >
                                <option disabled value="">Pilih paket sewa</option>
                                <option v-for="item in packages" :key="item.id" :value="item.id">
                                    {{ item.name }} — Rp {{ item.daily_price }} / hari
                                </option>
                            </select>
                            <div v-if="form.errors.rental_package_id" class="invalid-feedback" role="alert">{{ form.errors.rental_package_id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="device" class="form-label">Perangkat WiFi</label>
                            <select
                                v-select2
                                id="device"
                                data-placeholder="Pilih perangkat tersedia"
                                v-model="form.device_id"
                                class="form-select"
                                :class="{ 'is-invalid': form.errors.device_id }"
                                :disabled="form.processing || devices.length === 0"
                                required
                            >
                                <option disabled value="">Pilih perangkat tersedia</option>
                                <option v-for="device in devices" :key="device.id" :value="device.id">
                                    {{ device.code }} — {{ device.serial_number }}
                                </option>
                            </select>
                            <div v-if="form.errors.device_id" class="invalid-feedback" role="alert">{{ form.errors.device_id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start-date" class="form-label">Tanggal Mulai</label>
                            <input
                                id="start-date"
                                v-model="form.start_date"
                                type="date"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.start_date }"
                                required
                            >
                            <div v-if="form.errors.start_date" class="invalid-feedback" role="alert">{{ form.errors.start_date }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end-date" class="form-label">Tanggal Selesai</label>
                            <input
                                id="end-date"
                                v-model="form.end_date"
                                type="date"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.end_date }"
                                :min="form.start_date"
                                required
                            >
                            <div v-if="form.errors.end_date" class="invalid-feedback" role="alert">{{ form.errors.end_date }}</div>
                        </div>
                    </div>

                    <p class="text-muted">
                        Total harga = harga paket per hari × jumlah hari sewa.
                        Tanggal mulai dan selesai sama-sama dihitung, maksimal 365 hari.
                        Total ditampilkan setelah pemesanan disimpan.
                    </p>
                    <button type="submit" class="btn btn-primary" :disabled="packages.length === 0 || devices.length === 0">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pemesanan' }}
                    </button>
                </fieldset>
            </form>
        </div>
    </div>
</template>
