<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    booking: { type: Object, required: true },
    invoiceUrl: { type: String, default: null },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formAksi = useForm({});
const formKembali = useForm({ condition: 'available', return_notes: '' });
const pesanSalin = ref('');

function buatInvoice() {
    formAksi.post('/bookings/' + props.booking.id + '/invoice');
}

function serahkanPerangkat() {
    if (window.confirm('Serahkan perangkat kepada pelanggan?')) {
        formAksi.post('/bookings/' + props.booking.id + '/handover');
    }
}

function kembalikanPerangkat() {
    if (window.confirm('Selesaikan pengembalian perangkat?')) {
        formKembali.post('/bookings/' + props.booking.id + '/return');
    }
}

async function salinLink() {
    try {
        await navigator.clipboard.writeText(props.invoiceUrl);
        pesanSalin.value = 'Link berhasil disalin.';
    } catch {
        pesanSalin.value = 'Pilih teks link lalu salin secara manual.';
    }
}

function batalkanPemesanan() {
    const yakin = window.confirm('Batalkan pemesanan ' + props.booking.booking_number + '?');

    if (yakin) {
        formAksi.post('/bookings/' + props.booking.id + '/cancel', { preserveScroll: true });
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
            <span v-else-if="booking.status === 'confirmed'" class="badge bg-success-subtle text-success">Dikonfirmasi</span>
            <span v-else-if="booking.status === 'rented'" class="badge bg-primary-subtle text-primary">Sedang Disewa</span>
            <span v-else-if="booking.status === 'completed'" class="badge bg-success-subtle text-success">Selesai</span>
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

            <button v-if="booking.status === 'pending_payment'" type="button" class="btn btn-outline-danger" :disabled="formAksi.processing" @click="batalkanPemesanan">
                {{ formAksi.processing ? 'Membatalkan...' : 'Batalkan Pemesanan' }}
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title mb-0">Invoice Pelanggan</h2></div>
        <div class="card-body">
            <div v-if="booking.invoice">
                <p class="mb-2">Nomor Invoice: <strong>{{ booking.invoice.invoice_number }}</strong></p>
                <p v-if="booking.invoice.status === 'paid'" class="text-success">Lunas</p>
                <p v-else-if="booking.invoice.status === 'void'" class="text-danger">Dibatalkan</p>
                <p v-else class="text-warning">Belum Lunas</p>
                <label for="invoice-link" class="form-label">Link untuk Pelanggan</label>
                <input id="invoice-link" :value="invoiceUrl" type="text" class="form-control mb-3" readonly @focus="$event.target.select()">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary" @click="salinLink">Salin Link</button>
                    <a :href="invoiceUrl" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Lihat Invoice</a>
                </div>
                <p v-if="pesanSalin" class="small text-muted mt-2 mb-0" role="status">{{ pesanSalin }}</p>
                <p v-if="booking.invoice.payment" class="small text-muted mt-3 mb-0">
                    Referensi pembayaran: {{ booking.invoice.payment.reference }}
                </p>
            </div>
            <div v-else>
                <p class="text-muted">Pemesanan ini belum memiliki invoice.</p>
                <button v-if="booking.status === 'pending_payment'" type="button" class="btn btn-primary" :disabled="formAksi.processing" @click="buatInvoice">
                    Buat Invoice
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h2 class="card-title mb-0">Penyerahan Perangkat</h2></div>
                <div class="card-body">
                    <p class="text-muted">Perangkat diserahkan setelah pembayaran lunas.</p>
                    <p>Tanggal Penyerahan: {{ booking.handed_over_at || 'Belum diserahkan' }}</p>
                    <button
                        v-if="booking.status === 'confirmed'"
                        type="button"
                        class="btn btn-primary"
                        :disabled="formAksi.processing || formKembali.processing"
                        @click="serahkanPerangkat"
                    >Serahkan Perangkat</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h2 class="card-title mb-0">Pengembalian Perangkat</h2></div>
                <div class="card-body">
                    <p>Tanggal Pengembalian: {{ booking.returned_at || 'Belum dikembalikan' }}</p>
                    <p v-if="booking.return_notes">Catatan: {{ booking.return_notes }}</p>
                    <form v-if="booking.status === 'rented'" @submit.prevent="kembalikanPerangkat">
                        <fieldset :disabled="formAksi.processing || formKembali.processing">
                            <div class="mb-3">
                                <label for="return-condition" class="form-label">Kondisi Perangkat</label>
                                <select
                                    id="return-condition"
                                    v-select2
                                    v-model="formKembali.condition"
                                    class="form-select"
                                    :class="{ 'is-invalid': formKembali.errors.condition }"
                                    :disabled="formAksi.processing || formKembali.processing"
                                >
                                    <option value="available">Baik - kembali tersedia</option>
                                    <option value="maintenance">Perlu perawatan</option>
                                </select>
                                <div v-if="formKembali.errors.condition" class="invalid-feedback">{{ formKembali.errors.condition }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="return-note" class="form-label">Catatan Pengembalian</label>
                                <textarea
                                    id="return-note"
                                    v-model="formKembali.return_notes"
                                    class="form-control"
                                    :class="{ 'is-invalid': formKembali.errors.return_notes }"
                                    rows="2"
                                    maxlength="2000"
                                    placeholder="Contoh: Perangkat dan charger lengkap."
                                ></textarea>
                                <div v-if="formKembali.errors.return_notes" class="invalid-feedback">{{ formKembali.errors.return_notes }}</div>
                            </div>
                            <button type="submit" class="btn btn-success">
                                {{ formKembali.processing ? 'Menyimpan...' : 'Selesaikan Pengembalian' }}
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
