<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';
import BaseDataTable from '../../Components/BaseDataTable.vue';

defineOptions({ layout: AdminLayout });

const destinations = ['Jepang', 'Singapura', 'Korea Selatan', 'Thailand', 'Malaysia'];
const statuses = ['Tersedia', 'Disewa', 'Maintenance'];
const rows = ref(Array.from({ length: 15 }, (_, index) => ({
    code: `WF-${String(index + 1).padStart(3, '0')}`,
    serial: `DEMO-${String(index + 1).padStart(5, '0')}`,
    destination: destinations[index % destinations.length],
    status: statuses[index % statuses.length],
})));

const columns = [
    { data: 'code', title: 'Kode perangkat', responsivePriority: 1 },
    { data: 'serial', title: 'Nomor seri' },
    { data: 'destination', title: 'Destinasi' },
    { data: 'status', title: 'Status', name: 'status', responsivePriority: 2 },
];

const badgeClasses = {
    Tersedia: 'bg-success-subtle text-success',
    Disewa: 'bg-primary-subtle text-primary',
    Maintenance: 'bg-warning-subtle text-warning',
};

function addExample() {
    const number = rows.value.length + 1;
    rows.value.push({
        code: `WF-${String(number).padStart(3, '0')}`,
        serial: `DEMO-${String(number).padStart(5, '0')}`,
        destination: 'Indonesia',
        status: 'Tersedia',
    });
}
</script>

<template>
    <Head title="DataTables" />
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h1 class="h4 mb-sm-0">DataTables</h1>
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">Referensi UI</li>
                <li class="breadcrumb-item active" aria-current="page">DataTables</li>
            </ol>
        </nav>
    </div>
    <section class="card" aria-labelledby="datatable-title">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 id="datatable-title" class="card-title mb-1">Contoh DataTables</h2>
                <p class="text-muted mb-0">Data contoh untuk mencoba pencarian, pengurutan, pagination, dan tampilan responsif.</p>
            </div>
            <button type="button" class="btn btn-primary" @click="addExample">
                <i class="ri-add-line me-1" aria-hidden="true"></i>Tambah contoh
            </button>
        </div>
        <div class="card-body">
            <BaseDataTable :data="rows" :columns="columns">
                <template #column-status="{ cellData }">
                    <span class="badge" :class="badgeClasses[cellData]">{{ cellData }}</span>
                </template>
            </BaseDataTable>
        </div>
    </section>
</template>
