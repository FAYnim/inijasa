<?php
/**
 * Helper Functions
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current business ID from session
 * @return int|null
 */
function getCurrentBusinessId() {
    return $_SESSION['business_id'] ?? null;
}

/**
 * Get current user ID from session
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Redirect to a page
 * @param string $page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Require login, redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('../auth/login.php');
    }
}

/**
 * Sanitize output to prevent XSS
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency (Indonesian Rupiah)
 * @param float $amount
 * @param bool $withSymbol
 * @return string
 */
function formatCurrency($amount, $withSymbol = true) {
    $formatted = number_format($amount, 0, ',', '.');
    return $withSymbol ? 'Rp ' . $formatted : $formatted;
}

/**
 * Format date to Indonesian format
 * @param string $date
 * @param bool $withTime
 * @return string
 */
function formatDate($date, $withTime = false) {
    if (empty($date)) return '-';
    
    $timestamp = strtotime($date);
    $format = $withTime ? 'd M Y H:i' : 'd M Y';
    
    return date($format, $timestamp);
}

/**
 * Calculate percentage change
 * @param float $current
 * @param float $previous
 * @return float
 */
function calculatePercentageChange($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return (($current - $previous) / $previous) * 100;
}

/**
 * Get alert class based on percentage
 * @param float $percentage
 * @return string
 */
function getChangeClass($percentage) {
    if ($percentage > 0) return 'success';
    if ($percentage < 0) return 'danger';
    return 'secondary';
}

/**
 * Get icon based on percentage
 * @param float $percentage
 * @return string
 */
function getChangeIcon($percentage) {
    if ($percentage > 0) return 'fa-arrow-up';
    if ($percentage < 0) return 'fa-arrow-down';
    return 'fa-minus';
}

/**
 * Set flash message
 * @param string $type (success, danger, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 * @return array|null
 */
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

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
