# Rental WiFi — Laravel + Inertia + Vue Starter

Fondasi aplikasi dalam satu project Laravel, memakai tampilan Velzon lokal.
Halaman aktif menggunakan Vue 3 melalui Inertia; routing dan session ditangani
Laravel. Tidak membutuhkan project frontend terpisah, Vue Router, atau Pinia.

## Cakupan saat ini

- Login Vue dengan validasi, ingat saya, dan pembatasan percobaan login.
- Dashboard awal, layout responsif, sidebar, menu akun, dan logout melalui POST.
- CSS, font, ikon, dan gambar template di `public/assets`, tanpa script demo/CDN
  pada halaman aktif.
- Controller CRUD pengguna dan galeri demo Blade lama sudah dibersihkan.
- Semua view asli tersedia sebagai referensi di `resources/template-reference/views`.
- DataTables versi Vue tersedia melalui menu **Referensi UI → DataTables**
  (`/ui/datatables`), dengan search, sort, pagination, responsive, dan data reaktif.
- Belum ada modul booking, inventory rental, billing, payment gateway, atau n8n.
  Integrasi tersebut merupakan pengembangan berikutnya untuk technical test.

## Kebutuhan

- PHP 8.2 dengan ekstensi Laravel dan PDO sesuai database.
- Composer 2.
- Node.js 22.12+ atau Node.js 24 dan npm.
- MySQL/MariaDB atau PostgreSQL untuk penggunaan aplikasi.

Project masih menggunakan Laravel 10 dari template asal. Versi ini sudah melewati
masa dukungan keamanan; upgrade framework dan audit dependensi perlu ditangani
sebelum penggunaan produksi. Konversi template ini tidak meng-upgrade framework.

## Instalasi baru

```powershell
composer install
npm.cmd ci
```

Hanya jika `.env` belum ada, salin `.env.example` menjadi `.env`, lalu jalankan
`php artisan key:generate`. Atur `APP_URL` dan koneksi database di `.env` lokal.
Buat database kosong terlebih dahulu melalui MySQL/MariaDB atau PostgreSQL.

Untuk database baru yang sudah dikonfigurasi:

```powershell
php artisan migrate --seed
```

Seeder membuat akun demo `admin@gmail.com` dengan password contoh `123123`
pada environment local/testing. Password disimpan sebagai hash menggunakan
`Hash::make`. Jika akun sudah ada, seeder tidak membuat akun baru atau mengganti
password/data akun tersebut. Seeder demo tidak membuat akun di production.

Jika tabel sudah tersedia, cukup jalankan `php artisan db:seed`. Untuk akun
dengan nama/email pilihan sendiri, alternatifnya `php artisan app:create-user`.
Command tersebut meminta nama, email, password, dan konfirmasi; input password
disembunyikan dan minimal 12 karakter.

Untuk database template yang sudah memiliki pengguna, akun lama tetap dapat
digunakan. Tidak perlu reset database, mengubah APP_KEY yang sudah ada, atau
mengulang pembuatan akun. Proses konversi tidak menjalankan migrasi database.

## Menjalankan

Terminal pertama:

```powershell
php artisan serve
```

Terminal kedua:

```powershell
npm.cmd run dev
```

Buka `http://127.0.0.1:8000`. Tamu diarahkan ke `/login`; pengguna yang sudah
masuk diarahkan ke `/dashboard`. Untuk menguji hasil build lokal, hentikan Vite
lalu jalankan `npm.cmd run build` dan tetap jalankan server Laravel.

`npm.cmd` digunakan pada Windows bila PowerShell memblokir `npm.ps1`.

## Struktur

```text
app/Http/Controllers/        Auth dan dashboard
app/Http/Requests/Auth/      Validasi dan pembatasan login
app/Http/Middleware/         Middleware Inertia dan props pengguna
app/Console/Commands/        Pembuatan akun interaktif
app/Models/                 Model pengguna dengan UUID
resources/js/Pages/         Halaman Vue
resources/js/Layouts/       Layout admin dan tamu
resources/js/Components/    Sidebar, header, footer
resources/template-reference/  Seluruh contoh view template asli
resources/views/app.blade.php  Root document Inertia
public/assets/              Aset Velzon asli
routes/web.php              Routing halaman dan autentikasi
routes/api.php              Tempat endpoint integrasi berikutnya
```

Data halaman mengikuti alur route Laravel → controller → Inertia → Vue.
Hanya `id`, `name`, dan `email` pengguna yang dibagikan ke frontend.

Tiga migrasi dasar dipertahankan: users, failed_jobs, personal_access_tokens.
Tidak ada migrasi bisnis lama. UUID dan kolom `gender` template asal tetap
dipertahankan agar database lama kompatibel. Factory disesuaikan dengan skema
tersebut; kolom gender tidak digunakan oleh tampilan starter.

## Menggunakan DataTables

Gunakan `resources/js/Components/BaseDataTable.vue` dengan props `data`,
`columns`, dan `options` (opsional). Contoh lengkap berada di
`resources/js/Pages/Ui/DataTables.vue`. Named slot `#column-status` memperlihatkan
cara menampilkan badge Vue di sel tabel. Kirim baris lewat prop `data`, jangan
gunakan `v-for` pada tbody yang juga dikelola DataTables.

File Blade asli DataTables tersedia di
`resources/template-reference/views/components/tables/tables-datatables.blade.php`.
Seluruh galeri UI lama dipertahankan di folder referensi, sehingga komponen
lain bisa diambil dan dikonversi bertahap tanpa memulihkan controller CRUD lama.

## Pemeriksaan

```powershell
npm.cmd run build
php artisan route:list --except-vendor
```

## Cadangan konversi

Source sebelum konversi disimpan sebagai ZIP lokal di
`storage/app/template-backups/`. Aset `public/assets` tetap berada di lokasi
asal. ZIP tidak menyertakan `.env`, vendor, atau node_modules. Direktori backup
dan cache tool diabaikan Git, berada di luar direktori publik, dan tidak boleh
ikut dibagikan bersama source. Atribusi Velzon/Themesbrand tetap dipertahankan.
