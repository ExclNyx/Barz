<?php
$pageTitle = 'Layanan - Barz Barbershop';

$services = getServices(true);
$cat = strtoupper($_GET['cat'] ?? 'ALL');
$cats = ['ALL' => 'Semua', 'HAIRCUT' => 'Haircut', 'HAIRSTYLING' => 'Hairstyling', 'COLORING' => 'Coloring'];
if (!isset($cats[$cat])) $cat = 'ALL';

$icons = [
    'HAIRCUT' => '<svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>',
    'HAIRSTYLING' => '<svg class="ic" viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8"/></svg>',
    'COLORING' => '<svg class="ic" viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.65-.75 1.65-1.69 0-.44-.18-.84-.44-1.13-.26-.29-.43-.68-.43-1.12A1.68 1.68 0 0 1 14.46 16h2.08A2.46 2.46 0 0 0 19 13.54c0-.62-.24-1.19-.63-1.62C17.03 10.31 17 9.5 17 8.99 17 5.14 14.5 2 12 2z"/></svg>',
];

$groups = ['HAIRCUT' => [], 'HAIRSTYLING' => [], 'COLORING' => []];
foreach ($services as $s) {
    $c = serviceCategory($s);
    if (!isset($groups[$c])) $groups[$c] = [];
    $groups[$c][] = $s;
}
if ($cat !== 'ALL') {
    $groups = [$cat => $groups[$cat] ?? []];
}

ob_start();
?>

<section class="section wrap">
    <div class="sec-head center">
        <h1 class="sec-title">Layanan Kami</h1>
        <p>Pilih layanan yang sesuai gaya &amp; kebutuhan kamu.</p>
    </div>

    <div class="cat-tabs">
        <?php foreach ($cats as $k => $label): ?>
            <a href="/Barz/index.php?page=services<?php echo $k !== 'ALL' ? '&cat=' . $k : ''; ?>" class="cat-tab<?php echo $cat === $k ? ' on' : ''; ?>"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <?php $total = array_sum(array_map('count', $groups)); ?>
    <?php if ($total > 0): ?>
        <?php foreach ($groups as $gname => $items): ?>
            <?php if (count($items) === 0) continue; ?>
            <div class="svc-sec">
                <div class="svc-sec-head">
                    <span class="sec-ic"><?php echo $icons[$gname] ?? ''; ?></span>
                    <h2><?php echo ucfirst(strtolower($gname)); ?></h2>
                    <span class="svc-count"><?php echo count($items); ?> layanan</span>
                </div>
                <div class="svc3">
                    <?php foreach ($items as $service): ?>
                    <article class="svc">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="svc-img">
                        <?php else: ?>
                            <div class="svc-img-ph" aria-hidden="true">
                                <svg class="ic" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                            </div>
                        <?php endif; ?>
                        <div class="svc-body">
                            <span class="svc-cat"><?php echo $gname; ?></span>
                            <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                            <p class="desc"><?php echo htmlspecialchars($service['description']); ?></p>
                            <div class="svc-meta">
                                <span class="price"><?php echo formatPrice($service['price']); ?></span>
                                <span class="dur">
                                    <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php echo $service['duration']; ?> mnt
                                </span>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <a href="/Barz/index.php?page=booking&service=<?php echo $service['id']; ?>" class="button button-primary button-block">
                                    Booking
                                    <svg class="ic" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            <?php else: ?>
                                <a href="/Barz/index.php?page=login" class="button button-dark button-block">Login untuk Booking</a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="panel empty">
        <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <h3>Belum ada layanan yang tersedia</h3>
        <p>Admin sedang menyiapkan layanan. Coba cek lagi nanti ya.</p>
    </div>
    <?php endif; ?>

    <?php if (!isLoggedIn() && $total > 0): ?>
    <div class="panel invite">
        <h3>Siap tampil beda?</h3>
        <p>Daftar sekarang untuk mulai booking dan nikmati fitur lengkapnya.</p>
        <div class="cta-row">
            <a href="/Barz/index.php?page=register" class="button button-primary">Daftar Sekarang</a>
            <a href="/Barz/index.php?page=login" class="button button-outline">Sudah Punya Akun</a>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>
