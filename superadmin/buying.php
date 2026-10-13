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
<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Laporan Buying</h1>
        <button class="btn btn-p inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]" onclick="document.getElementById('addBuyingModal').showModal()">
            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Pembelian
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice rounded-[10px] py-3.5 px-4.5 text-[0.88rem] [margin-bottom:20px] flex items-center gap-2.5 border border-transparent"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="stat4 grid grid-cols-[repeat(auto-fit,_minmax(240px,_1fr))] gap-5 [margin-bottom:28px] max-[1200px]:grid-cols-2 max-[768px]:grid-cols-[1fr] max-[768px]:gap-3">
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Buying Bulan Ini</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($month['total']); ?></b></div>
            <div class="stat-ic o w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#f5e8c8] text-[#7a5c1a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Total Laporan</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo $month['cnt']; ?></b></div>
            <div class="stat-ic t w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#dce8f0] text-[#3a5a7a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Total Keseluruhan</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($grand); ?></b></div>
            <div class="stat-ic g w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#d4e8d2] text-[#4a7c3f]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
        </div>
    </div>

    <div class="card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
        <div class="card-b p-6">
            <div class="twrap overflow-x-auto w-full border border-barz-line rounded-[10px] bg-barz-surface"><table class="w-full [border-collapse:separate] [border-spacing:0] text-left"><thead><tr><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">ID</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Item</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Supplier</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Kategori</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Tanggal</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Biaya</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Aksi</th></tr></thead><tbody>
            <?php if (empty($reports)): ?>
            <tr class="transition-colors hover:bg-barz-bg"><td colspan="7" class="center py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0 text-center text-[#6b7a5c] py-9 px-5">Belum ada laporan buying</td></tr>
            <?php else: foreach ($reports as $r): ?>
            <tr class="transition-colors hover:bg-barz-bg">
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0">#<?php echo $r['id']; ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><strong><?php echo htmlspecialchars($r['item_name']); ?></strong><?php if ($r['notes']): ?><br><small><?php echo htmlspecialchars(substr($r['notes'], 0, 60)); ?></small><?php endif; ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars($r['supplier'] ?: '-'); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars($r['category']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatDate($r['purchase_date']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatPrice($r['cost']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0" class="inline">
                    <form method="POST"  onsubmit="return confirm('Yakin hapus laporan ini?')">
                        <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" name="delete_report" class="btn btn-s btn-d inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-[#f5e0e0] text-barz-danger border-[#e8a8a8] hover:bg-barz-danger hover:text-white hover:border-barz-danger py-1.5 px-3 text-[0.78rem] rounded-lg" aria-label="Hapus laporan">
                            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<dialog class="modal border border-barz-line rounded-2xl p-0 max-w-125 w-[calc(100vw_-_40px)] shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)] bg-barz-surface text-barz-ink m-auto bg-[rgba(45,58,31,.6)] [backdrop-filter:blur(4px)]" id="addBuyingModal">
    <form method="POST">
        <div class="modal-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3">
            <h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Tambah Pembelian</h3>
            <button type="button" class="modal-x bg-transparent border text-xl cursor-pointer text-[#6b7a5c] leading-none p-1 rounded-md transition-all hover:text-barz-ink hover:bg-[#f1e8db]" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b p-6">
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Nama Item</label><input type="text" name="item_name" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" required></div>
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Supplier</label><input type="text" name="supplier" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light"></div>
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Biaya (Rp)</label><input type="number" name="cost" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" required min="0" step="0.01"></div>
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Kategori</label><input type="text" name="category" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" value="General"></div>
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Tanggal Beli</label><input type="date" name="purchase_date" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" value="<?php echo date('Y-m-d'); ?>" required></div>
            <div class="field [margin-bottom:16px]"><label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Catatan</label><textarea name="notes" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" rows="3"></textarea></div>
        </div>
        <div class="modal-f py-4 px-6 border-t border-barz-footer flex justify-end gap-2.5 bg-barz-bg">
            <button type="button" class="btn inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_report" class="btn btn-p inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
