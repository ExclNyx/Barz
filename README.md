# Barz Barbershop — disesuaikan dengan Modul Praktikum 0–6

Project Barz tetap memakai domain, desain, halaman, dan alur bisnis barbershop milik project asli; project FlipPlay tidak disalin. Yang diselaraskan adalah **tools dan pola implementasi** dari modul praktikum: XAMPP/MySQL, Tailwind CSS v4 utility-first, JavaScript event + Fetch API, PHP OOP, REST API Laravel, validasi, JWT, CORS, dan Postman.

## 1. Struktur penting

```text
Barz/
├── index.php / customer/ / admin/ / superadmin/   # website PHP yang sudah ada
├── assets/
│   ├── css/tailwind.css                           # hasil build Tailwind (dipakai browser)
│   └── js/api.js                                  # fetch + JWT + integrasi Laravel API
├── src/css/tailwind.css                           # sumber Tailwind, hanya @import dan @theme
├── api/                                           # endpoint PHP lama untuk fallback XAMPP
├── database/schema.sql                            # schema MySQL; role dapat menyimpan super_admin
├── package.json                                   # scripts build/watch Tailwind CLI v4
└── api-server/                                    # source backend Laravel 12 + JWT + Postman collection
```

Tidak ada `tailwind.php`, `style.css`, atau `admin.css` lagi. File `assets/css/tailwind.css` adalah output build statis Tailwind; elemen PHP/HTML langsung memakai utility class seperti `flex`, `grid`, `p-4`, `rounded-2xl`, `bg-barz-accent`, `md:grid-cols-2`, dan `hover:...`. `src/css/tailwind.css` hanya berisi directive import dan theme token Tailwind—bukan kumpulan selector CSS buatan sendiri.

## 2. Menjalankan website di XAMPP

1. Ekstrak folder `Barz` ke `C:\xampp\htdocs\Barz`.
2. Buka XAMPP Control Panel, nyalakan **Apache** dan **MySQL**.
3. Buka phpMyAdmin di `http://localhost/phpmyadmin` dan import `database/schema.sql`.
   - Jika database Barz sebelumnya sudah ada dengan `users.role` berbentuk ENUM, jalankan SQL ini sekali agar `super_admin` dapat disimpan:

   ```sql
   ALTER TABLE users MODIFY role VARCHAR(30) NOT NULL DEFAULT 'customer';
   ```
4. Buka `http://localhost/Barz/create_admin.php` sekali untuk memastikan akun admin demo dibuat, lalu hapus/nonaktifkan file itu sebelum deployment.
5. Jalankan `http://localhost/Barz/generate_slots.php` untuk membuat jadwal slot 30 hari ke depan.
6. Buka website melalui `http://localhost/Barz`.

## 3. Build Tailwind CSS v4 (sesuai Modul 1)

Node.js dan npm harus terpasang. Dari terminal di folder `C:\xampp\htdocs\Barz`:

```powershell
npm install
npm run css:build
```

Untuk melihat perubahan utility class sambil bekerja:

```powershell
npm run css:watch
```

Hasil compile masuk ke `assets/css/tailwind.css`. File hasil build sudah disertakan di ZIP supaya halaman bisa memakai file CSS statis. Jika kamu mengubah utility class PHP/HTML, jalankan `npm run css:build` lagi. Tailwind v4 menggunakan CLI; tidak ada runtime CDN yang perlu mengompilasi halaman ketika dibuka.

## 4. Menjalankan Laravel API (sesuai Modul 4–6)

Ikuti instruksi di [`api-server/README.md`](api-server/README.md). Ringkasnya:

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

API berjalan terpisah di `http://127.0.0.1:8000/api`. Website di XAMPP tetap di `http://localhost/Barz`. Keduanya memakai database `barz_booking` yang sama. Uji API di Postman memakai collection `api-server/docs/Barz-API.postman_collection.json`.

**Catatan:** dependency Composer tidak dibundel. Jalankan `composer install` di komputer kamu sebelum menjalankan Laravel. API scaffold telah ditambahkan dan PHP syntax-nya dicek, tetapi proses runtime Laravel/JWT perlu diverifikasi setelah dependency dipasang di lingkungan lokal.

## 5. Pemetaan ke modul

| Modul | Implementasi di Barz |
|---|---|
| 0 — Setup | XAMPP (Apache/MySQL), Node/npm, Composer, Postman; project diletakkan di `htdocs` |
| 1 — Tailwind v4 | Utility-first class langsung di PHP/HTML, responsive utilities, build CLI ke `.css` statis |
| 2 — JavaScript & API | Event listeners, async `fetch`, JSON, REST method, fallback dan error handling |
| 3 — PHP OOP | Laravel Models, Controllers, Resources, Middleware; native PHP lama dijaga agar aplikasi tetap kompatibel |
| 4 — Laravel REST API | Route/controller/resource, CRUD layanan dan booking, MySQL/Eloquent, validasi request |
| 5 — JWT & upload | JWT Bearer Token, middleware role, CORS, unggah gambar layanan ke public storage |
| 6 — Frontend integration | `assets/js/api.js` memanggil login, slot, dan booking API; menangani status `422`; fallback ke PHP jika API mati |

## 6. Catatan database dan akun demo

Database `barz_booking` dipakai bersama oleh website PHP dan Laravel API. Kredensial demo yang dipakai README API adalah `admin@barz.com / barz2024` dan `superadmin@barz.com / superadmin` setelah seeding. Hindari menggunakan data/demo password ini di internet.
