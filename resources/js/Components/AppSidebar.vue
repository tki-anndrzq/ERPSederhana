<script setup>
import { Link, usePage } from '@inertiajs/vue3';

defineEmits(['navigate']);
const page = usePage();

// Halaman tambah dan edit masih termasuk menu yang sama.
function menuAktif(menu) {
    const url = page.url.split('?')[0];

    return url === menu || url.startsWith(menu + '/');
}
</script>

<template>
    <aside id="app-sidebar" class="app-menu navbar-menu" aria-label="Navigasi utama">
        <div class="navbar-brand-box">
            <Link href="/dashboard" class="logo logo-light" aria-label="Rental WiFi - Dashboard" @click="$emit('navigate')">
                <span class="logo-sm">
                    <img src="/assets/images/logo-sm.png" alt="" height="28">
                </span>
                <span class="logo-lg">
                    <img src="/assets/images/logo-light.png" alt="Velzon" height="22">
                </span>
            </Link>
        </div>
        <nav class="sidebar-scroll">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="menu-title"><span>Workspace</span></li>
                    <li class="nav-item">
                        <Link
                            href="/dashboard"
                            aria-label="Dashboard"
                            class="nav-link menu-link"
                            :class="{ active: page.url.split('?')[0] === '/dashboard' }"
                            :aria-current="page.url.split('?')[0] === '/dashboard' ? 'page' : undefined"
                            @click="$emit('navigate')"
                        >
                            <i class="ri-dashboard-2-line" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </Link>
                    </li>

                    <li class="menu-title"><span>Data Master</span></li>
                    <li class="nav-item">
                        <Link
                            href="/packages"
                            aria-label="Paket Sewa"
                            class="nav-link menu-link"
                            :class="{ active: menuAktif('/packages') }"
                            :aria-current="menuAktif('/packages') ? 'page' : undefined"
                            @click="$emit('navigate')"
                        >
                            <i class="ri-dropbox-line" aria-hidden="true"></i>
                            <span>Paket Sewa</span>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link
                            href="/devices"
                            aria-label="Perangkat"
                            class="nav-link menu-link"
                            :class="{ active: menuAktif('/devices') }"
                            :aria-current="menuAktif('/devices') ? 'page' : undefined"
                            @click="$emit('navigate')"
                        >
                            <i class="ri-wifi-line" aria-hidden="true"></i>
                            <span>Perangkat</span>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link
                            href="/users"
                            aria-label="Pengguna"
                            class="nav-link menu-link"
                            :class="{ active: page.url.split('?')[0].startsWith('/bookings') }"
                            :aria-current="page.url.split('?')[0].startsWith('/bookings') ? 'page' : undefined"
                            @click="$emit('navigate')"
                        >
                            <i class="ri-calendar-check-line" aria-hidden="true"></i>
                            <span>Pengguna</span>
                        </Link>
                    </li>
                    <li class="menu-title"><span>Transaksi</span></li>
                    <li class="nav-item">
                        <Link
                            href="/bookings"
                            aria-label="Pemesanan"
                            class="nav-link menu-link"
                            :class="{ active: page.url.split('?')[0].startsWith('/bookings') }"
                            :aria-current="page.url.split('?')[0].startsWith('/bookings') ? 'page' : undefined"
                            @click="$emit('navigate')"
                        >
                            <i class="ri-calendar-check-line" aria-hidden="true"></i>
                            <span>Pemesanan</span>
                        </Link>
                    </li>


                </ul>
            </div>
        </nav>
        <div class="sidebar-background"></div>
    </aside>
</template>
