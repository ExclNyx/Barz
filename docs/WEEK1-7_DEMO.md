# Panduan Demo Materi Week 1–7 pada Barz

Panduan ini membantu menyiapkan demonstrasi source code dan hasil aplikasi. Tunjukkan hubungan **kode → konsep → hasil**, bukan hanya tampilan website.

## Persiapan sebelum merekam

1. Pastikan repository di komputer sudah berisi kode terbaru yang akan didemokan.
2. Jalankan Apache dan MySQL melalui XAMPP.
3. Import `database/schema.sql` ke database `barz_booking` jika database belum tersedia.
4. Cocokkan konfigurasi `includes/init.php` dengan MySQL lokal.
5. Jalankan `npm install` dan `npm run css:build` jika dependency belum terpasang atau file styling berubah.
6. Login sebagai admin untuk demo fitur Admin.
7. Untuk Week 7, siapkan PHP 8.2+, Composer, file `.env` Laravel, dan Postman.

## Pemetaan source dan hasil

| Week | Materi | Buka file | Tunjukkan hasil |
|---|---|---|---|
| 1 | HTML | `customer/pages/home.php`, `customer/pages/booking.php` | Homepage dan form booking. Jelaskan elemen, atribut, form, input, tombol, dan struktur halaman. |
| 2 | CSS | `src/css/tailwind.css`, `assets/css/tailwind.css` | Tunjukkan theme token, utility class, breakpoint responsive, hover/focus, lalu ubah lebar browser. |
| 3 | JavaScript | `assets/js/main.js`, `assets/js/api.js`, script pada halaman booking | Tunjukkan event listener, DOM, validasi form, async/await, loading state, dan perubahan pilihan slot. |
| 4 | API | `assets/js/api.js`, `api/get_slots.php`, `api-server/routes/api.php` | Tunjukkan request Fetch, method HTTP, JSON, status response, fallback, dan pengujian Postman. |
| 5 | PHP + MySQL Native | `includes/init.php`, `includes/functions.php`, `auth/`, `customer/pages/booking.php`, `admin/` | Tunjukkan session, koneksi MySQLi, prepared statement, validasi, dan proses booking. Periksa hasil di phpMyAdmin. |
| 6 | OOP + MVC Manual | `admin/services.php`, `admin/barbers.php`, `php-native/models/`, `php-native/controllers/Admin/`, `php-native/views/Admin/` | Tunjukkan tambah/edit/cari/status/hapus layanan dan barber. Jelaskan Class, Object, Constructor, Encapsulation, Model, Controller, dan View. |
| 7 | Laravel MVC | `api-server/routes/api.php`, `api-server/app/Http/Controllers/Api/`, `api-server/app/Models/`, `api-server/app/Http/Resources/` | Jalankan Laravel API, lalu uji health, login/token, layanan, slot, booking, dan logout melalui Postman. |

## Week 6 — urutan demo OOP + MVC Manual

Week 6 diterapkan pada dua fitur admin yang benar-benar digunakan Barz: **Kelola Layanan** dan **Kelola Barber**. Tidak ada halaman latihan terpisah.

1. Login sebagai admin.
2. Buka menu **Layanan**. URL tetap `/Barz/admin/services.php`.
3. Tunjukkan `admin/services.php`. File ini menyiapkan koneksi dan role, membuat objek Model dan Controller, lalu memanggil View.
4. Tunjukkan `php-native/models/ServiceModel.php`. Jelaskan class, constructor, property `private $db`, dan method query yang mengelola tabel `services`.
5. Tunjukkan `php-native/controllers/Admin/ServiceController.php`. Jelaskan bahwa Controller membaca request, memvalidasi input, memanggil Model, lalu menyiapkan data untuk View.
6. Tunjukkan `php-native/views/Admin/services.php`. Jelaskan bahwa View berisi HTML/form/tabel dan tidak menjalankan query SQL.
7. Uji tambah satu layanan dengan nama `Layanan Uji MVC`, cari, edit, nonaktifkan, lalu hapus jika belum pernah dipakai booking.
8. Buka menu **Barbers**. Ulangi pola yang sama dengan `admin/barbers.php`, `BarberModel.php`, `BarberController.php`, dan `views/Admin/barbers.php`.
9. Uji tambah barber dengan nama `Barber Uji MVC`, edit, cari, nonaktifkan, lalu hapus jika belum dipakai booking/jadwal/slot.
10. Tunjukkan tabel `services` dan `barbers` di phpMyAdmin untuk membuktikan kedua fitur menggunakan database Barz yang sama.

### Cara menjelaskan alur MVC

Contoh ketika admin menambah layanan:

1. **Entry point:** browser membuka `admin/services.php`.
2. **Controller:** `ServiceController` menerima data form dan memeriksa validitasnya.
3. **Model:** `ServiceModel` menjalankan prepared statement `INSERT` ke tabel `services`.
4. **Controller:** menyimpan pesan hasil dan mengarahkan kembali ke halaman layanan.
5. **View:** `views/Admin/services.php` menampilkan tabel layanan yang sudah diperbarui.

### Konsep OOP yang perlu disebutkan

- **Class:** `ServiceModel` adalah cetak biru objek yang mengelompokkan properti dan method untuk data layanan.
- **Object:** `new ServiceModel(getDB())` membuat objek dari class tersebut.
- **Constructor:** `__construct(mysqli $db)` menerima koneksi ketika objek dibuat.
- **Encapsulation:** property `$db` dibuat `private`, sehingga tidak diakses langsung dari luar class.
- **Method:** `getAll()`, `create()`, `update()`, dan `deleteSafely()` adalah operasi milik Model.
- **Dependency injection sederhana:** Controller menerima Model melalui constructor, sehingga Controller tidak perlu membuat query database sendiri.

## Week 7 — urutan demo Laravel MVC

1. Jalankan backend dari folder `api-server`.
2. Tunjukkan `routes/api.php` untuk menjelaskan endpoint.
3. Tunjukkan satu Controller, misalnya `BookingController.php`, untuk menjelaskan penerimaan request, validasi, dan alur proses.
4. Tunjukkan `Booking.php` atau `Service.php` pada `app/Models/` untuk menjelaskan Eloquent ORM dan relasi data.
5. Tunjukkan `BookingResource.php` untuk menjelaskan pembentukan response JSON.
6. Tunjukkan middleware auth/role bila menjelaskan pembatasan akses.
7. Uji endpoint dengan Postman.

Week 7 sudah memiliki source Laravel, tetapi jangan menyatakan runtime berhasil sebelum server lokal benar-benar berjalan dan request Postman mendapat hasil yang diharapkan.

## Catatan agar penjelasan tidak tertukar

- Week 5 adalah PHP Native + MySQLi yang digunakan oleh website utama.
- Week 6 adalah OOP + MVC Manual PHP Native untuk Admin > Layanan dan Admin > Barbers. Kodenya berada di `php-native/`, bukan `api-server/app/`.
- Week 7 adalah Laravel MVC untuk backend REST API di `api-server/`.
- Tidak semua halaman Admin lama sudah dipindahkan ke MVC Manual. Untuk Week 6, jelaskan dua fitur yang memang sudah memakai pola tersebut.
