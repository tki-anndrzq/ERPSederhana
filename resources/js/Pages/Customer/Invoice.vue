<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import CustomerLayout from '../../Layouts/CustomerLayout.vue';

defineOptions({ layout: CustomerLayout });

const props = defineProps({
    invoice: { type: Object, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formBayar = useForm({});
const formCek = useForm({});

function bayar() {
    formBayar.post('/invoices/' + props.invoice.public_token + '/pay');
}

function cekStatus() {
    formCek.post('/invoices/' + props.invoice.public_token + '/check');
}

// Contoh hasil: 360000 menjadi Rp 360.000.
function formatRupiah(value) {
    return 'Rp ' + Number(value).toLocaleString('id-ID');
}
</script>

<template>
    <Head title="Invoice" />

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <p class="invoice-label mb-2">Tagihan penyewaan</p>
            <h1 class="h3 mb-1">Invoice</h1>
            <p class="text-muted mb-0">Rincian sewa dan pembayaran perangkat WiFi kamu.</p>
        </div>
    </div>

    <div v-if="success" class="alert alert-success" role="status">{{ success }}</div>
    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>
    <div v-if="formBayar.errors.payment" class="alert alert-danger" role="alert">
        {{ formBayar.errors.payment }}
    </div>

    <div class="card invoice-card shadow-sm">
        <!-- Bagian atas: nama usaha, nomor invoice, dan status. -->
        <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="invoice-brand-icon">
                    <i class="ri-wifi-line" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="h4 mb-1">Rental WiFi</h2>
                    <p class="text-muted mb-0">Temani perjalananmu, tetap terhubung.</p>
                </div>
            </div>
            <div>
                <p class="invoice-label mb-2">Nomor invoice</p>
                <h3 class="h6 invoice-number mb-2">{{ invoice.invoice_number }}</h3>
                <span v-if="invoice.status === 'unpaid'" class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Belum Lunas</span>
                <span v-else-if="invoice.status === 'paid'" class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Lunas</span>
                <span v-else class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2">Dibatalkan</span>
            </div>
        </div>

        <div class="p-3 p-md-4">
            <div class="row g-4">
                <!-- Bagian kiri: pelanggan dan rincian sewa. -->
                <div class="col-lg-8">
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <h3 class="invoice-label mb-3">Ditagihkan kepada</h3>
                            <p class="h6 mb-2 text-break">{{ invoice.booking.customer.name }}</p>
                            <p class="text-muted mb-1 text-break">{{ invoice.booking.customer.phone }}</p>
                            <p class="text-muted mb-0 text-break">{{ invoice.booking.customer.email || '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="invoice-label mb-3">Informasi pemesanan</h3>
                            <p class="fw-semibold mb-2 text-break">{{ invoice.booking.booking_number }}</p>
                            <p class="text-muted small mb-1">Tanggal diterbitkan</p>
                            <p class="mb-0">{{ invoice.created_at }}</p>
                        </div>
                    </div>

                    <div class="invoice-period p-3 mb-4">
                        <div class="row g-3">
                            <div class="col-6 col-sm-4">
                                <p class="invoice-label mb-2">Mulai sewa</p>
                                <p class="fw-semibold mb-0">{{ invoice.booking.start_date }}</p>
                            </div>
                            <div class="col-6 col-sm-4">
                                <p class="invoice-label mb-2">Selesai sewa</p>
                                <p class="fw-semibold mb-0">{{ invoice.booking.end_date }}</p>
                            </div>
                            <div class="col-12 col-sm-4">
                                <p class="invoice-label mb-2">Durasi</p>
                                <p class="fw-semibold mb-0">{{ invoice.booking.duration_days }} hari</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="h6 mb-3">Rincian penyewaan</h3>
                    <div class="table-responsive">
                        <table class="table invoice-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Paket &amp; perangkat</th>
                                    <th scope="col" class="text-end">Harga / hari</th>
                                    <th scope="col" class="text-center">Durasi</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-1">{{ invoice.booking.rental_package.name }}</p>
                                        <p class="small text-muted mb-1">Perangkat: {{ invoice.booking.device.code }}</p>
                                        <p class="small text-muted mb-0">Destinasi: {{ invoice.booking.destination }}</p>
                                    </td>
                                    <td class="text-end text-nowrap">{{ formatRupiah(invoice.booking.daily_rate) }}</td>
                                    <td class="text-center text-nowrap">{{ invoice.booking.duration_days }} hari</td>
                                    <td class="text-end text-nowrap fw-semibold">{{ formatRupiah(invoice.amount) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <p class="invoice-label mb-2">Catatan</p>
                        <p class="text-muted small mb-0">
                            Simpan nomor invoice ini sebagai referensi untuk pemesanan dan pembayaranmu.
                        </p>
                    </div>
                </div>

                <!-- Bagian kanan: total dan tombol pembayaran. -->
                <div class="col-lg-4">
                    <div class="invoice-summary p-3 p-md-4">
                        <h3 class="h6 mb-4">Ringkasan tagihan</h3>
                        <p class="text-muted mb-2">Total pembayaran</p>
                        <p class="invoice-total mb-2">{{ formatRupiah(invoice.amount) }}</p>
                        <p class="text-muted small mb-4">
                            {{ invoice.booking.duration_days }} hari sewa perangkat WiFi
                        </p>

                        <div v-if="invoice.status === 'unpaid'" class="border-top pt-3">
                            <p class="text-muted small mb-3">
                                Selesaikan pembayaran untuk mengonfirmasi pemesananmu.
                            </p>

                            <button
                                type="button"
                                class="btn btn-primary w-100 py-2"
                                :disabled="formBayar.processing || formCek.processing"
                                @click="bayar"
                            >
                                {{ formBayar.processing ? 'Memproses...' : 'Bayar Sekarang' }}
                            </button>

                            <button
                                v-if="invoice.payment"
                                type="button"
                                class="btn btn-outline-primary w-100 py-2 mt-2"
                                :disabled="formBayar.processing || formCek.processing"
                                @click="cekStatus"
                            >
                                {{ formCek.processing ? 'Memeriksa...' : 'Cek Status Pembayaran' }}
                            </button>
                            <p class="small text-muted text-center mt-3 mb-0">Pembayaran melalui Midtrans.</p>
                        </div>

                        <div v-else-if="invoice.status === 'paid'" class="border-top pt-3">
                            <p class="text-success fw-semibold mb-2">
                                <i class="ri-checkbox-circle-line me-1" aria-hidden="true"></i>
                                Pembayaran sudah lunas
                            </p>
                            <p class="small text-muted mb-1">Tanggal pelunasan</p>
                            <p class="mb-0">{{ invoice.paid_at || '-' }}</p>
                        </div>

                        <div v-else class="border-top pt-3">
                            <p class="text-danger fw-semibold mb-2">Invoice dibatalkan</p>
                            <p class="small text-muted mb-0">Invoice ini sudah tidak dapat dibayar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top px-3 px-md-4 py-3">
            <p class="small text-muted mb-0">Rental WiFi &middot; Tagihan penyewaan perangkat WiFi</p>
        </div>
    </div>
</template>

<style scoped>
/* CSS khusus invoice. Susunan kolom dan jarak memakai kelas Bootstrap. */
.invoice-card {
    border: 1px solid #e4e8f0;
    border-top: 4px solid #405189;
    border-radius: 14px;
    overflow: hidden;
}

.invoice-brand-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #eef1fa;
    color: #405189;
    font-size: 26px;
}

.invoice-label {
    color: #66758c;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.invoice-number {
    overflow-wrap: anywhere;
}

.invoice-period,
.invoice-summary {
    border: 1px solid #e6ebf3;
    border-radius: 10px;
    background: #f7f9fc;
}

.invoice-table {
    min-width: 480px;
}

.invoice-table th {
    padding: 12px;
    background: #f7f9fc;
    color: #66758c;
    font-size: 11px;
}

.invoice-table td {
    padding: 18px 12px;
}

.invoice-total {
    color: #293b66;
    font-size: 30px;
    font-weight: 700;
    line-height: 1.3;
    overflow-wrap: anywhere;
}
</style>
