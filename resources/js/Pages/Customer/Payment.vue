<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CustomerLayout from '../../Layouts/CustomerLayout.vue';
import exampleInvoice from '../../data/exampleInvoice';

defineOptions({ layout: CustomerLayout });

const props = defineProps({
    invoice: { type: Object, default: null },
    preview: { type: Boolean, default: false },
    mockEnabled: { type: Boolean, default: false },
    error: { type: String, default: null },
});

const invoiceData = computed(() => props.invoice || exampleInvoice);
const hasil = ref('');
const form = useForm({ method: 'bank', result: '' });

function bayar(status) {
    if (props.preview) {
        // Menu contoh tidak menyimpan transaksi.
        hasil.value = status;
        return;
    }

    form.result = status;
    form.post('/invoices/' + props.invoice.public_token + '/pay');
}

function cobaLagi() {
    hasil.value = '';
}
</script>

<template>
    <Head title="Pembayaran" />

    <div class="page-title-box d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">Pembayaran</h1>
        <span class="badge bg-info-subtle text-info">{{ preview ? 'Contoh' : 'Mock Payment' }}</span>
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">{{ error }}</div>
    <div v-if="form.errors.method || form.errors.result" class="alert alert-danger" role="alert">
        {{ form.errors.method || form.errors.result }}
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title mb-1">{{ invoiceData.invoice_number }}</h2>
                    <p class="text-muted mb-0">{{ invoiceData.booking.customer.name }}</p>
                </div>
                <div class="card-body">
                    <div class="bg-light rounded p-3 mb-4">
                        <p class="text-muted mb-1">Total Pembayaran</p>
                        <p class="h3 text-primary mb-0">Rp {{ invoiceData.amount }}</p>
                        <p class="small text-muted mt-2 mb-0">
                            {{ invoiceData.booking.rental_package.name }} -
                            {{ invoiceData.booking.duration_days }} hari -
                            {{ invoiceData.booking.device.code }}
                        </p>
                    </div>

                    <div v-if="invoiceData.status === 'paid'" class="alert alert-success" role="status">
                        Invoice ini sudah lunas.
                    </div>
                    <div v-else-if="invoiceData.status === 'void'" class="alert alert-danger" role="alert">
                        Invoice sudah dibatalkan dan tidak dapat dibayar.
                    </div>
                    <div v-else-if="hasil === 'success'" class="text-center py-3" role="status">
                        <i class="ri-checkbox-circle-line display-4 text-success" aria-hidden="true"></i>
                        <h3 class="h5 mt-3">Contoh Pembayaran Berhasil</h3>
                        <p class="text-muted">Contoh ini tidak mengubah invoice asli.</p>
                        <button type="button" class="btn btn-light" @click="cobaLagi">Ulangi Simulasi</button>
                    </div>
                    <div v-else-if="hasil === 'failed'" class="text-center py-3" role="alert">
                        <i class="ri-close-circle-line display-4 text-danger" aria-hidden="true"></i>
                        <h3 class="h5 mt-3">Contoh Pembayaran Gagal</h3>
                        <button type="button" class="btn btn-primary" @click="cobaLagi">Coba Lagi</button>
                    </div>
                    <div v-else>
                        <fieldset :disabled="form.processing">
                            <legend class="h6 mb-3">Metode Pembayaran</legend>
                            <div class="form-check border rounded p-3 ps-5 mb-2">
                                <input id="payment-bank" v-model="form.method" class="form-check-input" type="radio" name="payment-method" value="bank">
                                <label for="payment-bank" class="form-check-label">Transfer Bank</label>
                            </div>
                            <div class="form-check border rounded p-3 ps-5 mb-4">
                                <input id="payment-qris" v-model="form.method" class="form-check-input" type="radio" name="payment-method" value="qris">
                                <label for="payment-qris" class="form-check-label">QRIS</label>
                            </div>
                            <p class="small text-muted">Pembayaran sandbox untuk simulasi. Tidak ada uang yang ditagihkan.</p>
                            <p v-if="!preview && !mockEnabled" class="text-danger">Pembayaran sedang tidak tersedia.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-success" :disabled="!preview && !mockEnabled" @click="bayar('success')">
                                    {{ form.processing ? 'Memproses...' : 'Simulasikan Berhasil' }}
                                </button>
                                <button type="button" class="btn btn-outline-danger" :disabled="!preview && !mockEnabled" @click="bayar('failed')">
                                    Simulasikan Gagal
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    <div class="border-top mt-4 pt-3">
                        <Link :href="preview ? '/ui/invoice' : '/invoices/' + invoiceData.public_token" class="text-decoration-none">
                            Kembali ke Invoice
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
