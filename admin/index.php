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
<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Dashboard Admin</h1>
        <div class="who text-[0.82rem] font-medium text-[#6b7a5c] [margin-top:4px]">Logged in as: <?php echo htmlspecialchars($_SESSION['name']); ?></div>
    </div>
    <div class="stat4 grid grid-cols-[repeat(auto-fit,_minmax(240px,_1fr))] gap-5 [margin-bottom:28px] max-[1200px]:grid-cols-2 max-[768px]:grid-cols-[1fr] max-[768px]:gap-3">
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Booking Hari Ini</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo $stats['today_bookings']; ?></b></div>
            <div class="stat-ic g w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#d4e8d2] text-[#4a7c3f]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Pending Approval</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo $stats['pending_bookings']; ?></b></div>
            <div class="stat-ic t w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#dce8f0] text-[#3a5a7a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Revenue Bulan Ini</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo formatPrice($stats['month_revenue']); ?></b></div>
            <div class="stat-ic o w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#f5e8c8] text-[#7a5c1a]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
        </div>
        <div class="stat bg-barz-surface border border-barz-line rounded-2xl py-5.5 px-5 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] flex justify-between items-center transition hover:[transform:translateY(-2px)] hover:border-barz-footer hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div><small class="block text-[0.74rem] font-semibold uppercase tracking-wider text-[#6b7a5c] [margin-bottom:6px]">Chat Aktif</small><b class="text-[1.65rem] font-extrabold text-barz-ink leading-[1.2] tracking-[-0.02em]"><?php echo $stats['active_chats']; ?></b></div>
            <div class="stat-ic r w-12 h-12 rounded-xl flex items-center justify-center flex-none bg-[#f5d6d1] text-[#8b3a2e]"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5.5 h-5.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        </div>
    </div>
    <div class="act2 grid grid-cols-[repeat(auto-fit,_minmax(240px,_1fr))] gap-4 [margin-bottom:28px] max-[768px]:grid-cols-[1fr]">
        <a href="/Barz/admin/bookings.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Kelola Booking</b><small class="text-xs text-[#6b7a5c]">Konfirmasi, batalkan, lihat detail</small></div>
        </a>
        <a href="/Barz/admin/services.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Kelola Layanan</b><small class="text-xs text-[#6b7a5c]">Edit paket, harga, durasi</small></div>
        </a>
        <a href="/Barz/admin/barbers.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Kelola Barber</b><small class="text-xs text-[#6b7a5c]">Tambah, edit, nonaktifkan</small></div>
        </a>
        <a href="/Barz/admin/chats.php" class="act text-barz-accent no-underline bg-barz-surface border border-barz-line rounded-xl py-4.5 px-5 flex items-center gap-4 shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] transition hover:border-barz-accent hover:bg-barz-bg hover:[transform:translateY(-2px)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)]">
            <div class="act-ic w-11 h-11 rounded-[10px] bg-[#f1e8db] text-barz-ink flex items-center justify-center flex-none transition [.act:hover_&]:bg-barz-accent [.act:hover_&]:text-white"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-5 h-5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
            <div><b class="block text-base font-bold text-barz-ink [margin-bottom:2px]">Chat Customer</b><small class="text-xs text-[#6b7a5c]">Balas pesan pelanggan</small></div>
        </a>
    </div>
    <div class="card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
        <div class="card-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3 flex-wrap"><h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Booking Hari Ini</h3><a href="/Barz/admin/bookings.php" class="btn btn-s btn-o text-barz-accent no-underline inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-surface text-barz-ink border-barz-line hover:bg-barz-bg hover:text-barz-ink hover:border-barz-footer py-1.5 px-3 text-[0.78rem] rounded-lg">Lihat Semua</a></div>
        <div class="card-b p-6">
            <?php if (empty($upcomingBookings)): ?>
                <p class="center text-center text-[#6b7a5c] py-9 px-5">Tidak ada booking hari ini</p>
            <?php else: ?>
            <div class="twrap overflow-x-auto w-full border border-barz-line rounded-[10px] bg-barz-surface"><table class="w-full [border-collapse:separate] [border-spacing:0] text-left"><thead><tr><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">ID</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Waktu</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Customer</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Layanan</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Barber</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Status</th><th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Actions</th></tr></thead><tbody>
            <?php foreach ($upcomingBookings as $booking): ?>
            <tr class="transition-colors hover:bg-barz-bg">
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0">#<?php echo $booking['id']; ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo substr($booking['start_time'], 0, 5); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br><small><?php echo htmlspecialchars($booking['customer_phone']); ?></small></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars($booking['service_name']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo $booking['barber_name'] ?? '-'; ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo getStatusBadge($booking['status']); ?></td>
                <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><a href="/Barz/admin/bookings.php?view=<?php echo $booking['id']; ?>" class="btn btn-s btn-o text-barz-accent no-underline inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-surface text-barz-ink border-barz-line hover:bg-barz-bg hover:text-barz-ink hover:border-barz-footer py-1.5 px-3 text-[0.78rem] rounded-lg" aria-label="Lihat booking"><svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a></td>
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
