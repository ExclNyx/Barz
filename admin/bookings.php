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

<div class="page">
    <div class="page-top">
        <h1>Kelola Booking</h1>
        <div class="page-stats">
            <span class="stat-pill">Total: <strong><?php echo count($bookings); ?></strong></span>
            <span class="stat-pill pending">Pending: <strong><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'pending')); ?></strong></span>
            <span class="stat-pill confirmed">Confirmed: <strong><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed')); ?></strong></span>
            <span class="stat-pill completed">Done: <strong><?php echo count(array_filter($bookings, fn($b) => $b['status'] === 'completed')); ?></strong></span>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice notice-ok">
            <svg class="ic" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Filters Card -->
    <div class="card filter-card">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label class="filter-label" for="filterStatus">Status</label>
                <select name="filter" id="filterStatus" class="filter-select" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="no_show" <?php echo $filter === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="filterDate">Tanggal</label>
                <input type="date" name="date" id="filterDate" class="filter-input" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
            </div>
            <div class="filter-group filter-actions">
                <a href="bookings.php" class="btn btn-o btn-sm">Reset Filter</a>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="card table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Layanan</th>
                        <th>Barber</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="actions-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="9" class="empty-state">
                            <svg class="ic" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <p>Tidak ada booking pada filter ini</p>
                            <a href="bookings.php" class="btn btn-p btn-sm">Lihat Semua</a>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr data-id="<?php echo $booking['id']; ?>">
                            <td>#<?php echo $booking['id']; ?></td>
                            <td class="customer-col">
                                <div class="customer-info">
                                    <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                    <span><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                            <td><?php echo $booking['barber_name'] ? htmlspecialchars($booking['barber_name']) : '<span class="muted">Barber Mana Saja</span>'; ?></td>
                            <td><?php echo formatDate($booking['booking_date']); ?></td>
                            <td><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></td>
                            <td class="price-col"><?php echo formatPrice($booking['total_price']); ?></td>
                            <td><?php echo getStatusBadge($booking['status']); ?></td>
                            <td class="actions-col">
                                <div class="action-group">
                                    <button type="button" class="action-btn view-btn" onclick="document.getElementById('detailModal<?php echo $booking['id']; ?>').showModal()" aria-label="Lihat detail">
                                        <svg class="ic" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <button type="button" class="action-btn status-btn" onclick="document.getElementById('statusModal<?php echo $booking['id']; ?>').showModal()" aria-label="Ubah status">
                                        <svg class="ic" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                </div>
                                <div class="quick-status" data-booking="<?php echo $booking['id']; ?>">
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
                                    <form method="POST" class="quick-status-form" onsubmit="return confirm('Yakin ubah status ke <?php echo $q['label']; ?>?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $qs; ?>">
                                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                                        <input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                        <button type="submit" name="update_status" class="quick-status-btn <?php echo $q['class']; ?> <?php echo $isCurrent ? 'active' : ''; ?>" <?php echo $isCurrent ? 'disabled' : ''; ?>>
                                            <?php echo $q['label']; ?>
                                        </button>
                                    </form>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- Detail Modal -->
                        <dialog class="modal" id="detailModal<?php echo $booking['id']; ?>">
                            <div class="modal-h">
                                <h3>Detail Booking #<?php echo $booking['id']; ?></h3>
                                <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
                            </div>
                            <div class="modal-b">
                                <dl class="kv">
                                    <dt>Customer:</dt>
                                    <dd><?php echo htmlspecialchars($booking['customer_name']); ?></dd>

                                    <dt>Email:</dt>
                                    <dd><?php echo htmlspecialchars($booking['customer_email']); ?></dd>

                                    <dt>Phone:</dt>
                                    <dd><?php echo htmlspecialchars($booking['customer_phone']); ?></dd>

                                    <dt>Layanan:</dt>
                                    <dd><?php echo htmlspecialchars($booking['service_name']); ?></dd>

                                    <dt>Barber:</dt>
                                    <dd><?php echo $booking['barber_name'] ?? 'Barber Mana Saja'; ?></dd>

                                    <dt>Tanggal:</dt>
                                    <dd><?php echo formatDate($booking['booking_date']); ?></dd>

                                    <dt>Waktu:</dt>
                                    <dd><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></dd>

                                    <dt>Harga:</dt>
                                    <dd><strong><?php echo formatPrice($booking['total_price']); ?></strong></dd>

                                    <dt>Status:</dt>
                                    <dd><?php echo getStatusBadge($booking['status']); ?></dd>

                                    <?php if ($booking['notes']): ?>
                                    <dt>Catatan:</dt>
                                    <dd><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></dd>
                                    <?php endif; ?>
                                </dl>
                            </div>
                        </dialog>

                        <!-- Status Modal -->
                        <dialog class="modal" id="statusModal<?php echo $booking['id']; ?>">
                            <form method="POST">
                                <div class="modal-h">
                                    <h3>Update Status Booking #<?php echo $booking['id']; ?></h3>
                                    <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
                                </div>
                                <div class="modal-b">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                    <div class="field">
                                        <label class="label">Status Saat Ini</label>
                                        <div><?php echo getStatusBadge($booking['status']); ?></div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Status Baru</label>
                                        <select name="status" class="select" required>
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            <option value="no_show" <?php echo $booking['status'] === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-f">
                                    <button type="button" class="btn" onclick="this.closest('dialog').close()">Batal</button>
                                    <button type="submit" name="update_status" class="btn btn-p">Update Status</button>
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