<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Paket Jasa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$services = getServicePackages($businessId, true);

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $result = deleteServicePackage($_GET['delete']);
    if ($result['success']) {
        $success = 'Paket jasa berhasil dihapus';
    } else {
        $error = $result['message'];
    }
    header('Location: services.php');
    exit;
}
?>

<div class="main-content">
    <div class="page-header">
        <h2>Paket Jasa</h2>
        <a href="service-form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Paket
        </a>
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
    
    <div class="card">
        <div class="card-body">
            <?php if (count($services) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="servicesTable">
                        <thead>
                            <tr>
                                <th>Nama Paket</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                            <tr>
                                <td><strong><?= sanitize($service['package_name']) ?></strong></td>
                                <td><?= sanitize($service['description'] ?? '-') ?></td>
                                <td><?= formatCurrency($service['price']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $service['status'] ?>"><?= $service['status'] ?></span>
                                </td>
                                <td class="actions">
                                    <a href="service-form.php?id=<?= $service['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="services.php?delete=<?= $service['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box"></i>
                    <h4>Belum Ada Paket Jasa</h4>
                    <p>Tambahkan paket jasa untuk memulai</p>
                    <a href="service-form.php" class="btn btn-primary">Tambah Paket</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
