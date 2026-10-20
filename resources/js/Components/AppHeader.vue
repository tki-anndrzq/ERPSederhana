<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineProps({ sidebarExpanded: Boolean });
defineEmits(['toggle-sidebar']);

const page = usePage();
const accountMenu = ref(null);
const user = computed(() => page.props.auth.user);
</script>

<template>
    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex align-items-center gap-2">
                    <button
                        type="button"
                        class="btn btn-sm px-3 fs-20 header-item"
                        aria-label="Buka atau tutup navigasi"
                        aria-controls="app-sidebar"
                        :aria-expanded="sidebarExpanded"
                        @click="$emit('toggle-sidebar')"
                    >
                        <i class="ri-menu-2-line" aria-hidden="true"></i>
                    </button>
                    <span class="fw-semibold text-muted d-none d-sm-inline">Rental WiFi</span>
                </div>

                <details ref="accountMenu" class="header-item topbar-user account-menu" @keydown.esc="accountMenu.open = false">
                    <summary class="btn d-flex align-items-center gap-2 h-100" aria-label="Menu akun">
                        <span class="avatar-xs rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center text-primary">
                            <i class="ri-user-3-line fs-18" aria-hidden="true"></i>
                        </span>
                        <span class="text-start d-none d-md-block">
                            <span class="d-block fw-medium user-name-text">{{ user.name }}</span>
                            <span class="d-block fs-12 text-muted">{{ user.email }}</span>
                        </span>
                        <i class="ri-arrow-down-s-line" aria-hidden="true"></i>
                    </summary>
                    <div class="dropdown-menu dropdown-menu-end show shadow-sm">
                        <h6 class="dropdown-header">Akun saya</h6>
                        <Link href="/logout" method="post" as="button" class="dropdown-item">
                            <i class="ri-logout-box-r-line me-2" aria-hidden="true"></i>Keluar
                        </Link>
                    </div>
                </details>
            </div>
        </div>
    </header>
</template>
