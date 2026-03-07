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

$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($content_type, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $input = $_POST;
}

if (empty($input['business_name']) || trim($input['business_name']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama bisnis wajib diisi.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$limit_check = canCreateBusiness($user_id);
if (!$limit_check['can_create']) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => "Anda telah mencapai batas maksimum {$limit_check['limit']} bisnis.",
        'data'    => $limit_check
    ]);
    exit;
}

$valid_categories = ['Kreatif/Desain', 'Konsultan', 'Kebersihan', 'Perbaikan', 'Lainnya'];

$business_name = trim($input['business_name']);
$category      = isset($input['category']) && in_array($input['category'], $valid_categories, true)
                    ? $input['category']
                    : 'Lainnya';
$description   = isset($input['description']) ? trim($input['description']) : '';
$phone         = isset($input['phone']) ? trim($input['phone']) : '';
$email         = isset($input['email']) ? trim($input['email']) : '';
$set_as_active = isset($input['set_as_active']) ? (bool)$input['set_as_active'] : true;

// Validate email format if provided
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
    exit;
}

$is_primary = ($limit_check['current_count'] === 0) ? 1 : 0;

$stmt = mysqli_prepare($conn, "
    INSERT INTO businesses (user_id, business_name, category, description, phone, email, is_primary, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");
mysqli_stmt_bind_param($stmt, "isssssi",
    $user_id, $business_name, $category, $description, $phone, $email, $is_primary
);

if (mysqli_stmt_execute($stmt)) {
    $new_business_id = mysqli_insert_id($conn);

    if ($set_as_active) {
        switchBusiness($new_business_id, $user_id);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Bisnis berhasil dibuat.',
        'data'    => [
            'business_id'   => $new_business_id,
            'business_name' => $business_name,
            'category'      => $category,
            'is_active'     => $set_as_active
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal membuat bisnis. Silakan coba lagi.']);
}
