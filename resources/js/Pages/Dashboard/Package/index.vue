<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    package: { type: Array, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formHapus = useForm({});

function hapusPaket(item) {
    const yakin = window.confirm('Hapus paket ' + item.name + '?');

    if (yakin) {
        formHapus.delete('/packages/' + item.id, {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <Head title="Paket Sewa" />

    <div class="page-title-box">
        <h1 class="h4">Paket Sewa</h1>
    </div>

    <div v-if="success" class="alert alert-success" role="status">
        {{ success }}
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">
        {{ error }}
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h2 class="card-title mb-0">Daftar Paket Sewa</h2>
            <Link href="/packages/create" class="btn btn-primary">Tambah Paket</Link>
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
                        <tr v-for="(item, index) in package" :key="item.id">
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
                                    <Link :href="'/packages/' + item.id + '/edit'" class="btn btn-sm btn-warning">Edit</Link>
                                    <button type="button" class="btn btn-sm btn-danger" :disabled="formHapus.processing" @click="hapusPaket(item)">
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
