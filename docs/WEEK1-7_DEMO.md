# Panduan Demo Materi Week 1–7 pada Barz

Panduan ini dipakai untuk menyiapkan demonstrasi source code dan hasil aplikasi. Setiap konsep harus ditunjukkan dari file yang benar-benar digunakan oleh fitur Barz.

## Persiapan sebelum merekam

1. Pastikan repository di komputer sudah berisi versi kode terakhir yang akan didemokan.
2. Jalankan XAMPP (Apache dan MySQL).
3. Import `database/schema.sql` ke database `barz_booking`.
4. Build CSS dengan `npm install` lalu `npm run css:build` bila perlu.
5. Buat slot booking menggunakan `php generate_slots.php` dari terminal.
6. Login sebagai admin.
7. Untuk Week 7, jalankan Laravel API dari folder `api-server` dan pastikan `/api/health` membalas JSON `status: ok`.

## Bukti source dan hasil

| Materi | Buka file | Jelaskan | Tunjukkan hasil |
|---|---|---|---|
| Week 1 — HTML | `customer/pages/home.php`, `customer/pages/booking.php` | Struktur elemen, heading, form, input, button, tabel/kartu dan atribut | Homepage dan form booking di browser |
| Week 2 — CSS | `src/css/tailwind.css`, `assets/css/tailwind.css` | Utility-first CSS, theme token, responsive breakpoint, hover/focus, proses build | Ubah ukuran browser dan tunjukkan tampilan responsif |
| Week 3 — JavaScript | `assets/js/main.js`, `assets/js/api.js`, script pada booking page | DOM, event listener, submit, async/await, state loading, update DOM | Pilih layanan/tanggal/jam dan lihat perubahan pilihan/slot |
| Week 4 — API | `assets/js/api.js`, `api/get_slots.php`, `api-server/routes/api.php` | HTTP request, GET/POST/PATCH/DELETE, JSON, status code, error dan fallback | Network tab browser atau Postman |
| Week 5 — PHP + MySQL Native | `includes/init.php`, `includes/functions.php`, `auth/login.php`, `customer/pages/booking.php` | Session, MySQLi, prepared statement, SELECT/INSERT, validasi dan alur request | Login, buat booking, periksa hasil di halaman Booking Saya dan tabel MySQL |
| Week 6 — OOP + MVC Manual | `backend-php/demo-mvc/public/index.php`, `controllers/ServiceController.php`, `models/ServiceModel.php`, `views/services/index.php` | Class/object, constructor, encapsulation, Model, Controller, View dan CSRF | Tambah layanan, edit, cari, aktif/nonaktif, hapus; periksa halaman layanan utama |
| Week 7 — Laravel MVC | `api-server/routes/api.php`, `app/Http/Controllers/Api/`, `app/Models/`, `app/Http/Resources/`, `app/Http/Middleware/` | Route, Controller, Eloquent Model, Resource, Middleware, migration, validasi dan JWT | Jalankan request pada Postman; cek health, login, token, booking dan logout |

## Demonstrasi Week 6 secara berurutan

1. Login admin di website Barz.
2. Buka menu **Demo MVC (Week 6)**.
3. Tunjukkan `public/index.php`: koneksi ke bootstrap database, pengecekan akses, pembuatan object Model dan Controller.
4. Tunjukkan `ServiceController.php`: request/action, validasi input, CSRF, pemanggilan Model, redirect dan pemilihan View.
5. Tunjukkan `ServiceModel.php`: constructor, property private, prepared statement `SELECT`, `INSERT`, dan `UPDATE`.
6. Tunjukkan `views/services/index.php`: data dari Controller ditampilkan sebagai form dan tabel.
7. Tambah satu layanan uji (misalnya `Demo Week 6`), cari, edit, nonaktifkan, lalu hapus kembali. Jika layanan sudah memiliki booking, fitur hapus hanya menonaktifkannya agar histori tetap aman.
8. Buka halaman Layanan customer atau tabel `services` di phpMyAdmin untuk membuktikan bahwa data tersimpan pada database yang sama.

## Demonstrasi Week 7 menggunakan Postman

Urutan yang disarankan:

1. `GET /api/health`.
2. `GET /api/services`.
3. `GET /api/slots?service_id=1&date=YYYY-MM-DD` dengan tanggal yang masih berada dalam periode slot yang dibuat.
4. `POST /api/auth/login` dengan akun admin hasil seeder; collection menyimpan `access_token` ke variabel `token`.
5. `GET /api/auth/me` untuk membuktikan token valid.
6. `POST /api/bookings` untuk membuat booking; collection menyimpan ID booking ke `bookingId`.
7. `GET /api/bookings` untuk melihat booking.
8. `PATCH /api/admin/bookings/{{bookingId}}/status` untuk mengubah status.
9. `POST /api/auth/logout` untuk menginvalidasi token; collection menghapus variabel token.

Gunakan tanggal booking ke depan dan pastikan barber memiliki slot aktif. Untuk pengujian yang berulang, gunakan akun dan data demo lokal.

## Materi yang jangan dicampur

- Week 5 adalah PHP Native + MySQLi pada halaman utama Barz.
- Week 6 adalah implementasi OOP + MVC manual di `backend-php/demo-mvc/`. Jangan menjelaskan Laravel sebagai bukti tunggal MVC manual.
- Week 7 adalah Laravel MVC pada `api-server/`.
