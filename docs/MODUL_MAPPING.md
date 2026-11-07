# Pemetaan Barz ke Checklist Week 1–7

Pemetaan ini menunjukkan file konkret yang dapat dibuka saat presentasi. Week 1–4 tetap berada pada website utama, Week 5 menggunakan PHP Native + MySQLi yang sudah dipakai aplikasi, Week 6 memiliki implementasi OOP + MVC Manual tersendiri, dan Week 7 menggunakan backend Laravel.

| Week | Materi | Implementasi Barz | Bukti utama |
|---|---|---|---|
| 1 | HTML | Struktur halaman, form, tabel, semantic markup dan PHP server-rendered page | `index.php`, `customer/pages/`, `admin/` |
| 2 | CSS | Tailwind CSS v4 utility-first, responsive breakpoint, hover/focus state dan build CSS | `src/css/tailwind.css`, `assets/css/tailwind.css`, class pada file PHP |
| 3 | JavaScript | DOM selection, event listener, form event, validasi browser, loading state dan DOM update | `assets/js/main.js`, `assets/js/api.js`, `customer/pages/booking.php` |
| 4 | API | Fetch API, async/await, JSON, HTTP status handling, REST method, endpoint PHP fallback, Laravel API dan Postman | `assets/js/api.js`, `api/`, `api-server/routes/api.php`, `api-server/docs/Barz-API.postman_collection.json` |
| 5 | PHP + MySQL Native | Session, koneksi MySQLi, prepared statement, query, autentikasi, proses booking dan CRUD | `includes/init.php`, `includes/functions.php`, `auth/`, `api/`, `admin/`, `database/schema.sql` |
| 6 | OOP + MVC Manual | Class, object, constructor, encapsulation, Model, Controller, View dan operasi data layanan menggunakan MySQLi | `backend-php/demo-mvc/` |
| 7 | Laravel MVC | Route, Controller, Eloquent Model, Resource, Middleware, migration, validation, JWT dan REST API | `api-server/routes/`, `api-server/app/`, `api-server/database/` |

## Catatan penting

- PHP Native Week 5 tidak dipindahkan ke framework. Source utamanya tetap di root agar halaman lama dan URL aplikasi tidak rusak.
- Week 6 tidak menggunakan Laravel sebagai bukti MVC manual. Folder `backend-php/demo-mvc/` adalah implementasi PHP Native yang memakai tabel `services` dari database Barz yang sama.
- Week 7 berada di folder `api-server/` (nama folder backend Laravel pada repository saat ini). Frontend lama dan Laravel API berjalan terpisah tetapi menggunakan database MySQL `barz_booking` yang sama.
- Endpoint Laravel tidak dianggap teruji hanya karena route/controller tersedia. Uji runtime tetap dilakukan secara lokal dengan Composer, MySQL, dan Postman.
