<?php
/**
 * Local CLI utility to create an admin without exposing a password-reset URL.
 * Usage: php create_admin.php admin@barz.com 'strong-password' 'Admin Barz' 081234567890 [--reset]
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Script setup admin hanya boleh dijalankan melalui terminal.');
}

require_once __DIR__ . '/includes/init.php';

$email = strtolower(trim((string) ($argv[1] ?? '')));
$plainPassword = (string) ($argv[2] ?? '');
$name = trim((string) ($argv[3] ?? 'Admin Barz'));
$phone = trim((string) ($argv[4] ?? ''));
$resetExisting = ($argv[5] ?? '') === '--reset';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($plainPassword) < 8 || $name === '') {
    fwrite(STDERR, "Penggunaan: php create_admin.php email password-min-8-karakter [nama] [telepon] [--reset]" . PHP_EOL);
    exit(1);
}

$db = getDB();
$check = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$check->bind_param('s', $email);
$check->execute();
$existingUser = $check->get_result()->fetch_assoc();
$check->close();

$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
$role = 'admin';

if ($existingUser) {
    if (!$resetExisting) {
        fwrite(STDERR, "Email sudah terdaftar. Tambahkan --reset jika memang ingin mengganti password dan memastikan role admin." . PHP_EOL);
        exit(2);
    }

    $statement = $db->prepare('UPDATE users SET name = ?, phone = ?, password = ?, role = ? WHERE id = ?');
    $userId = (int) $existingUser['id'];
    $statement->bind_param('ssssi', $name, $phone, $hashedPassword, $role, $userId);
    $successMessage = 'Password dan role akun admin berhasil diperbarui untuk ' . $email;
} else {
    $statement = $db->prepare('INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)');
    $statement->bind_param('sssss', $name, $email, $phone, $hashedPassword, $role);
    $successMessage = 'Akun admin berhasil dibuat untuk ' . $email;
}

if (!$statement->execute()) {
    fwrite(STDERR, 'Operasi akun admin gagal: ' . $statement->error . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, $successMessage . PHP_EOL);
$statement->close();
