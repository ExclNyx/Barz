<?php
// Script untuk buat admin baru
// Jalankan sekali via browser: http://localhost/Barz/create_admin.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barz_booking";

// Credentials admin baru
$admin_name = "Admin Barz";
$admin_email = "admin@barz.com";
$admin_phone = "081234567890";
$admin_password = "barz2024"; // Password plaintext - akan di-hash otomatis

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash password
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

// Cek apakah email sudah ada
$check_sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing admin
    $update_sql = "UPDATE users SET name = ?, phone = ?, password = ?, role = 'admin' WHERE email = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssss", $admin_name, $admin_phone, $hashed_password, $admin_email);
    
    if ($stmt->execute()) {
        echo "✅ Admin berhasil diupdate!<br><br>";
    } else {
        echo "❌ Error update: " . $stmt->error . "<br>";
    }
} else {
    // Insert new admin
    $insert_sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssss", $admin_name, $admin_email, $admin_phone, $hashed_password);
    
    if ($stmt->execute()) {
        echo "✅ Admin baru berhasil dibuat!<br><br>";
    } else {
        echo "❌ Error insert: " . $stmt->error . "<br>";
    }
}

echo "<strong>Login Credentials:</strong><br>";
echo "Email: " . $admin_email . "<br>";
echo "Password: " . $admin_password . "<br><br>";
echo "<em>Simpan credentials ini dan hapus file create_admin.php setelah selesai!</em>";

$stmt->close();
$conn->close();
?>
