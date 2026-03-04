<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$metrics = getDashboardMetrics($businessId);
$chartData = getRevenueExpenseChart($businessId, 6);
$dealsByStage = getDealsByStage($businessId);
$recentDeals = getDeals($businessId);
$recentDeals = array_slice($recentDeals, 0, 5);
?>

<div class="main-content">
    <div class="page-header">
        <h2>Dashboard</h2>
        <a href="deal-form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Deal Baru
        </a>
    </div>
    
    <!-- Metrics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-success"><?= formatCurrency($metrics['monthly_revenue']) ?></div>
                        <div class="metric-label">Pendapatan Bulan Ini</div>
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
                        <div class="metric-value"><?= $metrics['active_deals'] ?></div>
                        <div class="metric-label">Deal Aktif</div>
                    </div>
                    <div class="metric-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value"><?= $metrics['total_clients'] ?></div>
                        <div class="metric-label">Total Klien</div>
                    </div>
                    <div class="metric-icon bg-info bg-opacity-10 text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-success"><?= formatCurrency($metrics['total_revenue']) ?></div>
                        <div class="metric-label">Total Pendapatan (Won)</div>
                    </div>
                    <div class="metric-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-warning"><?= $metrics['conversion_rate'] ?>%</div>
                        <div class="metric-label">Konversi Deal</div>
                    </div>
                    <div class="metric-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="metric-value text-danger"><?= formatCurrency($metrics['outstanding_payments']) ?></div>
                        <div class="metric-label">Piutang</div>
                    </div>
                    <div class="metric-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i>Pendapatan vs Pengeluaran (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-filter me-2"></i>Deal by Stage
                </div>
                <div class="card-body">
                    <canvas id="stageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Deals -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Deal Terbaru</span>
            <a href="deals.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body">
            <?php if (count($recentDeals) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Judul Deal</th>
                                <th>Klien</th>
                                <th>Nilai</th>
                                <th>Stage</th>
                                <th>Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDeals as $deal): ?>
                            <tr>
                                <td>
                                    <a href="deal-form.php?id=<?= $deal['id'] ?>"><?= sanitize($deal['deal_title']) ?></a>
                                </td>
                                <td><?= sanitize($deal['client_name'] ?? '-') ?></td>
                                <td><?= formatCurrency($deal['final_value']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $deal['current_stage'] ?>"><?= $deal['current_stage'] ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $deal['payment_status'] ?>"><?= $deal['payment_status'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>Belum Ada Deal</h4>
                    <p>Mulai buat deal pertama Anda</p>
                    <a href="deal-form.php" class="btn btn-primary">Buat Deal</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Revenue vs Expense Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($chartData, 'month')) ?>,
                datasets: [{
                    label: 'Pendapatan',
                    data: <?= json_encode(array_column($chartData, 'income')) ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Pengeluaran',
                    data: <?= json_encode(array_column($chartData, 'expense')) ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Stage Distribution Chart
    const stageCtx = document.getElementById('stageChart');
    if (stageCtx) {
        new Chart(stageCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'],
                datasets: [{
                    data: <?= json_encode(array_values($dealsByStage)) ?>,
                    backgroundColor: [
                        '#6c757d', '#17a2b8', '#ffc107', '#fd7e14', '#28a745', '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
