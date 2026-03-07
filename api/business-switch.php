<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['business_id']) || !is_numeric($input['business_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid business_id.']);
    exit;
}

$business_id = (int)$input['business_id'];
$user_id     = (int)$_SESSION['user_id'];

if (switchBusiness($business_id, $user_id)) {
    $business = getCurrentBusiness();
    echo json_encode([
        'success' => true,
        'message' => 'Business switched successfully.',
        'data'    => [
            'business_id'   => (int)$business['id'],
            'business_name' => $business['business_name'],
            'category'      => $business['category']
        ]
    ]);
} else {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied or business not found.']);
}
