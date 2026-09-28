<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Admin Dashboard - Barz Barbershop';
$db = getDB();
$stmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE booking_date = CURDATE()");
$stats['today_bookings'] = $stmt->fetch_assoc()['count'];
$stmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $stmt->fetch_assoc()['count'];
$stmt = $db->query("SELECT SUM(total_price) as total FROM bookings WHERE status = 'completed' AND MONTH(booking_date) = MONTH(CURDATE()) AND YEAR(booking_date) = YEAR(CURDATE())");
$stats['month_revenue'] = $stmt->fetch_assoc()['total'] ?? 0;
$stmt = $db->query("SELECT COUNT(*) as count FROM chats WHERE status = 'active'");
$stats['active_chats'] = $stmt->fetch_assoc()['count'];
$stmt = $db->prepare("SELECT b.*, s.name as service_name, u.name as customer_name, u.phone as customer_phone, br.name as barber_name FROM bookings b JOIN services s ON b.service_id = s.id JOIN users u ON b.customer_id = u.id LEFT JOIN barbers br ON b.barber_id = br.id WHERE b.booking_date = CURDATE() AND b.status IN ('pending', 'confirmed') ORDER BY b.start_time ASC LIMIT 10");
$stmt->execute();
$upcomingBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="page">
    <div class="page-top">
        <h1>Dashboard Admin</h1>
        <div class="who">Logged in as: <?php echo htmlspecialchars($_SESSION['name']); ?></div>
    </div>
    <div class="stat4">
        <div class="stat">
            <div><small>Booking Hari Ini</small><b><?php echo $stats['today_bookings']; ?></b></div>
            <div class="stat-ic g"><svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Pending Approval</small><b><?php echo $stats['pending_bookings']; ?></b></div>
            <div class="stat-ic t"><svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Revenue Bulan Ini</small><b><?php echo formatPrice($stats['month_revenue']); ?></b></div>
            <div class="stat-ic o"><svg class="ic" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Chat Aktif</small><b><?php echo $stats['active_chats']; ?></b></div>
            <div class="stat-ic r"><svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        </div>
    </div>
    <div class="act2">
        <a href="/Barz/admin/bookings.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><b>Kelola Booking</b><small>Konfirmasi, batalkan, lihat detail</small></div>
        </a>
        <a href="/Barz/admin/services.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
            <div><b>Kelola Layanan</b><small>Edit paket, harga, durasi</small></div>
        </a>
        <a href="/Barz/admin/barbers.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div><b>Kelola Barber</b><small>Tambah, edit, nonaktifkan</small></div>
        </a>
        <a href="/Barz/admin/chats.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
            <div><b>Chat Customer</b><small>Balas pesan pelanggan</small></div>
        </a>
    </div>
    <div class="card">
        <div class="card-h"><h3>Booking Hari Ini</h3><a href="/Barz/admin/bookings.php" class="btn btn-s btn-o">Lihat Semua</a></div>
        <div class="card-b">
            <?php if (empty($upcomingBookings)): ?>
                <p class="center">Tidak ada booking hari ini</p>
            <?php else: ?>
            <div class="twrap"><table><thead><tr><th>ID</th><th>Waktu</th><th>Customer</th><th>Layanan</th><th>Barber</th><th>Status</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($upcomingBookings as $booking): ?>
            <tr>
                <td>#<?php echo $booking['id']; ?></td>
                <td><?php echo substr($booking['start_time'], 0, 5); ?></td>
                <td><strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br><small><?php echo htmlspecialchars($booking['customer_phone']); ?></small></td>
                <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                <td><?php echo $booking['barber_name'] ?? '-'; ?></td>
                <td><?php echo getStatusBadge($booking['status']); ?></td>
                <td><a href="/Barz/admin/bookings.php?view=<?php echo $booking['id']; ?>" class="btn btn-s btn-o" aria-label="Lihat booking"><svg class="ic" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></td>
            </tr>
            <?php endforeach; ?>
            </tbody></table></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
