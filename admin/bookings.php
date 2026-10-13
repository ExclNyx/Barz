<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Kelola Booking - Admin';
$db = getDB();
$validStatuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
$editableStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];

// Update status, lalu pertahankan filter yang sedang digunakan admin.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $newStatus = $_POST['status'] ?? '';

    if ($bookingId > 0 && in_array($newStatus, $editableStatuses, true)) {
        $stmt = $db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $newStatus, $bookingId);
        if ($stmt->execute() && $stmt->affected_rows >= 0) {
            $_SESSION['success'] = 'Status booking #' . $bookingId . ' berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Status booking gagal diperbarui.';
        }
    } else {
        $_SESSION['error'] = 'Data status booking tidak valid.';
    }

    $returnFilter = $_POST['filter'] ?? 'all';
    if (!in_array($returnFilter, $validStatuses, true)) $returnFilter = 'all';
    $returnDate = $_POST['date'] ?? '';
    if ($returnDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $returnDate)) $returnDate = '';
    $query = ['filter' => $returnFilter];
    if ($returnDate !== '') $query['date'] = $returnDate;
    header('Location: bookings.php?' . http_build_query($query));
    exit;
}

// Default tanpa filter tanggal agar booking masa depan maupun riwayat tetap terlihat.
$filter = $_GET['filter'] ?? 'all';
if (!in_array($filter, $validStatuses, true)) $filter = 'all';
$dateFilter = trim($_GET['date'] ?? '');
if ($dateFilter !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFilter)) $dateFilter = '';

