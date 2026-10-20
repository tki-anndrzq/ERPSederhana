<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

defineOptions({ layout: GuestLayout });

const showPassword = ref(false);
const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Masuk" />
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="text-center mt-2 mb-4">
                <h1 class="h5 text-primary">Selamat datang kembali</h1>
                <p class="text-muted">Masuk untuk mengelola operasional rental WiFi.</p>
            </div>
            <form @submit.prevent="submit">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.email }"
                        :aria-invalid="Boolean(form.errors.email)"
                        :aria-describedby="form.errors.email ? 'email-error' : undefined"
                        autocomplete="username"
                        placeholder="nama@perusahaan.com"
                        required
                        autofocus
                    >
                    <div v-if="form.errors.email" id="email-error" class="invalid-feedback" role="alert">
                        {{ form.errors.email }}
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative auth-pass-inputgroup">
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            class="form-control pe-5"
                            :class="{ 'is-invalid': form.errors.password }"
                            :aria-invalid="Boolean(form.errors.password)"
                            :aria-describedby="form.errors.password ? 'password-error' : undefined"
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                            required
                        >
                        <button
                            type="button"
                            class="btn btn-link position-absolute end-0 top-0 text-muted"
                            :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                            :aria-pressed="showPassword"
                            aria-controls="password"
                            @click="showPassword = !showPassword"
                        >
                            <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'" aria-hidden="true"></i>
                        </button>
                        <div v-if="form.errors.password" id="password-error" class="invalid-feedback" role="alert">
                            {{ form.errors.password }}
                        </div>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input id="remember" v-model="form.remember" type="checkbox" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-success w-100" :disabled="form.processing" :aria-busy="form.processing">
                    {{ form.processing ? 'Memproses...' : 'Masuk' }}
                </button>
            </form>
        </div>
    </div>
</template>
