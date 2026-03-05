<?php
/**
 * Clients List Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Klien';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$source_filter = $_GET['source'] ?? '';

// Build query
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
$clients = mysqli_stmt_get_result($stmt);

// Get total clients
$total_clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM clients WHERE business_id = $business_id"))['count'];

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Klien</h2>
            <p class="text-muted mb-0">Total <?= $total_clients ?> klien terdaftar</p>
        </div>
        <a href="client-form.php" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Tambah Klien
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
    
    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-6">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari nama klien, perusahaan, atau email..."
                           value="<?= e($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="source">
                        <option value="">Semua Sumber</option>
                        <option value="Referral" <?= $source_filter === 'Referral' ? 'selected' : '' ?>>Referral</option>
                        <option value="Social Media" <?= $source_filter === 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                        <option value="Direct" <?= $source_filter === 'Direct' ? 'selected' : '' ?>>Direct</option>
                        <option value="Website" <?= $source_filter === 'Website' ? 'selected' : '' ?>>Website</option>
                        <option value="Lainnya" <?= $source_filter === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
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
    
    <!-- Clients Grid -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($clients) > 0): ?>
            <?php while ($client = mysqli_fetch_assoc($clients)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="client-avatar me-3">
                                <?= strtoupper(substr($client['client_name'], 0, 2)) ?>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1"><?= e($client['client_name']) ?></h5>
                                <?php if ($client['company']): ?>
                                    <p class="text-muted mb-0"><small><?= e($client['company']) ?></small></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="client-info">
                            <?php if ($client['email']): ?>
                                <p class="mb-2">
                                    <i class="fas fa-envelope text-muted me-2"></i>
                                    <small><?= e($client['email']) ?></small>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($client['phone']): ?>
                                <p class="mb-2">
                                    <i class="fas fa-phone text-muted me-2"></i>
                                    <small><?= e($client['phone']) ?></small>
                                </p>
                            <?php endif; ?>
                            
                            <p class="mb-2">
                                <i class="fas fa-tag text-muted me-2"></i>
                                <span class="badge bg-light text-dark"><?= e($client['source']) ?></span>
                            </p>
                        </div>
                        
                        <div class="d-flex gap-2 mt-3">
                            <a href="client-form.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="deal-form.php?client_id=<?= $client['id'] ?>" class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-plus me-1"></i>Buat Deal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">
                            <?php if ($search || $source_filter): ?>
                                Tidak ada klien yang sesuai dengan filter.
                            <?php else: ?>
                                Belum ada klien. <a href="client-form.php">Tambah klien pertama Anda</a>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.client-avatar {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--primary-color), #764ba2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
}

.client-info i {
    width: 20px;
}
</style>

<?php include 'includes/footer.php'; ?>
