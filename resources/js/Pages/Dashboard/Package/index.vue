<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({
    layout: AdminLayout,
});

defineProps({
    package: {
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

// Form ini dipakai untuk tambah dan edit.
const form = useForm({
    name: '',
    destination: '',
    daily_price: '',
    is_active: true,
});

// Form hapus dipisah agar tidak mengubah isian tambah/edit.
const formHapus = useForm({});

function tambahPaket() {
    editId.value = null;
    form.reset();
    form.clearErrors();
    tampilkanForm.value = true;
}

function editPaket(item) {
    editId.value = item.id;
    form.name = item.name;
    form.destination = item.destination;
    form.daily_price = item.daily_price;
    form.is_active = item.is_active;
    form.clearErrors();
    tampilkanForm.value = true;
}

function tutupForm() {
    tampilkanForm.value = false;
    editId.value = null;
    form.reset();
    form.clearErrors();
}

function simpanPaket() {
    if (editId.value === null) {
        form.post('/packages', {
            preserveScroll: true,
            onSuccess: () => tutupForm(),
        });
    } else {
        form.put('/packages/' + editId.value, {
            preserveScroll: true,
            onSuccess: () => tutupForm(),
        });
    }
}

function hapusPaket(item) {
    const yakin = window.confirm('Hapus paket "' + item.name + '"?');

    if (yakin) {
        formHapus.delete('/packages/' + item.id, {
            preserveScroll: true,
            onSuccess: (page) => {
                if (!page.props.error && editId.value === item.id) {
                    tutupForm();
                }
            },
        });
    }
}
</script>

<template>
    <div class="page-title-box">
        <h1 class="h4">Paket Sewa</h1>
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
                {{ editId === null ? 'Tambah Paket' : 'Edit Paket' }}
            </h2>
        </div>

        <div class="card-body">
            <form @submit.prevent="simpanPaket">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="package-name" class="form-label">Nama Paket</label>
                        <input
                            id="package-name"
                            v-model="form.name"
                            type="text"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.name }"
                            :aria-invalid="Boolean(form.errors.name)"
                            :aria-describedby="form.errors.name ? 'package-name-error' : undefined"
                            maxlength="255"
                            placeholder="Contoh: Jepang Harian"
                            required
                        >
                        <div v-if="form.errors.name" id="package-name-error" class="invalid-feedback" role="alert">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="package-destination" class="form-label">Destinasi</label>
                        <input
                            id="package-destination"
                            v-model="form.destination"
                            type="text"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.destination }"
                            :aria-invalid="Boolean(form.errors.destination)"
                            :aria-describedby="form.errors.destination ? 'package-destination-error' : undefined"
                            maxlength="255"
                            placeholder="Contoh: Jepang"
                            required
                        >
                        <div v-if="form.errors.destination" id="package-destination-error" class="invalid-feedback" role="alert">
                            {{ form.errors.destination }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="package-price" class="form-label">Harga per Hari (Rp)</label>
                        <input
                            id="package-price"
                            v-model="form.daily_price"
                            type="number"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.daily_price }"
                            :aria-invalid="Boolean(form.errors.daily_price)"
                            :aria-describedby="form.errors.daily_price ? 'package-price-error' : undefined"
                            min="1"
                            max="9999999999.99"
                            step="0.01"
                            placeholder="40000"
                            required
                        >
                        <div v-if="form.errors.daily_price" id="package-price-error" class="invalid-feedback" role="alert">
                            {{ form.errors.daily_price }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-md-4">
                            <input id="package-active" v-model="form.is_active" type="checkbox" class="form-check-input">
                            <label for="package-active" class="form-check-label">Paket aktif</label>
                            <div v-if="form.errors.is_active" class="text-danger mt-1" role="alert">
                                {{ form.errors.is_active }}
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
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h2 class="card-title mb-0">Daftar Paket Sewa</h2>
            <button type="button" class="btn btn-primary" :disabled="form.processing || formHapus.processing" @click="tambahPaket">
                Tambah Paket
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Paket</th>
                            <th>Destinasi</th>
                            <th>Harga per Hari</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item, index in package" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.destination }}</td>
                            <td>Rp {{ item.daily_price }}</td>
                            <td>
                                <span v-if="item.is_active" class="badge bg-success-subtle text-success">Aktif</span>
                                <span v-else class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-warning" :disabled="form.processing || formHapus.processing" @click="editPaket(item)">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" :disabled="form.processing || formHapus.processing" @click="hapusPaket(item)">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="package.length === 0">
                            <td colspan="6" class="text-center">
                                Belum ada data paket.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
