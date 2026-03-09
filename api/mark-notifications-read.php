<?php
/**
 * API Endpoint: Mark notifications as read
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Ensure user is logged in and has an active business
if (!isLoggedIn() || !getCurrentBusinessId()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Retrieve JSON payload
$data = json_decode(file_get_contents('php://input'), true);
$business_id = getCurrentBusinessId();
$notification_id = isset($data['id']) ? (int)$data['id'] : null;

if ($notification_id) {
    // Mark specific notification
    $stmt = mysqli_prepare($conn, "UPDATE notifications SET is_read = 1 WHERE id = ? AND business_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $business_id);
} else {
    // Mark all notifications for the business
    $stmt = mysqli_prepare($conn, "UPDATE notifications SET is_read = 1 WHERE business_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $business_id);
}

if ($stmt) {
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
}
