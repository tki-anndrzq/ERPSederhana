# Rental WiFi

## Instalasi

Siapkan PHP 8.2, Composer 2, Node.js 22.12+ atau 24, npm, dan PostgreSQL.
Aktifkan ekstensi PHP `pdo_pgsql` dan `curl`.

1. Buka terminal di folder project, lalu install dependensi:

   ```powershell
   composer install
   npm.cmd ci
   ```

2. Untuk instalasi baru, salin `.env.example` menjadi `.env`:

   ```powershell
   Copy-Item .env.example .env
   ```

   Jika `.env` sudah ada, gunakan file tersebut. Sesuaikan koneksi database:

   ```dotenv
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=rental_wifi
   DB_USERNAME=postgres
   DB_PASSWORD=isi_password_postgresql
   MIDTRANS_SERVER_KEY=isi_server_key_sandbox_midtrans
   MIDTRANS_IS_PRODUCTION=false
   ```

   Buat database `rental_wifi` di PostgreSQL terlebih dahulu. Sesuaikan nama
   database, port, username, dan password dengan konfigurasi komputermu.
   Ambil Server Key dari **Settings > Access Keys** di dashboard Midtrans mode
   **Sandbox**. Simpan key hanya di `.env`, jangan di Vue atau GitHub.

   Setelah mengubah konfigurasi, jalankan:

   ```powershell
   php artisan config:clear
   ```

3. Pada instalasi baru, buat application key:

   ```powershell
   php artisan key:generate
   ```

   Kemudian buat tabel dan akun demo:

   ```powershell
   php artisan migrate --seed
   ```

4. Jalankan Laravel di terminal pertama:

   ```powershell
   php artisan serve
   ```

   Jalankan Vite di terminal kedua:

   ```powershell
   npm.cmd run dev
   ```

   Biarkan kedua terminal berjalan, lalu buka `http://127.0.0.1:8000`.
   Jika `npm` bisa digunakan langsung di terminalmu, kamu boleh memakai
   `npm ci` dan `npm run dev`.

## Cara Penggunaan

1. **Login** menggunakan akun demo dari seeder:

   - Email: `admin@gmail.com`
   - Password: `123123`

   Jika akun tersebut sudah ada sebelumnya, seeder tidak mengganti passwordnya.

2. **Tambah paket sewa** melalui menu **Data Master > Paket Sewa**.
   Isi nama paket, destinasi, harga per hari, lalu aktifkan paket.

3. **Tambah perangkat** melalui menu **Data Master > Perangkat**.
   Isi kode perangkat, nomor seri, catatan bila perlu, dan pilih status **Tersedia**.

4. **Buat pemesanan** melalui menu **Transaksi > Pemesanan > Tambah Pemesanan**.
   Isi data pelanggan, tujuan perjalanan, paket, perangkat, serta tanggal sewa.
   Setelah disimpan, total harga dan invoice dibuat otomatis.

5. **Bagikan invoice** dengan membuka Detail Pemesanan, lalu klik **Salin Link**.
   Pelanggan dapat membuka link invoice tanpa login ke dashboard.

6. **Bayar melalui Midtrans sandbox** dari halaman invoice melalui tombol
   **Bayar Sekarang**. Pelanggan langsung diarahkan ke
   checkout Midtrans dan memilih metode pembayaran di sana.
   Selesaikan transaksi dengan [simulator sandbox Midtrans](https://docs.midtrans.com/docs/testing-payment-on-sandbox),
   kembali ke aplikasi, lalu klik **Cek Status Pembayaran**.
   Backend memeriksa status langsung ke Midtrans. Jika berhasil, invoice menjadi
   **Lunas** dan pemesanan **Dikonfirmasi**. Perangkat tetap **Dipesan** sampai diserahkan.

   Jika checkout ditutup, klik **Bayar Sekarang** pada invoice untuk membuka
   checkout yang sama. Checkout berlaku 30 menit. Percobaan yang ditolak masih bisa diulang
   melalui checkout yang sama. Setelah waktu habis, tunggu satu menit, lalu klik
   **Cek Status Pembayaran** sebelum membuat pembayaran baru atau membatalkan pesanan.
   Untuk checkout yang ditinggalkan sebelum memilih metode, sistem juga memeriksa
   batas waktu ini dan jawaban transaksi tidak ditemukan dari Midtrans.
   Jika checkout gagal dibuat dan URL belum tersimpan, minta admin memeriksa
   referensinya di dashboard Midtrans dahulu. Sistem mempertahankan status pending
   agar tidak membuat pembayaran ganda saat koneksi terputus. Setelah 31 menit,
   gunakan **Cek Status Pembayaran** untuk memeriksa kembali sebelum mencoba lagi.

7. **Serahkan perangkat** dari Detail Pemesanan setelah pembayaran lunas.
   Klik **Serahkan Perangkat** untuk mengubah status menjadi **Disewa**.

8. **Catat pengembalian** dari Detail Pemesanan. Pilih kondisi perangkat,
   isi catatan jika diperlukan, lalu klik **Selesaikan Pengembalian**.
   Perangkat menjadi **Tersedia** atau **Perawatan**, dan pemesanan selesai.

Dashboard menampilkan ringkasan perangkat, penyewaan, pembayaran, dan jadwal
pengembalian. Pemesanan yang masih **Menunggu Pembayaran** dapat dibatalkan
melalui tombol **Batalkan Pemesanan** pada halaman detail selama tidak ada
pembayaran Midtrans yang masih pending.

Untuk pembaruan status otomatis, saat aplikasi sudah memiliki URL publik,
isi **Payment Notification URL** pada konfigurasi Midtrans dengan
`https://domain-aplikasimu.com/midtrans/notification`.
Webhook memeriksa signature dan mengambil ulang status dari Midtrans.
Midtrans tidak bisa mengirim webhook ke localhost; gunakan tombol
**Cek Status Pembayaran** saat menjalankan aplikasi lokal.
Kembali dari checkout saja tidak menjadikan invoice lunas.
