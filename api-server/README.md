# Barz REST API (Laravel 12 + JWT)

Folder ini adalah backend API terpisah untuk melengkapi website PHP native Barz. API dan website memakai database MySQL yang sama (`barz_booking`), sehingga tidak perlu membuat database kedua.

## Tools sesuai modul praktikum

- PHP 8.2+, Composer, Laravel 12, MySQL/XAMPP.
- Tailwind CSS v4 untuk frontend (dibuild terpisah dengan npm).
- REST API JSON, `fetch`, JWT Bearer Token, dan Postman Collection.

## Setup pertama kali di Windows/XAMPP

1. Jalankan **Apache** dan **MySQL** dari XAMPP.
2. Buka `http://localhost/phpmyadmin`, buat/import database `barz_booking` memakai `../database/schema.sql`.
3. Buka terminal VS Code di folder `Barz/api-server`.
4. Pastikan PHP CLI yang dipakai Composer minimal 8.2 (`php -v`). Jika PowerShell masih memakai PHP XAMPP 7.4 dan PHP 8.3 tersedia di `C:\php83`, jalankan `$env:Path = "C:\php83;" + $env:Path`, lalu pastikan versinya sudah berubah.
5. Jalankan:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve --host=127.0.0.1 --port=8000
   ```

6. Pastikan `.env` mengarah ke MySQL XAMPP (`DB_HOST=127.0.0.1`, `DB_DATABASE=barz_booking`, `DB_USERNAME=root`; password disesuaikan dengan XAMPP).
7. Cek API: `http://127.0.0.1:8000/api/health`.
8. Import `docs/Barz-API.postman_collection.json` ke Postman. Base URL sudah diset ke `http://127.0.0.1:8000/api`.

> Composer/vendor tidak ikut dikemas. Jalankan `composer install` di komputer lokal agar dependency Laravel/JWT terpasang. Kode scaffold sudah disediakan, tetapi API tidak dapat dijalankan dari lingkungan pembuatan ZIP karena Composer/dependency tidak tersedia di sini.

## Endpoint

| Method | Endpoint | Auth | Fungsi |
|---|---|---|---|
| GET | `/api/health` | Publik | Cek server hidup |
| GET | `/api/services` | Publik | Daftar layanan aktif |
| GET | `/api/services/{id}` | Publik | Detail layanan aktif |
| POST | `/api/auth/register` | Publik | Registrasi customer |
| POST | `/api/auth/login` | Publik | Login, mengembalikan JWT |
| GET | `/api/auth/me` | JWT | Profil token aktif |
| POST | `/api/auth/logout` | JWT | Invalidate token |
| GET | `/api/slots?service_id=1&date=2026-12-01` | Publik | Slot waktu untuk kalender booking |
| GET | `/api/bookings` | JWT | Riwayat booking sendiri; admin dapat melihat semua |
| POST | `/api/bookings` | JWT | Membuat booking dengan validasi |
| GET | `/api/admin/services` | Admin/Super Admin | Daftar semua layanan termasuk yang nonaktif |
| POST | `/api/admin/services` | Admin/Super Admin | Tambah layanan (mendukung unggah gambar) |
| PUT/PATCH | `/api/admin/services/{id}` | Admin/Super Admin | Ubah layanan |
| DELETE | `/api/admin/services/{id}` | Admin/Super Admin | Hapus/nonaktifkan layanan |
| PATCH | `/api/admin/bookings/{id}/status` | Admin/Super Admin | Ubah status booking |

Login menghasilkan `access_token`; pakai header `Authorization: Bearer <token>` pada endpoint yang dilindungi. Logout memanggil blacklist JWT. Validasi Laravel memakai status HTTP `422` dengan field `errors`. Proses pembuatan booking menggunakan transaksi database dan pemeriksaan bentrok waktu sebelum menyimpan booking.

## Demo credentials

Pada database baru, seeder membuat akun demo berikut. Jika akun sudah ada, seeder hanya memperbarui nama, telepon, dan role; password yang sudah ada tidak di-reset:

- Admin: `admin@barz.com` / `admin123`
- Super Admin: `superadmin@barz.com` / `superadmin`

Jangan gunakan password demo pada deployment publik.

## Keterkaitan dengan website PHP

`assets/js/api.js` mencoba menggunakan API Laravel untuk login JWT dan penyimpanan booking. Halaman booking juga mencoba mengambil slot dari `/api/slots`. Bila API tidak aktif atau request tidak bisa terhubung, pemuatan slot dan form booking kembali menggunakan endpoint/form PHP native yang sudah ada. Dengan demikian, website XAMPP tetap dapat diuji secara mandiri.
