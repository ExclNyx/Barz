<?php
/**
 * One-time initializer for Barz's Firestore collections.
 * Run from terminal: php scripts/seed_firestore.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this initializer from the command line only.');
}
require_once __DIR__ . '/../includes/init.php';

$services = [
    1 => ['name'=>'Haircut Basic', 'description'=>'Potong rambut standar dengan teknik dasar', 'price'=>35000, 'duration'=>30, 'is_active'=>1, 'category'=>'HAIRCUT'],
    2 => ['name'=>'Haircut Premium', 'description'=>'Potong rambut dengan styling modern dan konsultasi', 'price'=>75000, 'duration'=>45, 'is_active'=>1, 'category'=>'HAIRCUT'],
    3 => ['name'=>'Haircut + Shave', 'description'=>'Paket lengkap potong rambut dan cukur jenggot', 'price'=>85000, 'duration'=>60, 'is_active'=>1, 'category'=>'HAIRCUT'],
    4 => ['name'=>'Kids Haircut', 'description'=>'Potong rambut khusus anak-anak', 'price'=>30000, 'duration'=>25, 'is_active'=>1, 'category'=>'HAIRCUT'],
    5 => ['name'=>'Beard Trim', 'description'=>'Rapikan jenggot dan kumis', 'price'=>25000, 'duration'=>20, 'is_active'=>1, 'category'=>'HAIRSTYLING'],
    6 => ['name'=>'Hair Coloring', 'description'=>'Pewarnaan rambut dengan produk premium', 'price'=>150000, 'duration'=>90, 'is_active'=>1, 'category'=>'COLORING'],
];
foreach ($services as $id => $service) {
    if (!fs_get('services', $id)) fs_put('services', $id, $service);
}

$barbers = [
    1 => ['name'=>'Andi', 'specialization'=>'Classic & Modern Cuts', 'experience_years'=>5, 'is_active'=>1],
    2 => ['name'=>'Budi', 'specialization'=>'Fade & Undercut Specialist', 'experience_years'=>3, 'is_active'=>1],
    3 => ['name'=>'Cecep', 'specialization'=>'Beard & Styling Expert', 'experience_years'=>7, 'is_active'=>1],
];
foreach ($barbers as $id => $barber) {
    if (!fs_get('barbers', $id)) fs_put('barbers', $id, $barber);
}

$settings = [
    'shop_name'=>'Barz Barbershop', 'shop_address'=>'Jl. Contoh No. 123, Jakarta',
    'shop_phone'=>'021-12345678', 'shop_email'=>'info@barz.com',
    'opening_time'=>'09:00', 'closing_time'=>'21:00', 'slot_duration'=>'15',
    'booking_advance_days'=>'30', 'whatsapp_number'=>'6281234567890'
];
foreach ($settings as $key => $value) {
    if (!fs_get('settings', $key)) fs_put('settings', $key, ['setting_key'=>$key, 'setting_value'=>$value]);
}

$seedUsers = [
    ['name'=>'Admin Barz', 'email'=>'admin@barz.com', 'phone'=>'081234567890', 'password'=>'barz2024', 'role'=>'admin'],
    ['name'=>'Super Admin Barz', 'email'=>'superadmin@barz.com', 'phone'=>'081234567891', 'password'=>'superadmin', 'role'=>'super_admin'],
];
foreach ($seedUsers as $seedUser) {
    $existing = fs_find('users', fn($u) => strtolower($u['email'] ?? '') === strtolower($seedUser['email']));
    if (!$existing) {
        $password = $seedUser['password'];
        unset($seedUser['password']);
        $seedUser['password'] = password_hash($password, PASSWORD_DEFAULT);
        $seedUser['email_verified_at'] = null;
        fs_add('users', $seedUser);
        echo "Created user: {$seedUser['email']}\n";
    } else {
        echo "User already exists; left unchanged: {$seedUser['email']}\n";
    }
}

echo "\nFirestore seed selesai.\nKoleksi services, barbers, settings, dan users siap.\n";
echo "Demo admin: admin@barz.com / barz2024\nDemo super admin: superadmin@barz.com / superadmin\nGanti kata sandi setelah pengujian.\n";
