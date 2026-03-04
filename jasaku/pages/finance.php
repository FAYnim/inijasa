<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Keuangan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$type = $_GET['type'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$transactions = getTransactions($businessId, $type, $startDate, $endDate);

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $result = deleteTransaction($_GET['delete']);
    if ($result['success']) {
        $success = 'Transaksi berhasil dihapus';
    } else {
        $error = $result['message'];
    }
    header('Location: finance.php');
    exit;
}

// Calculate totals
$totalIncome = array_sum(array_filter(array_map(function($t) { return $t['type'] === 'Income' ? $t['amount'] : 0; }, $transactions)));
$totalExpense = array_sum(array_filter(array_map(function($t) { return $t['type'] === 'Expense' ? $t['amount'] : 0; }, $transactions)));
?>

<div class="main-content">
    <div class="page-header">
        <h2>Keuangan</h2>
        <div>
            <a href="transaction-form.php?type=Income" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Pemasukan
            </a>
            <a href="transaction-form.php?type=Expense" class="btn btn-danger">
                <i class="fas fa-minus me-2"></i>Pengeluaran
            </a>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= sanitize($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= sanitize($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-success"><?= formatCurrency($totalIncome) ?></div>
                        <div class="metric-label">Total Pemasukan</div>
                    </div>
                    <div class="metric-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-danger"><?= formatCurrency($totalExpense) ?></div>
                        <div class="metric-label">Total Pengeluaran</div>
                    </div>
                    <div class="metric-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value <?= $totalIncome - $totalExpense >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= formatCurrency($totalIncome - $totalExpense) ?>
                        </div>
                        <div class="metric-label">Saldo</div>
                    </div>
                    <div class="metric-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-2">
                    <select class="form-select" name="type">
                        <option value="">Semua Jenis</option>
                        <option value="Income" <?= $type === 'Income' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="Expense" <?= $type === 'Expense' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="start_date" placeholder="Tanggal Mulai" value="<?= sanitize($startDate) ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="end_date" placeholder="Tanggal Selesai" value="<?= sanitize($endDate) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
                <?php if ($type || $startDate || $endDate): ?>
                <div class="col-md-2">
                    <a href="finance.php" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Transactions List -->
    <div class="card">
        <div class="card-body">
            <?php if (count($transactions) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Metode</th>
                                <th>Deal</th>
                                <th class="text-end">Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><?= formatDate($tx['transaction_date']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $tx['type'] === 'Income' ? 'success' : 'danger' ?>">
                                        <?= $tx['type'] ?>
                                    </span>
                                </td>
                                <td><strong><?= sanitize($tx['title']) ?></strong></td>
                                <td><?= sanitize($tx['category'] ?? '-') ?></td>
                                <td><?= sanitize($tx['method'] ?? '-') ?></td>
                                <td><?= sanitize($tx['deal_title'] ?? '-') ?></td>
                                <td class="text-end fw-bold <?= $tx['type'] === 'Income' ? 'text-success' : 'text-danger' ?>">
                                    <?= $tx['type'] === 'Income' ? '+' : '-' ?><?= formatCurrency($tx['amount']) ?>
                                </td>
                                <td class="actions">
                                    <a href="transaction-form.php?id=<?= $tx['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="finance.php?delete=<?= $tx['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="6" class="text-end">Total</td>
                                <td class="text-end">
                                    <span class="text-success"><?= formatCurrency($totalIncome) ?></span> / 
                                    <span class="text-danger"><?= formatCurrency($totalExpense) ?></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-wallet"></i>
                    <h4>Belum Ada Transaksi</h4>
                    <p>Catat pemasukan atau pengeluaran pertama Anda</p>
                    <div>
                        <a href="transaction-form.php?type=Income" class="btn btn-success">Pemasukan</a>
                        <a href="transaction-form.php?type=Expense" class="btn btn-danger">Pengeluaran</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
