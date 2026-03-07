<?php
/**
 * Helper Functions
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentBusinessId() {
    return $_SESSION['business_id'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function redirect($page) {
    header("Location: $page");
    exit();
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login.php');
    }
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount, $withSymbol = true) {
    $formatted = number_format($amount, 0, ',', '.');
    return $withSymbol ? 'Rp ' . $formatted : $formatted;
}

function formatDate($date, $withTime = false) {
    if (empty($date)) return '-';
    
    $timestamp = strtotime($date);
    $format = $withTime ? 'd M Y H:i' : 'd M Y';
    
    return date($format, $timestamp);
}

function calculatePercentageChange($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return (($current - $previous) / $previous) * 100;
}

function getChangeClass($percentage) {
    if ($percentage > 0) return 'success';
    if ($percentage < 0) return 'danger';
    return 'secondary';
}

function getChangeIcon($percentage) {
    if ($percentage > 0) return 'fa-arrow-up';
    if ($percentage < 0) return 'fa-arrow-down';
    return 'fa-minus';
}

function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================================
// Multi-Business Management Functions
// ============================================================

/**
 * Get all businesses for a specific user
 */
function getUserBusinesses($user_id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "
        SELECT id, business_name, category, description, logo_path, is_primary, created_at
        FROM businesses
        WHERE user_id = ?
        ORDER BY is_primary DESC, business_name ASC
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $businesses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $businesses[] = $row;
    }
    return $businesses;
}

/**
 * Get business limit for a specific user (null = unlimited)
 */
function getUserBusinessLimit($user_id) {
    global $conn;

    // Check user-specific limit first
    $stmt = mysqli_prepare($conn, "SELECT business_limit FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['business_limit'] !== null) {
            return (int)$row['business_limit'];
        }
    }

    // Fallback to system default
    $stmt = mysqli_prepare($conn, "SELECT config_value FROM system_config WHERE config_key = 'default_business_limit'");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['config_value'] === null ? null : (int)$row['config_value'];
    }

    return null; // Unlimited by default
}

/**
 * Check if user can create more businesses
 * Returns ['can_create' => bool, 'current_count' => int, 'limit' => int|null]
 */
function canCreateBusiness($user_id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM businesses WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $current_count = (int)$row['count'];

    $limit = getUserBusinessLimit($user_id);
    $can_create = $limit === null || $current_count < $limit;

    return [
        'can_create'    => $can_create,
        'current_count' => $current_count,
        'limit'         => $limit
    ];
}

/**
 * Get current active business details
 */
function getCurrentBusiness() {
    global $conn;

    $business_id = getCurrentBusinessId();
    if (!$business_id) return null;

    $stmt = mysqli_prepare($conn, "
        SELECT id, business_name, category, description, address, phone, email, logo_path, is_primary
        FROM businesses
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

/**
 * Switch active business in session after validating ownership
 */
function switchBusiness($business_id, $user_id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT id FROM businesses WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $business_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        return false;
    }

    $_SESSION['business_id'] = (int)$business_id;
    return true;
}

/**
 * Validate business ownership
 */
function isBusinessOwner($business_id, $user_id) {
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT id FROM businesses WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $business_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}
?>
