<script setup>

import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

defineProps({
    users: { type: Array, required: true },
    success: { type: String, default: null },
    error: { type: String, default: null },
});

const formHapus = useForm({});

function hapusUser(user){
    const yakin = window.confirm('Hapus Pengguna' + user.name + '?');

    if(yakin){
        formHapus.delete('/users/' + user.id, {
            preserveScroll: true,
        })
    }
}
</script>
<template>
    <Head title="Pengguna" />

    <div class="page-title-box">
        <h1 class="h4">Pengguna</h1>
    </div>

    <div v-if="success" class="alert alert-success" role="status">
        {{ success }}
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">
        {{ error }}
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-users-center justify-content-between gap-2">
            <h2 class="card-title mb-0">Daftar Pengguna</h2>
            <Link href="/users/create" class="btn btn-primary">Tambah Pengguna</Link>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(user, index) in users" :key="user.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.gender }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <Link :href="'/users/' + user.id + '/edit'" class="btn btn-sm btn-warning">Edit</Link>
                                    <button type="button" class="btn btn-sm btn-danger" :disabled="formHapus.processing" @click="hapusUser(user)">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="users.length === 0">
                            <td colspan="6" class="text-center">
                                Belum ada Pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
