<?php
/**
 * Dashboard Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$page_title = 'Dashboard';
$business_id = getCurrentBusinessId();

// Redirect to business setup if no business
if (!$business_id) {
    redirect('setup-business.php');
}

// ==================== FETCH DASHBOARD METRICS ====================

// 1. Total Revenue (Current Month)
$current_month = date('Y-m');
$stmt = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(amount), 0) as total_revenue
    FROM transactions
    WHERE business_id = ? 
    AND type = 'Income'
    AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
");
mysqli_stmt_bind_param($stmt, "is", $business_id, $current_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_revenue = mysqli_fetch_assoc($result)['total_revenue'];

// Total Revenue (Previous Month) for comparison
$previous_month = date('Y-m', strtotime('-1 month'));
$stmt = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(amount), 0) as total_revenue
    FROM transactions
    WHERE business_id = ? 
    AND type = 'Income'
    AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
");
mysqli_stmt_bind_param($stmt, "is", $business_id, $previous_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$previous_revenue = mysqli_fetch_assoc($result)['total_revenue'];

$revenue_change = calculatePercentageChange($total_revenue, $previous_revenue);

// 2. Total Active Deals
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as active_deals
    FROM deals
    WHERE business_id = ?
    AND current_stage NOT IN ('Won', 'Lost')
");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$active_deals = mysqli_fetch_assoc($result)['active_deals'];

// Active Deals Previous Month
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as active_deals
    FROM deals
    WHERE business_id = ?
    AND current_stage NOT IN ('Won', 'Lost')
    AND DATE_FORMAT(created_at, '%Y-%m') = ?
");
mysqli_stmt_bind_param($stmt, "is", $business_id, $previous_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$previous_active_deals = mysqli_fetch_assoc($result)['active_deals'];

$deals_change = calculatePercentageChange($active_deals, $previous_active_deals);

// 3. Total Clients
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as total_clients
    FROM clients
    WHERE business_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_clients = mysqli_fetch_assoc($result)['total_clients'];

// Previous month clients
$stmt = mysqli_prepare($conn, "
    SELECT COUNT(*) as total_clients
    FROM clients
    WHERE business_id = ?
    AND DATE_FORMAT(created_at, '%Y-%m') <= ?
");
mysqli_stmt_bind_param($stmt, "is", $business_id, $previous_month);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$previous_clients = mysqli_fetch_assoc($result)['total_clients'];

$clients_change = calculatePercentageChange($total_clients, $previous_clients);

// 4. Deal Conversion Rate
$stmt = mysqli_prepare($conn, "
    SELECT 
        COUNT(*) as total_deals,
        SUM(CASE WHEN current_stage = 'Won' THEN 1 ELSE 0 END) as won_deals
    FROM deals
    WHERE business_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$deal_stats = mysqli_fetch_assoc($result);
$conversion_rate = $deal_stats['total_deals'] > 0 
    ? ($deal_stats['won_deals'] / $deal_stats['total_deals']) * 100 
    : 0;

// 5. Outstanding Payments
$stmt = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(d.final_value - COALESCE(dp.paid_amount, 0)), 0) as outstanding
    FROM deals d
    LEFT JOIN (
        SELECT deal_id, SUM(amount) as paid_amount
        FROM deal_payments
        GROUP BY deal_id
    ) dp ON d.id = dp.deal_id
    WHERE d.business_id = ?
    AND d.current_stage = 'Won'
    AND COALESCE(dp.paid_amount, 0) < d.final_value
");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$outstanding_payments = mysqli_fetch_assoc($result)['outstanding'];

// ==================== CHART DATA: Revenue vs Expense (6 Months) ====================
$chart_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_label = date('M Y', strtotime("-$i months"));
    
    // Revenue
    $stmt = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(amount), 0) as total
        FROM transactions
        WHERE business_id = ?
        AND type = 'Income'
        AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
    ");
    mysqli_stmt_bind_param($stmt, "is", $business_id, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $revenue = mysqli_fetch_assoc($result)['total'];
    
    // Expense
    $stmt = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(amount), 0) as total
        FROM transactions
        WHERE business_id = ?
        AND type = 'Expense'
        AND DATE_FORMAT(transaction_date, '%Y-%m') = ?
    ");
    mysqli_stmt_bind_param($stmt, "is", $business_id, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $expense = mysqli_fetch_assoc($result)['total'];
    
    $chart_data[] = [
        'month' => $month_label,
        'revenue' => $revenue,
        'expense' => $expense
    ];
}

// ==================== RECENT DEALS ====================
$stmt = mysqli_prepare($conn, "
    SELECT 
        d.id,
        d.deal_title,
        d.final_value,
        d.current_stage,
        d.expected_close_date,
        c.client_name
    FROM deals d
    LEFT JOIN clients c ON d.client_id = c.id
    WHERE d.business_id = ?
    ORDER BY d.created_at DESC
    LIMIT 5
");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$recent_deals = mysqli_stmt_get_result($stmt);

// Include header and sidebar
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h2 class="page-title">Dashboard</h2>
        <p class="text-muted">Selamat datang kembali! Berikut ringkasan bisnis Anda.</p>
    </div>
    
    <?php
    // Display flash message if exists
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- KPI Metrics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon revenue">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="kpi-info">
                        <h6 class="kpi-label">Total Revenue</h6>
                        <p class="kpi-period">Bulan Ini</p>
                    </div>
                </div>
                <div class="kpi-value"><?= formatCurrency($total_revenue) ?></div>
                <div class="kpi-trend <?= getChangeClass($revenue_change) ?>">
                    <i class="fas <?= getChangeIcon($revenue_change) ?>"></i>
                    <span><?= number_format(abs($revenue_change), 1) ?>%</span>
                    <span class="trend-label">vs bulan lalu</span>
                </div>
            </div>
        </div>
        
        <!-- Active Deals Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon deals">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="kpi-info">
                        <h6 class="kpi-label">Active Deals</h6>
                        <p class="kpi-period">Saat Ini</p>
                    </div>
                </div>
                <div class="kpi-value"><?= number_format($active_deals) ?></div>
                <div class="kpi-trend <?= getChangeClass($deals_change) ?>">
                    <i class="fas <?= getChangeIcon($deals_change) ?>"></i>
                    <span><?= number_format(abs($deals_change), 1) ?>%</span>
                    <span class="trend-label">dari bulan lalu</span>
                </div>
            </div>
        </div>
        
        <!-- Total Clients Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon clients">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-info">
                        <h6 class="kpi-label">Total Klien</h6>
                        <p class="kpi-period">Database</p>
                    </div>
                </div>
                <div class="kpi-value"><?= number_format($total_clients) ?></div>
                <div class="kpi-trend <?= getChangeClass($clients_change) ?>">
                    <i class="fas <?= getChangeIcon($clients_change) ?>"></i>
                    <span><?= number_format(abs($clients_change), 1) ?>%</span>
                    <span class="trend-label">pertumbuhan</span>
                </div>
            </div>
        </div>
        
        <!-- Conversion Rate Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-icon conversion">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="kpi-info">
                        <h6 class="kpi-label">Conversion Rate</h6>
                        <p class="kpi-period">All Time</p>
                    </div>
                </div>
                <div class="kpi-value"><?= number_format($conversion_rate, 1) ?>%</div>
                <div class="kpi-badge">
                    <?php if ($conversion_rate >= 50): ?>
                        <span class="badge bg-success">Excellent</span>
                    <?php elseif ($conversion_rate >= 30): ?>
                        <span class="badge bg-warning">Good</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Needs Improvement</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Outstanding Payments Alert -->
    <?php if ($outstanding_payments > 0): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
        <div>
            <strong>Outstanding Payments:</strong>
            Anda memiliki <strong><?= formatCurrency($outstanding_payments) ?></strong> pembayaran yang belum lunas.
            <a href="finance.php" class="alert-link ms-2">Lihat Detail</a>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Charts & Recent Activity -->
    <div class="row g-4">
        <!-- Revenue vs Expense Chart -->
        <div class="col-12 col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <div>
                        <h5 class="chart-title">Revenue vs Expense</h5>
                        <p class="chart-subtitle">6 Bulan Terakhir</p>
                    </div>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportChart()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="revenueExpenseChart" height="80"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Deals -->
        <div class="col-12 col-lg-4">
            <div class="activity-card">
                <div class="activity-header">
                    <h5 class="activity-title">Recent Deals</h5>
                    <a href="deals.php" class="activity-link">Lihat Semua</a>
                </div>
                <div class="activity-body">
                    <?php if (mysqli_num_rows($recent_deals) > 0): ?>
                        <div class="activity-list">
                            <?php while ($deal = mysqli_fetch_assoc($recent_deals)): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?= strtolower($deal['current_stage']) ?>">
                                        <?php
                                        $stage_icons = [
                                            'Lead' => 'fa-envelope',
                                            'Qualified' => 'fa-check-circle',
                                            'Proposal' => 'fa-file-alt',
                                            'Negotiation' => 'fa-comments',
                                            'Won' => 'fa-trophy',
                                            'Lost' => 'fa-times-circle'
                                        ];
                                        ?>
                                        <i class="fas <?= $stage_icons[$deal['current_stage']] ?? 'fa-circle' ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6 class="activity-item-title"><?= e($deal['deal_title']) ?></h6>
                                        <p class="activity-item-meta">
                                            <span class="client-name"><?= e($deal['client_name']) ?></span>
                                            <span class="deal-value"><?= formatCurrency($deal['final_value']) ?></span>
                                        </p>
                                        <div class="activity-item-footer">
                                            <span class="badge badge-<?= strtolower($deal['current_stage']) ?>">
                                                <?= e($deal['current_stage']) ?>
                                            </span>
                                            <?php if ($deal['expected_close_date']): ?>
                                                <span class="expected-date">
                                                    <i class="far fa-calendar me-1"></i>
                                                    <?= formatDate($deal['expected_close_date']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-handshake empty-icon"></i>
                            <p class="empty-text">Belum ada deals</p>
                            <a href="deal-form.php" class="btn btn-sm btn-primary">Buat Deal Baru</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="quick-actions">
                <h5 class="section-title mb-3">Quick Actions</h5>
                <div class="action-buttons">
                    <a href="deal-form.php" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Buat Deal Baru</span>
                    </a>
                    <a href="client-form.php" class="action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Tambah Klien</span>
                    </a>
                    <a href="transaction-form.php" class="action-btn">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Catat Transaksi</span>
                    </a>
                    <a href="service-form.php" class="action-btn">
                        <i class="fas fa-box"></i>
                        <span>Tambah Paket Jasa</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Configuration -->
<script>
// Prepare chart data from PHP
const chartData = <?= json_encode($chart_data) ?>;

const labels = chartData.map(item => item.month);
const revenueData = chartData.map(item => parseFloat(item.revenue));
const expenseData = chartData.map(item => parseFloat(item.expense));

// Create Chart
const ctx = document.getElementById('revenueExpenseChart').getContext('2d');
const revenueExpenseChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Revenue',
                data: revenueData,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                borderRadius: 6,
                barThickness: 'flex',
                maxBarThickness: 40
            },
            {
                label: 'Expense',
                data: expenseData,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 2,
                borderRadius: 6,
                barThickness: 'flex',
                maxBarThickness: 40
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 13,
                    weight: '600'
                },
                bodyFont: {
                    size: 12
                },
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                    },
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Export Chart Function
function exportChart() {
    const link = document.createElement('a');
    link.download = 'revenue-expense-chart.png';
    link.href = document.getElementById('revenueExpenseChart').toDataURL();
    link.click();
}
</script>

<style>
/* Dashboard Specific Styles */
.page-header {
    margin-bottom: 1.5rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

/* KPI Cards */
.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    height: 100%;
}