// Ringkasan selalu menghitung semua booking, tidak terpengaruh filter tabel.
$summary = $db->query("SELECT COUNT(*) AS total,
    COALESCE(SUM(status = 'pending'), 0) AS pending,
    COALESCE(SUM(status = 'confirmed'), 0) AS confirmed,
    COALESCE(SUM(status = 'completed'), 0) AS completed,
    COALESCE(SUM(status = 'cancelled'), 0) AS cancelled,
    COALESCE(SUM(status = 'no_show'), 0) AS no_show
    FROM bookings")->fetch_assoc();

$params = [];
$types = '';
$where = 'WHERE 1 = 1';
if ($dateFilter !== '') {
    $where .= ' AND b.booking_date = ?';
    $params[] = $dateFilter;
    $types .= 's';
}
if ($filter !== 'all') {
    $where .= ' AND b.status = ?';
    $params[] = $filter;
    $types .= 's';
}

$sql = "SELECT b.*, s.name AS service_name, u.name AS customer_name, u.phone AS customer_phone,
        u.email AS customer_email, br.name AS barber_name
        FROM bookings b
        INNER JOIN services s ON b.service_id = s.id
        INNER JOIN users u ON b.customer_id = u.id
        LEFT JOIN barbers br ON b.barber_id = br.id
        $where
        ORDER BY b.booking_date DESC, b.start_time DESC, b.id DESC";

if ($params) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $bookings = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$quickStatuses = [
    'pending' => ['label' => 'Menunggu', 'classes' => 'border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100'],
    'confirmed' => ['label' => 'Konfirmasi', 'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'],
    'completed' => ['label' => 'Selesai', 'classes' => 'border-sky-200 bg-sky-50 text-sky-800 hover:bg-sky-100'],
    'cancelled' => ['label' => 'Batalkan', 'classes' => 'border-rose-200 bg-rose-50 text-rose-800 hover:bg-rose-100'],
    'no_show' => ['label' => 'Tidak hadir', 'classes' => 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200'],
];

ob_start();
?>
<main class="mx-auto w-full max-w-[1700px] px-4 py-8 sm:px-6 lg:px-8 max-[768px]:pt-[76px]">
    <header class="mb-7 flex flex-col gap-5 border-b border-barz-line pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-barz-accent">Administrasi Barz</p>
            <h1 class="text-3xl font-extrabold tracking-tight text-barz-ink sm:text-4xl">Kelola Booking</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">Pantau jadwal pelanggan, konfirmasi kedatangan, dan perbarui status layanan dalam satu tempat.</p>
        </div>
        <div class="rounded-xl border border-barz-line bg-white px-4 py-3 shadow-sm">
            <p class="text-xs font-medium text-stone-500">Booking sesuai filter</p>
            <p class="mt-1 text-2xl font-extrabold text-barz-ink"><?php echo count($bookings); ?><span class="ml-1 text-sm font-medium text-stone-500">jadwal</span></p>
        </div>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900" role="status"><svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg><p><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900" role="alert"><svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01" stroke-linecap="round"/></svg><p><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p></div>
    <?php endif; ?>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan seluruh booking">
        <article class="rounded-2xl border border-barz-line bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-medium text-stone-500">Semua booking</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-barz-ink"><?php echo (int)$summary['total']; ?></p><p class="mt-1 text-xs text-stone-500">Seluruh tanggal</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#889063]/15 text-barz-accent"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round"/></svg></span></div></article>
        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-medium text-stone-500">Menunggu konfirmasi</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-amber-800"><?php echo (int)$summary['pending']; ?></p><p class="mt-1 text-xs text-stone-500">Perlu ditinjau admin</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg></span></div></article>
        <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-medium text-stone-500">Terkonfirmasi</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-emerald-800"><?php echo (int)$summary['confirmed']; ?></p><p class="mt-1 text-xs text-stone-500">Jadwal telah disetujui</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div></article>
        <article class="rounded-2xl border border-sky-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-medium text-stone-500">Selesai</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-sky-800"><?php echo (int)$summary['completed']; ?></p><p class="mt-1 text-xs text-stone-500">Layanan sudah diberikan</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5 9.5 17 19 7" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div></article>
    </section>

    <section class="mb-6 rounded-2xl border border-barz-line bg-white shadow-sm">
        <div class="border-b border-barz-line px-5 py-4 sm:px-6"><h2 class="font-bold text-barz-ink">Filter booking</h2><p class="mt-1 text-sm text-stone-500">Tanggal dikosongkan secara default supaya booking mendatang tidak tersembunyi.</p></div>
        <form method="GET" class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-[minmax(200px,1fr)_minmax(200px,1fr)_auto] lg:items-end sm:px-6">
            <div><label for="filterStatus" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-stone-500">Status</label><select name="filter" id="filterStatus" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm text-barz-ink outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15"><option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua status</option><option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Menunggu</option><option value="confirmed" <?php echo $filter === 'confirmed' ? 'selected' : ''; ?>>Terkonfirmasi</option><option value="completed" <?php echo $filter === 'completed' ? 'selected' : ''; ?>>Selesai</option><option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option><option value="no_show" <?php echo $filter === 'no_show' ? 'selected' : ''; ?>>Tidak hadir</option></select></div>
            <div><label for="filterDate" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-stone-500">Tanggal (opsional)</label><input type="date" name="date" id="filterDate" value="<?php echo htmlspecialchars($dateFilter); ?>" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm text-barz-ink outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15"><p class="mt-1.5 text-xs text-stone-400">Kosongkan untuk semua tanggal.</p></div>
            <div class="flex flex-col gap-2 sm:flex-row lg:justify-end"><button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-barz-accent px-5 py-3 text-sm font-bold text-white transition hover:bg-barz-accent-hover focus:outline-none focus:ring-4 focus:ring-[#889063]/20"><svg class="h-4 w-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16M7 12h10m-7 7h4" stroke-linecap="round"/></svg>Terapkan</button><a href="bookings.php" class="inline-flex items-center justify-center rounded-xl border border-stone-200 bg-white px-4 py-3 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">Reset</a></div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-barz-line bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-barz-line bg-white p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div><h2 class="text-lg font-bold text-barz-ink">Daftar booking</h2><p class="mt-1 text-sm text-stone-500">Pilih detail untuk melihat informasi atau perbarui status melalui tombol aksi.</p></div>
            <label class="relative block w-full sm:max-w-sm"><span class="sr-only">Cari booking</span><svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 fill-none stroke-stone-400 stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5" stroke-linecap="round"/></svg><input type="search" data-booking-search class="w-full rounded-xl border border-stone-200 bg-barz-bg py-2.5 pl-10 pr-4 text-sm text-barz-ink outline-none transition placeholder:text-stone-400 focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Cari customer, layanan, ID..."></label>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] border-collapse text-left">
                <thead class="bg-barz-bg"><tr>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Booking</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Customer</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Layanan</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Barber</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Jadwal</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Harga</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Status</th>
                    <th scope="col" class="border-b border-barz-line px-4 py-4 text-right text-xs font-bold uppercase tracking-wider text-stone-500">Aksi</th>
                </tr></thead>
                <tbody id="bookingsTableBody" class="divide-y divide-barz-line">
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="8" class="px-6 py-16 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-barz-bg text-barz-accent"><svg class="h-7 w-7 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke-linecap="round"/></svg></div>
                            <p class="font-semibold text-barz-ink">Belum ada booking pada filter ini</p><p class="mt-1 text-sm text-stone-500">Coba reset filter tanggal atau status untuk menampilkan semua jadwal.</p>
                            <a href="bookings.php" class="mt-4 inline-flex items-center justify-center rounded-xl bg-barz-accent px-4 py-2.5 text-sm font-bold text-white transition hover:bg-barz-accent-hover">Lihat semua booking</a>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <?php $searchText = strtolower('#' . $booking['id'] . ' ' . $booking['customer_name'] . ' ' . ($booking['customer_email'] ?? '') . ' ' . ($booking['customer_phone'] ?? '') . ' ' . $booking['service_name'] . ' ' . ($booking['barber_name'] ?? '')); ?>
                            <tr data-booking-row data-search="<?php echo htmlspecialchars($searchText, ENT_QUOTES); ?>" class="transition-colors odd:bg-white even:bg-[#fcfbf8] hover:bg-[#f5f4ec]">
                                <td class="whitespace-nowrap px-4 py-4 align-top"><p class="font-bold text-barz-ink">#<?php echo (int)$booking['id']; ?></p><p class="mt-1 text-xs text-stone-500">Dibuat <?php echo date('d M Y', strtotime($booking['created_at'])); ?></p></td>
                                <td class="min-w-52 px-4 py-4 align-top"><p class="font-bold text-barz-ink"><?php echo htmlspecialchars($booking['customer_name']); ?></p><p class="mt-1 text-xs text-stone-500"><?php echo htmlspecialchars($booking['customer_phone'] ?: 'No. telepon belum diisi'); ?></p><p class="mt-1 text-xs text-stone-500"><?php echo htmlspecialchars($booking['customer_email']); ?></p></td>
                                <td class="max-w-56 px-4 py-4 align-top"><p class="font-semibold text-barz-ink"><?php echo htmlspecialchars($booking['service_name']); ?></p><?php if (!empty($booking['notes'])): ?><p class="mt-1 max-w-52 truncate text-xs text-stone-500" title="<?php echo htmlspecialchars($booking['notes'], ENT_QUOTES); ?>">Catatan: <?php echo htmlspecialchars($booking['notes']); ?></p><?php endif; ?></td>
                                <td class="px-4 py-4 align-top text-sm text-stone-600"><?php echo !empty($booking['barber_name']) ? htmlspecialchars($booking['barber_name']) : 'Belum ditetapkan'; ?></td>
                                <td class="whitespace-nowrap px-4 py-4 align-top"><p class="font-semibold text-barz-ink"><?php echo formatDate($booking['booking_date']); ?></p><p class="mt-1 text-xs text-stone-500"><?php echo substr($booking['start_time'], 0, 5); ?>–<?php echo substr($booking['end_time'], 0, 5); ?></p></td>
                                <td class="whitespace-nowrap px-4 py-4 align-top font-bold text-barz-ink"><?php echo formatPrice($booking['total_price']); ?></td>
                                <td class="px-4 py-4 align-top"><?php echo getStatusBadge($booking['status']); ?></td>
                                <td class="min-w-44 px-4 py-4 align-top"><div class="flex justify-end gap-2">
                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 transition hover:border-barz-accent hover:bg-[#889063]/10 hover:text-barz-ink focus:outline-none focus:ring-4 focus:ring-[#889063]/15" onclick="document.getElementById('detailModal<?php echo (int)$booking['id']; ?>').showModal()" aria-label="Detail booking #<?php echo (int)$booking['id']; ?>" title="Lihat detail"><svg class="h-4.5 w-4.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button>
                                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 transition hover:border-barz-accent hover:bg-[#889063]/10 hover:text-barz-ink focus:outline-none focus:ring-4 focus:ring-[#889063]/15" onclick="document.getElementById('statusModal<?php echo (int)$booking['id']; ?>').showModal()" aria-label="Ubah status booking #<?php echo (int)$booking['id']; ?>" title="Ubah status"><svg class="h-4.5 w-4.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></button>
                                </div>
                                <div class="mt-2 flex flex-wrap justify-end gap-1.5">
                                    <?php foreach ($quickStatuses as $statusKey => $statusMeta): $isCurrent = $booking['status'] === $statusKey; ?>
                                        <form method="POST" onsubmit="return confirm('Ubah status booking #<?php echo (int)$booking['id']; ?> menjadi <?php echo htmlspecialchars($statusMeta['label'], ENT_QUOTES); ?>?');">
                                            <input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>"><input type="hidden" name="status" value="<?php echo $statusKey; ?>"><input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>"><input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>">
                                            <button type="submit" name="update_status" class="rounded-lg border px-2 py-1 text-[0.68rem] font-bold transition focus:outline-none focus:ring-2 focus:ring-[#889063]/20 disabled:cursor-default disabled:opacity-50 <?php echo $statusMeta['classes']; ?>" <?php echo $isCurrent ? 'disabled' : ''; ?>><?php echo $statusMeta['label']; ?></button>
                                        </form>
                                    <?php endforeach; ?>
                                </div></td>
                            </tr>

                            <dialog id="detailModal<?php echo (int)$booking['id']; ?>" class="m-auto w-[calc(100%-2rem)] max-w-2xl rounded-2xl border border-barz-line bg-white p-0 text-barz-ink shadow-2xl backdrop:bg-[rgba(20,30,15,.58)] backdrop:backdrop-blur-sm">
                                <div class="flex items-start justify-between gap-4 border-b border-barz-line px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-barz-accent">Detail booking</p><h2 class="mt-1 text-xl font-extrabold">Booking #<?php echo (int)$booking['id']; ?></h2></div><button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xl text-stone-500 transition hover:bg-stone-100" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button></div>
                                <div class="grid grid-cols-1 gap-x-6 gap-y-4 px-6 py-5 sm:grid-cols-2">
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Customer</p><p class="mt-1 font-semibold"><?php echo htmlspecialchars($booking['customer_name']); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Kontak</p><p class="mt-1 font-semibold"><?php echo htmlspecialchars($booking['customer_phone'] ?: 'Belum ada telepon'); ?></p><p class="mt-1 text-sm text-stone-500"><?php echo htmlspecialchars($booking['customer_email']); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Layanan</p><p class="mt-1 font-semibold"><?php echo htmlspecialchars($booking['service_name']); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Barber (ditetapkan sistem)</p><p class="mt-1 font-semibold"><?php echo !empty($booking['barber_name']) ? htmlspecialchars($booking['barber_name']) : 'Belum ditetapkan'; ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Tanggal</p><p class="mt-1 font-semibold"><?php echo formatDate($booking['booking_date']); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Waktu</p><p class="mt-1 font-semibold"><?php echo substr($booking['start_time'], 0, 5); ?>–<?php echo substr($booking['end_time'], 0, 5); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Harga</p><p class="mt-1 text-lg font-extrabold text-barz-accent"><?php echo formatPrice($booking['total_price']); ?></p></div>
                                    <div><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status</p><div class="mt-1"><?php echo getStatusBadge($booking['status']); ?></div></div>
                                    <?php if (!empty($booking['notes'])): ?><div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Catatan pelanggan</p><p class="mt-1 whitespace-pre-line text-sm leading-6 text-stone-700"><?php echo htmlspecialchars($booking['notes']); ?></p></div><?php endif; ?>
                                </div>
                                <div class="flex justify-end border-t border-barz-line bg-barz-bg px-6 py-4"><button type="button" class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold transition hover:bg-stone-50" onclick="this.closest('dialog').close()">Tutup</button></div>
                            </dialog>

                            <dialog id="statusModal<?php echo (int)$booking['id']; ?>" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-barz-line bg-white p-0 text-barz-ink shadow-2xl backdrop:bg-[rgba(20,30,15,.58)] backdrop:backdrop-blur-sm">
                                <form method="POST">
                                    <div class="flex items-start justify-between gap-4 border-b border-barz-line px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-barz-accent">Manajemen booking</p><h2 class="mt-1 text-xl font-extrabold">Ubah status #<?php echo (int)$booking['id']; ?></h2></div><button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xl text-stone-500 transition hover:bg-stone-100" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button></div>
                                    <div class="space-y-4 px-6 py-5"><input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>"><input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>"><input type="hidden" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>"><div class="rounded-xl bg-barz-bg p-4"><p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Status saat ini</p><div class="mt-2"><?php echo getStatusBadge($booking['status']); ?></div></div><div><label for="status-<?php echo (int)$booking['id']; ?>" class="mb-1.5 block text-sm font-semibold">Status baru</label><select id="status-<?php echo (int)$booking['id']; ?>" name="status" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" required><?php foreach ($editableStatuses as $status): ?><option value="<?php echo $status; ?>" <?php echo $booking['status'] === $status ? 'selected' : ''; ?>><?php echo ['pending'=>'Menunggu','confirmed'=>'Terkonfirmasi','completed'=>'Selesai','cancelled'=>'Dibatalkan','no_show'=>'Tidak hadir'][$status]; ?></option><?php endforeach; ?></select></div></div>
                                    <div class="flex flex-col-reverse gap-2 border-t border-barz-line bg-barz-bg px-6 py-4 sm:flex-row sm:justify-end"><button type="button" class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50" onclick="this.closest('dialog').close()">Batal</button><button type="submit" name="update_status" class="rounded-xl bg-barz-accent px-5 py-2.5 text-sm font-bold text-white transition hover:bg-barz-accent-hover focus:outline-none focus:ring-4 focus:ring-[#889063]/20">Simpan status</button></div>
                                </form>
                            </dialog>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr data-booking-search-empty class="hidden"><td colspan="8" class="px-6 py-12 text-center text-sm text-stone-500">Tidak ada booking yang cocok dengan kata kunci.</td></tr>
                </tbody>
            </table>
        </div>
        <footer class="flex flex-col gap-1 border-t border-barz-line bg-barz-bg px-5 py-3 text-xs text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6"><span>Menampilkan <?php echo count($bookings); ?> booking berdasarkan filter di atas.</span><span>Ringkasan status menghitung semua tanggal, bukan hanya hasil filter.</span></footer>
    </section>
</main>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
