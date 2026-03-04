<?php
/**
 * Helper Functions
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once __DIR__ . '/db.php';

// ==================== AUTH FUNCTIONS ====================

function registerUser($fullName, $email, $password) {
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Email sudah terdaftar'];
    }
    $stmt->close();
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $db->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullName, $email, $passwordHash);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        $stmt->close();
        return ['success' => true, 'user_id' => $userId];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal membuat akun'];
    }
}

function loginUser($email, $password) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id, full_name, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['success' => false, 'message' => 'Email atau password salah'];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (password_verify($password, $user['password_hash'])) {
        return ['success' => true, 'user' => $user];
    } else {
        return ['success' => false, 'message' => 'Email atau password salah'];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /jasaku/auth/login.php');
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// ==================== BUSINESS FUNCTIONS ====================

function getUserBusinesses($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM businesses WHERE user_id = ? ORDER BY is_primary DESC, created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $businesses = [];
    while ($row = $result->fetch_assoc()) {
        $businesses[] = $row;
    }
    $stmt->close();
    
    return $businesses;
}

function getBusinessById($businessId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM businesses WHERE id = ?");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $business = $result->fetch_assoc();
    $stmt->close();
    
    return $business;
}

function createBusiness($userId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO businesses (user_id, business_name, category, description, address, phone, email, logo_path, is_primary) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $isPrimary = 1;
    $logoPath = $data['logo_path'] ?? null;
    
    $stmt->bind_param("isssssssi", 
        $userId, 
        $data['business_name'], 
        $data['category'], 
        $data['description'], 
        $data['address'], 
        $data['phone'], 
        $data['email'],
        $logoPath,
        $isPrimary
    );
    
    if ($stmt->execute()) {
        $businessId = $stmt->insert_id;
        $stmt->close();
        return ['success' => true, 'business_id' => $businessId];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal membuat bisnis'];
    }
}

function updateBusiness($businessId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE businesses SET 
                          business_name = ?, 
                          category = ?, 
                          description = ?, 
                          address = ?, 
                          phone = ?, 
                          email = ?" . 
                          (isset($data['logo_path']) ? ", logo_path = ?" : "") . 
                          " WHERE id = ?");
    
    if (isset($data['logo_path'])) {
        $stmt->bind_param("sssssssi", 
            $data['business_name'], 
            $data['category'], 
            $data['description'], 
            $data['address'], 
            $data['phone'], 
            $data['email'],
            $data['logo_path'],
            $businessId
        );
    } else {
        $stmt->bind_param("ssssssi", 
            $data['business_name'], 
            $data['category'], 
            $data['description'], 
            $data['address'], 
            $data['phone'], 
            $data['email'],
            $businessId
        );
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal update bisnis'];
    }
}

function userHasBusiness($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM businesses WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['count'] > 0;
}

// ==================== SERVICE PACKAGE FUNCTIONS ====================

function getServicePackages($businessId, $includeInactive = false) {
    $db = getDB();
    
    $sql = "SELECT * FROM service_packages WHERE business_id = ?";
    if (!$includeInactive) {
        $sql .= " AND status = 'Active'";
    }
    $sql .= " AND is_deleted = 0 ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    $stmt->close();
    
    return $packages;
}

function getServicePackageById($packageId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM service_packages WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
    
    return $package;
}

function createServicePackage($businessId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO service_packages (business_id, package_name, description, price, status) 
                          VALUES (?, ?, ?, ?, ?)");
    
    $status = $data['status'] ?? 'Active';
    $description = $data['description'] ?? '';
    
    $stmt->bind_param("issds", $businessId, $data['package_name'], $description, $data['price'], $status);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal membuat paket jasa'];
    }
}

function updateServicePackage($packageId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE service_packages SET 
                          package_name = ?, 
                          description = ?, 
                          price = ?, 
                          status = ? 
                          WHERE id = ?");
    
    $description = $data['description'] ?? '';
    
    $stmt->bind_param("ssdsi", $data['package_name'], $description, $data['price'], $data['status'], $packageId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal update paket jasa'];
    }
}

function deleteServicePackage($packageId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE service_packages SET is_deleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $packageId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal hapus paket jasa'];
    }
}

// ==================== CLIENT FUNCTIONS ====================

function getClients($businessId, $search = '', $source = '') {
    $db = getDB();
    
    $sql = "SELECT * FROM clients WHERE business_id = ?";
    $params = [$businessId];
    
    if ($search) {
        $sql .= " AND (client_name LIKE ? OR company LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if ($source) {
        $sql .= " AND source = ?";
        $params[] = $source;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    $stmt->close();
    
    return $clients;
}

function getClientById($clientId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $client = $result->fetch_assoc();
    $stmt->close();
    
    return $client;
}

function createClient($businessId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO clients (business_id, client_name, company, email, phone, address, notes, source) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $company = $data['company'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $address = $data['address'] ?? '';
    $notes = $data['notes'] ?? '';
    $source = $data['source'] ?? 'Direct';
    
    $stmt->bind_param("isssssss", $businessId, $data['client_name'], $company, $email, $phone, $address, $notes, $source);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal menambah klien'];
    }
}

function updateClient($clientId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE clients SET 
                          client_name = ?, 
                          company = ?, 
                          email = ?, 
                          phone = ?, 
                          address = ?, 
                          notes = ?, 
                          source = ? 
                          WHERE id = ?");
    
    $company = $data['company'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $address = $data['address'] ?? '';
    $notes = $data['notes'] ?? '';
    $source = $data['source'] ?? 'Direct';
    
    $stmt->bind_param("sssssssi", $data['client_name'], $company, $email, $phone, $address, $notes, $source, $clientId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal update klien'];
    }
}

function deleteClient($clientId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->bind_param("i", $clientId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal hapus klien'];
    }
}

// ==================== DEAL FUNCTIONS ====================

function getDeals($businessId, $stage = '') {
    $db = getDB();
    
    $sql = "SELECT d.*, c.client_name, c.company, sp.package_name 
            FROM deals d 
            LEFT JOIN clients c ON d.client_id = c.id 
            LEFT JOIN service_packages sp ON d.service_package_id = sp.id 
            WHERE d.business_id = ?";
    $params = [$businessId];
    
    if ($stage) {
        $sql .= " AND d.current_stage = ?";
        $params[] = $stage;
    }
    
    $sql .= " ORDER BY d.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat("i", count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $deals = [];
    while ($row = $result->fetch_assoc()) {
        $deals[] = $row;
    }
    $stmt->close();
    
    return $deals;
}

function getDealById($dealId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT d.*, c.client_name, c.company, sp.package_name, sp.price as service_price
                          FROM deals d 
                          LEFT JOIN clients c ON d.client_id = c.id 
                          LEFT JOIN service_packages sp ON d.service_package_id = sp.id 
                          WHERE d.id = ?");
    $stmt->bind_param("i", $dealId);
    $stmt->execute();
    $result = $stmt->get_result();
    $deal = $result->fetch_assoc();
    $stmt->close();
    
    return $deal;
}

function createDeal($businessId, $data) {
    $db = getDB();
    
    $dealValue = $data['deal_value'];
    $discountPercent = $data['discount_percent'] ?? 0;
    $finalValue = $dealValue - ($dealValue * $discountPercent / 100);
    
    $stmt = $db->prepare("INSERT INTO deals (business_id, client_id, service_package_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $servicePackageId = $data['service_package_id'] ?? null;
    $expectedCloseDate = $data['expected_close_date'] ?? null;
    $notes = $data['notes'] ?? '';
    $stage = 'Lead';
    
    $stmt->bind_param("iiisdissss", 
        $businessId, 
        $data['client_id'], 
        $servicePackageId, 
        $data['deal_title'], 
        $dealValue, 
        $discountPercent,
        $finalValue,
        $stage,
        $expectedCloseDate,
        $notes
    );
    
    if ($stmt->execute()) {
        $dealId = $stmt->insert_id;
        $stmt->close();
        return ['success' => true, 'deal_id' => $dealId];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal membuat deal'];
    }
}

function updateDeal($dealId, $data) {
    $db = getDB();
    
    $dealValue = $data['deal_value'];
    $discountPercent = $data['discount_percent'] ?? 0;
    $finalValue = $dealValue - ($dealValue * $discountPercent / 100);
    
    $stmt = $db->prepare("UPDATE deals SET 
                          client_id = ?, 
                          service_package_id = ?, 
                          deal_title = ?, 
                          deal_value = ?, 
                          discount_percent = ?, 
                          final_value = ?,
                          expected_close_date = ?, 
                          notes = ?,
                          payment_status = ?
                          WHERE id = ?");
    
    $servicePackageId = $data['service_package_id'] ?? null;
    $expectedCloseDate = $data['expected_close_date'] ?? null;
    $notes = $data['notes'] ?? '';
    $paymentStatus = $data['payment_status'] ?? 'Pending';
    
    $stmt->bind_param("iisdisssi", 
        $data['client_id'], 
        $servicePackageId, 
        $data['deal_title'], 
        $dealValue, 
        $discountPercent,
        $finalValue,
        $expectedCloseDate,
        $notes,
        $paymentStatus,
        $dealId
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal update deal'];
    }
}

function updateDealStage($dealId, $newStage) {
    $db = getDB();
    
    $closedAt = null;
    if ($newStage === 'Won' || $newStage === 'Lost') {
        $closedAt = date('Y-m-d H:i:s');
    }
    
    $stmt = $db->prepare("UPDATE deals SET current_stage = ?, closed_at = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newStage, $closedAt, $dealId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal update stage'];
    }
}

function deleteDeal($dealId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM deals WHERE id = ?");
    $stmt->bind_param("i", $dealId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal hapus deal'];
    }
}

// ==================== DEAL PAYMENT FUNCTIONS ====================

function getDealPayments($dealId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM deal_payments WHERE deal_id = ? ORDER BY payment_date DESC");
    $stmt->bind_param("i", $dealId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
    
    return $payments;
}

function addDealPayment($dealId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes) VALUES (?, ?, ?, ?, ?)");
    
    $method = $data['method'] ?? 'Transfer';
    $notes = $data['notes'] ?? '';
    
    $stmt->bind_param("idsss", $dealId, $data['amount'], $data['payment_date'], $method, $notes);
    
    if ($stmt->execute()) {
        // Update deal payment status
        updateDealPaymentStatus($dealId);
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal menambah pembayaran'];
    }
}

function updateDealPaymentStatus($dealId) {
    $db = getDB();
    
    // Get deal final value and total payments
    $stmt = $db->prepare("SELECT final_value FROM deals WHERE id = ?");
    $stmt->bind_param("i", $dealId);
    $stmt->execute();
    $result = $stmt->get_result();
    $deal = $result->fetch_assoc();
    $stmt->close();
    
    if (!$deal) return;
    
    $stmt = $db->prepare("SELECT SUM(amount) as total_paid FROM deal_payments WHERE deal_id = ?");
    $stmt->bind_param("i", $dealId);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
    $stmt->close();
    
    $totalPaid = $payment['total_paid'] ?? 0;
    
    $status = 'Pending';
    if ($totalPaid <= 0) {
        $status = 'Pending';
    } elseif ($totalPaid >= $deal['final_value']) {
        $status = 'Paid';
    } else {
        $status = 'Partial';
    }
    
    $stmt = $db->prepare("UPDATE deals SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $dealId);
    $stmt->execute();
    $stmt->close();
}

// ==================== TRANSACTION FUNCTIONS ====================

function getTransactions($businessId, $type = '', $startDate = '', $endDate = '') {
    $db = getDB();
    
    $sql = "SELECT t.*, d.deal_title FROM transactions t LEFT JOIN deals d ON t.deal_id = d.id WHERE t.business_id = ?";
    $params = [$businessId];
    
    if ($type) {
        $sql .= " AND t.type = ?";
        $params[] = $type;
    }
    
    if ($startDate) {
        $sql .= " AND t.transaction_date >= ?";
        $params[] = $startDate;
    }
    
    if ($endDate) {
        $sql .= " AND t.transaction_date <= ?";
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY t.transaction_date DESC, t.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $stmt->close();
    
    return $transactions;
}

function createTransaction($businessId, $data) {
    $db = getDB();
    
    $stmt = $db->prepare("INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $method = $data['method'] ?? '';
    $notes = $data['notes'] ?? '';
    $dealId = $data['deal_id'] ?? null;
    
    $stmt->bind_param("isssdsss", 
        $businessId, 
        $data['type'], 
        $data['title'], 
        $data['category'], 
        $data['amount'], 
        $data['transaction_date'], 
        $method, 
        $notes,
        $dealId
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal membuat transaksi'];
    }
}

function deleteTransaction($transactionId) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $transactionId);
    
    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => 'Gagal hapus transaksi'];
    }
}

// ==================== DASHBOARD FUNCTIONS ====================

function getDashboardMetrics($businessId) {
    $db = getDB();
    $metrics = [];
    
    // Current month revenue (Income)
    $currentMonth = date('Y-m');
    $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE business_id = ? AND type = 'Income' AND DATE_FORMAT(transaction_date, '%Y-%m') = ?");
    $stmt->bind_param("is", $businessId, $currentMonth);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $metrics['monthly_revenue'] = $row['total'];
    $stmt->close();
    
    // Total revenue (all time from deals won)
    $stmt = $db->prepare("SELECT COALESCE(SUM(final_value), 0) as total FROM deals WHERE business_id = ? AND current_stage = 'Won'");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $metrics['total_revenue'] = $row['total'];
    $stmt->close();
    
    // Active deals (not Won or Lost)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM deals WHERE business_id = ? AND current_stage NOT IN ('Won', 'Lost')");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $metrics['active_deals'] = $row['count'];
    $stmt->close();
    
    // Total clients
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM clients WHERE business_id = ?");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $metrics['total_clients'] = $row['count'];
    $stmt->close();
    
    // Deal conversion rate
    $stmt = $db->prepare("SELECT 
                          COUNT(CASE WHEN current_stage = 'Won' THEN 1 END) as won,
                          COUNT(*) as total 
                          FROM deals WHERE business_id = ?");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalDeals = $row['total'] > 0 ? $row['total'] : 1;
    $wonDeals = $row['won'];
    $metrics['conversion_rate'] = round(($wonDeals / $totalDeals) * 100, 1);
    $stmt->close();
    
    // Outstanding payments
    $stmt = $db->prepare("SELECT COALESCE(SUM(final_value - (
                          SELECT COALESCE(SUM(amount), 0) FROM deal_payments WHERE deal_id = deals.id
                          )), 0) as outstanding 
                          FROM deals WHERE business_id = ? AND current_stage = 'Won' AND payment_status != 'Paid'");
    $stmt->bind_param("i", $businessId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $metrics['outstanding_payments'] = $row['outstanding'];
    $stmt->close();
    
    return $metrics;
}

function getRevenueExpenseChart($businessId, $months = 6) {
    $db = getDB();
    $chartData = [];
    
    for ($i = $months - 1; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthName = date('M', strtotime("-$i months"));
        
        // Income
        $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE business_id = ? AND type = 'Income' AND DATE_FORMAT(transaction_date, '%Y-%m') = ?");
        $stmt->bind_param("is", $businessId, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $income = $row['total'];
        $stmt->close();
        
        // Expense
        $stmt = $db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE business_id = ? AND type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = ?");
        $stmt->bind_param("is", $businessId, $month);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $expense = $row['total'];
        $stmt->close();
        
        $chartData[] = [
            'month' => $monthName,
            'income' => $income,
            'expense' => $expense
        ];
    }
    
    return $chartData;
}

function getDealsByStage($businessId) {
    $db = getDB();
    $stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
    $stageData = [];
    
    foreach ($stages as $stage) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM deals WHERE business_id = ? AND current_stage = ?");
        $stmt->bind_param("is", $businessId, $stage);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stageData[$stage] = $row['count'];
        $stmt->close();
    }
    
    return $stageData;
}

// ==================== UTILITY FUNCTIONS ====================

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate($date) {
    if (!$date) return '';
    return date('d M Y', strtotime($date));
}

function formatDateTime($date) {
    if (!$date) return '';
    return date('d M Y H:i', strtotime($date));
}

function sanitize($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

function getCurrentBusinessId() {
    return $_SESSION['business_id'] ?? null;
}

function setCurrentBusiness($businessId) {
    $_SESSION['business_id'] = $businessId;
}

function hasPermission($businessId) {
    $userId = getCurrentUserId();
    if (!$userId) return false;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM businesses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $businessId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['count'] > 0;
}
