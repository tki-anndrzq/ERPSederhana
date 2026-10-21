<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    device: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    code: props.device.code,
    serial_number: props.device.serial_number,
    status: props.device.status,
    note: props.device.note,
});

function simpanDevice() {
    form.put('/devices/' + props.device.id);
}
</script>

<template>
    <Head title="Edit Perangkat" />

    <div class="page-title-box">
        <h1 class="h4">Edit Perangkat</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">Form Perangkat WiFi</h2>
        </div>

        <div class="card-body">
            <form @submit.prevent="simpanDevice">
                <fieldset :disabled="form.processing">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="device-code" class="form-label">Kode Perangkat</label>
                            <input
                                id="device-code"
                                v-model="form.code"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.code }"
                                maxlength="255"
                                placeholder="Contoh: WIFI-001"
                                :disabled="form.processing"
                                required
                            >
                            <div v-if="form.errors.code" class="invalid-feedback" role="alert">
                                {{ form.errors.code }}
                            </div>
                        </div>
    
                        <div class="col-md-6 mb-3">
                            <label for="device-serial-number" class="form-label">Nomor Seri</label>
                            <input
                                id="device-serial-number"
                                v-model="form.serial_number"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.serial_number }"
                                maxlength="255"
                                placeholder="Contoh: SN123456"
                                :disabled="form.processing"
                                required
                            >
                            <div v-if="form.errors.serial_number" class="invalid-feedback" role="alert">
                                {{ form.errors.serial_number }}
                            </div>
                        </div>
    
                        <div class="col-md-6 mb-3">
                            <label for="device-note" class="form-label">Catatan</label>
                            <textarea
                                id="device-note"
                                v-model="form.note"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.note }"
                                rows="3"
                                placeholder="Contoh: Charger lengkap. Boleh dikosongkan."
                                :disabled="form.processing"
                            ></textarea>
                            <div v-if="form.errors.note" class="invalid-feedback" role="alert">
                                {{ form.errors.note }}
                            </div>
                        </div>
    
                        <div class="col-md-6 mb-3">
                            <label for="device-status" class="form-label">Status</label>
                            <select
                                v-select2
                                id="device-status"
                                v-model="form.status"
                                class="form-select"
                                :class="{ 'is-invalid': form.errors.status }"
                                :disabled="form.processing"
                                required
                            >
                                <option value="available">Tersedia</option>
                                <option value="maintenance">Perawatan</option>
                            </select>
                            <div v-if="form.errors.status" class="invalid-feedback" role="alert">
                                {{ form.errors.status }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <Link href="/devices" class="btn btn-light">Batal</Link>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</template>
