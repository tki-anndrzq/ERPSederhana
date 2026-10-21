# Rental WiFi

## Instalasi

Siapkan PHP 8.2, Composer 2, Node.js 22.12+ atau 24, npm, dan PostgreSQL.
Aktifkan ekstensi PHP `pdo_pgsql`.

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
   MOCK_PAYMENT_ENABLED=true
   ```

   Buat database `rental_wifi` di PostgreSQL terlebih dahulu. Sesuaikan nama
   database, port, username, dan password dengan konfigurasi komputermu.

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

6. **Lakukan pembayaran simulasi** dari halaman invoice melalui tombol
   **Bayar Sekarang**. Pilih simulasi berhasil atau gagal. Pembayaran saat ini
   masih menggunakan mock, belum terhubung ke Midtrans.
   Jika berhasil, invoice menjadi **Lunas** dan pemesanan **Dikonfirmasi**.

7. **Serahkan perangkat** dari Detail Pemesanan setelah pembayaran lunas.
   Klik **Serahkan Perangkat** untuk mengubah status menjadi **Disewa**.

8. **Catat pengembalian** dari Detail Pemesanan. Pilih kondisi perangkat,
   isi catatan jika diperlukan, lalu klik **Selesaikan Pengembalian**.
   Perangkat menjadi **Tersedia** atau **Perawatan**, dan pemesanan selesai.

Dashboard menampilkan ringkasan perangkat, penyewaan, pembayaran, dan jadwal
pengembalian. Pemesanan yang masih **Menunggu Pembayaran** dapat dibatalkan
melalui tombol **Batalkan Pemesanan** pada halaman detail.
