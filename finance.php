<?php
/**
 * Finance Page - Income & Expense Tracking
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Keuangan';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

// Get active tab (income or expense)
$active_tab = $_GET['tab'] ?? 'income';
$active_tab = in_array($active_tab, ['income', 'expense']) ? $active_tab : 'income';

// Get filter parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query based on active tab
$query = "SELECT t.*, d.deal_title, c.client_name 
          FROM transactions t
          LEFT JOIN deals d ON t.deal_id = d.id
          LEFT JOIN clients c ON d.client_id = c.id
          WHERE t.business_id = ? AND t.type = ?";
$params = [$business_id, $active_tab === 'income' ? 'Income' : 'Expense'];
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
$transactions = mysqli_stmt_get_result($stmt);

// Get summary stats (prepared statement)
$stats_query = "SELECT 
    SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) as total_income,
    SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) as total_expense,
    COUNT(CASE WHEN type = 'Income' THEN 1 END) as income_count,
    COUNT(CASE WHEN type = 'Expense' THEN 1 END) as expense_count
FROM transactions 
WHERE business_id = ?";
$stats_params = [$business_id];
$stats_types = "i";

// Apply date filters to stats if provided
if ($date_from) {
    $stats_query .= " AND transaction_date >= ?";
    $stats_params[] = $date_from;
    $stats_types .= "s";
}
if ($date_to) {
    $stats_query .= " AND transaction_date <= ?";
    $stats_params[] = $date_to;
    $stats_types .= "s";
}

$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, $stats_types, ...$stats_params);
mysqli_stmt_execute($stats_stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stats_stmt));
$net_profit = $stats['total_income'] - $stats['total_expense'];

// Category options
$income_categories = ['Deal Payment', 'Lainnya'];
$expense_categories = ['Operasional', 'Marketing', 'Tools', 'Lainnya'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Keuangan</h2>
            <p class="text-muted mb-0">Kelola pemasukan dan pengeluaran bisnis</p>
        </div>
        <div class="d-flex gap-2">
            <a href="export-csv.php?type=transactions&tab=<?= $active_tab ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>&category=<?= urlencode($category_filter) ?>&search=<?= urlencode($search) ?>" 
               class="btn btn-outline-success">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
            <a href="transaction-form.php?type=<?= $active_tab ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Tambah Transaksi
            </a>
        </div>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Pemasukan</p>
                            <h3 class="mb-0 text-success"><?= formatCurrency($stats['total_income']) ?></h3>
                            <small class="text-muted"><?= $stats['income_count'] ?> transaksi</small>
                        </div>
                        <div class="metric-icon bg-success">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-danger bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Pengeluaran</p>
                            <h3 class="mb-0 text-danger"><?= formatCurrency($stats['total_expense']) ?></h3>
                            <small class="text-muted"><?= $stats['expense_count'] ?> transaksi</small>
                        </div>
                        <div class="metric-icon bg-danger">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-<?= $net_profit >= 0 ? 'info' : 'warning' ?> bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Laba Bersih</p>
                            <h3 class="mb-0 text-<?= $net_profit >= 0 ? 'info' : 'warning' ?>"><?= formatCurrency($net_profit) ?></h3>
                            <small class="text-muted">
                                <?= $net_profit >= 0 ? 'Surplus' : 'Defisit' ?>
                            </small>
                        </div>
                        <div class="metric-icon bg-<?= $net_profit >= 0 ? 'info' : 'warning' ?>">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'income' ? 'active' : '' ?>" 
               href="?tab=income">
                <i class="fas fa-arrow-up me-2"></i>Pemasukan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'expense' ? 'active' : '' ?>" 
               href="?tab=expense">
                <i class="fas fa-arrow-down me-2"></i>Pengeluaran
            </a>
        </li>
    </ul>
    
    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="tab" value="<?= $active_tab ?>">
                
                <div class="col-md-4">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari judul atau catatan..."
                           value="<?= e($search) ?>">
                </div>
                
                <div class="col-md-2">
                    <select class="form-select" name="category">
                        <option value="">Semua Kategori</option>
                        <?php 
                        $categories = $active_tab === 'income' ? $income_categories : $expense_categories;
                        foreach ($categories as $cat): 
                        ?>
                            <option value="<?= $cat ?>" <?= $category_filter === $cat ? 'selected' : '' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <input type="date" 
                           class="form-control" 
                           name="date_from" 
                           placeholder="Dari Tanggal"
                           value="<?= e($date_from) ?>">
                </div>
                
                <div class="col-md-2">
                    <input type="date" 
                           class="form-control" 
                           name="date_to" 
                           placeholder="Sampai Tanggal"
                           value="<?= e($date_to) ?>">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($transactions) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <?php if ($active_tab === 'income'): ?>
                                <th>Deal Terkait</th>
                            <?php endif; ?>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($trans = mysqli_fetch_assoc($transactions)): ?>
                        <tr>
                            <td>
                                <small><?= formatDate($trans['transaction_date']) ?></small>
                            </td>
                            <td>
                                <strong><?= e($trans['title']) ?></strong>
                                <?php if ($trans['notes']): ?>
                                    <br><small class="text-muted"><?= e(substr($trans['notes'], 0, 50)) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= e($trans['category']) ?></span>
                            </td>
                            <?php if ($active_tab === 'income'): ?>
                                <td>
                                    <?php if ($trans['deal_id']): ?>
                                        <small>
                                            <a href="deals.php?highlight=<?= $trans['deal_id'] ?>">
                                                <?= e($trans['deal_title']) ?>
                                            </a>
                                            <br>
                                            <span class="text-muted"><?= e($trans['client_name']) ?></span>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <small><?= e($trans['method'] ?? '-') ?></small>
                            </td>
                            <td class="text-end">
                                <strong class="text-<?= $active_tab === 'income' ? 'success' : 'danger' ?>">
                                    <?= formatCurrency($trans['amount']) ?>
                                </strong>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="transaction-form.php?id=<?= $trans['id'] ?>&type=<?= $active_tab ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="transaction-form.php" class="d-inline" 
                                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $trans['id'] ?>">
                                        <input type="hidden" name="type" value="<?= $active_tab ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?= $active_tab === 'income' ? '5' : '4' ?>" class="text-end">
                                <strong>Total <?= $active_tab === 'income' ? 'Pemasukan' : 'Pengeluaran' ?>:</strong>
                            </td>
                            <td class="text-end">
                                <strong class="text-<?= $active_tab === 'income' ? 'success' : 'danger' ?>">
                                    <?php
                                    // Calculate filtered total
                                    mysqli_data_seek($transactions, 0);
                                    $filtered_total = 0;
                                    while ($t = mysqli_fetch_assoc($transactions)) {
                                        $filtered_total += $t['amount'];
                                    }
                                    echo formatCurrency($filtered_total);
                                    ?>
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">
                    <?php if ($search || $category_filter || $date_from || $date_to): ?>
                        Tidak ada transaksi yang sesuai dengan pencarian.
                    <?php else: ?>
                        Belum ada transaksi <?= $active_tab === 'income' ? 'pemasukan' : 'pengeluaran' ?>.
                    <?php endif; ?>
                </p>
                <?php if ($search || $category_filter || $date_from || $date_to): ?>
                    <a href="finance.php?tab=<?= $active_tab ?>" class="btn btn-secondary">Reset Filter</a>
                <?php else: ?>
                    <a href="transaction-form.php?type=<?= $active_tab ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Transaksi
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
