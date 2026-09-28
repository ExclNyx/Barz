<?php
require_once __DIR__ . '/../includes/init.php';
requireSuperAdmin();

$pageTitle = 'Kelola Admin - Super Admin';
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->bind_param("ssss", $name, $email, $phone, $hash);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Admin berhasil ditambahkan';
        } else {
            $_SESSION['error'] = 'Gagal menambah admin (email mungkin sudah dipakai)';
        }
        header('Location: /Barz/superadmin/admins.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin'])) {
    $id = (int)$_POST['user_id'];
    if ($id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = 'Tidak bisa menghapus akun sendiri';
    } else {
        $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && $row['role'] === 'super_admin') {
            $_SESSION['error'] = 'Tidak bisa menghapus super_admin';
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['success'] = 'Admin dihapus';
        }
    }
    header('Location: /Barz/superadmin/admins.php');
    exit;
}

$admins = $db->query("SELECT id, name, email, phone, role, created_at FROM users WHERE role IN ('admin','super_admin') ORDER BY id")->fetch_all(MYSQLI_ASSOC);

ob_start();
?>
<div class="page">
    <div class="page-top">
        <h1>Kelola Admin</h1>
        <button class="btn btn-p" onclick="document.getElementById('addAdminModal').showModal()">
            <svg class="ic" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Admin
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="notice notice-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-b">
            <div class="twrap"><table><thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Role</th><th>Aksi</th></tr></thead><tbody>
            <?php foreach ($admins as $a): ?>
            <tr>
                <td>#<?php echo $a['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($a['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($a['email']); ?></td>
                <td><?php echo htmlspecialchars($a['phone'] ?? '-'); ?></td>
                <td><?php echo $a['role'] === 'super_admin' ? '<span class="tag tag-confirmed">Super Admin</span>' : '<span class="tag tag-pending">Admin</span>'; ?></td>
                <td>
                    <?php if ($a['id'] != $_SESSION['user_id'] && $a['role'] !== 'super_admin'): ?>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Yakin hapus admin ini?')">
                        <input type="hidden" name="user_id" value="<?php echo $a['id']; ?>">
                        <button type="submit" name="delete_admin" class="btn btn-s btn-d" aria-label="Hapus admin">
                            <svg class="ic" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                    <?php else: ?>-<?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
    </div>
</div>

<dialog class="modal" id="addAdminModal">
    <form method="POST">
        <div class="modal-h">
            <h3>Tambah Admin</h3>
            <button type="button" class="modal-x" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b">
            <div class="field"><label class="label">Nama</label><input type="text" name="name" class="input" required></div>
            <div class="field"><label class="label">Email</label><input type="email" name="email" class="input" required></div>
            <div class="field"><label class="label">Telepon</label><input type="text" name="phone" class="input"></div>
            <div class="field"><label class="label">Password</label><input type="password" name="password" class="input" required minlength="6"></div>
        </div>
        <div class="modal-f">
            <button type="button" class="btn" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_admin" class="btn btn-p">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
