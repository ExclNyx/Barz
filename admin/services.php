<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Layanan - Admin';
$db = getDB();

// Handle service CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = sanitize($_POST['name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $duration = (int)($_POST['duration'] ?? 30);
    
    if ($name && $price > 0) {
        $stmt = $db->prepare("INSERT INTO services (name, description, price, duration, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("ssdi", $name, $desc, $price, $duration);
        $stmt->execute();
        $_SESSION['success'] = 'Layanan berhasil ditambahkan';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_service'])) {
    $id = (int)$_POST['service_id'];
    $stmt = $db->prepare("UPDATE services SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = 'Status layanan diperbarui';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $id = (int)$_POST['service_id'];
    $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = 'Layanan dihapus';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$services = $db->query("SELECT * FROM services ORDER BY name")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="page">
    <div class="page-top">
        <h1>Kelola Layanan</h1>
        <button class="btn btn-p" onclick="document.getElementById('addServiceModal').showModal()">
            <svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Layanan
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Services Table -->
    <div class="card">
        <div class="card-b">
            <div class="twrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                        <tr>
                            <td>#<?php echo $svc['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($svc['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($svc['description'], 0, 60)); ?></td>
                            <td><?php echo formatPrice($svc['price']); ?></td>
                            <td><?php echo $svc['duration']; ?> menit</td>
                            <td>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                                    <button type="submit" name="toggle_service" class="btn btn-s <?php echo $svc['is_active'] ? 'btn-g' : 'btn-o'; ?>">
                                        <?php echo $svc['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus layanan ini?')">
                                    <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                                    <button type="submit" name="delete_service" class="btn btn-s btn-d" aria-label="Hapus layanan">
                                        <svg class="ic" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<dialog class="modal" id="addServiceModal">
    <form method="POST">
        <div class="modal-h">
            <h3>Tambah Layanan</h3>
            <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b">
            <div class="field">
                <label class="label">Nama Layanan</label>
                <input type="text" name="name" class="input" required>
            </div>
            <div class="field">
                <label class="label">Deskripsi</label>
                <textarea name="description" class="area" rows="3"></textarea>
            </div>
            <div class="field">
                <label class="label">Harga (Rp)</label>
                <input type="number" name="price" class="input" required min="0">
            </div>
            <div class="field">
                <label class="label">Durasi (menit)</label>
                <input type="number" name="duration" class="input" value="30" required>
            </div>
        </div>
        <div class="modal-f">
            <button type="button" class="btn" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_service" class="btn btn-p">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
