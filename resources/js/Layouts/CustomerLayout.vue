<template>
    <div class="min-vh-100 bg-light d-flex flex-column">
        <header class="bg-white border-bottom">
            <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span class="h5 mb-0 text-primary">Rental WiFi</span>
                <span class="text-muted">Layanan Pelanggan</span>
            </div>
        </header>

        <main class="container py-4 py-md-5 flex-grow-1">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <slot />
                </div>
            </div>
        </main>

        <footer class="text-center text-muted py-3">
            &copy; {{ new Date().getFullYear() }} Rental WiFi
        </footer>
    </div>
</template>
