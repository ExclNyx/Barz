# Barz Barbershop

Website booking barbershop berbasis PHP Native + MySQLi, Tailwind CSS v4, JavaScript Fetch API, dan backend REST API Laravel 12 + JWT. Project mempertahankan alur customer, admin, booking, layanan, barber, dan chat yang sudah ada.

## 1. Pemetaan materi Week 1–7

| Week | Materi | Bukti kode di Barz |
|---|---|---|
| 1 | HTML | `index.php`, `customer/pages/`, `admin/`, form dan tabel HTML |
| 2 | CSS | `src/css/tailwind.css` dan hasil build `assets/css/tailwind.css` |
| 3 | JavaScript | `assets/js/main.js`, `assets/js/api.js`, event handler pada halaman booking |
| 4 | API | `fetch`, async/await, JSON, REST endpoint PHP/Laravel, error handling dan Postman |
| 5 | PHP + MySQL Native | `includes/`, `auth/`, `api/`, `admin/`, `database/schema.sql` |
| 6 | OOP + MVC Manual | `admin/services.php`, `admin/barbers.php`, dan folder `php-native/` — fitur Kelola Layanan dan Barber memakai MVC manual |
| 7 | Laravel MVC | `api-server/` — Routes, Controllers, Eloquent Models, Resources, Middleware dan JWT |

Pemetaan file lengkap tersedia di [`docs/MODUL_MAPPING.md`](docs/MODUL_MAPPING.md).

## 2. Persyaratan

- XAMPP dengan Apache dan MySQL aktif.
- PHP 8.2 atau lebih baru untuk backend Laravel 12. Website PHP Native beserta fitur MVC manual dapat dijalankan melalui XAMPP.
- Composer 2.x.
- Node.js dan npm untuk build Tailwind CSS.
- Postman untuk menguji REST API.

## 3. Menjalankan website PHP Native

1. Ekstrak folder `Barz` ke `C:\xampp\htdocs\Barz`.
2. Nyalakan Apache dan MySQL di XAMPP.
3. Buka phpMyAdmin, buat/import database `barz_booking` menggunakan `database/schema.sql`.
4. Pastikan konfigurasi database pada `includes/init.php` sesuai dengan XAMPP lokal (`localhost`, user `root`, password sesuai setup MySQL).
5. Build CSS jika dependency belum tersedia atau ada perubahan utility class:

   ```powershell
   npm install
   npm run css:build
   ```

6. Untuk demo lokal, buat atau samakan password akun admin melalui terminal lokal. Perintah ini membuat akun jika belum ada atau meresetnya jika sudah ada:

   ```powershell
   php create_admin.php admin@barz.com "admin123" "Admin Barz" 081234567890 --reset
   ```

   Password `admin123` hanya untuk database demo lokal. Ganti dengan password kuat sebelum deployment.

7. Buat jadwal slot dari terminal pada root project:

   ```powershell
   php generate_slots.php
   ```

8. Buka `http://localhost/Barz`.

`generate_slots.php` hanya dapat dijalankan melalui terminal karena script tersebut membuat ulang slot booking.

## 4. Week 6 — OOP + MVC Manual pada fitur Kelola Layanan dan Barber

Week 6 diterapkan langsung pada dua fitur yang memang digunakan Barz, yaitu **Admin > Layanan** dan **Admin > Barbers**, bukan halaman demo terpisah. URL tetap `http://localhost/Barz/admin/services.php` dan `http://localhost/Barz/admin/barbers.php`.

Struktur implementasi:

- `admin/services.php` — entry point/rute halaman dan pemanggil Controller.
- `php-native/models/ServiceModel.php` dan `BarberModel.php` — class Model, constructor, property private, dan operasi MySQLi untuk tabel `services` serta `barbers`.
- `php-native/controllers/Admin/ServiceController.php` dan `BarberController.php` — class Controller yang menangani request, validasi, CSRF, flash message, dan penyiapan data View.
- `php-native/views/Admin/services.php` dan `barbers.php` — tampilan daftar, pencarian, tambah, edit, aktif/nonaktif, dan hapus.
- `includes/init.php` — koneksi MySQLi yang sudah digunakan aplikasi Barz; MVC memakai `getDB()` dari sini.

Tidak ada database baru. Model menggunakan tabel `services` dan `barbers` pada database `barz_booking` yang sama dengan halaman customer dan fitur booking. Layanan yang sudah memiliki riwayat booking akan dinonaktifkan, bukan dihapus, agar histori tetap aman.

Untuk demo, login sebagai admin lalu buka menu **Layanan** dan **Barbers**. Coba tambah data uji, edit, cari, ubah status, dan hapus kembali. Tunjukkan entry point, file Model, Controller, dan View ketika menjelaskan pola MVC. Penjelasan rinci tersedia di `docs/WEEK6_MVC_GUIDE.md`, sedangkan urutan demonstrasi Week 1–7 tersedia di `docs/WEEK1-7_DEMO.md`.

## 5. Menjalankan backend Laravel — Week 7

Website PHP Native dan Laravel API menggunakan database MySQL yang sama (`barz_booking`). Laravel berjalan sebagai server API terpisah. Sebelum menjalankan Composer dan Artisan, pastikan `php -v` menunjukkan PHP 8.2 atau lebih baru. Jika perlu, ikuti pengaturan PATH pada [`api-server/README.md`](api-server/README.md).

```powershell
cd C:\xampp\htdocs\Barz\api-server
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

Sesuaikan `.env` dengan database MySQL XAMPP. Setelah server berjalan, periksa:

`http://127.0.0.1:8000/api/health`

Import collection `api-server/docs/Barz-API.postman_collection.json` ke Postman. Collection tersebut berisi request untuk health check, layanan, slot, login, JWT, booking, dan operasi admin.

Pada database baru, akun demo yang dibuat oleh seeder:

- Admin: `admin@barz.com` / `admin123`
- Super Admin: `superadmin@barz.com` / `superadmin`

Seeder tidak mengubah password akun yang sudah ada. Gunakan password demo hanya untuk pengujian lokal dan ganti sebelum deployment.

## 6. Alur integrasi frontend dan API

- Login dan registrasi mencoba menggunakan Laravel API terlebih dahulu.
- Login API menyimpan JWT pada `sessionStorage`; autentikasi sesi website tetap dilakukan oleh PHP Native.
- Halaman booking mencoba mengirim booking ke Laravel jika JWT tersedia.
- Slot waktu diambil dari Laravel API dan memiliki fallback ke endpoint PHP Native.
- Logout mencoba menginvalidasi JWT Laravel, kemudian menghapus token browser dan mengakhiri sesi PHP.
- Jika Laravel tidak berjalan, website masih dapat menggunakan alur PHP Native yang lama.

## 7. Batasan pengujian

Source code PHP dan JavaScript dapat diperiksa sintaksnya secara lokal. Untuk memastikan Week 7 berfungsi penuh, dependency Laravel harus dipasang dan API harus diuji terhadap MySQL XAMPP menggunakan Postman. File `.env`, `vendor/`, dan kredensial lokal tidak disertakan dalam repository.
