<script setup>
import { computed, useSlots } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';

DataTable.use(DataTablesCore);

const props = defineProps({
    data: { type: Array, required: true },
    columns: { type: Array, required: true },
    options: { type: Object, default: () => ({}) },
});

// Data dari pengguna ditampilkan sebagai teks, bukan HTML. Kolom khusus dapat
// memakai named slot Vue seperti #column-status atau #column-actions.
const slots = useSlots();
const columns = computed(() => props.columns.map((column, index) => {
    const namedSlot = column.name ? `column-${column.name}` : null;
    const slotName = namedSlot && slots[namedSlot]
        ? namedSlot
        : (slots[`column-${index}`] ? `column-${index}` : null);
    const textRenderer = DataTablesCore.render.text();

    return {
        ...column,
        render: column.render ?? (slotName
            ? { ...textRenderer, display: `#${slotName}` }
            : textRenderer),
    };
}));

const options = computed(() => ({
    responsive: true,
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50],
    ...props.options,
    language: {
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ baris',
        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
        infoEmpty: 'Belum ada data',
        infoFiltered: '(disaring dari _MAX_ data)',
        zeroRecords: 'Data tidak ditemukan',
        emptyTable: 'Belum ada data',
        paginate: { first: 'Pertama', previous: 'Sebelumnya', next: 'Berikutnya', last: 'Terakhir' },
        ...props.options.language,
    },
}));
</script>

<template>
    <div class="datatable-container">
        <DataTable :data="data" :columns="columns" :options="options" class="table table-bordered table-striped align-middle nowrap w-100">
            <template v-for="(_, name) in $slots" #[name]="slotProps">
                <slot :name="name" v-bind="slotProps ?? {}" />
            </template>
        </DataTable>
    </div>
</template>

<style scoped>
.datatable-container {
    min-width: 0;
}

.datatable-container :deep(div.dt-container div.dt-layout-row) {
    gap: 0.75rem;
}

.datatable-container :deep(table.dataTable td) {
    vertical-align: middle;
}
</style>
