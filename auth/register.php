<?php
$pageTitle = 'Register - Barz Barbershop';

if (isLoggedIn()) {
    redirect('home', 'customer');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation
    if ($password !== $confirmPassword) {
        $error = 'Password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        $db = getDB();

        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}

ob_start();
?>

<div class="wrap auth-wrap">
    <div class="auth-card wide">

        <div class="center">
            <h2>Buat Akun</h2>
            <p class="hint">Gratis dan cepat, hanya butuh 1 menit.</p>
        </div>

        <?php if ($error): ?>
            <div class="notice notice-error" role="alert">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="notice notice-ok center">
                <p><?php echo $success; ?></p>
                <p class="mt-3"><a href="/Barz/index.php?page=login" class="button button-primary">Login sekarang</a></p>
            </div>
        <?php else: ?>

        <form method="POST">
            <div class="field">
                <label class="label">Nama Lengkap</label>
                <input type="text" class="input" name="name" required autofocus value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="field">
                <label class="label">Email</label>
                <input type="email" class="input" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="field">
                <label class="label">No. Handphone</label>
                <input type="tel" class="input" name="phone" required placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="field">
                <label class="label">Password</label>
                <input type="password" class="input" name="password" required minlength="6">
                <p class="micro">Minimal 6 karakter</p>
            </div>

            <div class="field">
                <label class="label">Konfirmasi Password</label>
                <input type="password" class="input" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" class="button button-primary button-block">
                Daftar Akun
                <svg class="ic" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>

            <div class="center mt-5">
                <p class="hint">
                                    Sudah punya akun? <a href="/Barz/index.php?area=auth&page=login" class="tlink">Login di sini</a>
                                </p>
            </div>
        </form>

        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../customer/layout.php';
?>
