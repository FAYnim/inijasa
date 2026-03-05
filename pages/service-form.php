<?php
/**
 * Service Form Page (Create/Edit Service Package)
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$business_id = getCurrentBusinessId();
if (!$business_id) {
    redirect('setup-business.php');
}

$service_id = $_GET['id'] ?? null;
$is_edit = !empty($service_id);
$page_title = $is_edit ? 'Edit Paket Jasa' : 'Tambah Paket Jasa Baru';

$error = '';
$service = null;

// Load service data if editing
if ($is_edit) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM services WHERE id = ? AND business_id = ? AND is_deleted = 0");
    mysqli_stmt_bind_param($stmt, "ii", $service_id, $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $service = mysqli_fetch_assoc($result);
    
    if (!$service) {
        setFlashMessage('danger', 'Paket jasa tidak ditemukan.');
        redirect('services.php');
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_name = trim($_POST['service_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $status = $_POST['status'] ?? 'Active';
    
    // Validation
    if (empty($service_name)) {
        $error = 'Nama paket jasa wajib diisi.';
    } elseif (empty($price) || !is_numeric($price) || $price < 0) {
        $error = 'Harga harus berupa angka valid dan tidak boleh negatif.';
    } else {
        // Check if service name is unique for this business (excluding current service if editing)
        if ($is_edit) {
            $check_stmt = mysqli_prepare($conn, 
                "SELECT id FROM services WHERE service_name = ? AND business_id = ? AND is_deleted = 0 AND id != ?"
            );
            mysqli_stmt_bind_param($check_stmt, "sii", $service_name, $business_id, $service_id);
        } else {
            $check_stmt = mysqli_prepare($conn, 
                "SELECT id FROM services WHERE service_name = ? AND business_id = ? AND is_deleted = 0"
            );
            mysqli_stmt_bind_param($check_stmt, "si", $service_name, $business_id);
        }
        
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Nama paket jasa sudah digunakan. Gunakan nama yang berbeda.';
        } else {
            if ($is_edit) {
                // Update service
                $stmt = mysqli_prepare($conn, 
                    "UPDATE services SET 
                        service_name = ?, 
                        description = ?, 
                        price = ?, 
                        status = ?
                     WHERE id = ? AND business_id = ?"
                );
                mysqli_stmt_bind_param($stmt, "ssdsii", 
                    $service_name, $description, $price, $status,
                    $service_id, $business_id
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    setFlashMessage('success', 'Paket jasa berhasil diupdate.');
                    redirect('services.php');
                } else {
                    $error = 'Gagal mengupdate paket jasa.';
                }
            } else {
                // Insert new service
                $stmt = mysqli_prepare($conn, 
                    "INSERT INTO services (business_id, service_name, description, price, status) 
                     VALUES (?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt, "issds", 
                    $business_id, $service_name, $description, $price, $status
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    setFlashMessage('success', 'Paket jasa berhasil ditambahkan.');
                    redirect('services.php');
                } else {
                    $error = 'Gagal menambahkan paket jasa.';
                }
            }
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1"><?= $page_title ?></h2>
                    <p class="text-muted mb-0">Lengkapi informasi paket jasa yang Anda tawarkan</p>
                </div>
                <a href="services.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Service Form -->
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="service_name" class="form-label">
                                    Nama Paket Jasa <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="service_name" 
                                       name="service_name" 
                                       placeholder="Contoh: Desain Logo & Brand Identity"
                                       value="<?= e($service['service_name'] ?? '') ?>"
                                       required>
                                <small class="form-text text-muted">
                                    Nama paket harus unik untuk bisnis Anda
                                </small>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="5"
                                          placeholder="Jelaskan detail paket jasa ini, apa saja yang termasuk, berapa lama waktu pengerjaannya, dll."><?= e($service['description'] ?? '') ?></textarea>
                                <small class="form-text text-muted">
                                    Deskripsi yang jelas akan membantu klien memahami nilai paket Anda
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">
                                    Harga <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="price" 
                                           name="price" 
                                           placeholder="0"
                                           step="0.01"
                                           min="0"
                                           value="<?= $service['price'] ?? '' ?>"
                                           required>
                                </div>
                                <small class="form-text text-muted">
                                    Harga standar paket (dapat disesuaikan saat membuat deal)
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" <?= ($service['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Inactive" <?= ($service['status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                                <small class="form-text text-muted">
                                    Hanya paket aktif yang dapat dipilih saat membuat deal
                                </small>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <a href="services.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                <?= $is_edit ? 'Update Paket' : 'Simpan Paket' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($is_edit): ?>
            <!-- Additional Info Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Informasi Tambahan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Dibuat pada:</small>
                            <p class="mb-0"><?= formatDate($service['created_at']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Terakhir diupdate:</small>
                            <p class="mb-0"><?= formatDate($service['updated_at']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
