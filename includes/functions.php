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

        // Slot is available only if is_available=1, no conflicting booking, and fits in hours
        $available = false;
        if ($fitsInOperatingHours && $row['is_available']) {
            if (!isSlotBooked($date, $slotStart, $slotEndTime, $barberId)) {
                $available = true;
            }
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

    $sql = "SELECT COUNT(*) as count FROM time_slots ts
            WHERE ts.date = ?
            AND ts.is_available = 1
            AND ts.start_time < ?
            AND ts.end_time > ?";

    $params = [$date, $endTime, $startTime];
    $types = "sss";

    if ($barberId) {
        $sql .= " AND ts.barber_id = ?";
        $params[] = $barberId;
        $types .= "i";
    }

    $sql .= " AND NOT EXISTS (
        SELECT 1 FROM bookings b
        WHERE b.booking_date = ?
        AND b.barber_id = ts.barber_id
        AND b.status NOT IN ('cancelled', 'no_show')
        AND b.start_time < ?
        AND b.end_time > ?
    )";
    $params = array_merge($params, [$date, $endTime, $startTime]);
    $types .= "sss";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['count'] > 0;
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
        'pending' => '<span class="tag tag-pending">Pending</span>',
        'confirmed' => '<span class="tag tag-confirmed">Confirmed</span>',
        'completed' => '<span class="tag tag-completed">Completed</span>',
        'cancelled' => '<span class="tag tag-cancelled">Cancelled</span>',
        'no_show' => '<span class="tag tag-no_show">No Show</span>'
    ];

    return $badges[$status] ?? $status;
}

function redirect($page, $area = 'customer') {
    header("Location: /Barz/index.php?area=$area&page=$page");
    exit;
}
