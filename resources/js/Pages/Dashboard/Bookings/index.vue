<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    bookings: { type: Array, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});
</script>

<template>
    <Head title="Pemesanan" />

    <div class="page-title-box">
        <h1 class="h4">Pemesanan</h1>
    </div>

    <div v-if="success" class="alert alert-success" role="status">{{ success }}</div>
    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h2 class="card-title mb-1">Daftar Pemesanan</h2>
                <p class="text-muted mb-0">Pemesanan sewa perangkat WiFi pelanggan.</p>
            </div>
            <Link href="/bookings/create" class="btn btn-primary">Tambah Pemesanan</Link>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nomor Pesanan</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Destinasi</th>
                            <th scope="col">Paket / Perangkat</th>
                            <th scope="col">Tanggal Sewa</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(booking, index) in bookings" :key="booking.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ booking.booking_number }}</td>
                            <td>
                                {{ booking.customer.name }}
                                <div class="small text-muted">{{ booking.customer.phone }}</div>
                            </td>
                            <td>{{ booking.destination }}</td>
                            <td>
                                {{ booking.rental_package.name }}
                                <div class="small text-muted">{{ booking.device.code }}</div>
                            </td>
                            <td>
                                {{ booking.start_date }} s.d. {{ booking.end_date }}
                                <div class="small text-muted">{{ booking.duration_days }} hari</div>
                            </td>
                            <td>Rp {{ booking.total_amount }}</td>
                            <td>
                                <span v-if="booking.status === 'pending_payment'" class="badge bg-warning-subtle text-warning">
                                    Menunggu Pembayaran
                                </span>
                                <span v-else-if="booking.status === 'cancelled'" class="badge bg-danger-subtle text-danger">
                                    Dibatalkan
                                </span>
                                <span v-else>{{ booking.status }}</span>
                            </td>
                            <td>
                                <Link :href="'/bookings/' + booking.id" class="btn btn-sm btn-outline-primary">
                                    Detail
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="bookings.length === 0">
                            <td colspan="9" class="text-center text-muted">Belum ada pemesanan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
