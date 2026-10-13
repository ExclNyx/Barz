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

<div class="wrap auth-wrap max-w-280 my-0 mx-auto [padding-left:24px] [padding-right:24px] flex justify-center py-14 px-0">
    <div class="auth-card wide w-full max-w-110 bg-white border border-barz-line rounded-[20px] shadow-[0_4px_24px_rgba(0,0,0,.06)] py-10 px-9 max-w-120">

        <div class="center text-center [margin-bottom:28px]">
            <h2 class="font-sans font-bold leading-tight tracking-tight text-2xl [margin-bottom:6px] font-extrabold">Buat Akun</h2>
            <p class="hint text-[.9rem] text-barz-muted">Gratis dan cepat, hanya butuh 1 menit.</p>
        </div>

        <?php if ($error): ?>
            <div class="notice notice-error rounded-xl py-3.5 px-4 text-[.9rem] [margin-bottom:20px] flex gap-2 items-start bg-[rgba(217,127,110,.1)] text-[#a84f3a] border-l-[4px] border-barz-danger" role="alert">
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round] w-4.5 h-4.5 [margin-top:2px]" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="notice notice-ok center text-center rounded-xl py-3.5 px-4 text-[.9rem] [margin-bottom:20px] flex gap-2 items-start bg-[rgba(111,168,111,.12)] text-[#2d6b2d] border-l-[4px] border-barz-success [margin-bottom:28px]">
                <p class="mt-3"><?php echo $success; ?></p>
                <p ><a href="/Barz/index.php?page=login" class="button button-primary text-barz-accent inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover disabled:opacity-50 disabled:cursor-not-allowed border-barz-accent hover:border-barz-accent-hover">Login sekarang</a></p>
            </div>
        <?php else: ?>

        <form method="POST" id="registerForm">
            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Nama Lengkap</label>
                <input type="text" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="name" required autofocus value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Email</label>
                <input type="email" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">No. Handphone</label>
                <input type="tel" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="phone" required placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Password</label>
                <input type="password" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="password" required minlength="6">
                <p class="micro text-[.75rem] text-barz-muted [margin-top:4px]">Minimal 6 karakter</p>
            </div>

            <div class="field [margin-bottom:16px]">
                <label class="label block text-[.85rem] font-semibold [margin-bottom:6px]">Konfirmasi Password</label>
                <input type="password" class="input w-full py-3 px-3.5 border border-barz-line rounded bg-white text-barz-ink text-[.95rem] font-sans transition-colors focus:[outline:none] focus:border-barz-accent text-barz-muted" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" class="button button-primary button-block font-sans inline-flex items-center justify-center gap-2 py-3.5 px-7 rounded-2xl font-semibold text-[.95rem] no-underline cursor-pointer border border-transparent transition-colors bg-barz-accent text-white hover:bg-barz-accent-hover w-full disabled:opacity-50 disabled:cursor-not-allowed border-barz-accent hover:border-barz-accent-hover">
                Daftar Akun
                <svg class="ic w-5 h-5 flex-none [fill:none] stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>

            <div class="center text-center [margin-bottom:28px] mt-5" >
                <p class="hint text-[.9rem] text-barz-muted">
                                    Sudah punya akun? <a href="/Barz/index.php?area=auth&page=login" class="tlink text-barz-accent font-semibold no-underline hover:underline">Login di sini</a>
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
