<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    gender: props.user.gender,
});

function simpanUser() {
    form.put('/users/' + props.user.id);
}
</script>
<template>
        <Head title="Tambah Pengguna" />

    <div class="page-title-box">
        <h1 class="h4">Tambah Pengguna</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">Tambah Pengguna</h2>
        </div>

        <div class="card-body">
            <form @submit.prevent="simpanUser">
                <fieldset :disabled="form.processing">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user" class="form-label">Nama Pengguna</label>
                            <input
                                id="user"
                                v-model="form.name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                :aria-invalid="Boolean(form.errors.name)"
                                :aria-describedby="form.errors.name ? 'user-error' : undefined"
                                maxlength="255"
                                placeholder="Contoh: Jepang Harian"
                                required
                            >
                            <div v-if="form.errors.name" id="user-error" class="invalid-feedback" role="alert">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user-email" class="form-label">Email</label>
                            <input
                                id="user-email"
                                v-model="form.email"
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                :aria-invalid="Boolean(form.errors.email)"
                                :aria-describedby="form.errors.email ? 'user-email-error' : undefined"
                                maxlength="255"
                                placeholder="Contoh: Jepang"
                                required
                            >
                            <div v-if="form.errors.email" id="user-email-error" class="invalid-feedback" role="alert">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user-gender" class="form-label">Jenis Kelamin</label>
                            <select
                                v-select2
                                id="user-gender"
                                v-model="form.gender"
                                class="form-select"
                                :class="{ 'is-invalid': form.errors.gender }"
                                :disabled="form.processing"
                                required
                            >
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div v-if="form.errors.gender" class="invalid-feedback" role="alert">
                                {{ form.errors.gender }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user-password" class="form-label">Password</label>
                            <input
                                id="user-password"
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                :aria-invalid="Boolean(form.errors.password)"
                                :aria-describedby="form.errors.password ? 'user-password-error' : undefined"
                                maxlength="255"
                                placeholder="8 karakter atau lebih"
                                nullable
                            >
                            <div v-if="form.errors.password" id="user-password-error" class="invalid-feedback" role="alert">
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <Link href="/users" class="btn btn-light">Batal</Link>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</template>
