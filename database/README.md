# Database Barz menggunakan Firebase Cloud Firestore

`schema.mysql.legacy.sql` adalah skema lama untuk MySQL dan **tidak dipakai** pada versi Firebase. Jangan impor file SQL ke Firestore Console.

Setelah Composer dan kredensial server siap, jalankan dari terminal pada root project:

```powershell
php scripts/seed_firestore.php
```

Skrip membuat data awal pada koleksi `services`, `barbers`, `settings`, dan akun demo `users`. Booking, slot jadwal, chat, pesan, dan laporan pembelian akan dibuat ketika fiturnya digunakan.
