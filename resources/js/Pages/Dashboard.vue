<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });
const page = usePage();

defineProps({
    availableDevices: { type: Number, required: true },
    activeRentals: { type: Number, required: true },
    pendingPayments: { type: Number, required: true },
    returnsToday: { type: Number, required: true },
    returns: { type: Array, required: true },
});
</script>

<template>
    <Head title="Dashboard" />
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h1 class="h4 mb-sm-0">Dashboard</h1>
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">Workspace</li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
    </div>
    <div class="card welcome-card border-0">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex align-items-center gap-4">
                <div class="flex-grow-1">
                    <p class="text-uppercase text-primary fw-semibold mb-2">Rental WiFi</p>
                    <h2 class="h3 mb-3">Selamat datang, {{ page.props.auth.user.name }}.</h2>
                    <p class="text-muted mb-0">Ruang kerja untuk mengelola operasional penyewaan WiFi.</p>
                </div>
                <div class="avatar-lg d-none d-sm-flex align-items-center justify-content-center rounded-circle bg-white">
                    <i class="ri-router-line display-5 text-primary" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <section class="card" aria-labelledby="overview-title">
        <div class="card-header d-flex justify-content-between align-items-center gap-2">
            <h2 id="overview-title" class="card-title mb-0">Ringkasan Operasional</h2>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="border rounded p-3 h-100">
                        <p class="text-muted mb-2">Perangkat Tersedia</p>
                        <p class="h3 text-success mb-0">{{ availableDevices }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="border rounded p-3 h-100">
                        <p class="text-muted mb-2">Penyewaan Aktif</p>
                        <p class="h3 text-primary mb-0">{{ activeRentals }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="border rounded p-3 h-100">
                        <p class="text-muted mb-2">Menunggu Pembayaran</p>
                        <p class="h3 text-warning mb-0">{{ pendingPayments }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="border rounded p-3 h-100">
                        <p class="text-muted mb-2">Pengembalian Hari Ini</p>
                        <p class="h3 text-info mb-0">{{ returnsToday }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card" aria-labelledby="return-schedule-title">
        <div class="card-header d-flex justify-content-between align-items-center gap-2">
            <h2 id="return-schedule-title" class="card-title mb-0">Jadwal Pengembalian</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Pemesanan</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Perangkat</th>
                            <th scope="col">Tanggal Kembali</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="booking in returns" :key="booking.id">
                            <td><Link :href="'/bookings/' + booking.id">{{ booking.booking_number }}</Link></td>
                            <td>{{ booking.customer.name }}</td>
                            <td>{{ booking.device.code }}</td>
                            <td>{{ booking.end_date }}</td>
                        </tr>
                        <tr v-if="returns.length === 0">
                            <td colspan="4" class="text-center text-muted">Belum ada penyewaan aktif.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</template>
