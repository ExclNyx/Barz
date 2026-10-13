<?php
requireLogin();

$pageTitle = 'Booking Saya - Barz Barbershop';

$bookings = getBookingsByCustomer($_SESSION['user_id']);

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $bookingId = (int)$_POST['booking_id'];
    $db = getDB();
    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $bookingId, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Booking berhasil dibatalkan';
        redirect('my-bookings', 'customer');
    }
}

ob_start();

?>

<section class="section wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] py-14 px-0 max-[600px]:py-10 max-[600px]:px-0">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <div>
                <h1 class="font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:6px]">Booking Saya</h1>
                <p>Kelola semua jadwal cukur kamu di sini.</p>
            </div>
            <a href="/Barz/index.php?page=booking" class="button button-primary text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Booking Baru
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-ok rounded-xl py-3.5 px-4 text-[.9rem] [margin-bottom:20px] flex gap-2 items-start bg-[rgba(111,168,111,.12)] text-[#2d6b2d] border-l-[4px] border-barz-success" role="status">
            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5 [margin-top:2px]" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="panel empty bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] py-10 px-8 text-center">
            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-9 h-9 text-barz-accent [margin-bottom:12px]" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <h3 class="font-sans font-bold leading-tight tracking-tight [margin-bottom:8px]">Belum ada booking</h3>
            <p class="text-barz-ink [margin-bottom:20px]">Kamu belum pernah membuat janji potong rambut.</p>
            <a href="/Barz/index.php?page=booking" class="button button-primary text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover disabled:opacity-50 disabled:cursor-not-allowed">Buat Booking Sekarang</a>
        </div>
    <?php else: ?>
        <div class="my-list flex flex-col gap-4">
            <?php foreach ($bookings as $booking): ?>
            <article class="book-item bg-white border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] overflow-hidden hover:border-[rgba(136,144,99,.4)]">
                <div class="book-item-head flex justify-between items-start gap-3 py-4.5 px-5 border-b border-barz-line bg-[rgba(249,247,242,.6)]">
                    <div>
                        <strong class="text-[.95rem]">Booking #<?php echo $booking['id']; ?></strong>
                        <span class="mt-1 inline-block"><?php echo getStatusBadge($booking['status']); ?></span>
                    </div>
                    <div class="bill text-xl font-bold text-barz-accent [margin-bottom:6px]"><?php echo formatPrice($booking['total_price']); ?></div>
                </div>
                <div class="book-item-body p-5">
                    <div class="kv-row flex justify-between gap-3 py-2 px-0 border-b border-barz-line last:border-b">
                        <span class="lbl text-[.75rem] text-barz-muted block [margin-bottom:2px] min-w-20 flex-none">Layanan</span>
                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                    </div>
                    <div class="kv-row flex justify-between gap-3 py-2 px-0 border-b border-barz-line last:border-b">
                        <span class="lbl text-[.75rem] text-barz-muted block [margin-bottom:2px] min-w-20 flex-none">Tanggal</span>
                        <span class="val font-semibold flex items-center gap-1.5">
                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-[15px] h-[15px] text-barz-accent" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?php echo formatDate($booking['booking_date']); ?>
                        </span>
                    </div>
                    <div class="kv-row flex justify-between gap-3 py-2 px-0 border-b border-barz-line last:border-b">
                        <span class="lbl text-[.75rem] text-barz-muted block [margin-bottom:2px] min-w-20 flex-none">Waktu</span>
                        <span class="val font-semibold flex items-center gap-1.5">
                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-[15px] h-[15px] text-barz-accent" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?>
                        </span>
                    </div>
                    <?php if ($booking['notes']): ?>
                    <div class="notebox bg-barz-bg border border-barz-line rounded-xl p-3 [margin-top:12px] text-[.82rem] mt-3" >
                        <span class="lbl text-[.75rem] text-barz-muted block [margin-bottom:2px]">Catatan</span>
                        <p><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="micro text-[.75rem] text-barz-muted [margin-top:4px] mt-2" >Dibuat: <?php echo date('d M Y H:i', strtotime($booking['created_at'])); ?></div>
                </div>
                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                <div class="book-item-foot py-4 px-5 border-t border-barz-line flex justify-between items-center gap-3 flex-wrap">
                    <?php if ($booking['status'] === 'pending'): ?>
                        <p class="warnline text-[.78rem] text-[#8a6d3b] [margin-bottom:10px] flex gap-1.5 items-center">
                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-[15px] h-[15px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Menunggu konfirmasi admin
                        </p>
                    <?php endif; ?>
                    <form method="POST" onsubmit="return confirm('Yakin ingin membatalkan booking ini?');">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <button type="submit" name="cancel_booking" class="button button-danger-ghost button-small font-sans inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors py-[9px] px-4 text-[.85rem] disabled:opacity-50 disabled:cursor-not-allowed bg-transparent text-barz-danger border-barz-line hover:bg-[rgba(217,127,110,.08)] hover:border-barz-danger">
                            <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Batalkan
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../customer/layout.php';
?>