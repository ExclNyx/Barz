# Week 6 — OOP + MVC Manual (PHP Native)

Halaman demo ini menerapkan OOP dan pola Model–View–Controller secara manual menggunakan PHP Native + MySQLi. Sintaks demo dijaga kompatibel dengan PHP 7.4 atau lebih baru; Laravel Week 7 sendiri membutuhkan PHP 8.2 atau lebih baru. Halaman menggunakan tabel `services` yang sama dengan website Barz, sehingga perubahan yang disimpan tetap terlihat di fitur layanan utama.

## Menjalankan

1. Jalankan Apache dan MySQL melalui XAMPP.
2. Pastikan database `barz_booking` sudah di-import dari `database/schema.sql`.
3. Jalankan website dan login menggunakan akun admin.
4. Buka `http://localhost/Barz/backend-php/demo-mvc/public/`.

Akses demo dibatasi untuk admin yang sudah login. Tidak perlu Composer atau framework untuk menjalankan bagian ini.

## Struktur dan tanggung jawab

```text
demo-mvc/
├── public/
│   └── index.php                  # front controller / entry point
├── models/
│   └── ServiceModel.php           # query MySQLi untuk tabel services
├── controllers/
│   └── ServiceController.php      # request, validasi, alur aplikasi
└── views/
    └── services/
        └── index.php              # tampilan form dan tabel layanan
```

Alurnya:

`Browser -> public/index.php -> ServiceController -> ServiceModel -> MySQL`

`Browser <- public/index.php <- ServiceController <- View (HTML)`

## Materi OOP yang terlihat

- **Class dan object:** `ServiceModel` dan `ServiceController` dibuat sebagai class, lalu instance dibuat di `public/index.php`.
- **Constructor:** dependency database dan model diberikan melalui `__construct()`.
- **Encapsulation:** koneksi disimpan sebagai property `private` pada Model.
- **Method:** `getAll()`, `findById()`, `create()`, `update()`, `setActive()`, dan `deleteSafely()` membungkus operasi data.
- **Dependency injection sederhana:** Controller menerima object `ServiceModel`, bukan membuat koneksi database sendiri.

## Materi MVC yang terlihat

- **Model:** seluruh query untuk tabel `services` diletakkan di `models/ServiceModel.php`.
- **View:** `views/services/index.php` hanya bertugas menampilkan data dan form.
- **Controller:** `controllers/ServiceController.php` menerima action, memvalidasi input, memanggil Model, dan menampilkan View.

Form POST dilindungi token CSRF. Saat hapus, layanan tanpa histori booking dapat dihapus permanen; layanan yang sudah dipakai booking hanya dinonaktifkan agar histori dan foreign key tetap aman.
