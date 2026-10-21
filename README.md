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
- CRUD paket sewa dan perangkat WiFi.
- Pemesanan: daftar, form tambah, detail, perhitungan harga, dan pembatalan.
- Billing, payment gateway, penyerahan/pengembalian perangkat, dan n8n
  merupakan pengembangan berikutnya untuk technical test.

## Kebutuhan

- PHP 8.2 dengan ekstensi Laravel dan PDO sesuai database.
- Composer 2.
- Node.js 22.12+ atau Node.js 24 dan npm.
- PostgreSQL dengan ekstensi PHP `pdo_pgsql`.

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
Buat database PostgreSQL kosong terlebih dahulu.

Untuk database baru yang sudah dikonfigurasi:

```powershell
php artisan migrate --seed
```

Seeder membuat akun demo `admin@gmail.com` dengan password contoh `123123`
pada environment local/testing. Password disimpan sebagai hash menggunakan
`Hash::make`. Jika akun sudah ada, seeder tidak membuat akun baru atau mengganti
password/data akun tersebut. Seeder demo tidak membuat akun di production.

Untuk database yang sudah digunakan, jalankan `php artisan migrate` agar tabel
dan perubahan kolom terbaru diterapkan, lalu `php artisan db:seed` bila memerlukan
akun demo. Untuk akun
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

## Halaman Data Master

Halaman paket sewa dan perangkat dipisah agar alurnya mudah dibaca:

```text
resources/js/Pages/Dashboard/
  Package/
    index.vue   daftar paket dan tombol hapus
    create.vue  form tambah paket
    edit.vue    form edit paket
  Devices/
    index.vue   daftar perangkat dan tombol hapus
    create.vue  form tambah perangkat
    edit.vue    form edit perangkat
```

Controller tetap di `app/Http/Controllers/Dashboard`. Method `index` mengambil
daftar, `create` menampilkan form tambah, `store` menyimpan data baru, `edit`
mengambil data untuk form edit, `update` menyimpan perubahan, dan `destroy`
menghapus data. Validasi langsung di controller; form Vue memakai `useForm`.
Setelah berhasil menyimpan, pengguna kembali ke daftar dengan pesan berhasil.

## Pemesanan

Buka menu **Transaksi > Pemesanan** (`/bookings`), lalu klik **Tambah Pemesanan**.
Siapkan minimal satu paket aktif dan satu perangkat berstatus Tersedia.

- `app/Http/Controllers/Dashboard/BookingController.php` menangani daftar,
  form tambah, simpan, detail, dan pembatalan. Validasi langsung di controller
  mengikuti pola controller paket dan perangkat.
- `resources/js/Pages/Dashboard/Bookings/index.vue` menampilkan daftar.
- `create.vue` berisi form pelanggan, tujuan perjalanan, paket, perangkat, dan tanggal.
- `show.vue` menampilkan rincian dan tombol pembatalan.
- Model `Booking` terhubung dengan `Customer`, `RentalPackage`, dan `Devices`.
  Relasi dimuat menggunakan `with()` untuk menghindari N+1.

Satu pemesanan memakai satu perangkat. Untuk versi awal ini, data pelanggan
dicatat baru pada setiap pemesanan. Belum ada pemilihan pelanggan lama.
Tujuan perjalanan disimpan di `bookings.destination`.

Backend menghitung hari secara inklusif: tanggal 21 sampai 23 adalah 3 hari,
tanggal yang sama adalah 1 hari, maksimal 365 hari. Harga paket per hari disalin
ke `daily_rate`, lalu dikalikan jumlah hari untuk menentukan `total_amount`.
Perubahan harga paket setelahnya tidak mengubah harga pemesanan lama.

Saat disimpan, pemesanan berstatus `pending_payment` dan perangkat `reserved`.
Hanya paket aktif dan perangkat `available` yang bisa dipilih. Perangkat langsung
dicadangkan, termasuk untuk sewa di masa depan; belum ada penjadwalan perangkat
berdasarkan rentang tanggal. Transaksi database dan `lockForUpdate()` menjaga
agar perangkat tidak dipesan dua admin bersamaan.

Pemesanan yang masih `pending_payment` dapat dibatalkan menjadi `cancelled`;
perangkat kembali `available`. Riwayat pemesanan tetap disimpan. Perangkat yang
sedang Dipesan/Disewa tidak dapat diedit dari master perangkat, dan perangkat
yang memiliki riwayat pemesanan tidak dapat dihapus karena foreign key.

Status perangkat menggunakan enum pada migration asli tabel `devices`, dengan
nilai `available`, `reserved`, `rented`, dan `maintenance`; default `available`.
Admin hanya mengatur Tersedia/Perawatan dari master;
status Dipesan/Disewa mengikuti transaksi. Alur pembayaran dan perubahan ke
Disewa saat penyerahan akan ditambahkan pada tahap berikutnya.

## Dropdown Select2

Dropdown paket/perangkat pada pemesanan dan status pada form perangkat memakai
Select2. Dependensi jQuery dan Select2 terpasang melalui npm dan dibundel Vite.
Integrasi berada di `resources/js/directives/select2.js`, didaftarkan pada `app.js`.

Untuk dropdown berikutnya, tetap gunakan select biasa dengan `v-model`, lalu
tambahkan `v-select2`. Daftar pilihan tetap ditulis menggunakan option dan v-for.
Directive meneruskan pilihan ke Vue dan membersihkan Select2 saat pindah halaman.

```html
<select v-select2 v-model="form.status">
    <option value="available">Tersedia</option>
    <option value="maintenance">Perawatan</option>
</select>
```

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
