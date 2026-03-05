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
?>
