# Week 6 — OOP + MVC Manual pada Barz

## Kenapa MVC diterapkan di Barz?

Barz memiliki halaman Admin untuk mengelola **Layanan** dan **Barber**. Sebelum pemisahan MVC, file halaman admin menangani request, query SQL, dan HTML dalam satu file. Week 6 diterapkan dengan memisahkan tanggung jawab fitur yang benar-benar dipakai, bukan membuat aplikasi latihan baru.

Fitur yang memakai PHP Native MVC Manual:

- Admin > Layanan: `admin/services.php`
- Admin > Barbers: `admin/barbers.php`

URL, menu, database, dan tampilan utama admin tetap berada di aplikasi Barz.

## Struktur folder

```text
php-native/
├── models/
│   ├── ServiceModel.php
│   └── BarberModel.php
├── controllers/
│   └── Admin/
│       ├── ServiceController.php
│       └── BarberController.php
└── views/
    └── Admin/
        ├── services.php
        └── barbers.php

admin/
├── services.php       # entry point fitur layanan
└── barbers.php        # entry point fitur barber

includes/
└── init.php           # session, helper, dan koneksi MySQLi
```

Folder `php-native/` sengaja berbeda dari `api-server/app/`. `php-native/` adalah kode PHP Native untuk Week 6, sedangkan `api-server/app/` adalah kode Laravel untuk Week 7.

## Arti MVC dalam fitur Barz

### 1. Model — mengelola data

- `ServiceModel` mengakses tabel `services`.
- `BarberModel` mengakses tabel `barbers`.
- Query SQL dan prepared statement berada di Model.
- Model menerima koneksi yang sudah dibuat oleh `getDB()` dari `includes/init.php`.

### 2. View — menampilkan halaman

- `php-native/views/Admin/services.php` menampilkan tabel dan formulir layanan.
- `php-native/views/Admin/barbers.php` menampilkan tabel dan formulir barber.
- View hanya menampilkan data yang disiapkan Controller. View tidak membuat koneksi atau menjalankan query SQL.

### 3. Controller — mengatur alur

- `ServiceController` menangani tambah, edit, cari, ubah status, dan hapus layanan.
- `BarberController` menangani tambah, edit, cari, ubah status, dan hapus barber.
- Controller memvalidasi input, memanggil Model, mengatur pesan hasil, kemudian mengirim data ke View.

## Konsep OOP yang bisa ditunjukkan

| Konsep | Bukti kode | Penjelasan |
|---|---|---|
| Class | `class ServiceModel`, `class AdminServiceController`, `class BarberModel`, `class AdminBarberController` | Cetak biru objek yang mengelompokkan data dan perilaku. |
| Object | `new ServiceModel(getDB())` | Objek dibuat dari class agar method-nya bisa digunakan. |
| Constructor | `__construct(mysqli $db)` | Menerima koneksi database ketika objek dibuat. |
| Encapsulation | `private $db`, `private $serviceModel`, `private $barberModel` | Properti internal tidak diakses langsung dari luar class. |
| Method | `getAll()`, `create()`, `update()`, `deleteSafely()` | Perilaku/operasi yang dimiliki class. |
| Dependency injection sederhana | Controller menerima Model melalui constructor | Controller tidak membuat query atau koneksi database sendiri. |

## Alur ketika admin menambah layanan

1. Admin membuka `admin/services.php`.
2. Entry point memuat `includes/init.php`, mengecek role admin, lalu membuat objek Model dengan `getDB()`.
3. Entry point membuat objek Controller dan memanggil `handle()`.
4. Controller membaca dan memvalidasi request POST.
5. Controller memanggil `ServiceModel::create()`.
6. Model menjalankan prepared statement ke tabel `services`.
7. Controller mengarahkan browser kembali ke halaman layanan dengan pesan berhasil/gagal.
8. Pada request GET berikutnya, Controller mengambil data melalui Model dan View menampilkannya.

Alur Barber sama, tetapi menggunakan `BarberModel` dan `BarberController`.

## Hubungan dengan database

Tidak ada database baru. Kedua Model menggunakan koneksi yang dibuat oleh `includes/init.php`:

```php
$serviceModel = new ServiceModel(getDB());
$barberModel = new BarberModel(getDB());
```

- Database: `barz_booking`
- Tabel layanan: `services`
- Tabel barber: `barbers`

`database/schema.sql` hanya membuat database/tabel saat setup. File itu bukan file koneksi aplikasi.

## Cara demo di video

1. Login sebagai admin dan buka menu **Layanan**.
2. Tampilkan `admin/services.php` sebagai entry point.
3. Buka `ServiceController.php`; tunjukkan `handle()`, validasi request, dan pemanggilan Model.
4. Buka `ServiceModel.php`; tunjukkan constructor, `private $db`, dan prepared statement.
5. Buka View; tunjukkan tabel/form yang menerima data dari Controller.
6. Di browser, tambah satu layanan uji lalu buktikan barisnya masuk ke tabel `services`.
7. Edit atau nonaktifkan layanan itu, lalu hapus kembali bila belum dipakai booking.
8. Ulangi penjelasan singkat pada menu **Barbers**, tunjukkan `BarberController` dan `BarberModel`.

Jangan menghapus data layanan/barber yang sudah terhubung dengan booking atau jadwal. Implementasi `deleteSafely()` akan menonaktifkan data yang sudah dipakai agar riwayat tidak rusak.

## Batas penerapan

Week 6 saat ini diterapkan pada dua fitur Admin (Layanan dan Barber). Halaman Admin lain seperti Booking, Chat, dan Customer belum seluruhnya dipindahkan ke PHP Native MVC. Jangan mengklaim semua halaman Barz sudah memakai MVC Manual.
