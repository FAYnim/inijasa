<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Check authentication
if (!isLoggedIn()) {
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';
$businessId = getCurrentBusinessId();

switch ($action) {
    case 'update_deal_stage':
        $dealId = $_POST['deal_id'] ?? 0;
        $stage = $_POST['stage'] ?? '';
        
        if ($dealId && $stage) {
            $result = updateDealStage($dealId, $stage);
            $response = $result;
        }
        break;
        
    case 'delete_service':
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $result = deleteServicePackage($id);
            $response = $result;
        }
        break;
        
    case 'delete_client':
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $result = deleteClient($id);
            $response = $result;
        }
        break;
        
    case 'delete_deal':
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $result = deleteDeal($id);
            $response = $result;
        }
        break;
        
    case 'delete_transaction':
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $result = deleteTransaction($id);
            $response = $result;
        }
        break;
        
    default:
        $response['message'] = 'Invalid action';
}

echo json_encode($response);
exit;
