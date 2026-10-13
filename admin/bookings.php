<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Kelola Booking - Admin';

$db = getDB();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bookingId = (int)$_POST['booking_id'];
    $newStatus = $_POST['status'];

    $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $bookingId);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Status booking berhasil diupdate';
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query(array_filter($_GET)));
    exit;
}

// Get all bookings with filters
$filter = $_GET['filter'] ?? 'all';
$dateFilter = $_GET['date'] ?? date('Y-m-d'); // default hari ini

$validStatuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
if (!in_array($filter, $validStatuses)) {
    $filter = 'all';
}

if ($dateFilter && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) {
    $dateFilter = date('Y-m-d');
}

$params = [];
$types = '';
$where = 'WHERE 1=1';

if ($dateFilter) {
    $where .= ' AND b.booking_date = ?';
    $params[] = $dateFilter;
    $types .= 's';
}

if ($filter !== 'all') {
    $where .= ' AND b.status = ?';
    $params[] = $filter;
    $types .= 's';
}

$sql = "SELECT b.*, s.name as service_name, u.name as customer_name, u.phone as customer_phone,
        u.email as customer_email, br.name as barber_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u ON b.customer_id = u.id
        LEFT JOIN barbers br ON b.barber_id = br.id
        $where
        ORDER BY b.booking_date DESC, b.start_time DESC";

if ($params) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $bookings = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}

ob_start();
?>

