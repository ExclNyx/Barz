<?php
$pageTitle = 'Home - Barz Barbershop';

// Fetch featured services
$services = getServices(true);

ob_start();
?>

<section class="wrap hero2 max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] grid grid-cols-2 gap-10 items-center text-left pt-18 px-0 pb-14 max-[960px]:grid-cols-[1fr] max-[600px]:gap-6 max-[600px]:pt-13 max-[600px]:px-0 max-[600px]:pb-10">
    <div>
        <span class="hero-badge inline-block py-1.5 px-4 rounded-full bg-[rgba(136,144,99,.15)] border border-[rgba(136,144,99,.3)] text-barz-ink text-[.75rem] font-bold tracking-[.08em] uppercase [margin-bottom:16px]">Premium Barbershop Experience</span>
        <h1 class="font-sans font-bold leading-tight tracking-tight text-5xl [margin-bottom:16px] max-[960px]:text-4xl max-[600px]:text-2xl">Best <span class="hl text-barz-accent">Hairstyle</span>, Percaya Diri Maksimal</h1>
        <p class="lead text-lg text-barz-ink max-w-160 mt-0 mx-auto mb-7 mx-0">Booking jadwal potong rambut tanpa antri. Datang, duduk, kelihatan keren.</p>
        <div class="cta-row flex flex-wrap gap-3 justify-center justify-start">
            <a href="/Barz/index.php?page=booking" class="button button-primary text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover disabled:opacity-50 disabled:cursor-not-allowed">Booking Sekarang</a>
            <a href="/Barz/index.php?page=services" class="button button-outline text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-transparent border-barz-accent hover:bg-barz-accent hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">Lihat Layanan</a>
        </div>
    </div>
    <div class="hero-art bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] min-h-85 flex items-center justify-center text-barz-accent" role="img" aria-label="Barz Barbershop">
        <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-24 h-24 opacity-40" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
    </div>
</section>

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="about2 grid grid-cols-2 gap-10 items-center max-[960px]:grid-cols-[1fr] max-[600px]:gap-6">
        <div>
            <p class="micro text-[.75rem] text-barz-muted [margin-top:4px] text-barz-accent font-bold tracking-[.08em] uppercase" >About BARZ</p>
            <h2 class="sec-title font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:8px]">Kombinasi Seni Potong Klasik &amp; Sentuhan Modern</h2>
            <p>Barz adalah barbershop modern yang fokus pada kenyamanan dan efisiensi waktu kamu. Kami paham bahwa waktu kamu berharga, makanya sistem booking online kami bikin kamu bisa langsung datang dan potong tanpa harus antri lama.</p>
            <p>Barber kami punya pengalaman bertahun-tahun dalam berbagai gaya potongan, dari klasik sampai modern. Tempat yang nyaman, harga transparan, dan hasil yang memuaskan.</p>
            <div class="about-feat grid grid-cols-2 gap-3 [margin-top:20px] max-[600px]:grid-cols-[1fr]">
                <div class="about-feat-item p-4 rounded-2xl bg-barz-bg border border-barz-line">
                    <strong class="block text-[.9rem] [margin-bottom:4px]">Steril &amp; Higienis</strong>
                    <p class="text-[.8rem] text-barz-muted">Pembersihan alat UV sanitizer setiap pemakaian.</p>
                </div>
                <div class="about-feat-item p-4 rounded-2xl bg-barz-bg border border-barz-line">
                    <strong class="block text-[.9rem] [margin-bottom:4px]">Produk Premium</strong>
                    <p class="text-[.8rem] text-barz-muted">Menggunakan lini clay &amp; pomade pilihan.</p>
                </div>
            </div>
        </div>
        <div class="about-imgs grid grid-cols-2 gap-3">
            <div class="about-img-ph bg-barz-bg border border-barz-line rounded-2xl min-h-55 flex items-center justify-center text-barz-accent [&:nth-child(2)]:[margin-top:24px] max-[600px]:[&:nth-child(2)]:[margin-top:0]" role="img" aria-label="Suasana Barz">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-12 h-12 opacity-40" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
            <div class="about-img-ph bg-barz-bg border border-barz-line rounded-2xl min-h-55 flex items-center justify-center text-barz-accent [&:nth-child(2)]:[margin-top:24px] max-[600px]:[&:nth-child(2)]:[margin-top:0]" role="img" aria-label="Detail potongan Barz">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-12 h-12 opacity-40" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
        </div>
    </div>
