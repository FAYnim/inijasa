<?php
/**
 * Deals List Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$page_title = 'Deals';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

// Get filter parameters
$filter_stage = $_GET['stage'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "
    SELECT 
        d.*,
        c.client_name,
        c.company,
        s.service_name
    FROM deals d
    LEFT JOIN clients c ON d.client_id = c.id
    LEFT JOIN services s ON d.service_id = s.id
    WHERE d.business_id = ?
";

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
$deals = mysqli_stmt_get_result($stmt);

// Get stage counts
$stage_counts = [];
$stages = ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost'];
foreach ($stages as $stage) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM deals WHERE business_id = ? AND current_stage = ?");
    mysqli_stmt_bind_param($stmt, "is", $business_id, $stage);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stage_counts[$stage] = mysqli_fetch_assoc($result)['count'];
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Deals</h2>
            <p class="text-muted mb-0">Kelola semua kesepakatan bisnis Anda</p>
        </div>
        <a href="deal-form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Deal Baru
        </a>
    </div>
    
    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Stage Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-pills stage-filters">
                <li class="nav-item">
                    <a class="nav-link <?= $filter_stage === '' ? 'active' : '' ?>" href="deals.php">
                        Semua <span class="badge bg-secondary ms-1"><?= array_sum($stage_counts) ?></span>
                    </a>
                </li>
                <?php foreach ($stages as $stage): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $filter_stage === $stage ? 'active' : '' ?>" 
                       href="deals.php?stage=<?= urlencode($stage) ?>">
                        <?= $stage ?> <span class="badge bg-secondary ms-1"><?= $stage_counts[$stage] ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    
    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-6">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari deal, klien, atau perusahaan..."
                           value="<?= e($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="stage">
                        <option value="">Semua Stage</option>
                        <?php foreach ($stages as $stage): ?>
                        <option value="<?= $stage ?>" <?= $filter_stage === $stage ? 'selected' : '' ?>>
                            <?= $stage ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Deals Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Deal</th>
                            <th>Klien</th>
                            <th>Paket Jasa</th>
                            <th>Nilai</th>
                            <th>Stage</th>
                            <th>Expected Close</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($deals) > 0): ?>
                            <?php while ($deal = mysqli_fetch_assoc($deals)): ?>
                            <tr>
                                <td>
                                    <strong><?= e($deal['deal_title']) ?></strong>
                                    <?php if ($deal['discount_percent'] > 0): ?>
                                        <br><small class="text-success">
                                            <i class="fas fa-tag"></i> Diskon <?= $deal['discount_percent'] ?>%
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= e($deal['client_name']) ?>
                                    <?php if ($deal['company']): ?>
                                        <br><small class="text-muted"><?= e($deal['company']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($deal['service_name'] ?? '-') ?></td>
                                <td>
                                    <?php if ($deal['discount_percent'] > 0): ?>
                                        <small class="text-muted text-decoration-line-through">
                                            <?= formatCurrency($deal['deal_value']) ?>
                                        </small><br>
                                    <?php endif; ?>
                                    <strong><?= formatCurrency($deal['final_value']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-<?= strtolower($deal['current_stage']) ?>">
                                        <?= e($deal['current_stage']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $deal['expected_close_date'] ? formatDate($deal['expected_close_date']) : '-' ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="deal-form.php?id=<?= $deal['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="deal-detail.php?id=<?= $deal['id'] ?>" 
                                           class="btn btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">
                                        <?php if ($search || $filter_stage): ?>
                                            Tidak ada deal yang sesuai dengan filter.
                                        <?php else: ?>
                                            Belum ada deal. <a href="deal-form.php">Buat deal pertama Anda</a>
                                        <?php endif; ?>
                                    </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stage-filters {
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.stage-filters .nav-link {
    white-space: nowrap;
    border-radius: 8px;
    font-weight: 600;
    color: #6B7280;
    padding: 0.625rem 1rem;
}

.stage-filters .nav-link.active {
    background: var(--primary-color);
    color: white;
}

.stage-filters .nav-link .badge {
    font-size: 0.75rem;
}

.stage-filters .nav-link.active .badge {
    background: rgba(255, 255, 255, 0.3) !important;
}
</style>

<?php include '../includes/footer.php'; ?>
