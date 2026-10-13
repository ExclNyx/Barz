# Catatan Perbaikan UI/UX dan Booking Barz

## Perbaikan yang ditargetkan

- Hamburger sidebar admin/superadmin memakai utility `md:hidden`, jadi hanya muncul pada ukuran layar mobile/tablet sempit. Sidebar tetap tersedia di desktop.
- Hamburger navigasi pelanggan memakai `hidden` dan breakpoint `max-[960px]:inline-flex`, tanpa `inline-flex` di kelas dasar yang sebelumnya dapat menimpa perilaku tersembunyi.
- Filter tanggal di admin bookings sekarang opsional. Jika tanggal kosong, tabel menampilkan booking dari semua tanggal; ini membuat booking masa depan seperti 20 Oktober tidak tersembunyi saat hari ini 13 Oktober.
- Ringkasan status admin menghitung semua booking, independen dari filter tabel.
- Penentuan slot memperhitungkan barber aktif dan bentrok waktu untuk seluruh durasi layanan. Booking baru memilih barber tersedia dari backend; pelanggan tidak memilih barber di form dan riwayat pelanggan tidak menampilkan label barber.
- Halaman Kelola Layanan mendapat kartu ringkasan, pencarian lokal, tabel responsive dengan pemisah baris, badge status, dan aksi layanan yang lebih jelas.
- Halaman Kelola Booking mendapat ringkasan status, filter, pencarian lokal, detail booking, dan dialog ubah status.
- Layanan yang sudah direferensikan oleh booking historis tidak dihapus secara fisik. Aksi hapus menonaktifkannya agar relasi riwayat tetap aman.
- Styling halaman yang diperbarui memakai utility Tailwind langsung pada HTML/PHP. Tidak ada `tailwind.php`, stylesheet lama, blok `<style>`, atribut `style="..."`, atau selector CSS custom di file PHP/CSS.

## Menjalankan perubahan Tailwind

Di direktori project:

```powershell
npm install
npm run css:build
```

Kemudian jalankan melalui XAMPP seperti sebelumnya.

## Pemeriksaan

Pemeriksaan sintaks PHP (`php -l`) dilakukan pada 62 file dan pemeriksaan sintaks JavaScript (`node --check`) berhasil. Uji end-to-end browser/database harus tetap dilakukan di komputer lokal menggunakan database Barz milik pengguna.
