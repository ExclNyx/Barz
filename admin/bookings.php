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
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get all bookings with filters
$filter = $_GET['filter'] ?? 'all';
$dateFilter = $_GET['date'] ?? date('Y-m-d'); // default hari ini

// Validate filter against whitelist to prevent SQL injection
$validStatuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
if (!in_array($filter, $validStatuses)) {
    $filter = 'all';
}

// Validate date format
if ($dateFilter && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) {
    $dateFilter = date('Y-m-d');
}

if ($dateFilter) {
    // For single date filter, prepare single query
    $filterClause = $filter !== 'all'
        ? " AND b.status = ?"
        : "";
    $sql = "SELECT b.*, s.name as service_name, u.name as customer_name, u.phone as customer_phone,
            u.email as customer_email, br.name as barber_name
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN users u ON b.customer_id = u.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            WHERE b.booking_date = ?" . $filterClause . " ORDER BY b.start_time DESC";

    if ($filter !== 'all') {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $dateFilter, $filter);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $dateFilter);
    }
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} elseif ($filter !== 'all') {
    $sql = "SELECT b.*, s.name as service_name, u.name as customer_name, u.phone as customer_phone,
            u.email as customer_email, br.name as barber_name
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN users u ON b.customer_id = u.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            WHERE b.status = ? ORDER BY b.booking_date DESC, b.start_time DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $sql = "SELECT b.*, s.name as service_name, u.name as customer_name, u.phone as customer_phone,
            u.email as customer_email, br.name as barber_name
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN users u ON b.customer_id = u.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            ORDER BY b.booking_date DESC, b.start_time DESC";
    $bookings = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}

ob_start();
?>

<div class="page">
    <div class="page-top">
        <h1>Kelola Booking</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card">
        <div class="card-b">
            <form method="GET" class="f2">
                <div class="field">
                    <label class="label">Status</label>
                    <select name="filter" class="select" onchange="this.form.submit()">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua Status</option>
                        <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="no_show" <?php echo $filter === 'no_show' ? 'selected' : ''; ?>>No Show</option>
                    </select>
                </div>
                <div class="field">
                    <label class="label">Tanggal</label>
                    <input type="date" name="date" class="input" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="this.form.submit()">
                </div>
                <div class="field">
                    <a href="bookings.php" class="btn">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-b">
            <div class="twrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Layanan</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Barber</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" class="center">Tidak ada booking</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($booking['customer_phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                <td><?php echo formatDate($booking['booking_date']); ?></td>
                                <td><?php echo substr($booking['start_time'], 0, 5); ?> - <?php echo substr($booking['end_time'], 0, 5); ?></td>
                                <td><?php echo $booking['barber_name'] ?? '-'; ?></td>
                                <td><?php echo formatPrice($booking['total_price']); ?></td>
                                <td><?php echo getStatusBadge($booking['status']); ?></td>
                                <td>
                                    <div class="btnrow">
                                        <button type="button" class="btn btn-s btn-o" onclick="document.getElementById('detailModal<?php echo $booking['id']; ?>').showModal()" aria-label="Lihat detail">
                                            <svg class="ic" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <button type="button" class="btn btn-s btn-o" onclick="document.getElementById('statusModal<?php echo $booking['id']; ?>').showModal()" aria-label="Ubah status">
                                            <svg class="ic" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                    </div>
                                    <div class="status-quick" style="display:flex;gap:4px;margin-top:8px;flex-wrap:wrap">
                                        <?php
                                        $quickStatuses = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                                        foreach ($quickStatuses as $qs => $qlabel):
                                            $isCurrent = $booking['status'] === $qs;
                                            $btnClass = $isCurrent ? 'btn btn-s btn-p' : 'btn btn-s btn-o';
                                        ?>
                                        <form method="POST" style="display:inline" onsubmit="return confirm('Yakin ubah status ke <?php echo $qlabel; ?>?');">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $qs; ?>">
                                            <button type="submit" name="update_status" class="<?php echo $btnClass; ?>" style="padding:4px 8px;font-size:.7rem" <?php echo $isCurrent ? 'disabled' : ''; ?>><?php echo $qlabel; ?></button>
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
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
