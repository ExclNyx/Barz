# Setup Firebase Console untuk Barz (PHP)

Project ID yang terlihat di Firebase Console pada screenshot: `barz-28363`.

## A. Buat Cloud Firestore

1. Buka https://console.firebase.google.com/ dan pilih project **Barz**.
2. Klik **Databases & Storage** pada sidebar kiri.
3. Pilih **Firestore Database**.
4. Klik **Create database**.
5. Pilih **Standard edition** jika opsi tersebut muncul.
6. Pilih lokasi `asia-southeast2` (Jakarta) jika tersedia dan paling sesuai untuk project-mu. Lokasi database tidak dapat diubah sesudah dibuat.
7. Pilih **Production mode**, lalu klik **Create**.

Jangan klik **Add app** dulu; kode PHP ini mengakses Firestore dari server menggunakan library PHP, bukan Firebase Web SDK di browser.

## B. Siapkan akun service untuk PHP

1. Di Firebase Console, buka **Settings → Project settings → Service accounts**.
2. Buat/generate private key service account untuk development lokal. File yang diunduh berformat JSON.
3. Buka Google Cloud IAM untuk project `barz-28363`, temukan service account tersebut, lalu beri akses Cloud Firestore yang diperlukan, misalnya role **Cloud Datastore User** (`roles/datastore.user`).
4. Pindahkan file JSON ke lokasi di luar folder project/web, contoh `C:\BarzSecrets\barz-28363-service-account.json`.
5. Jangan mengirim atau commit file JSON tersebut ke GitHub. Jangan pernah menaruhnya di folder `Barz`.

Server client library menggunakan IAM dan tidak dibatasi oleh Firestore Security Rules. Jangan membuat rules publik agar database bisa diakses siapa saja. Referensi: https://firebase.google.com/docs/firestore/quickstart-server

## C. Pasang dependency dan buat dokumen awal

Prasyarat: PHP 8.2+ aktif, Composer 2, dan ekstensi `grpc` aktif pada PHP yang sama dengan yang dipakai menjalankan aplikasi.

Di PowerShell, buka terminal dalam folder `Barz`:

```powershell
composer install
$env:GOOGLE_APPLICATION_CREDENTIALS = 'C:\BarzSecrets\barz-28363-service-account.json'
$env:FIREBASE_PROJECT_ID = 'barz-28363'
php scripts/seed_firestore.php
```

Seeder membuat beberapa service, barber, pengaturan toko, dan akun demo bila akun tersebut belum ada. Karena itu seeder aman dijalankan ulang tanpa mengganti password akun yang sudah dibuat.

Akun demo awal:

- Admin: `admin@barz.com` / `barz2024`
- Super admin: `superadmin@barz.com` / `superadmin`

Segera ganti password setelah testing.

## D. Jalankan lokal tanpa XAMPP

Pertahankan folder bernama `Barz` karena URL di kode masih menggunakan prefix `/Barz`.

Misalnya susun folder seperti ini:

```text
C:\WebApps\Barz\          <- source aplikasi
C:\BarzSecrets\...json    <- kredensial privat, di luar aplikasi
```

Di PowerShell, dari `C:\WebApps\Barz` jalankan dependency dan seeder sesuai bagian C. Untuk menjalankan built-in PHP server dari folder induk `C:\WebApps`, jalankan:

```powershell
$env:GOOGLE_APPLICATION_CREDENTIALS = 'C:\BarzSecrets\barz-28363-service-account.json'
$env:FIREBASE_PROJECT_ID = 'barz-28363'
php -S 127.0.0.1:8000
```

Lalu buka http://127.0.0.1:8000/Barz/.

PHP built-in server hanya untuk development lokal, bukan hosting produksi.

## Batasan versi migrasi

- Login dan session masih dikelola oleh PHP. Password di koleksi `users` disimpan sebagai hash; Firebase Authentication belum diaktifkan.
- Sejumlah fitur memuat koleksi lalu memfilter di PHP agar cocok dengan struktur UI lama. Ini cukup untuk demo/data kecil, bukan pola paling efisien untuk jumlah data besar.
- Penguncian waktu booking perlu transaksi/strategi reservasi tambahan sebelum dipakai banyak pengguna secara bersamaan.
