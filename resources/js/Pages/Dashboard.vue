<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });
const page = usePage();
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
        <div class="card-header"><h2 id="overview-title" class="card-title mb-0">Ringkasan operasional</h2></div>
        <div class="card-body text-center py-5">
            <div class="avatar-md mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center">
                <i class="ri-inbox-line fs-32 text-muted" aria-hidden="true"></i>
            </div>
            <h3 class="h5">Belum ada ringkasan operasional</h3>
            <p class="text-muted mb-0">Ringkasan booking, perangkat, dan pembayaran akan ditampilkan di sini.</p>
        </div>
    </section>
</template>
