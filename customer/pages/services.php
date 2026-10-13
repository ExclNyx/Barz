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

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="sec-head center [margin-bottom:32px] text-center">
        <h1 class="sec-title font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:8px]">Layanan Kami</h1>
        <p>Pilih layanan yang sesuai gaya &amp; kebutuhan kamu.</p>
    </div>

    <div class="cat-tabs flex gap-2 [margin-bottom:20px] flex-wrap">
        <?php foreach ($cats as $k => $label): ?>
            <a href="/Barz/index.php?page=services<?php echo $k !== 'ALL' ? '&cat=' . $k : ''; ?>" class="cat-tab<?php echo $cat === $k ? ' on' : ''; ?> text-barz-accent py-2 px-4 border border-barz-line rounded-2xl bg-white text-[.85rem] font-semibold cursor-pointer transition no-underline text-barz-ink hover:border-barz-accent [&.on]:bg-barz-ink [&.on]:text-white [&.on]:border-barz-ink"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <?php $total = array_sum(array_map('count', $groups)); ?>
    <?php if ($total > 0): ?>
        <?php foreach ($groups as $gname => $items): ?>
            <?php if (count($items) === 0) continue; ?>
            <div class="svc-sec [margin-bottom:40px]">
                <div class="svc-sec-head flex items-center gap-3 border-b border-barz-line [padding-bottom:12px] [margin-bottom:20px]">
                    <span class="sec-ic"><?php echo $icons[$gname] ?? ''; ?></span>
                    <h2 class="font-sans font-bold leading-tight tracking-tight text-xl tracking-[.02em]"><?php echo ucfirst(strtolower($gname)); ?></h2>
                    <span class="svc-count text-[.75rem] bg-[rgba(136,144,99,.15)] text-barz-ink py-0.5 px-2.5 rounded-full font-bold"><?php echo count($items); ?> layanan</span>
                </div>
                <div class="svc3 grid grid-cols-3 gap-5 max-[960px]:grid-cols-2 max-[600px]:grid-cols-[1fr]">
                    <?php foreach ($items as $service): ?>
                    <article class="svc bg-white border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="svc-img max-w-full block h-50 object-cover w-full bg-barz-bg">
                        <?php else: ?>
                            <div class="svc-img-ph h-50 bg-barz-bg flex items-center justify-center text-barz-accent" aria-hidden="true">
                                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-10 h-10 opacity-50" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
                            </div>
                        <?php endif; ?>
                        <div class="svc-body p-6 flex flex-col flex-1">
                            <span class="svc-cat inline-block py-0.5 px-2 rounded text-[.7rem] font-bold bg-[rgba(136,144,99,.15)] text-barz-accent [margin-bottom:8px] uppercase tracking-[.04em]"><?php echo $gname; ?></span>
                            <h3 class="font-sans font-bold leading-tight tracking-tight text-xl [margin-bottom:8px]"><?php echo htmlspecialchars($service['name']); ?></h3>
                            <p class="desc text-[.9rem] text-barz-ink [margin-bottom:16px] flex-1"><?php echo htmlspecialchars($service['description']); ?></p>
                            <div class="svc-meta flex items-center justify-between [margin-bottom:20px] [padding-bottom:16px] border-b border-barz-line">
                                <span class="price text-xl font-bold text-barz-accent"><?php echo formatPrice($service['price']); ?></span>
                                <span class="dur text-[.85rem] text-barz-muted inline-flex items-center gap-1.5">
                                    <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-[15px] h-[15px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php echo $service['duration']; ?> mnt
                                </span>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <a href="/Barz/index.php?page=booking&service=<?php echo $service['id']; ?>" class="button button-primary button-block text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover w-full disabled:opacity-50 disabled:cursor-not-allowed">
                                    Booking
                                    <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            <?php else: ?>
                                <a href="/Barz/index.php?page=login" class="button button-dark button-block text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-ink text-white hover:bg-barz-ink-hover w-full disabled:opacity-50 disabled:cursor-not-allowed">Login untuk Booking</a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="panel empty bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] py-10 px-8 text-center">
        <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-9 h-9 text-barz-accent [margin-bottom:12px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <h3 class="font-sans font-bold leading-tight tracking-tight [margin-bottom:8px]">Belum ada layanan yang tersedia</h3>
        <p class="text-barz-ink [margin-bottom:20px]">Admin sedang menyiapkan layanan. Coba cek lagi nanti ya.</p>
    </div>
    <?php endif; ?>

    <?php if (!isLoggedIn() && $total > 0): ?>
    <div class="panel invite bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] [margin-top:40px] p-9 text-center max-w-180 [margin-left:auto] [margin-right:auto]">
        <h3 class="font-sans font-bold leading-tight tracking-tight [margin-bottom:8px]">Siap tampil beda?</h3>
        <p class="[margin-bottom:20px]">Daftar sekarang untuk mulai booking dan nikmati fitur lengkapnya.</p>
        <div class="cta-row flex flex-wrap gap-3 justify-center">
            <a href="/Barz/index.php?page=register" class="button button-primary text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover disabled:opacity-50 disabled:cursor-not-allowed">Daftar Sekarang</a>
            <a href="/Barz/index.php?page=login" class="button button-outline text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-transparent border-barz-accent hover:bg-barz-accent hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">Sudah Punya Akun</a>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>
