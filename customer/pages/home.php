<?php
$pageTitle = 'Home - Barz Barbershop';

// Fetch featured services
$services = getServices(true);

ob_start();
?>

<section class="wrap hero2">
    <div>
        <span class="hero-badge">Premium Barbershop Experience</span>
        <h1>Best <span class="hl">Hairstyle</span>, Percaya Diri Maksimal</h1>
        <p class="lead">Booking jadwal potong rambut tanpa antri. Datang, duduk, kelihatan keren.</p>
        <div class="cta-row">
            <a href="/Barz/index.php?page=booking" class="button button-primary">Booking Sekarang</a>
            <a href="/Barz/index.php?page=services" class="button button-outline">Lihat Layanan</a>
        </div>
    </div>
    <div class="hero-art" role="img" aria-label="Barz Barbershop">
        <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
    </div>
</section>

<section class="section wrap">
    <div class="about2">
        <div>
            <p class="micro text-[#889063] font-bold tracking-[.08em] uppercase">About BARZ</p>
            <h2 class="sec-title">Kombinasi Seni Potong Klasik &amp; Sentuhan Modern</h2>
            <p>Barz adalah barbershop modern yang fokus pada kenyamanan dan efisiensi waktu kamu. Kami paham bahwa waktu kamu berharga, makanya sistem booking online kami bikin kamu bisa langsung datang dan potong tanpa harus antri lama.</p>
            <p>Barber kami punya pengalaman bertahun-tahun dalam berbagai gaya potongan, dari klasik sampai modern. Tempat yang nyaman, harga transparan, dan hasil yang memuaskan.</p>
            <div class="about-feat">
                <div class="about-feat-item">
                    <strong>Steril &amp; Higienis</strong>
                    <p>Pembersihan alat UV sanitizer setiap pemakaian.</p>
                </div>
                <div class="about-feat-item">
                    <strong>Produk Premium</strong>
                    <p>Menggunakan lini clay &amp; pomade pilihan.</p>
                </div>
            </div>
        </div>
        <div class="about-imgs">
            <div class="about-img-ph" role="img" aria-label="Suasana Barz">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
            <div class="about-img-ph" role="img" aria-label="Detail potongan Barz">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
        </div>
    </div>
</section>



<section class="marquee-section">
    <div class="wrap center mb-6">
        <p class="micro text-[#889063] font-bold tracking-[.08em] uppercase">Inspirasi Gaya Rambut</p>
        <h2 class="sec-title">Gaya Rambut Favorit Pelanggan BARZ</h2>
        <p class="micro">Gulir otomatis ke kiri — arahkan kursor untuk pause</p>
    </div>
    <div class="marquee-track">
        <?php
        $styles = [
            ['name' => 'Low Taper Fade', 'desc' => 'Gradasi halus di pelipis dan leher bawah.', 'tag' => 'Popular 2026'],
            ['name' => 'Textured French Crop', 'desc' => 'Poni pendek bertekstur, samping undercut kencang.', 'tag' => 'Trending'],
            ['name' => 'Mid Fade Pompadour', 'desc' => 'Atas bervolume disisir ke belakang.', 'tag' => 'Classic'],
            ['name' => 'Modern Buzz Cut Fade', 'desc' => 'Rapi militer dengan gradasi tajam.', 'tag' => 'Low Maintenance'],
            ['name' => 'Slick Back Undercut', 'desc' => 'Klimis klasik, sisi tajam.', 'tag' => 'Sharp Look'],
        ];
        foreach (array_merge($styles, $styles) as $s): ?>
        <div class="marquee-card">
            <div class="marquee-card-img" aria-hidden="true">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
            <div class="marquee-card-body">
                <span class="marquee-tag"><?php echo $s['tag']; ?></span>
                <h4><?php echo $s['name']; ?></h4>
                <p><?php echo $s['desc']; ?></p>
                <a href="/Barz/index.php?page=booking" class="button button-primary button-small button-block">Pilih Gaya Ini</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section wrap">
    <div class="loc-grid">
        <div class="loc-info">
            <p class="micro text-[#889063] font-bold tracking-[.08em] uppercase">Lokasi BARZ</p>
            <h2 class="sec-title">Kunjungi Outlet BARZ Studio</h2>
            <p>Strategis di pusat kota. Parkir luas, ruang tunggu AC, free Wi-Fi.</p>
            <div class="loc-item">
                <svg class="ic" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div><strong>Alamat Utama:</strong><br><span class="text-[#5A5A5A]">Jl. Boulevard Barbershop No. 88, Malang Center</span></div>
            </div>
            <div class="loc-item">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <div><strong>Jam Operasional:</strong><br><span class="text-[#5A5A5A]">Setiap Hari: 09:00 - 21:00 WIB</span></div>
            </div>
        </div>
        <div class="loc-map">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.5012!2d112.6209!3d-7.9809!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6281a1a1a1a1a%3A0x1a1a1a1a1a1a1a1a!2sJl.%20Boulevard%20Barbershop%20No.%2088%2C%20Malang!5e0!3m2!1sid!2sid!4v1695000000000!5m2!1sid!2sid"
                width="100%" height="260" class="border-0 rounded-2xl" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>
