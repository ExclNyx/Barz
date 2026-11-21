# PRD — Barz Barbershop Booking System

**Versi:** 1.0  
**Tanggal:** 2026-09-24  
**Status:** Draft  

---

## 1. Latar Belakang

Barbershop saat ini menerima booking via Instagram DM. Admin kewalahan membalas pesan satu per satu, respons lambat, dan customer sering batal karena tidak tahu slot tersedia. Dibutuhkan sistem booking mandiri berbasis web yang bisa diakses dari HP.

---

## 2. Tujuan Produk

- Customer bisa booking sendiri tanpa menunggu admin balas
- Admin punya satu dashboard untuk kelola semua booking
- Chat antara customer dan admin terpusat di website
- Sistem bisa di-deploy ke shared hosting / VPS

---

## 3. Pengguna

| Tipe       | Deskripsi                                              |
|------------|--------------------------------------------------------|
| Customer   | Pengunjung website, booking layanan potong rambut      |
| Admin      | Staff barbershop, kelola booking, jadwal, chat         |

---

## 4. Fitur Utama

### 4.1 Customer

**F-C01 — Lihat Layanan**
- Tampilkan daftar paket: nama, harga, durasi, deskripsi
- Tidak perlu login untuk melihat

**F-C02 — Booking**
- Pilih paket layanan
- Pilih barber (opsional)
- Pilih tanggal dari kalender
- Pilih slot waktu tersedia (slot penuh = disabled)
- Isi nama + nomor HP
- Terima token booking unik setelah submit

**F-C03 — Kelola Booking**
- Lihat status booking (pending / confirmed / done / cancelled)
- Cancel booking (hanya jika status masih pending)
- Akses via halaman my-bookings (butuh login)

**F-C04 — Chat dengan Admin**
- Chat per sesi booking
- Customer kirim pesan, admin balas
- Riwayat chat tersimpan

**F-C05 — Autentikasi Customer**
- Register dengan email + password
- Login / logout
- Session berbasis PHP

---

### 4.2 Admin

**F-A01 — Dashboard**
- Total booking hari ini
- Booking pending menunggu konfirmasi
- Booking selesai bulan ini
- Shortcut ke halaman utama

**F-A02 — Kelola Booking**
- Lihat semua booking (filter: hari ini, minggu ini, semua)
- Ubah status: pending → confirmed → done, atau cancelled
- Lihat detail: customer, layanan, waktu, barber

**F-A03 — Kelola Layanan (Paket)**
- Tambah / edit / hapus paket
- Field: nama, harga, durasi (menit), deskripsi, status aktif

**F-A04 — Kelola Barber**
- Tambah / edit / hapus data barber
- Field: nama, foto, spesialisasi, status aktif

**F-A05 — Kelola Jadwal**
- Atur slot waktu tersedia per hari
- Buka / tutup slot tertentu
- Atur maksimum booking per slot

**F-A06 — Chat Management**
- Lihat semua percakapan aktif
- Balas pesan customer per booking
- Tandai percakapan selesai

**F-A07 — Login Admin**
- Login dengan email + password
- Semua halaman admin dilindungi session

---

## 5. Alur Utama (User Journey)

### Booking Baru
```
Customer buka /booking
  → Pilih paket
  → Pilih barber (skip opsional)
  → Pilih tanggal dari kalender
  → Pilih slot waktu tersedia
  → Isi nama + nomor HP
  → Submit
  → Redirect ke halaman detail booking + token
  → Status: pending

Admin dapat notif di dashboard
  → Buka detail booking
  → Ubah status → confirmed
  → Customer lihat status berubah di halamannya
```

### Chat
```
Customer buka halaman booking mereka
  → Klik tab Chat
  → Kirim pesan
  → Admin buka /admin/chats
  → Admin balas
  → Customer lihat balasan
```

---

## 6. Tech Stack

| Layer      | Teknologi                          |
|------------|------------------------------------|
| Backend    | PHP 8.x (vanilla, no framework)    |
| Database   | MySQL 8                            |
| Frontend   | HTML + CSS + JavaScript vanilla    |
| UI Kit     | Bootstrap 5                        |
| Icons      | Bootstrap Icons                    |
| Hosting    | Shared hosting cPanel / VPS        |

---

## 7. Skema Database

| Tabel           | Fungsi                                     |
|-----------------|--------------------------------------------|
| users           | Akun customer + admin                      |
| services        | Paket layanan (nama, harga, durasi)        |
| barbers         | Data barber                                |
| schedules       | Jadwal kerja barber per hari               |
| time_slots      | Slot waktu per jadwal                      |
| bookings        | Data booking customer                      |
| chats           | Sesi chat per booking                      |
| chat_messages   | Pesan dalam satu sesi chat                 |
| settings        | Konfigurasi aplikasi                       |

---

## 8. Batasan Sistem (Scope)

**Dalam scope v1.0:**
- Booking, jadwal, paket, barber, chat
- Admin dashboard dasar
- Auth customer + admin
- Deploy ke 1 cabang

**Di luar scope v1.0:**
- Payment gateway / pembayaran online
- Notifikasi WhatsApp / email otomatis
- Multi-cabang
- Mobile app native
- Google Calendar sync
- Export laporan (PDF/Excel)
- Loyalty program / poin

---

## 9. Rencana Pengembangan Bertahap

| Phase | Konten                                                   | Target Push |
|-------|----------------------------------------------------------|-------------|
| P1    | Setup project, DB schema, auth (login/register)          | Push 1      |
| P2    | Landing page + halaman layanan (customer)                | Push 2      |
| P3    | Alur booking (pilih paket → slot → konfirmasi)           | Push 3      |
| P4    | Halaman my-bookings customer                             | Push 4      |
| P5    | Admin dashboard + kelola booking                         | Push 5      |
| P6    | Admin: kelola layanan, barber, jadwal                    | Push 6      |
| P7    | Fitur chat customer ↔ admin                              | Push 7      |
| P8    | Polish: validasi, error handling, mobile responsiveness  | Push 8      |

---

## 10. Kriteria Selesai (Definition of Done)

- Tiap halaman bisa diakses tanpa PHP error
- Flow booking dari awal sampai konfirmasi admin berjalan
- Admin bisa login, lihat, dan ubah status booking
- Chat terkirim dan terbaca kedua pihak
- Tampilan mobile-friendly (Bootstrap responsive)
- Bisa di-deploy ke shared hosting tanpa modifikasi tambahan

---

## 11. Risiko

| Risiko                          | Mitigasi                                           |
|---------------------------------|----------------------------------------------------|
| Chat tidak real-time            | Gunakan polling JS tiap 5 detik (cukup untuk v1)   |
| Slot double booking             | Lock di DB level: cek max booking sebelum insert   |
| Admin lupa ubah status          | Tampilkan badge pending yang mencolok di dashboard |
| Customer salah input no HP      | Validasi format di frontend + backend              |
