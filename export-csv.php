<?php
/**
 * CSV Export Handler
 * IniJasa - Platform Manajemen Bisnis Jasa
 * 
 * Supports: transactions, deals, clients, profit_loss, pipeline
 * Usage: export-csv.php?type=transactions&tab=income&date_from=...&date_to=...
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();
if (!$business_id) {
    redirect('setup-business');
}

$export_type = $_GET['type'] ?? '';
$allowed_types = ['transactions', 'deals', 'clients', 'profit_loss', 'pipeline'];

if (!in_array($export_type, $allowed_types)) {
    setFlashMessage('danger', 'Tipe export tidak valid.');
    redirect('dashboard');
}

// Set CSV headers
$filename = '';
switch ($export_type) {
    case 'transactions':
        $tab = $_GET['tab'] ?? 'income';
        $filename = "transaksi_{$tab}_" . date('Y-m-d') . ".csv";
        break;
    case 'deals':
        $filename = "deals_" . date('Y-m-d') . ".csv";
        break;
    case 'clients':
        $filename = "klien_" . date('Y-m-d') . ".csv";
        break;
    case 'profit_loss':
        $filename = "laba_rugi_" . date('Y-m-d') . ".csv";
        break;
    case 'pipeline':
        $filename = "pipeline_report_" . date('Y-m-d') . ".csv";
        break;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// BOM for UTF-8 Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// ============================================================
// Export: Transactions
// ============================================================
if ($export_type === 'transactions') {
    $tab = $_GET['tab'] ?? 'income';
    $tab = in_array($tab, ['income', 'expense']) ? $tab : 'income';
    $search = $_GET['search'] ?? '';
    $category_filter = $_GET['category'] ?? '';
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';

    // Header row
    fputcsv($output, ['Tanggal', 'Judul', 'Kategori', 'Jumlah', 'Metode Pembayaran', 'Deal Terkait', 'Klien', 'Catatan']);

    // Build query
    $query = "SELECT t.*, d.deal_title, c.client_name 
              FROM transactions t
              LEFT JOIN deals d ON t.deal_id = d.id
              LEFT JOIN clients c ON d.client_id = c.id
              WHERE t.business_id = ? AND t.type = ?";
    $params = [$business_id, $tab === 'income' ? 'Income' : 'Expense'];
    $types = "is";

    if ($search) {
        $query .= " AND (t.title LIKE ? OR t.notes LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }

    if ($category_filter) {
        $query .= " AND t.category = ?";
        $params[] = $category_filter;
        $types .= "s";
    }

    if ($date_from) {
        $query .= " AND t.transaction_date >= ?";
        $params[] = $date_from;
        $types .= "s";
    }

    if ($date_to) {
        $query .= " AND t.transaction_date <= ?";
        $params[] = $date_to;
        $types .= "s";
    }

    $query .= " ORDER BY t.transaction_date DESC, t.created_at DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['transaction_date'],
            $row['title'],
            $row['category'],
            $row['amount'],
            $row['method'] ?? '-',
            $row['deal_title'] ?? '-',
            $row['client_name'] ?? '-',
            $row['notes'] ?? ''
        ]);
    }
}

// ============================================================
// Export: Deals
// ============================================================
if ($export_type === 'deals') {
    $filter_stage = $_GET['stage'] ?? '';
    $search = $_GET['search'] ?? '';

    fputcsv($output, ['Judul Deal', 'Klien', 'Perusahaan', 'Paket Jasa', 'Nilai', 'Diskon (%)', 'Nilai Akhir', 'Stage', 'Expected Close', 'Tanggal Dibuat']);

    $query = "SELECT d.*, c.client_name, c.company, s.service_name
              FROM deals d
              LEFT JOIN clients c ON d.client_id = c.id
              LEFT JOIN services s ON d.service_id = s.id
              WHERE d.business_id = ?";
    $params = [$business_id];
    $types = "i";

    if ($filter_stage) {
        $query .= " AND d.current_stage = ?";
        $params[] = $filter_stage;
        $types .= "s";
    }

    if ($search) {
        $query .= " AND (d.deal_title LIKE ? OR c.client_name LIKE ? OR c.company LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }

    $query .= " ORDER BY d.created_at DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['deal_title'],
            $row['client_name'] ?? '-',
            $row['company'] ?? '-',
            $row['service_name'] ?? '-',
            $row['deal_value'],
            $row['discount_percent'],
            $row['final_value'],
            $row['current_stage'],
            $row['expected_close_date'] ?? '-',
            date('Y-m-d', strtotime($row['created_at']))
        ]);
    }
}

// ============================================================
// Export: Clients
// ============================================================
if ($export_type === 'clients') {
    $search = $_GET['search'] ?? '';
    $source_filter = $_GET['source'] ?? '';

    fputcsv($output, ['Nama Klien', 'Perusahaan', 'Email', 'Telepon', 'Alamat', 'Sumber', 'Tanggal Dibuat']);

    $query = "SELECT * FROM clients WHERE business_id = ?";
    $params = [$business_id];
    $types = "i";

    if ($search) {
        $query .= " AND (client_name LIKE ? OR company LIKE ? OR email LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }

    if ($source_filter) {
        $query .= " AND source = ?";
        $params[] = $source_filter;
        $types .= "s";
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['client_name'],
            $row['company'] ?? '-',
            $row['email'] ?? '-',
            $row['phone'] ?? '-',
            $row['address'] ?? '-',
            $row['source'] ?? '-',
            date('Y-m-d', strtotime($row['created_at']))
        ]);
    }
}

// ============================================================
// Export: Profit & Loss (Laba Rugi)
// ============================================================
if ($export_type === 'profit_loss') {
    $date_from = $_GET['date_from'] ?? date('Y-m', strtotime('-5 months'));
    $date_to = $_GET['date_to'] ?? date('Y-m');

    fputcsv($output, ['Bulan', 'Total Pemasukan', 'Total Pengeluaran', 'Laba Bersih']);

    // Generate months range
    $start = new DateTime($date_from . '-01');
    $end = new DateTime($date_to . '-01');
    $end->modify('first day of next month');
    $interval = new DateInterval('P1M');
    $period = new DatePeriod($start, $interval, $end);

    $grand_income = 0;
    $grand_expense = 0;

    foreach ($period as $dt) {
        $month = $dt->format('Y-m');
        $month_label = $dt->format('M Y');

        $stmt = mysqli_prepare($conn, "
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END), 0) as expense
            FROM transactions
            WHERE business_id = ? AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
        ");
        mysqli_stmt_bind_param($stmt, "is", $business_id, $month);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        $income = $row['income'];
        $expense = $row['expense'];
        $net = $income - $expense;

        $grand_income += $income;
        $grand_expense += $expense;

        fputcsv($output, [$month_label, $income, $expense, $net]);
    }

    // Total row
    fputcsv($output, ['TOTAL', $grand_income, $grand_expense, $grand_income - $grand_expense]);
}

// ============================================================
// Export: Pipeline Report
// ============================================================
if ($export_type === 'pipeline') {
    fputcsv($output, ['Stage', 'Jumlah Deal', 'Total Nilai', 'Rata-Rata Nilai']);

    $stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
    $total_deals = 0;
    $total_value = 0;

    foreach ($stages as $stage) {
        $stmt = mysqli_prepare($conn, "
            SELECT COUNT(*) as count, COALESCE(SUM(final_value), 0) as total_value, COALESCE(AVG(final_value), 0) as avg_value
            FROM deals
            WHERE business_id = ? AND current_stage = ?
        ");
        mysqli_stmt_bind_param($stmt, "is", $business_id, $stage);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        $total_deals += $row['count'];
        $total_value += $row['total_value'];

        fputcsv($output, [
            $stage,
            $row['count'],
            $row['total_value'],
            round($row['avg_value'], 2)
        ]);
    }

    // Total row
    $avg_total = $total_deals > 0 ? round($total_value / $total_deals, 2) : 0;
    fputcsv($output, ['TOTAL', $total_deals, $total_value, $avg_total]);

    // Win rate
    $won_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM deals WHERE business_id = ? AND current_stage = 'Won'");
    mysqli_stmt_bind_param($won_stmt, "i", $business_id);
    mysqli_stmt_execute($won_stmt);
    $won_count = mysqli_fetch_assoc(mysqli_stmt_get_result($won_stmt))['c'];
    
    $all_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM deals WHERE business_id = ?");
    mysqli_stmt_bind_param($all_stmt, "i", $business_id);
    mysqli_stmt_execute($all_stmt);
    $all_count = mysqli_fetch_assoc(mysqli_stmt_get_result($all_stmt))['c'];

    $win_rate = $all_count > 0 ? round(($won_count / $all_count) * 100, 1) : 0;
    fputcsv($output, []);
    fputcsv($output, ['Win Rate', $win_rate . '%']);
}

fclose($output);
exit();
