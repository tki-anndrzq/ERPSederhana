<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    devices: {
        type: Array,
        required: true,
    },
    success: {
        type: String,
        default: null,
    },
    error: {
        type: String,
        default: null,
    },
});

const tampilkanForm = ref(false);
const editId = ref(null);

const form = useForm({
    code: '',
    serial_number: '',
    status: true,
    note: '',
});

const formHapus = useForm({});

function tambahDevice() {
    editId.value = null;
    form.reset();
    form.clearErrors();
    tampilkanForm.value = true;
}

function editDevice(device) {
    editId.value = device.id;
    form.code = device.code;
    form.serial_number = device.serial_number;
    form.status = device.status;
    form.note = device.note;
    form.clearErrors();
    tampilkanForm.value = true;
}

function tutupForm() {
    tampilkanForm.value = false;
    editId.value = null;
    form.reset();
    form.clearErrors();
}

function simpanDevice() {
    if (editId.value === null) {
        form.post('/devices', {
            preserveScroll: true,
            onSuccess: () => tutupForm(),
        });
    } else {
        form.put('/devices/' + editId.value, {
            preserveScroll: true,
            onSuccess: () => tutupForm(),
        });
    }
}


function hapusDevice(device) {
    const yakin = window.confirm('Hapus perangkat dengan kode ' + device.code + '?');
    if (yakin) {
        formHapus.delete('/devices/' + device.id, {
            preserveScroll: true,
            onSuccess: (page) => {
                if (!page.props.error && editId.value === device.id) {
                    tutupForm();
                }
            },
        });
    }
}

</script>


<template>
    <div class="page-title-box">
        <h1 class="h4">Perangkat WiFi</h1>
    </div>

    <div v-if="success" class="alert alert-success" role="status">
        {{ success }}
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">
        {{ error }}
    </div>
    <div v-if="tampilkanForm" class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">
                {{ editId === null ? 'Tambah Perangkat' : 'Edit Perangkat' }}
            </h2>
        </div>

        <div class="card-body">
            <form @submit.prevent="simpanDevice">
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
                            :disabled="form.processing || formHapus.processing"
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
                            :disabled="form.processing || formHapus.processing"
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
                            :disabled="form.processing || formHapus.processing"
                        ></textarea>
                        <div v-if="form.errors.note" class="invalid-feedback" role="alert">
                            {{ form.errors.note }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-md-4">
                            <input
                                id="device-status"
                                v-model="form.status"
                                type="checkbox"
                                class="form-check-input"
                                :disabled="form.processing || formHapus.processing"
                            >
                            <label for="device-status" class="form-check-label">Tersedia untuk disewakan</label>
                            <div v-if="form.errors.status" class="text-danger mt-1" role="alert">
                                {{ form.errors.status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing || formHapus.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                    <button type="button" class="btn btn-light" :disabled="form.processing || formHapus.processing" @click="tutupForm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h2 class="card-title mb-1">Daftar Perangkat WiFi</h2>
                <p class="text-muted mb-0">Data perangkat WiFi untuk disewakan.</p>
            </div>

            <button type="button" class="btn btn-primary" :disabled="form.processing || formHapus.processing" @click="tambahDevice">
                Tambah Perangkat
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kode Perangkat</th>
                            <th scope="col">Nomor Seri</th>
                            <th scope="col">Status</th>
                            <th scope="col">Catatan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="device, index in devices" :key="device.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ device.code }}</td>
                            <td>{{ device.serial_number }}</td>
                            <td>
                                <span v-if="device.status" class="badge bg-success-subtle text-success">
                                    Tersedia
                                </span>
                                <span v-else class="badge bg-danger-subtle text-danger">
                                    Tidak tersedia
                                </span>
                            </td>
                            <td>{{ device.note || '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" :disabled="form.processing || formHapus.processing" @click="editDevice(device)">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" :disabled="form.processing || formHapus.processing" @click="hapusDevice(device)">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="devices.length === 0">
                            <td colspan="6" class="text-center text-muted">
                                Belum ada perangkat WiFi.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
