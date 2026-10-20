<template>
    <div class="auth-page-wrapper pt-5">
        <div class="auth-one-bg-position auth-one-bg" aria-hidden="true">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" focusable="false">
                    <path d="M0,36 C144,53 432,123 720,124 C1008,124 1296,56 1440,40 L1440,140 L0,140 Z" />
                </svg>
            </div>
        </div>
        <main class="auth-page-content">
            <div class="container">
                <div class="text-center mt-sm-5 mb-4">
                    <img src="/assets/images/logo-light.png" alt="Velzon" height="24">
                    <p class="mt-3 text-white-50">Rental WiFi</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5"><slot /></div>
                </div>
            </div>
        </main>
        <footer class="footer text-center">
            <div class="container text-muted">
                &copy; {{ new Date().getFullYear() }} Rental WiFi. Template Velzon oleh Themesbrand.
            </div>
        </footer>
    </div>
</template>
