<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    booking: { type: Object, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formBatal = useForm({});

function batalkanPemesanan() {
    const yakin = window.confirm('Batalkan pemesanan ' + props.booking.booking_number + '?');

    if (yakin) {
        formBatal.post('/bookings/' + props.booking.id + '/cancel', { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Detail Pemesanan" />

    <div class="page-title-box d-flex align-items-center justify-content-between">
        <h1 class="h4">Detail Pemesanan</h1>
        <Link href="/bookings" class="btn btn-light">Kembali ke Daftar</Link>
    </div>

    <div v-if="success" class="alert alert-success" role="status">{{ success }}</div>
    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-2">{{ booking.booking_number }}</h2>
            <span v-if="booking.status === 'pending_payment'" class="badge bg-warning-subtle text-warning">Menunggu Pembayaran</span>
            <span v-else-if="booking.status === 'cancelled'" class="badge bg-danger-subtle text-danger">Dibatalkan</span>
            <span v-else>{{ booking.status }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h3 class="h6">Pelanggan</h3>
                    <p class="mb-1">{{ booking.customer.name }}</p>
                    <p class="mb-1">{{ booking.customer.phone }}</p>
                    <p class="mb-0">{{ booking.customer.email || '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h3 class="h6">Perangkat</h3>
                    <p class="mb-1">Kode: {{ booking.device.code }}</p>
                    <p class="mb-0">Nomor seri: {{ booking.device.serial_number }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr><th scope="row">Tujuan Perjalanan</th><td>{{ booking.destination }}</td></tr>
                        <tr><th scope="row">Paket Sewa</th><td>{{ booking.rental_package.name }}</td></tr>
                        <tr><th scope="row">Tanggal Mulai</th><td>{{ booking.start_date }}</td></tr>
                        <tr><th scope="row">Tanggal Selesai</th><td>{{ booking.end_date }}</td></tr>
                        <tr><th scope="row">Lama Sewa</th><td>{{ booking.duration_days }} hari</td></tr>
                        <tr><th scope="row">Harga per Hari</th><td>Rp {{ booking.daily_rate }}</td></tr>
                        <tr><th scope="row">Total Harga</th><td class="fw-bold">Rp {{ booking.total_amount }}</td></tr>
                    </tbody>
                </table>
            </div>

            <button v-if="booking.status === 'pending_payment'" type="button" class="btn btn-outline-danger" :disabled="formBatal.processing" @click="batalkanPemesanan">
                {{ formBatal.processing ? 'Membatalkan...' : 'Batalkan Pemesanan' }}
            </button>
        </div>
    </div>
</template>
