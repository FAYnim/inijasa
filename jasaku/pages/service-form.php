<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Paket Jasa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$serviceId = $_GET['id'] ?? null;
$service = $serviceId ? getServicePackageById($serviceId) : null;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = trim($_POST['package_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'Active';
    
    if (empty($packageName)) {
        $error = 'Nama paket wajib diisi';
    } elseif ($price < 0) {
        $error = 'Harga tidak valid';
    } else {
        $data = [
            'package_name' => $packageName,
            'description' => $description,
            'price' => $price,
            'status' => $status
        ];
        
        if ($service) {
            $result = updateServicePackage($serviceId, $data);
            if ($result['success']) {
                $success = 'Paket jasa berhasil diupdate';
                $service = getServicePackageById($serviceId);
            } else {
                $error = $result['message'];
            }
        } else {
            $result = createServicePackage($businessId, $data);
            if ($result['success']) {
                header('Location: services.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>

<div class="main-content">
    <div class="page-header">
        <h2><?= $service ? 'Edit' : 'Tambah' ?> Paket Jasa</h2>
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
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="package_name" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="package_name" name="package_name" required value="<?= sanitize($service['package_name'] ?? $_POST['package_name'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?= sanitize($service['description'] ?? $_POST['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" required min="0" step="0.01" value="<?= sanitize($service['price'] ?? $_POST['price'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?= ($service['status'] ?? $_POST['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= ($service['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="services.php" class="btn btn-outline-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