</section>



<section class="marquee-section overflow-hidden border-t border-barz-line border-b py-10 px-0 bg-white">
    <div class="wrap center max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] text-center mb-6" >
        <p class="micro text-[.75rem] text-barz-muted [margin-top:4px] text-barz-accent font-bold tracking-[.08em] uppercase" >Inspirasi Gaya Rambut</p>
        <h2 class="sec-title font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:8px]">Gaya Rambut Favorit Pelanggan BARZ</h2>
        <p class="micro text-[.75rem] text-barz-muted [margin-top:4px]">Gulir otomatis ke kiri — arahkan kursor untuk pause</p>
    </div>
    <div class="marquee-track flex gap-5 w-[max-content] animate-scroll-left hover:[animation-play-state:paused]">
        <?php
        $styles = [
            ['name' => 'Low Taper Fade', 'desc' => 'Gradasi halus di pelipis dan leher bawah.', 'tag' => 'Popular 2026'],
            ['name' => 'Textured French Crop', 'desc' => 'Poni pendek bertekstur, samping undercut kencang.', 'tag' => 'Trending'],
            ['name' => 'Mid Fade Pompadour', 'desc' => 'Atas bervolume disisir ke belakang.', 'tag' => 'Classic'],
            ['name' => 'Modern Buzz Cut Fade', 'desc' => 'Rapi militer dengan gradasi tajam.', 'tag' => 'Low Maintenance'],
            ['name' => 'Slick Back Undercut', 'desc' => 'Klimis klasik, sisi tajam.', 'tag' => 'Sharp Look'],
        ];
        foreach (array_merge($styles, $styles) as $s): ?>
        <div class="marquee-card shrink-0 w-70 bg-barz-bg border border-barz-line rounded-2xl overflow-hidden">
            <div class="marquee-card-img h-45 bg-barz-surface flex items-center justify-center text-barz-accent text-5xl" aria-hidden="true">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-12 h-12 opacity-40" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
            </div>
            <div class="marquee-card-body p-4">
                <span class="marquee-tag inline-block py-0.5 px-2 rounded text-[.7rem] font-bold bg-barz-ink text-white [margin-bottom:8px]"><?php echo $s['tag']; ?></span>
                <h4 class="font-sans font-bold leading-tight tracking-tight text-[.95rem] [margin-bottom:4px]"><?php echo $s['name']; ?></h4>
                <p class="text-[.8rem] text-barz-muted [margin-bottom:12px]"><?php echo $s['desc']; ?></p>
                <a href="/Barz/index.php?page=booking" class="button button-primary button-small button-block text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover py-[9px] px-4 text-[.85rem] w-full disabled:opacity-50 disabled:cursor-not-allowed">Pilih Gaya Ini</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="loc-grid grid grid-cols-[1fr_2fr] gap-6 max-[960px]:grid-cols-[1fr]">
        <div class="loc-info flex flex-col gap-4">
            <p class="micro text-[.75rem] text-barz-muted [margin-top:4px] text-barz-accent font-bold tracking-[.08em] uppercase" >Lokasi BARZ</p>
            <h2 class="sec-title font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:8px]">Kunjungi Outlet BARZ Studio</h2>
            <p>Strategis di pusat kota. Parkir luas, ruang tunggu AC, free Wi-Fi.</p>
            <div class="loc-item flex gap-2.5 text-[.88rem]">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] text-barz-accent" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div><strong>Alamat Utama:</strong><br class="text-barz-muted"><span >Jl. Boulevard Barbershop No. 88, Malang Center</span></div>
            </div>
            <div class="loc-item flex gap-2.5 text-[.88rem]">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] text-barz-accent" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <div><strong>Jam Operasional:</strong><br class="text-barz-muted"><span >Setiap Hari: 09:00 - 21:00 WIB</span></div>
            </div>
        </div>
        <div class="loc-map rounded-2xl border border-barz-line overflow-hidden min-h-65 bg-barz-bg flex items-center justify-center relative" class="border rounded-2xl">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.5012!2d112.6209!3d-7.9809!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6281a1a1a1a1a%3A0x1a1a1a1a1a1a1a1a!2sJl.%20Boulevard%20Barbershop%20No.%2088%2C%20Malang!5e0!3m2!1sid!2sid!4v1695000000000!5m2!1sid!2sid"
                width="100%" height="260"  allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>
