<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    devices: { type: Array, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formHapus = useForm({});

function hapusDevice(device) {
    const yakin = window.confirm('Hapus perangkat ' + device.code + '?');

    if (yakin) {
        formHapus.delete('/devices/' + device.id, {
            preserveScroll: true,
        });
    }
}
</script>


<template>
    <Head title="Perangkat WiFi" />

    <div class="page-title-box">
        <h1 class="h4">Perangkat WiFi</h1>
    </div>

    <div v-if="success" class="alert alert-success" role="status">
        {{ success }}
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">
        {{ error }}
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h2 class="card-title mb-1">Daftar Perangkat WiFi</h2>
                <p class="text-muted mb-0">Data perangkat WiFi untuk disewakan.</p>
            </div>

            <Link href="/devices/create" class="btn btn-primary">Tambah Perangkat</Link>
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
                        <tr v-for="(device, index) in devices" :key="device.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ device.code }}</td>
                            <td>{{ device.serial_number }}</td>
                            <td>
                                <span v-if="device.status === 'available'" class="badge bg-success-subtle text-success">
                                    Tersedia
                                </span>
                                <span v-else-if="device.status === 'reserved'" class="badge bg-warning-subtle text-warning">
                                    Dipesan
                                </span>
                                <span v-else-if="device.status === 'rented'" class="badge bg-primary-subtle text-primary">
                                    Disewa
                                </span>
                                <span v-else class="badge bg-danger-subtle text-danger">
                                    Perawatan
                                </span>
                            </td>
                            <td>{{ device.note || '-' }}</td>
                            <td>
                                <Link v-if="device.status === 'available' || device.status === 'maintenance'" :href="'/devices/' + device.id + '/edit'" class="btn btn-sm btn-outline-primary">Edit</Link>
                                <button type="button" class="btn btn-sm btn-outline-danger" :disabled="formHapus.processing || device.status === 'reserved' || device.status === 'rented'" @click="hapusDevice(device)">
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
