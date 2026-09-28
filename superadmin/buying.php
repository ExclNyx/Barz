<?php
require_once __DIR__ . '/../includes/init.php';
requireSuperAdmin();

$pageTitle = 'Laporan Buying - Super Admin';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_report'])) {
    $item = sanitize($_POST['item_name'] ?? '');
    $supplier = sanitize($_POST['supplier'] ?? '');
    $cost = (float)($_POST['cost'] ?? 0);
    $category = sanitize($_POST['category'] ?? 'General');
    $date = $_POST['purchase_date'] ?? date('Y-m-d');
    $notes = sanitize($_POST['notes'] ?? '');
    if ($item && $cost > 0) {
        $stmt = $db->prepare("INSERT INTO buying_reports (item_name, supplier, cost, category, purchase_date, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsssi", $item, $supplier, $cost, $category, $date, $notes, $_SESSION['user_id']);
        $stmt->execute();
        $_SESSION['success'] = 'Laporan buying ditambahkan';
        header('Location: /Barz/superadmin/buying.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_report'])) {
    $id = (int)$_POST['report_id'];
    $stmt = $db->prepare("DELETE FROM buying_reports WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = 'Laporan dihapus';
    header('Location: /Barz/superadmin/buying.php');
    exit;
}

$stmt = $db->query("SELECT COALESCE(SUM(cost),0) as total, COUNT(*) as cnt FROM buying_reports WHERE MONTH(purchase_date) = MONTH(CURDATE()) AND YEAR(purchase_date) = YEAR(CURDATE())");
$month = $stmt->fetch_assoc();
$stmt = $db->query("SELECT COALESCE(SUM(cost),0) as total FROM buying_reports");
$grand = $stmt->fetch_assoc()['total'];
$reports = $db->query("SELECT * FROM buying_reports ORDER BY purchase_date DESC, id DESC")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="page">
    <div class="page-top">
        <h1>Laporan Buying</h1>
        <button class="btn btn-p" onclick="document.getElementById('addBuyingModal').showModal()">
            <svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Pembelian
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="stat4">
        <div class="stat">
            <div><small>Buying Bulan Ini</small><b><?php echo formatPrice($month['total']); ?></b></div>
            <div class="stat-ic o"><svg class="ic" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Total Laporan</small><b><?php echo $month['cnt']; ?></b></div>
            <div class="stat-ic t"><svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        </div>
        <div class="stat">
            <div><small>Total Keseluruhan</small><b><?php echo formatPrice($grand); ?></b></div>
            <div class="stat-ic g"><svg class="ic" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
        </div>
    </div>

    <div class="card">
        <div class="card-b">
            <div class="twrap"><table><thead><tr><th>ID</th><th>Item</th><th>Supplier</th><th>Kategori</th><th>Tanggal</th><th>Biaya</th><th>Aksi</th></tr></thead><tbody>
            <?php if (empty($reports)): ?>
            <tr><td colspan="7" class="center">Belum ada laporan buying</td></tr>
            <?php else: foreach ($reports as $r): ?>
            <tr>
                <td>#<?php echo $r['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($r['item_name']); ?></strong><?php if ($r['notes']): ?><br><small><?php echo htmlspecialchars(substr($r['notes'], 0, 60)); ?></small><?php endif; ?></td>
                <td><?php echo htmlspecialchars($r['supplier'] ?: '-'); ?></td>
                <td><?php echo htmlspecialchars($r['category']); ?></td>
                <td><?php echo formatDate($r['purchase_date']); ?></td>
                <td><?php echo formatPrice($r['cost']); ?></td>
                <td>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus laporan ini?')">
                        <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" name="delete_report" class="btn btn-s btn-d" aria-label="Hapus laporan">
                            <svg class="ic" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<dialog class="modal" id="addBuyingModal">
    <form method="POST">
        <div class="modal-h">
            <h3>Tambah Pembelian</h3>
            <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b">
            <div class="field"><label class="label">Nama Item</label><input type="text" name="item_name" class="input" required></div>
            <div class="field"><label class="label">Supplier</label><input type="text" name="supplier" class="input"></div>
            <div class="field"><label class="label">Biaya (Rp)</label><input type="number" name="cost" class="input" required min="0" step="0.01"></div>
            <div class="field"><label class="label">Kategori</label><input type="text" name="category" class="input" value="General"></div>
            <div class="field"><label class="label">Tanggal Beli</label><input type="date" name="purchase_date" class="input" value="<?php echo date('Y-m-d'); ?>" required></div>
            <div class="field"><label class="label">Catatan</label><textarea name="notes" class="input" rows="3"></textarea></div>
        </div>
        <div class="modal-f">
            <button type="button" class="btn" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_report" class="btn btn-p">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
