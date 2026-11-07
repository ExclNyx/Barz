# Database MySQL Barz

Barz menggunakan MySQL dengan database bernama `barz_booking`.

## Setup

1. Nyalakan MySQL di XAMPP.
2. Buka phpMyAdmin.
3. Buat/import database melalui file `schema.sql`.
4. Pastikan konfigurasi di `includes/init.php` sesuai dengan MySQL lokal.
5. Backend Laravel di `api-server/` memakai database yang sama melalui file `.env` miliknya.

## Struktur inti

- `users`: akun customer, admin, dan super admin.
- `services`: daftar layanan dan harga.
- `barbers`: barber yang tersedia.
- `bookings`: transaksi booking customer.
- `schedules` dan `time_slots`: jadwal operasional dan slot booking.
- `chats` dan `chat_messages`: percakapan customer dan admin.
- `settings`: pengaturan operasional barbershop.

`schema.mysql.legacy.sql` adalah arsip skema lama dan tidak dipakai dalam setup Barz saat ini.
