<?php
// Helper Functions

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin']);
}

function isSuperAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

function requireSuperAdmin() {
    if (!isSuperAdmin()) {
        header('Location: /Barz/index.php?page=home');
        exit;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /Barz/index.php?page=login');
        exit;
    }
}

function requireAdmin() {
    // Only admin (not super_admin) can access admin area
    if (!isAdmin() || isSuperAdmin()) {
        header('Location: /Barz/index.php?page=home');
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function formatDate($date) {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $d = date_parse($date);
    return $d['day'] . ' ' . $months[$d['month'] - 1] . ' ' . $d['year'];
}

// ---- SECURITY: CSRF ----

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// ---- SECURITY: Session fixation protection ----

function refreshSession() {
    session_regenerate_id(true);
}

// ---- SECURITY: Input validation ----

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

function getServices($activeOnly = true) {
    $db = getDB();
    $sql = "SELECT * FROM services" . ($activeOnly ? " WHERE is_active = 1" : "") . " ORDER BY name";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Kategori: kolom DB, fallback parse "(KATEGORI)" dari nama.
function serviceCategory($s) {
    if (!empty($s['category'])) return strtoupper($s['category']);
    if (preg_match('/\((HAIRCUT|HAIRSTYLING|COLORING)\)/i', $s['name'] ?? '', $m)) return strtoupper($m[1]);
    return 'HAIRCUT';
}

function getBarbers($activeOnly = true) {
    $db = getDB();
    $sql = "SELECT * FROM barbers" . ($activeOnly ? " WHERE is_active = 1" : "") . " ORDER BY name";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function isSlotBooked($date, $startTime, $endTime, $barberId = null) {
    $db = getDB();

    if ($barberId) {
        // Specific barber check
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings
            WHERE booking_date = ?
                AND barber_id = ?
                AND status NOT IN ('cancelled', 'no_show')
                AND start_time < ?
                AND end_time > ?");
        $stmt->bind_param("siss", $date, $barberId, $endTime, $startTime);
    } else {
        // Any barber - check if ALL barbers are booked for this slot
        $stmt = $db->prepare("SELECT COUNT(DISTINCT b.barber_id) as count FROM bookings b
            WHERE b.booking_date = ?
                AND b.status NOT IN ('cancelled', 'no_show')
                AND b.barber_id IS NOT NULL
                AND b.start_time < ?
                AND b.end_time > ?");
        $stmt->bind_param("sss", $date, $endTime, $startTime);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['count'] > 0;
}

function getAvailableSlots($date, $serviceId, $barberId = null) {
    $db = getDB();

    // Get service duration
    $stmt = $db->prepare("SELECT duration FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    if (!$service) return [];
    $duration = $service['duration']; // in minutes

    // Get closing time
    $closingTime = '21:00:00';

    if ($barberId) {
        // Get all time_slots for this barber on this date
        $sql = "SELECT ts.id, ts.start_time, ts.end_time, ts.is_available
                FROM time_slots ts
                WHERE ts.date = ? AND ts.barber_id = ?
                ORDER BY ts.start_time";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $date, $barberId);
    } else {
        // Get distinct time slots across all barbers for this date
        // Show slot if AT LEAST ONE barber has it available
        $sql = "SELECT ts.start_time, ts.end_time, MAX(ts.is_available) as is_available
                FROM time_slots ts
                WHERE ts.date = ? AND ts.is_available = 1
                GROUP BY ts.start_time, ts.end_time
                ORDER BY ts.start_time";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $date);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slotStart = $row['start_time'];
        $slotEndTime = date('H:i:s', strtotime($slotStart) + ($duration * 60));

        // Check if booking fits within operating hours
        $fitsInOperatingHours = ($slotEndTime <= $closingTime);

        // Slot tersedia jika setidaknya satu barber aktif memiliki slot awal ini
        // dan tidak memiliki booking lain yang bertabrakan selama durasi layanan.
        $available = false;
        if ($fitsInOperatingHours && $row['is_available']) {
            $available = isSlotAvailable($date, $slotStart, $slotEndTime, $barberId);
        }

        $slots[] = [
            'start' => substr($slotStart, 0, 5),
            'end' => substr($slotEndTime, 0, 5),
            'available' => $available
        ];
    }

    return $slots;
}

function isSlotAvailable($date, $startTime, $endTime, $barberId = null) {
    $db = getDB();

    // Slot pilihan harus benar-benar dimulai pada jam yang dipilih dan dimiliki barber aktif.
    // Bentrok diperiksa terhadap seluruh durasi layanan, bukan hanya slot 15 menit pertama.
    $sql = "SELECT COUNT(*) AS count
            FROM time_slots ts
            INNER JOIN barbers br ON br.id = ts.barber_id AND br.is_active = 1
            WHERE ts.date = ?
              AND ts.start_time = ?
              AND ts.is_available = 1";
    $params = [$date, $startTime];
    $types = 'ss';

    if ($barberId !== null) {
        $sql .= " AND ts.barber_id = ?";
        $params[] = (int)$barberId;
        $types .= 'i';
    }

    $sql .= " AND NOT EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.booking_date = ts.date
                  AND b.barber_id = ts.barber_id
                  AND b.status NOT IN ('cancelled', 'no_show')
                  AND b.start_time < ?
                  AND b.end_time > ?
            )";
    $params[] = $endTime;
    $params[] = $startTime;
    $types .= 'ss';

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    return (int)($row['count'] ?? 0) > 0;
}

function createBooking($customerId, $serviceId, $barberId, $date, $startTime, $endTime, $totalPrice, $notes = '') {
    $db = getDB();

    $stmt = $db->prepare("INSERT INTO bookings (customer_id, service_id, barber_id, booking_date, start_time, end_time, total_price, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiisssds", $customerId, $serviceId, $barberId, $date, $startTime, $endTime, $totalPrice, $notes);

    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function getBookingsByCustomer($customerId) {
    $db = getDB();

    $sql = "SELECT b.*, s.name as service_name, s.duration, br.name as barber_name
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            LEFT JOIN barbers br ON b.barber_id = br.id
            WHERE b.customer_id = ?
            ORDER BY b.booking_date DESC, b.start_time DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSetting($key, $default = '') {
    $db = getDB();

    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }

    return $default;
}

function sendNotification($userId, $subject, $message) {
    // Placeholder for email/WhatsApp notification
    // Implement with PHPMailer or WhatsApp API later
    return true;
}

function getStatusBadge($status) {
    $badges = [
        'pending' => ['label' => 'Menunggu', 'classes' => 'border-amber-200 bg-amber-50 text-amber-800'],
        'confirmed' => ['label' => 'Terkonfirmasi', 'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-800'],
        'completed' => ['label' => 'Selesai', 'classes' => 'border-sky-200 bg-sky-50 text-sky-800'],
        'cancelled' => ['label' => 'Dibatalkan', 'classes' => 'border-rose-200 bg-rose-50 text-rose-800'],
        'no_show' => ['label' => 'Tidak Hadir', 'classes' => 'border-slate-200 bg-slate-100 text-slate-700'],
    ];

    if (!isset($badges[$status])) {
        return '<span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">' . htmlspecialchars((string)$status) . '</span>';
    }

    $badge = $badges[$status];
    return '<span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold ' . $badge['classes'] . '">' . $badge['label'] . '</span>';
}

function redirect($page, $area = 'customer') {
    header("Location: /Barz/index.php?area=$area&page=$page");
    exit;
}
