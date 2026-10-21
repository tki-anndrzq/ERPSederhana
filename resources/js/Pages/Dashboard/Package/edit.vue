<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../../Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    package: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.package.name,
    destination: props.package.destination,
    daily_price: props.package.daily_price,
    is_active: props.package.is_active,
});

function simpanPaket() {
    form.put('/packages/' + props.package.id);
}
</script>

<template>
    <Head title="Edit Paket" />

    <div class="page-title-box">
        <h1 class="h4">Edit Paket</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title mb-0">Form Paket Sewa</h2>
        </div>

        <div class="card-body">
            <form @submit.prevent="simpanPaket">
                <fieldset :disabled="form.processing">
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
                        <button type="submit" class="btn btn-primary">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <Link href="/packages" class="btn btn-light">Batal</Link>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</template>
