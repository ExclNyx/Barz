# Arsitektur Barz

Barz menggunakan dua jalur backend karena aplikasi berkembang dari PHP Native menjadi memiliki REST API Laravel. Keduanya menggunakan database MySQL `barz_booking`, tetapi struktur kode dan cara kerjanya berbeda.

```text
Browser
  |
  +-- Website PHP Native
  |     +-- admin/services.php  -> php-native Controller -> Model -> MySQLi
  |     +-- admin/barbers.php   -> php-native Controller -> Model -> MySQLi
  |     +-- halaman PHP Native lain (modul lama)
  |     +-- includes/init.php   -> koneksi MySQLi
  |
  +-- Fetch API (fitur yang terhubung ke API)
        +-- api-server/routes/api.php
              -> Laravel Controller
              -> Eloquent Model
              -> MySQL melalui api-server/.env
```

**PHP Native MVC (Week 6)** diterapkan pada fitur admin layanan dan barber. Folder kodenya `php-native/` agar tidak tertukar dengan `api-server/app/` milik Laravel.

**Laravel MVC (Week 7)** berada di `api-server/`. Laravel menerima request API dan mengembalikan JSON. `database/schema.sql` adalah skrip pembuatan skema, bukan konfigurasi koneksi runtime.
