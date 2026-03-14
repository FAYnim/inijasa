<?php
/**
 * Services List Page
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Paket Jasa';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business');
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $service_id = (int)$_GET['id'];
    
    // Verify ownership
    $check = mysqli_query($conn, "SELECT id FROM services WHERE id = $service_id AND business_id = $business_id");
    if (mysqli_num_rows($check) > 0) {
        // Soft delete
        mysqli_query($conn, "UPDATE services SET is_deleted = 1 WHERE id = $service_id");
        setFlashMessage('success', 'Paket jasa berhasil dihapus.');
    } else {
        setFlashMessage('danger', 'Paket jasa tidak ditemukan.');
    }
    
    redirect('services');
}

// Get filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$query = "SELECT * FROM services WHERE business_id = ? AND is_deleted = 0";
$params = [$business_id];
$types = "i";

if ($search) {
    $query .= " AND (service_name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$services = mysqli_stmt_get_result($stmt);

// Get stats
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status = 'Inactive' THEN 1 ELSE 0 END) as inactive
FROM services 
WHERE business_id = $business_id AND is_deleted = 0";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title mb-1">Paket Jasa</h2>
            <p class="text-muted mb-0"><?= $stats['total'] ?> paket jasa · <?= $stats['active'] ?> aktif · <?= $stats['inactive'] ?> nonaktif</p>
        </div>
        <a href="service-form" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Tambah Paket Jasa
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
                           placeholder="Cari nama paket jasa atau deskripsi..."
                           value="<?= e($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="Active" <?= $status_filter === 'Active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Inactive" <?= $status_filter === 'Inactive' ? 'selected' : '' ?>>Nonaktif</option>
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
    
    <!-- Services Table -->
    <div class="card">
        <div class="card-body">
            <?php if (mysqli_num_rows($services) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:68px;">Gambar</th>
                            <th>Nama Paket</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($service = mysqli_fetch_assoc($services)): ?>
                        <tr>
                            <td>
                                <?php if (!empty($service['image_path']) && file_exists($service['image_path'])): ?>
                                    <img src="<?= e($service['image_path']) ?>"
                                         alt="<?= e($service['service_name']) ?>"
                                         class="service-thumb">
                                <?php else: ?>
                                    <div class="service-thumb-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($service['service_name']) ?></strong>
                            </td>
                            <td>
                                <?php if ($service['description']): ?>
                                    <small class="text-muted">
                                        <?= e(substr($service['description'], 0, 80)) ?>
                                        <?= strlen($service['description']) > 80 ? '...' : '' ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= formatCurrency($service['price']) ?></strong>
                            </td>
                            <td>
                                <?php if ($service['status'] === 'Active'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="service-form?id=<?= $service['id'] ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="services?action=delete&id=<?= $service['id'] ?>" 
                                       class="btn btn-outline-danger"
                                       title="Hapus"
                                       onclick="return confirm('Yakin ingin menghapus paket jasa ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">
                    <?php if ($search || $status_filter): ?>
                        Tidak ada paket jasa yang sesuai dengan pencarian.
                    <?php else: ?>
                        Belum ada paket jasa. Mulai tambahkan paket jasa pertama Anda!
                    <?php endif; ?>
                </p>
                <?php if ($search || $status_filter): ?>
                    <a href="services" class="btn btn-secondary">Reset Filter</a>
                <?php else: ?>
                    <a href="service-form" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Paket Jasa
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
