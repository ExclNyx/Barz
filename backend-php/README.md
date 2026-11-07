# Backend PHP Native Barz

Penerapan Week 5 (PHP + MySQL Native) pada website utama tetap berada di root project agar URL, halaman, dan fitur lama tidak rusak.

File yang menjadi bukti utama:

- `../includes/init.php` — session dan koneksi MySQLi.
- `../includes/functions.php` — fungsi autentikasi, query layanan/barber/booking, dan helper.
- `../auth/` — proses login, logout, dan registrasi PHP Native.
- `../api/` — endpoint PHP Native untuk chat dan slot booking.
- `../admin/` dan `../customer/` — halaman PHP yang memakai database.
- `../database/schema.sql` — struktur database MySQL.

Untuk materi Week 6 (OOP + MVC Manual), buka [`demo-mvc/README.md`](demo-mvc/README.md). Implementasi demo MVC terpisah dari halaman lama, tetapi menggunakan tabel `services` yang sama.
