<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '../../Layouts/CustomerLayout.vue';
import exampleInvoice from '../../data/exampleInvoice';

defineOptions({ layout: CustomerLayout });

const props = defineProps({
    invoice: { type: Object, default: null },
    preview: { type: Boolean, default: false },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

// Menu preview memakai contoh. Link invoice asli memakai data dari controller.
const invoiceData = computed(() => props.invoice || exampleInvoice);
</script>

<template>
    <Head title="Invoice" />

    <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h1 class="h4 mb-0">Invoice</h1>
        <span v-if="preview" class="badge bg-info-subtle text-info">Data Contoh</span>
    </div>

    <div v-if="success" class="alert alert-success" role="status">{{ success }}</div>
    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h5 mb-1">Rental WiFi</h2>
                <p class="text-muted mb-0">Tagihan Penyewaan Perangkat WiFi</p>
            </div>
            <div>
                <h3 class="h6 mb-2">{{ invoiceData.invoice_number }}</h3>
                <span v-if="invoiceData.status === 'unpaid'" class="badge bg-warning-subtle text-warning">Belum Lunas</span>
                <span v-else-if="invoiceData.status === 'paid'" class="badge bg-success-subtle text-success">Lunas</span>
                <span v-else class="badge bg-danger-subtle text-danger">Dibatalkan</span>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h3 class="h6 text-muted mb-3">Ditagihkan Kepada</h3>
                    <p class="fw-semibold mb-1">{{ invoiceData.booking.customer.name }}</p>
                    <p class="mb-1">{{ invoiceData.booking.customer.phone }}</p>
                    <p class="mb-0">{{ invoiceData.booking.customer.email || '-' }}</p>
                </div>
                <div class="col-md-6">
                    <h3 class="h6 text-muted mb-3">Informasi Invoice</h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-5 mb-2">Nomor Pemesanan</dt>
                        <dd class="col-sm-7 mb-2">{{ invoiceData.booking.booking_number }}</dd>
                        <dt class="col-sm-5 mb-2">Tanggal Invoice</dt>
                        <dd class="col-sm-7 mb-2">{{ invoiceData.created_at }}</dd>
                        <dt class="col-sm-5 mb-2">Status</dt>
                        <dd class="col-sm-7 mb-2">
                            <span v-if="invoiceData.status === 'paid'">Lunas</span>
                            <span v-else-if="invoiceData.status === 'void'">Dibatalkan</span>
                            <span v-else>Belum Lunas</span>
                        </dd>
                        <dt class="col-sm-5 mb-0">Tanggal Pelunasan</dt>
                        <dd class="col-sm-7 mb-0">{{ invoiceData.paid_at || '-' }}</dd>
                    </dl>
                </div>
            </div>

            <h3 class="h6 mb-3">Rincian Sewa</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Paket / Perangkat</th>
                            <th scope="col">Destinasi</th>
                            <th scope="col">Periode Sewa</th>
                            <th scope="col">Lama Sewa</th>
                            <th scope="col" class="text-end">Harga per Hari</th>
                            <th scope="col" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <p class="fw-semibold mb-1">{{ invoiceData.booking.rental_package.name }}</p>
                                <span class="text-muted">{{ invoiceData.booking.device.code }}</span>
                            </td>
                            <td>{{ invoiceData.booking.destination }}</td>
                            <td>{{ invoiceData.booking.start_date }} s.d. {{ invoiceData.booking.end_date }}</td>
                            <td>{{ invoiceData.booking.duration_days }} hari</td>
                            <td class="text-end text-nowrap">Rp {{ invoiceData.booking.daily_rate }}</td>
                            <td class="text-end text-nowrap">Rp {{ invoiceData.amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-3">
                <div class="col-md-6 col-lg-4">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <span class="fw-semibold">Total Tagihan</span>
                            <span class="h5 mb-0 text-primary">Rp {{ invoiceData.amount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-top mt-4 pt-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <p class="text-muted mb-0">Cantumkan nomor invoice sebagai referensi pembayaran.</p>
                <Link
                    v-if="invoiceData.status === 'unpaid'"
                    :href="preview ? '/ui/payment' : '/invoices/' + invoiceData.public_token + '/pay'"
                    class="btn btn-primary"
                >Bayar Sekarang</Link>
            </div>
        </div>
    </div>
</template>
