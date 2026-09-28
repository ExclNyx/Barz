<?php
require_once __DIR__ . '/../includes/init.php';
requireSuperAdmin();

$pageTitle = 'Dashboard Eksekutif - Super Admin';
$db = getDB();

$stmt = $db->query("SELECT COALESCE(SUM(total_price),0) as total FROM bookings WHERE status = 'completed'");
$revenue = $stmt->fetch_assoc()['total'];
$stmt = $db->query("SELECT COALESCE(SUM(cost),0) as total FROM buying_reports");
$buyTotal = $stmt->fetch_assoc()['total'];
$stmt = $db->query("SELECT COALESCE(SUM(cost),0) as total FROM buying_reports WHERE MONTH(purchase_date) = MONTH(CURDATE()) AND YEAR(purchase_date) = YEAR(CURDATE())");
$buyMonth = $stmt->fetch_assoc()['total'];
$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role IN ('admin','super_admin')");
$adminCount = $stmt->fetch_assoc()['count'];
$recent = $db->query("SELECT * FROM buying_reports ORDER BY purchase_date DESC, id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="page">
    <div class="page-top">
        <h1>Dashboard Eksekutif</h1>
        <div class="who">Logged in as: <?php echo htmlspecialchars($_SESSION['name']); ?></div>
    </div>
    <div class="stat4">
        <div class="stat">
            <div><small>Total Revenue</small><b><?php echo formatPrice($revenue); ?></b></div>
            <div class="stat-ic g"><svg class="ic" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Buying Bulan Ini</small><b><?php echo formatPrice($buyMonth); ?></b></div>
            <div class="stat-ic o"><svg class="ic" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Estimasi Profit Kotor</small><b><?php echo formatPrice($revenue - $buyTotal); ?></b></div>
            <div class="stat-ic t"><svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Jumlah Admin</small><b><?php echo $adminCount; ?></b></div>
            <div class="stat-ic r"><svg class="ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        </div>
    </div>
    <div class="act2">
        <a href="/Barz/superadmin/admins.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div><b>Kelola Admin</b><small>Tambah, hapus akun admin</small></div>
        </a>
        <a href="/Barz/superadmin/buying.php" class="act">
            <div class="act-ic"><svg class="ic" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
            <div><b>Laporan Buying</b><small>Catat belanja operasional</small></div>
        </a>
    </div>
    <div class="card">
        <div class="card-h"><h3>Buying Terbaru</h3><a href="/Barz/superadmin/buying.php" class="btn btn-s btn-o">Lihat Semua</a></div>
        <div class="card-b">
            <?php if (empty($recent)): ?>
                <p class="center">Belum ada laporan buying</p>
            <?php else: ?>
            <div class="twrap"><table><thead><tr><th>Item</th><th>Supplier</th><th>Tanggal</th><th>Biaya</th></tr></thead><tbody>
            <?php foreach ($recent as $r): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($r['item_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($r['supplier'] ?: '-'); ?></td>
                <td><?php echo formatDate($r['purchase_date']); ?></td>
                <td><?php echo formatPrice($r['cost']); ?></td>
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
