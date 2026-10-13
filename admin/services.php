<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Kelola Layanan - Admin';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $duration = (int)($_POST['duration'] ?? 0);

    if ($name === '' || $price <= 0 || $duration <= 0) {
        $_SESSION['error'] = 'Nama, harga, dan durasi layanan harus diisi dengan benar.';
    } else {
        $stmt = $db->prepare("INSERT INTO services (name, description, price, duration, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param('ssdi', $name, $description, $price, $duration);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Layanan berhasil ditambahkan.';
        } else {
            $_SESSION['error'] = 'Layanan gagal ditambahkan. Silakan coba lagi.';
        }
    }
    header('Location: services.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_service'])) {
    $id = (int)($_POST['service_id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare('UPDATE services SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['success'] = 'Status layanan berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Layanan tidak ditemukan atau statusnya tidak berubah.';
        }
    }
    header('Location: services.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $id = (int)($_POST['service_id'] ?? 0);
    if ($id > 0) {
        // Booking lama harus tetap punya referensi layanan untuk kebutuhan riwayat.
        $check = $db->prepare('SELECT COUNT(*) AS total FROM bookings WHERE service_id = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $bookingCount = (int)$check->get_result()->fetch_assoc()['total'];

        if ($bookingCount > 0) {
            $stmt = $db->prepare('UPDATE services SET is_active = 0 WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $_SESSION['success'] = 'Layanan memiliki riwayat booking, jadi dinonaktifkan agar riwayat tetap aman.';
        } else {
            $stmt = $db->prepare('DELETE FROM services WHERE id = ?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Layanan berhasil dihapus.';
            } else {
                $_SESSION['error'] = 'Layanan gagal dihapus. Periksa kembali data terkait.';
            }
        }
    }
    header('Location: services.php');
    exit;
}

$services = $db->query('SELECT * FROM services ORDER BY is_active DESC, name ASC')->fetch_all(MYSQLI_ASSOC);
$totalServices = count($services);
$activeServices = count(array_filter($services, static fn($service) => (int)$service['is_active'] === 1));
$inactiveServices = $totalServices - $activeServices;

ob_start();
?>
<main class="mx-auto w-full max-w-[1600px] px-4 py-8 sm:px-6 lg:px-8 max-[768px]:pt-[76px]">
    <header class="mb-7 flex flex-col gap-5 border-b border-barz-line pb-6 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-barz-accent">Administrasi Barz</p>
            <h1 class="text-3xl font-extrabold tracking-tight text-barz-ink sm:text-4xl">Kelola Layanan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-stone-600">Atur katalog layanan, harga, durasi, dan status layanan yang tersedia untuk pelanggan.</p>
        </div>
        <button type="button" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-barz-accent px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-barz-accent-hover hover:shadow-md focus:outline-none focus:ring-4 focus:ring-[#889063]/20" onclick="document.getElementById('addServiceModal').showModal()">
            <svg class="h-5 w-5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
            Tambah Layanan
        </button>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900" role="status">
            <svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <p><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01" stroke-linecap="round"/></svg>
            <p><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        </div>
    <?php endif; ?>

    <section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3" aria-label="Ringkasan layanan">
        <article class="rounded-2xl border border-barz-line bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-stone-500">Total layanan</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-barz-ink"><?php echo $totalServices; ?></p><p class="mt-1 text-xs text-stone-500">Seluruh katalog terdaftar</p></div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#889063]/15 text-barz-accent"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke-linecap="round"/></svg></span>
            </div>
        </article>
        <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-stone-500">Layanan aktif</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-emerald-800"><?php echo $activeServices; ?></p><p class="mt-1 text-xs text-stone-500">Dapat dipilih pelanggan</p></div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
        </article>
        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-stone-500">Layanan nonaktif</p><p class="mt-2 text-3xl font-extrabold tracking-tight text-amber-800"><?php echo $inactiveServices; ?></p><p class="mt-1 text-xs text-stone-500">Tersimpan untuk riwayat</p></div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><svg class="h-6 w-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v10m0 5h.01M5.2 21h13.6a2 2 0 0 0 1.73-3L13.73 4.4a2 2 0 0 0-3.46 0L3.47 18a2 2 0 0 0 1.73 3Z" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            </div>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-barz-line bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-barz-line bg-white p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-barz-ink">Daftar layanan</h2>
                <p class="mt-1 text-sm text-stone-500">Cari layanan, ubah status, atau hapus layanan yang belum memiliki riwayat booking.</p>
            </div>
            <label class="relative block w-full sm:max-w-sm">
                <span class="sr-only">Cari layanan</span>
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 fill-none stroke-stone-400 stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5" stroke-linecap="round"/></svg>
                <input type="search" id="serviceSearch" data-service-search class="w-full rounded-xl border border-stone-200 bg-barz-bg py-2.5 pl-10 pr-4 text-sm text-barz-ink outline-none transition placeholder:text-stone-400 focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Cari nama atau deskripsi...">
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] border-collapse text-left">
                <thead class="bg-barz-bg">
                    <tr>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Layanan</th>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Deskripsi</th>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Harga</th>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Durasi</th>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-xs font-bold uppercase tracking-wider text-stone-500">Status</th>
                        <th scope="col" class="border-b border-barz-line px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-stone-500">Aksi</th>
                    </tr>
                </thead>
                <tbody id="servicesTableBody" class="divide-y divide-barz-line">
                    <?php if (empty($services)): ?>
                        <tr><td colspan="6" class="px-6 py-14 text-center">
                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-barz-bg text-barz-accent"><svg class="h-7 w-7 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h5" stroke-linecap="round"/></svg></div>
                            <p class="font-semibold text-barz-ink">Belum ada layanan</p><p class="mt-1 text-sm text-stone-500">Tambahkan layanan pertama untuk mulai menerima booking.</p>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <?php $searchText = strtolower($service['name'] . ' ' . ($service['description'] ?? '')); ?>
                            <tr data-service-row data-search="<?php echo htmlspecialchars($searchText, ENT_QUOTES); ?>" class="transition-colors odd:bg-white even:bg-[#fcfbf8] hover:bg-[#f5f4ec]">
                                <td class="px-5 py-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#889063]/12 text-barz-accent"><svg class="h-5 w-5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.12 15.88M14.47 14.48 20 20M8.12 8.12 12 12" stroke-linecap="round"/></svg></span>
                                        <div><p class="font-bold text-barz-ink"><?php echo htmlspecialchars($service['name']); ?></p><p class="mt-0.5 text-xs text-stone-500">ID layanan #<?php echo (int)$service['id']; ?></p></div>
                                    </div>
                                </td>
                                <td class="max-w-xs px-5 py-4 text-sm leading-6 text-stone-600"><?php echo htmlspecialchars($service['description'] ?: 'Belum ada deskripsi'); ?></td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-bold text-barz-ink"><?php echo formatPrice($service['price']); ?></td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-lg bg-stone-100 px-2.5 py-1.5 text-xs font-semibold text-stone-700"><?php echo (int)$service['duration']; ?> menit</span></td>
                                <td class="px-5 py-4">
                                    <?php if ((int)$service['is_active'] === 1): ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-stone-200 bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600"><span class="h-1.5 w-1.5 rounded-full bg-stone-400"></span>Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST">
                                            <input type="hidden" name="service_id" value="<?php echo (int)$service['id']; ?>">
                                            <button type="submit" name="toggle_service" class="inline-flex items-center justify-center rounded-lg border px-3 py-2 text-xs font-bold transition focus:outline-none focus:ring-4 focus:ring-[#889063]/15 <?php echo (int)$service['is_active'] === 1 ? 'border-amber-200 bg-amber-50 text-amber-800 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'; ?>" aria-label="<?php echo (int)$service['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan'; ?> <?php echo htmlspecialchars($service['name'], ENT_QUOTES); ?>">
                                                <?php echo (int)$service['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                            </button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Hapus layanan ini jika belum memiliki riwayat booking? Layanan yang memiliki booking akan dinonaktifkan agar riwayat tidak rusak.');">
                                            <input type="hidden" name="service_id" value="<?php echo (int)$service['id']; ?>">
                                            <button type="submit" name="delete_service" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:border-rose-300 hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-100" aria-label="Hapus atau arsipkan <?php echo htmlspecialchars($service['name'], ENT_QUOTES); ?>" title="Hapus / arsipkan layanan">
                                                <svg class="h-4.5 w-4.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-11 0 1 14h12l1-14M10 10v6m4-6v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr id="serviceSearchEmpty" class="hidden"><td colspan="6" class="px-6 py-10 text-center text-sm text-stone-500">Tidak ada layanan yang cocok dengan kata kunci tersebut.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-1 border-t border-barz-line bg-barz-bg px-5 py-3 text-xs text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <span>Tips: layanan yang sudah dipakai pada booking akan dinonaktifkan, bukan dihapus.</span>
            <span><?php echo $activeServices; ?> aktif dari <?php echo $totalServices; ?> layanan</span>
        </div>
    </section>
</main>

<dialog id="addServiceModal" class="m-auto w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-barz-line bg-white p-0 text-barz-ink shadow-2xl backdrop:bg-[rgba(20,30,15,.58)] backdrop:backdrop-blur-sm">
    <form method="POST">
        <div class="flex items-start justify-between gap-4 border-b border-barz-line px-6 py-5">
            <div><h2 class="text-xl font-extrabold text-barz-ink">Tambah layanan</h2><p class="mt-1 text-sm text-stone-500">Masukkan rincian layanan yang akan tersedia untuk pelanggan.</p></div>
            <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xl text-stone-500 transition hover:bg-stone-100 hover:text-stone-800" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="space-y-4 px-6 py-5">
            <div><label for="serviceName" class="mb-1.5 block text-sm font-semibold">Nama layanan</label><input id="serviceName" type="text" name="name" maxlength="100" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Contoh: Gentleman's Cut" required></div>
            <div><label for="serviceDescription" class="mb-1.5 block text-sm font-semibold">Deskripsi</label><textarea id="serviceDescription" name="description" rows="3" class="w-full resize-y rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="Jelaskan layanan secara singkat..."></textarea></div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div><label for="servicePrice" class="mb-1.5 block text-sm font-semibold">Harga (Rp)</label><input id="servicePrice" type="number" name="price" min="1" step="0.01" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" placeholder="50000" required></div>
                <div><label for="serviceDuration" class="mb-1.5 block text-sm font-semibold">Durasi (menit)</label><input id="serviceDuration" type="number" name="duration" min="1" max="480" value="30" class="w-full rounded-xl border border-stone-200 bg-white px-3.5 py-3 text-sm outline-none transition focus:border-barz-accent focus:ring-4 focus:ring-[#889063]/15" required></div>
            </div>
        </div>
        <div class="flex flex-col-reverse gap-2 border-t border-barz-line bg-barz-bg px-6 py-4 sm:flex-row sm:justify-end">
            <button type="button" class="rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_service" class="rounded-xl bg-barz-accent px-5 py-2.5 text-sm font-bold text-white transition hover:bg-barz-accent-hover focus:outline-none focus:ring-4 focus:ring-[#889063]/20">Simpan layanan</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
