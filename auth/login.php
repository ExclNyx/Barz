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

<div class="wrap auth-wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] flex justify-center py-14 px-0">
    <div class="auth-card w-full max-w-110 bg-white border border-barz-line rounded-[20px] shadow-[0_4px_24px_rgba(0,0,0,.06)] py-10 px-9">

        <div class="center text-center [margin-bottom:28px]">
            <h2 class="font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:6px] font-extrabold">Login</h2>
            <p class="hint text-[.9rem] text-barz-muted">Masuk untuk melihat jadwal atau booking baru.</p>
        </div>

        <?php if (isset($_GET['registered']) && $_GET['registered'] === '1'): ?>
            <div class="mb-4 rounded-xl border border-barz-success bg-[#f0f9f0] px-4 py-3 text-sm text-[#2d6b2d]" role="status">Registrasi berhasil melalui API. Silakan login dengan akun barumu.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="notice notice-error rounded-xl py-3.5 px-4 text-[.9rem] [margin-bottom:20px] flex gap-2 items-start bg-[rgba(217,127,110,.1)] text-[#a84f3a] border-l-[4px] border-barz-danger" role="alert">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5 [margin-top:2px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Email</label>
                <input type="email" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="email" required autofocus>
            </div>

            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Password</label>
                <input type="password" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="password" required>
            </div>

            <button type="submit" class="button button-primary button-block font-sans inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover w-full disabled:opacity-50 disabled:cursor-not-allowed border-barz-accent hover:border-barz-accent-hover">
                Masuk
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>

            <div class="center text-center [margin-bottom:28px] mt-5" >
                            <p class="hint text-[.9rem] text-barz-muted">
                                Belum punya akun? <a href="/Barz/index.php?area=auth&page=register" class="tlink text-barz-accent font-semibold no-underline hover:underline">Daftar di sini</a>
                            </p>
                        </div>
        </form>

        <hr class="divider border border-t border-barz-line my-6 mx-0">

                <div class="demo-box bg-[rgba(136,144,99,.1)] border border-barz-line rounded-xl p-3.5 text-[.85rem]">
                    <strong>Demo Account:</strong>
                    <ul class="mt-1.5 mr-0 mb-0 ml-4.5 text-[.8rem] text-barz-muted">
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
