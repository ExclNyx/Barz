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

$statusLabel = [
    'pending' => 'Menunggu',
    'confirmed' => 'Terkonfirmasi',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    'no_show' => 'Tidak Hadir',
];
?>

<section class="section wrap">
    <div class="page-head">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Booking Saya</h1>
                <p>Kelola semua jadwal cukur kamu di sini.</p>
            </div>
            <a href="/Barz/index.php?page=booking" class="button button-primary">
                <svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Booking Baru
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-ok" role="status">
            <svg class="ic" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="panel empty">
            <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <h3>Belum ada booking</h3>
            <p>Kamu belum pernah membuat janji potong rambut.</p>
            <a href="/Barz/index.php?page=booking" class="button button-primary">Buat Booking Sekarang</a>
        </div>
    <?php else: ?>
        <div class="my-list">
            <?php foreach ($bookings as $booking): ?>
            <article class="book-item">
                <div class="book-item-head">
                    <div>
                        <strong>Booking #<?php echo $booking['id']; ?></strong>
                        <span class="tag tag-<?php echo $booking['status']; ?>" style="margin-left:12px"><?php echo $statusLabel[$booking['status']] ?? $booking['status']; ?></span>
                    </div>
                    <div class="bill"><?php echo formatPrice($booking['total_price']); ?></div>
                </div>
                <div class="book-item-body">
                    <div class="kv-row">
                        <span class="lbl">Layanan</span>
                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                    </div>
                    <div class="kv-row">
                        <span class="lbl">Barber</span>
                        <strong><?php echo htmlspecialchars($booking['barber_name'] ?? 'Siapa Saja'); ?></strong>
                    </div>
                    <div class="kv-row">
                        <span class="lbl">Tanggal</span>
                        <span class="val">
                            <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?php echo formatDate($booking['booking_date']); ?>
                        </span>
                    </div>
                    <div class="kv-row">
                        <span class="lbl">Waktu</span>
                        <span class="val">
                            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?>
                        </span>
                    </div>
                    <?php if ($booking['notes']): ?>
                    <div class="notebox" style="margin-top:12px">
                        <span class="lbl">Catatan</span>
                        <p><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="micro" style="margin-top:8px">Dibuat: <?php echo date('d M Y H:i', strtotime($booking['created_at'])); ?></div>
                </div>
                <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                <div class="book-item-foot">
                    <?php if ($booking['status'] === 'pending'): ?>
                        <p class="warnline">
                            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Menunggu konfirmasi admin
                        </p>
                    <?php endif; ?>
                    <form method="POST" onsubmit="return confirm('Yakin ingin membatalkan booking ini?');">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <button type="submit" name="cancel_booking" class="button button-danger-ghost button-small">
                            <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
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