<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Kelola Booking</h1>
        <div class="page-stats flex gap-2 flex-wrap">
            <span class="stat-pill inline-flex items-center gap-1.5 py-1.5 px-3.5 bg-barz-surface border border-barz-line rounded-full text-xs font-medium text-[#6b7a5c] shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)]">Total: <strong class="text-barz-ink font-bold"><?php echo count($bookings); ?></strong></span>
            <span class="stat-pill pending inline-flex items-center gap-1.5 py-1.5 px-3.5 bg-barz-surface border border-barz-line rounded-full text-xs font-medium text-[#6b7a5c] shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] bg-[#fdf3e0] border-[#e8c58a] text-[#8b5a1a]">Pending: <strong class="text-barz-ink font-bold"><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'pending')); ?></strong></span>
            <span class="stat-pill confirmed inline-flex items-center gap-1.5 py-1.5 px-3.5 bg-barz-surface border border-barz-line rounded-full text-xs font-medium text-[#6b7a5c] shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] bg-[#e0f0e0] border-[#a8d8a8] text-[#2d5a2d]">Confirmed: <strong class="text-barz-ink font-bold"><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed')); ?></strong></span>
            <span class="stat-pill completed inline-flex items-center gap-1.5 py-1.5 px-3.5 bg-barz-surface border border-barz-line rounded-full text-xs font-medium text-[#6b7a5c] shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] bg-[#e0e8f0] border-[#a8c8e8] text-[#2d4a6b]">Done: <strong class="text-barz-ink font-bold"><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'completed')); ?></strong></span>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-ok rounded-[10px] py-3.5 px-4.5 text-[0.88rem] [margin-bottom:20px] flex items-center gap-2.5 border border-transparent bg-[#e0f0e0] text-[#2d5a2d] border-[#a8d8a8]">
            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Filters Card -->
    <div class="card filter-card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden [margin-bottom:20px]">
        <form method="GET" class="filter-form flex gap-4 items-end flex-wrap p-5 max-[768px]:flex-col max-[768px]:items-stretch">
            <div class="filter-group flex flex-col gap-1.5 flex-1 min-w-40 max-[768px]:w-full">
                <label class="filter-label text-[0.72rem] font-bold uppercase tracking-wider text-[#6b7a5c]" for="filterStatus">Status</label>
                <select name="filter" id="filterStatus" class="filter-select py-2.5 px-3.5 border border-barz-line rounded-lg text-[0.88rem] font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)]" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="no_show" <?php echo $filter === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                </select>
            </div>
            <div class="filter-group flex flex-col gap-1.5 flex-1 min-w-40 max-[768px]:w-full">
                <label class="filter-label text-[0.72rem] font-bold uppercase tracking-wider text-[#6b7a5c]" for="filterDate">Tanggal</label>
                <input type="date" name="date" id="filterDate" class="filter-input py-2.5 px-3.5 border border-barz-line rounded-lg text-[0.88rem] font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)]" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
            </div>
            <div class="filter-group filter-actions flex flex-col gap-1.5 flex-1 min-w-40 items-end max-[768px]:w-full">
                <a href="bookings.php" class="btn btn-o btn-sm text-barz-accent no-underline py-2 px-4 text-[0.82rem] inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-surface text-barz-ink border-barz-line hover:bg-barz-bg hover:text-barz-ink hover:border-barz-footer">Reset Filter</a>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="card table-card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
        <div class="table-wrapper overflow-x-auto w-full border border-barz-line rounded-[10px] bg-barz-surface">
            <table class="w-full [border-collapse:separate] [border-spacing:0] text-left">
                <thead>
                    <tr>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">ID</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Customer</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Layanan</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Barber</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Tanggal</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Waktu</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Harga</th>
                        <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Status</th>
                        <th class="actions-col bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap min-w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr class="transition-colors hover:bg-barz-bg">
                        <td colspan="9" class="empty-state py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0 text-center text-[#6b7a5c] py-9 px-5">
                            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-12 h-12 [margin-bottom:12px] opacity-50" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <p class="text-sm">Tidak ada booking pada filter ini</p>
                            <a href="bookings.php" class="btn btn-p btn-sm text-barz-accent no-underline py-2 px-4 text-[0.82rem] inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]">Lihat Semua</a>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr data-id="<?php echo $booking['id']; ?>" class="transition-colors hover:bg-barz-bg">
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0">#<?php echo $booking['id']; ?></td>
                            <td class="customer-col py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0 min-w-50">
                                <div class="customer-info flex flex-col gap-0.5">
                                    <strong class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                    <span class="text-[0.78rem] text-[#6b7a5c]"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo $booking['barber_name'] ? htmlspecialchars($booking['barber_name']) : '<span class="muted">Barber Mana Saja</span>'; ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatDate($booking['booking_date']); ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></td>
                            <td class="price-col py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0 font-bold text-barz-accent whitespace-nowrap"><?php echo formatPrice($booking['total_price']); ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo getStatusBadge($booking['status']); ?></td>
                            <td class="actions-col py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0 min-w-40 whitespace-nowrap">
                                <div class="action-group flex gap-1.5 [margin-bottom:6px]">
                                    <button type="button" class="action-btn view-btn inline-flex items-center justify-center w-8 h-8 border border-barz-line bg-barz-surface rounded-lg text-[#6b7a5c] cursor-pointer transition-all hover:bg-barz-accent hover:border-barz-accent hover:text-white hover:[transform:translateY(-1px)]" onclick="document.getElementById('detailModal<?php echo $booking['id']; ?>').showModal()" aria-label="Lihat detail">
                                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" class="action-btn status-btn inline-flex items-center justify-center w-8 h-8 border border-barz-line bg-barz-surface rounded-lg text-[#6b7a5c] cursor-pointer transition-all hover:bg-barz-accent hover:border-barz-accent hover:text-white hover:[transform:translateY(-1px)]" onclick="document.getElementById('statusModal<?php echo $booking['id']; ?>').showModal()" aria-label="Ubah status">
                                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </div>
                                <div class="quick-status flex gap-1 flex-wrap" data-booking="<?php echo $booking['id']; ?>">
                                    <?php
                                    $quickStatuses = [
                                        'pending' => ['label' => 'Pending', 'class' => 'tag-pending'],
                                        'confirmed' => ['label' => 'Confirmed', 'class' => 'tag-confirmed'],
                                        'completed' => ['label' => 'Completed', 'class' => 'tag-completed'],
                                        'cancelled' => ['label' => 'Cancelled', 'class' => 'tag-cancelled'],
                                    ];
                                    foreach ($quickStatuses as $qs => $q): 
                                        $isCurrent = $booking['status'] === $qs;
                                    ?>
                                    <form method="POST" class="quick-status-form m-0" onsubmit="return confirm('Yakin ubah status ke <?php echo $q['label']; ?>?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $qs; ?>">
                                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                                        <input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                        <button type="submit" name="update_status" class="quick-status-btn <?php echo $q['class']; ?> <?php echo $isCurrent ? 'active' : ''; ?> inline-flex items-center justify-center py-1 px-2 rounded-md text-[0.68rem] font-semibold border border-transparent cursor-pointer transition-all font-sans whitespace-nowrap disabled:opacity-45 disabled:cursor-not-allowed [&:not(:disabled):hover]:[transform:translateY(-1px)] [&.active]:[box-shadow:0_0_0_2px_currentColor] [&.active]:font-bold" <?php echo $isCurrent ? 'disabled' : ''; ?>>
                                            <?php echo $q['label']; ?>
                                        </button>
                                    </form>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- Detail Modal -->
                        <dialog class="modal border border-barz-line rounded-2xl p-0 max-w-125 w-[calc(100vw_-_40px)] shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)] bg-barz-surface text-barz-ink m-auto bg-[rgba(45,58,31,.6)] [backdrop-filter:blur(4px)]" id="detailModal<?php echo $booking['id']; ?>">
                            <div class="modal-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3">
                                <h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Detail Booking #<?php echo $booking['id']; ?></h3>
                                <button type="button" class="modal-x bg-transparent border text-xl cursor-pointer text-[#6b7a5c] leading-none p-1 rounded-md transition-all hover:text-barz-ink hover:bg-[#f1e8db]" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
                            </div>
                            <div class="modal-b p-6">
                                <dl class="kv grid grid-cols-[140px_1fr] gap-[10px 14px] text-[0.88rem]">
                                    <dt class="text-[#6b7a5c] font-medium">Customer:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['customer_name']); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Email:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['customer_email']); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Phone:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['customer_phone']); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Layanan:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['service_name']); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Barber:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo $booking['barber_name'] ?? 'Barber Mana Saja'; ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Tanggal:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo formatDate($booking['booking_date']); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Waktu:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Harga:</dt>
                                    <dd class="font-semibold text-barz-ink"><strong><?php echo formatPrice($booking['total_price']); ?></strong></dd>

                                    <dt class="text-[#6b7a5c] font-medium">Status:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo getStatusBadge($booking['status']); ?></dd>

                                    <?php if ($booking['notes']): ?>
                                    <dt class="text-[#6b7a5c] font-medium">Catatan:</dt>
                                    <dd class="font-semibold text-barz-ink"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></dd>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </dialog>

                        <!-- Status Modal -->
                        <dialog class="modal border border-barz-line rounded-2xl p-0 max-w-125 w-[calc(100vw_-_40px)] shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)] bg-barz-surface text-barz-ink m-auto bg-[rgba(45,58,31,.6)] [backdrop-filter:blur(4px)]" id="statusModal<?php echo $booking['id']; ?>">
                            <form method="POST">
                                <div class="modal-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3">
                                    <h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Update Status Booking #<?php echo $booking['id']; ?></h3>
                                    <button type="button" class="modal-x bg-transparent border text-xl cursor-pointer text-[#6b7a5c] leading-none p-1 rounded-md transition-all hover:text-barz-ink hover:bg-[#f1e8db]" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
                                </div>
                                <div class="modal-b p-6">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                    <div class="field [margin-bottom:16px]">
                                        <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Status Saat Ini</label>
                                        <div><?php echo getStatusBadge($booking['status']); ?></div>
                                    </div>
                                    <div class="field [margin-bottom:16px]">
                                        <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Status Baru</label>
                                        <select name="status" class="select w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)]" required>
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="no_show" <?php echo $booking['status'] === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-f py-4 px-6 border-t border-barz-footer flex justify-end gap-2.5 bg-barz-bg">
                                    <button type="button" class="btn inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans" onclick="this.closest('dialog').close()">Batal</button>
                                    <button type="submit" name="update_status" class="btn btn-p inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]">Update Status</button>
                                </div>
                            </form>
                        </dialog>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>