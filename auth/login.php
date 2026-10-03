<?php
$pageTitle = 'Login - Barz Barbershop';

if (isLoggedIn()) {
    redirect('home', 'customer');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'super_admin') {
                header('Location: /Barz/superadmin/index.php');
                exit;
            }
            redirect($user['role'] === 'admin' ? 'admin' : 'home', $user['role'] === 'admin' ? 'admin' : 'customer');
        } else {
            $error = 'Email atau password salah';
        }
    } else {
        $error = 'Email atau password salah';
    }
}

ob_start();
?>

<div class="wrap auth-wrap">
    <div class="auth-card">

        <div class="center">
            <h2>Login</h2>
            <p class="hint">Masuk untuk melihat jadwal atau booking baru.</p>
        </div>

        <?php if ($error): ?>
            <div class="notice notice-error" role="alert">
                <svg class="ic" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label class="label">Email</label>
                <input type="email" class="input" name="email" required autofocus>
            </div>

            <div class="field">
                <label class="label">Password</label>
                <input type="password" class="input" name="password" required>
            </div>

            <button type="submit" class="button button-primary button-block">
                Masuk
                <svg class="ic" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>

            <div class="center mt-5">
                            <p class="hint">
                                Belum punya akun? <a href="/Barz/index.php?area=auth&page=register" class="tlink">Daftar di sini</a>
                            </p>
                        </div>
        </form>

        <hr class="divider">

                <div class="demo-box">
                    <strong>Demo Account:</strong>
                    <ul>
                        <li>Super Admin: superadmin@barz.com / superadmin</li>
                        <li>Admin: admin@barz.com / barz2024</li>
                        <li>Customer: Daftar baru atau gunakan email sendiri</li>
                    </ul>
                </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../customer/layout.php';
?>
