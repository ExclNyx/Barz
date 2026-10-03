<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$pageTitle = 'Barbers - Admin';
$db = getDB();

// Handle barber CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_barber'])) {
    $name = sanitize($_POST['name'] ?? '');
    $specialization = sanitize($_POST['specialization'] ?? '');
    $experience = (int)($_POST['experience'] ?? 0);
    $active = (int)($_POST['is_active'] ?? 1);

    if ($name) {
        $stmt = $db->prepare("INSERT INTO barbers (name, specialization, experience_years, is_active) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $name, $specialization, $experience, $active);
        $stmt->execute();
        $_SESSION['success'] = 'Barber berhasil ditambahkan';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_barber'])) {
    $id = (int)$_POST['barber_id'];
    $stmt = $db->prepare("UPDATE barbers SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = 'Status barber diperbarui';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$barbers = $db->query("SELECT * FROM barbers ORDER BY name")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>

<div class="page">
    <div class="page-top">
        <h1>Kelola Barber</h1>
        <button class="btn btn-p" onclick="document.getElementById('addBarberModal').showModal()">
            <svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Barber
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-b">
            <div class="twrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Spesialisasi</th>
                            <th>Pengalaman</th>
                            <th>Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barbers as $barber): ?>
                        <tr>
                            <td>#<?php echo $barber['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($barber['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($barber['specialization']); ?></td>
                            <td><?php echo $barber['experience_years']; ?> tahun</td>
                            <td>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="barber_id" value="<?php echo $barber['id']; ?>">
                                    <button type="submit" name="toggle_barber" class="btn btn-s <?php echo $barber['is_active'] ? 'btn-g' : 'btn-o'; ?>">
                                        <?php echo $barber['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" class="inline" onsubmit="return confirm('Yakin hapus barber ini?')">
                                    <input type="hidden" name="barber_id" value="<?php echo $barber['id']; ?>">
                                    <button type="submit" name="delete_barber" class="btn btn-s btn-d" aria-label="Hapus barber">
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

<!-- Add Barber Modal -->
<dialog class="modal" id="addBarberModal">
    <form method="POST">
        <div class="modal-h">
            <h3>Tambah Barber</h3>
            <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b">
            <div class="field">
                <label class="label">Nama</label>
                <input type="text" name="name" class="input" required>
            </div>
            <div class="field">
                <label class="label">Spesialisasi</label>
                <input type="text" name="specialization" class="input">
            </div>
            <div class="field">
                <label class="label">Pengalaman (tahun)</label>
                <input type="number" name="experience" class="input" value="0" min="0">
            </div>
            <div class="field">
                <label class="label">Aktif</label>
                <select name="is_active" class="select">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
        </div>
        <div class="modal-f">
            <button type="button" class="btn" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_barber" class="btn btn-p">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
