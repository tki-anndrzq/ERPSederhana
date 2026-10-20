<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import AppHeader from '../Components/AppHeader.vue';
import AppSidebar from '../Components/AppSidebar.vue';
import AppFooter from '../Components/AppFooter.vue';

const mobile = ref(false);
const sidebarOpen = ref(false);
const collapsed = ref(false);
const sidebarExpanded = computed(() => mobile.value ? sidebarOpen.value : !collapsed.value);

function syncLayout() {
    document.documentElement.dataset.sidebarSize = mobile.value || !collapsed.value ? 'lg' : 'sm';
    document.body.classList.toggle('vertical-sidebar-enable', mobile.value && sidebarOpen.value);
}

function updateViewport() {
    mobile.value = window.innerWidth < 768;
    sidebarOpen.value = false;
    syncLayout();
}

function toggleSidebar() {
    if (mobile.value) {
        sidebarOpen.value = !sidebarOpen.value;
    } else {
        collapsed.value = !collapsed.value;
    }
}

function closeSidebar() {
    sidebarOpen.value = false;
}

function handleEscape(event) {
    if (event.key === 'Escape' && sidebarOpen.value) {
        closeSidebar();
        document.querySelector('[aria-controls="app-sidebar"]')?.focus();
    }
}

watch([mobile, sidebarOpen, collapsed], syncLayout);

onMounted(() => {
    updateViewport();
    window.addEventListener('resize', updateViewport);
    window.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', updateViewport);
    window.removeEventListener('keydown', handleEscape);
    document.body.classList.remove('vertical-sidebar-enable');
    document.documentElement.dataset.sidebarSize = 'lg';
});
</script>

<template>
    <div id="layout-wrapper">
        <a href="#main-content" class="visually-hidden-focusable skip-link">Langsung ke konten</a>
        <AppHeader :sidebar-expanded="sidebarExpanded" @toggle-sidebar="toggleSidebar" />
        <div :inert="mobile && !sidebarOpen">
            <AppSidebar @navigate="closeSidebar" />
        </div>
        <button
            v-if="mobile && sidebarOpen"
            type="button"
            class="vertical-overlay sidebar-overlay"
            aria-label="Tutup navigasi"
            @click="closeSidebar"
        ></button>
        <div class="main-content">
            <main id="main-content" class="page-content" tabindex="-1">
                <div class="container-fluid"><slot /></div>
            </main>
            <AppFooter />
        </div>
    </div>
</template>
