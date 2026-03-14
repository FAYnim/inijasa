<?php
/**
 * Reports Page - Laba-Rugi & Pipeline Report
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Laporan';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business');
}

// Active tab
$active_tab = $_GET['tab'] ?? 'profit_loss';
$active_tab = in_array($active_tab, ['profit_loss', 'pipeline']) ? $active_tab : 'profit_loss';

// ============================================================
// Profit & Loss Data
// ============================================================
$date_from = $_GET['date_from'] ?? date('Y-m', strtotime('-5 months'));
$date_to = $_GET['date_to'] ?? date('Y-m');

$pl_data = [];
$grand_income = 0;
$grand_expense = 0;

$start = new DateTime($date_from . '-01');
$end = new DateTime($date_to . '-01');
$end->modify('first day of next month');
$interval = new DateInterval('P1M');
$period = new DatePeriod($start, $interval, $end);

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

    $pl_data[] = [
        'month' => $month_label,
        'income' => $income,
        'expense' => $expense,
        'net' => $net
    ];
}

// ============================================================
// Pipeline Data
// ============================================================
$stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
$pipeline_data = [];
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

    $pipeline_data[] = [
        'stage' => $stage,
        'count' => $row['count'],
        'total_value' => $row['total_value'],
        'avg_value' => $row['avg_value']
    ];
}

// Win rate
$won_count = 0;
foreach ($pipeline_data as $p) {
    if ($p['stage'] === 'Won') $won_count = $p['count'];
}
$win_rate = $total_deals > 0 ? round(($won_count / $total_deals) * 100, 1) : 0;

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Laporan</h2>
            <p class="text-muted mb-0">Analisis kinerja bisnis Anda</p>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'profit_loss' ? 'active' : '' ?>" 
               href="?tab=profit_loss">
                <i class="fas fa-chart-line me-2"></i>Laba-Rugi
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab === 'pipeline' ? 'active' : '' ?>" 
               href="?tab=pipeline">
                <i class="fas fa-funnel-dollar me-2"></i>Pipeline
            </a>
        </li>
    </ul>

    <?php if ($active_tab === 'profit_loss'): ?>
    <!-- ============================================================ -->
    <!-- PROFIT & LOSS TAB -->
    <!-- ============================================================ -->
    
    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3 align-items-end">
                <input type="hidden" name="tab" value="profit_loss">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Dari Bulan</label>
                    <input type="month" class="form-control" name="date_from" 
                           value="<?= e($date_from) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sampai Bulan</label>
                    <input type="month" class="form-control" name="date_to" 
                           value="<?= e($date_to) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="export-csv?type=profit_loss&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>" 
                       class="btn btn-outline-success w-100">
                        <i class="fas fa-file-csv me-2"></i>Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Total Pemasukan</p>
                            <h3 class="mb-0 text-success"><?= formatCurrency($grand_income) ?></h3>
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
                            <h3 class="mb-0 text-danger"><?= formatCurrency($grand_expense) ?></h3>
                        </div>
                        <div class="metric-icon bg-danger">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?php $grand_net = $grand_income - $grand_expense; ?>
            <div class="card bg-<?= $grand_net >= 0 ? 'info' : 'warning' ?> bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">Laba Bersih</p>
                            <h3 class="mb-0 text-<?= $grand_net >= 0 ? 'info' : 'warning' ?>"><?= formatCurrency($grand_net) ?></h3>
                            <small class="text-muted"><?= $grand_net >= 0 ? 'Surplus' : 'Defisit' ?></small>
                        </div>
                        <div class="metric-icon bg-<?= $grand_net >= 0 ? 'info' : 'warning' ?>">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit & Loss Table -->
    <div class="card">
        <div class="card-body">
            <?php if (count($pl_data) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-end">Pemasukan</th>
                            <th class="text-end">Pengeluaran</th>
                            <th class="text-end">Laba Bersih</th>
                            <th class="text-center" style="width: 200px">Rasio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pl_data as $row): ?>
                        <tr>
                            <td><strong><?= $row['month'] ?></strong></td>
                            <td class="text-end text-success"><?= formatCurrency($row['income']) ?></td>
                            <td class="text-end text-danger"><?= formatCurrency($row['expense']) ?></td>
                            <td class="text-end">
                                <strong class="text-<?= $row['net'] >= 0 ? 'success' : 'danger' ?>">
                                    <?= formatCurrency($row['net']) ?>
                                </strong>
                            </td>
                            <td>
                                <?php
                                $total_flow = $row['income'] + $row['expense'];
                                $income_pct = $total_flow > 0 ? ($row['income'] / $total_flow) * 100 : 50;
                                ?>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-success" style="width: <?= $income_pct ?>%"></div>
                                    <div class="progress-bar bg-danger" style="width: <?= 100 - $income_pct ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end"><strong class="text-success"><?= formatCurrency($grand_income) ?></strong></td>
                            <td class="text-end"><strong class="text-danger"><?= formatCurrency($grand_expense) ?></strong></td>
                            <td class="text-end">
                                <strong class="text-<?= $grand_net >= 0 ? 'success' : 'danger' ?>">
                                    <?= formatCurrency($grand_net) ?>
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data untuk periode yang dipilih.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($active_tab === 'pipeline'): ?>
    <!-- ============================================================ -->
    <!-- PIPELINE TAB -->
    <!-- ============================================================ -->

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-primary"><?= $total_deals ?></h3>
                    <small class="text-muted">Total Deals</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-success"><?= formatCurrency($total_value) ?></h3>
                    <small class="text-muted">Total Nilai Pipeline</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-info"><?= $win_rate ?>%</h3>
                    <small class="text-muted">Win Rate</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-warning"><?= $total_deals > 0 ? formatCurrency($total_value / $total_deals) : 'Rp 0' ?></h3>
                    <small class="text-muted">Rata-Rata Nilai Deal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="export-csv?type=pipeline" class="btn btn-outline-success">
            <i class="fas fa-file-csv me-2"></i>Export CSV
        </a>
    </div>

    <!-- Pipeline Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Stage</th>
                            <th class="text-center">Jumlah Deal</th>
                            <th class="text-end">Total Nilai</th>
                            <th class="text-end">Rata-Rata Nilai</th>
                            <th style="width: 200px">Distribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pipeline_data as $row): ?>
                        <?php
                        $stage_colors = [
                            'Lead' => '#6B7280',
                            'Qualified' => '#3B82F6',
                            'Proposal' => '#F59E0B',
                            'Negotiation' => '#8B5CF6',
                            'Won' => '#10B981',
                            'Lost' => '#EF4444'
                        ];
                        $pct = $total_deals > 0 ? ($row['count'] / $total_deals) * 100 : 0;
                        ?>
                        <tr>
                            <td>
                                <span class="badge" style="background-color: <?= $stage_colors[$row['stage']] ?>; padding: 6px 12px;">
                                    <?= $row['stage'] ?>
                                </span>
                            </td>
                            <td class="text-center"><strong><?= $row['count'] ?></strong></td>
                            <td class="text-end"><?= formatCurrency($row['total_value']) ?></td>
                            <td class="text-end"><?= formatCurrency($row['avg_value']) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                        <div class="progress-bar" style="width: <?= $pct ?>%; background-color: <?= $stage_colors[$row['stage']] ?>"></div>
                                    </div>
                                    <small class="text-muted" style="min-width: 36px"><?= round($pct, 1) ?>%</small>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-center"><strong><?= $total_deals ?></strong></td>
                            <td class="text-end"><strong><?= formatCurrency($total_value) ?></strong></td>
                            <td class="text-end"><strong><?= $total_deals > 0 ? formatCurrency($total_value / $total_deals) : '-' ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}
</style>

<?php include 'includes/footer.php'; ?>