.kpi-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.kpi-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.kpi-icon.revenue {
    background: linear-gradient(135deg, #10B981, #059669);
}

.kpi-icon.deals {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
}

.kpi-icon.clients {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.kpi-icon.conversion {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.kpi-info {
    flex: 1;
}

.kpi-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6B7280;
    margin-bottom: 0.125rem;
}

.kpi-period {
    font-size: 0.75rem;
    color: #9CA3AF;
    margin: 0;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.75rem;
    line-height: 1;
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.kpi-trend.success {
    color: #10B981;
}

.kpi-trend.danger {
    color: #EF4444;
}

.kpi-trend.secondary {
    color: #6B7280;
}

.kpi-trend .trend-label {
    font-weight: 400;
    color: #6B7280;
}

.kpi-badge {
    margin-top: 0.5rem;
}

/* Chart Card */
.chart-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

.chart-subtitle {
    font-size: 0.875rem;
    color: #6B7280;
    margin: 0;
}

.chart-body {
    position: relative;
    height: 300px;
}

/* Activity Card */
.activity-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.activity-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark-color);
    margin: 0;
}

.activity-link {
    font-size: 0.875rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.activity-link:hover {
    text-decoration: underline;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    transition: background 0.2s;
}

.activity-item:hover {
    background: var(--light-color);
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.875rem;
    color: white;
}

.activity-icon.lead {
    background: #9CA3AF;
}

.activity-icon.qualified {
    background: #3B82F6;
}

.activity-icon.proposal {
    background: #8B5CF6;
}

.activity-icon.negotiation {
    background: #F59E0B;
}

.activity-icon.won {
    background: #10B981;
}

.activity-icon.lost {
    background: #EF4444;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-item-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-item-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    color: #6B7280;
    margin-bottom: 0.5rem;
}

.deal-value {
    font-weight: 600;
    color: var(--dark-color);
}

.activity-item-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.badge {
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
}

.badge-lead {
    background: #E5E7EB;
    color: #4B5563;
}

.badge-qualified {
    background: #DBEAFE;
    color: #1E40AF;
}

.badge-proposal {
    background: #EDE9FE;
    color: #6B21A8;
}

.badge-negotiation {
    background: #FEF3C7;
    color: #92400E;
}

.badge-won {
    background: #D1FAE5;
    color: #065F46;
}

.badge-lost {
    background: #FEE2E2;
    color: #991B1B;
}

.expected-date {
    font-size: 0.625rem;
    color: #9CA3AF;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 3rem;
    color: #D1D5DB;
    margin-bottom: 1rem;
}

.empty-text {
    color: #6B7280;
    margin-bottom: 1rem;
}

/* Quick Actions */
.quick-actions {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark-color);
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: var(--light-color);
    border: 2px dashed #D1D5DB;
    border-radius: 10px;
    text-decoration: none;
    color: var(--dark-color);
    transition: all 0.3s;
}

.action-btn:hover {
    background: white;
    border-color: var(--primary-color);
    color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
}

.action-btn i {
    font-size: 2rem;
    margin-bottom: 0.75rem;
}

.action-btn span {
    font-size: 0.875rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .kpi-value {
        font-size: 1.5rem;
    }
    
    .chart-body {
        height: 250px;
    }
    
    .action-buttons {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
