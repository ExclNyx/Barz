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
<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Dashboard Eksekutif</h1>
        <div class="who text-[0.82rem] font-medium text-[#6b7a5c] [margin-top:4px]">Logged in as: <?php echo htmlspecialchars($_SESSION['name']); ?></div>
    </div>
    <div class="stat4 grid grid-cols-[repeat(auto-fit,_minmax(240px,_1fr))] gap-5 [margin-bottom:28px] max-[1200px]:grid-cols-2 max-[768px]:grid-cols-[1fr] max-[768px]:gap-3">
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Total Revenue</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($revenue); ?></b></div>
            <div class="stat-ic g w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#d4e8d2] text-[#4a7c3f]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Buying Bulan Ini</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($buyMonth); ?></b></div>
            <div class="stat-ic o w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#f5e8c8] text-[#7a5c1a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Estimasi Profit Kotor</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($revenue - $buyTotal); ?></b></div>
            <div class="stat-ic t w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#dce8f0] text-[#3a5a7a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Jumlah Admin</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo $adminCount; ?></b></div>
            <div class="stat-ic r w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#f5d6d1] text-[#8b3a2e]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        </div>
    </div>
    <div class="act2 grid grid-cols-[repeat(auto-fit,_minmax(240px,_1fr))] gap-4 [margin-bottom:28px] max-[768px]:grid-cols-[1fr]">
        <a href="/Barz/superadmin/admins.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Kelola Admin</b><small class="text-xs text-[#6b7a5c]">Tambah, hapus akun admin</small></div>
        </a>
        <a href="/Barz/superadmin/buying.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Laporan Buying</b><small class="text-xs text-[#6b7a5c]">Catat belanja operasional</small></div>
        </a>
    </div>
    <div class="card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
        <div class="card-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3 flex-wrap"><h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Buying Terbaru</h3><a href="/Barz/superadmin/buying.php" class="btn btn-s btn-o text-barz-accent no-underline inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-surface text-barz-ink border-barz-line hover:bg-barz-bg hover:text-barz-ink hover:border-barz-footer py-1.5 px-3 text-[0.78rem] rounded-lg">Lihat Semua</a></div>
        <div class="card-b p-6">
            <?php if (empty($recent)): ?>
                <p class="center text-center text-[#6b7a5c] py-9 px-5">Belum ada laporan buying</p>
            <?php else: ?>
            <div class="twrap overflow-x-auto w-full border border-barz-line rounded-[10px] bg-barz-surface"><table class="w-full [border-collapse:separate] [border-spacing:0] text-left"><thead><tr><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Item</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Supplier</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Tanggal</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Biaya</th></tr></thead><tbody>
            <?php foreach ($recent as $r): ?>
            <tr class="transition-colors hover:bg-barz-bg">
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><strong><?php echo htmlspecialchars($r['item_name']); ?></strong></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars($r['supplier'] ?: '-'); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatDate($r['purchase_date']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatPrice($r['cost']); ?></td>
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
