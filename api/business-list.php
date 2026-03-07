<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$current_business_id = getCurrentBusinessId();

$businesses = getUserBusinesses($user_id);

foreach ($businesses as &$business) {
    $business['is_active'] = ($business['id'] == $current_business_id);
}
unset($business);

$limit_info = canCreateBusiness($user_id);

echo json_encode([
    'success' => true,
    'data' => [
        'businesses'          => $businesses,
        'current_business_id' => $current_business_id,
        'limit_info'          => $limit_info
    ]
]);
