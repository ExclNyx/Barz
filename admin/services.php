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

<div class="page py-8 px-9 max-w-340 my-0 mx-auto max-[768px]:py-5 max-[768px]:px-4 max-[768px]:[padding-top:68px]">
    <div class="page-top flex justify-between items-start gap-4 flex-wrap [margin-bottom:28px] [padding-bottom:20px] border-b border-barz-line max-[768px]:flex-col max-[768px]:items-stretch">
        <h1 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-[1.65rem] font-extrabold tracking-[-0.02em]">Kelola Layanan</h1>
        <button class="btn btn-p inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]" onclick="document.getElementById('addServiceModal').showModal()">
            <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Layanan
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="notice rounded-[10px] py-3.5 px-4.5 text-[0.88rem] [margin-bottom:20px] flex items-center gap-2.5 border border-transparent">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Services Table -->
    <div class="card bg-barz-surface border border-barz-line rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04),_0_1px_3px_rgba(0,0,0,0.03)] [margin-bottom:24px] overflow-hidden">
        <div class="card-b p-6">
            <div class="twrap overflow-x-auto w-full border border-barz-line rounded-[10px] bg-barz-surface">
                <table class="w-full [border-collapse:separate] [border-spacing:0] text-left">
                    <thead>
                        <tr>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">ID</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Nama</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Deskripsi</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Harga</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Durasi</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Aktif</th>
                            <th class="bg-barz-bg text-[#6b7a5c] text-[0.72rem] font-bold uppercase tracking-[0.06em] py-3.5 px-4.5 border-b border-barz-line whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                        <tr class="transition-colors hover:bg-barz-bg">
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0">#<?php echo $svc['id']; ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><strong><?php echo htmlspecialchars($svc['name']); ?></strong></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo htmlspecialchars(substr($svc['description'], 0, 60)); ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo formatPrice($svc['price']); ?></td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0"><?php echo $svc['duration']; ?> menit</td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0" class="inline">
                                <form method="POST" >
                                    <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                                    <button type="submit" name="toggle_service" class="btn btn-s <?php echo $svc['is_active'] ? 'btn-g' : 'btn-o'; ?> inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans py-1.5 px-3 text-[0.78rem] rounded-lg">
                                        <?php echo $svc['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4.5 text-[0.88rem] text-barz-ink border-b border-barz-footer align-middle border-b-0" class="inline">
                                <form method="POST"  onsubmit="return confirm('Yakin hapus layanan ini?')">
                                    <input type="hidden" name="service_id" value="<?php echo $svc['id']; ?>">
                                    <button type="submit" name="delete_service" class="btn btn-s btn-d inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-[#f5e0e0] text-barz-danger border-[#e8a8a8] hover:bg-barz-danger hover:text-white hover:border-barz-danger py-1.5 px-3 text-[0.78rem] rounded-lg" aria-label="Hapus layanan">
                                        <svg class="ic w-4.5 h-4.5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4 h-4" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
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
<dialog class="modal border border-barz-line rounded-2xl p-0 max-w-125 w-[calc(100vw_-_40px)] shadow-[0_12px_32px_rgba(0,0,0,0.08),_0_4px_12px_rgba(0,0,0,0.05)] bg-barz-surface text-barz-ink m-auto bg-[rgba(45,58,31,.6)] [backdrop-filter:blur(4px)]" id="addServiceModal">
    <form method="POST">
        <div class="modal-h py-4.5 px-6 border-b border-barz-footer flex justify-between items-center gap-3">
            <h3 class="font-sans text-barz-ink font-bold tracking-[-0.015em] text-lg m-0">Tambah Layanan</h3>
            <button type="button" class="modal-x bg-transparent border text-xl cursor-pointer text-[#6b7a5c] leading-none p-1 rounded-md transition-all hover:text-barz-ink hover:bg-[#f1e8db]" onclick="this.closest('dialog').close()" aria-label="Tutup">&times;</button>
        </div>
        <div class="modal-b p-6">
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Nama Layanan</label>
                <input type="text" name="name" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" required>
            </div>
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Deskripsi</label>
                <textarea name="description" class="area w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" rows="3"></textarea>
            </div>
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Harga (Rp)</label>
                <input type="number" name="price" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" required min="0">
            </div>
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[0.82rem] font-semibold text-barz-ink [margin-bottom:6px]">Durasi (menit)</label>
                <input type="number" name="duration" class="input w-full py-2.5 px-3.5 border border-barz-line rounded-lg text-sm font-sans bg-barz-surface text-barz-ink transition-all focus:[outline:none] focus:border-barz-accent focus:shadow-[0_0_0_3px_rgba(136,144,99,0.12)] text-barz-muted-light" value="30" required>
            </div>
        </div>
        <div class="modal-f py-4 px-6 border-t border-barz-footer flex justify-end gap-2.5 bg-barz-bg">
            <button type="button" class="btn inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" name="add_service" class="btn btn-p inline-flex items-center justify-center gap-2 py-2.5 px-4.5 rounded-2xl text-[0.86rem] font-semibold no-underline cursor-pointer border border-transparent transition-all font-sans bg-barz-accent text-white border-barz-accent hover:bg-barz-accent-hover hover:border-barz-accent-hover hover:[transform:translateY(-1px)] hover:shadow-[0_4px_10px_rgba(136,_144,_99,_.3)]">Tambah</button>
        </div>
    </form>
</dialog>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
