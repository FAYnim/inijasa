<?php
/**
 * Business Profile Edit Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$page_title = 'Profil Bisnis';
$business_id = getCurrentBusinessId();

if (!$business_id) {
    redirect('setup-business.php');
}

$error = '';

// Load current business data
$stmt = mysqli_prepare($conn, "SELECT * FROM businesses WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $business_id);
mysqli_stmt_execute($stmt);
$business = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$business) {
    setFlashMessage('danger', 'Bisnis tidak ditemukan.');
    redirect('dashboard.php');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_name = trim($_POST['business_name'] ?? '');
    $category = $_POST['category'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validation
    if (empty($business_name)) {
        $error = 'Nama bisnis wajib diisi.';
    } elseif (empty($category)) {
        $error = 'Kategori bisnis wajib dipilih.';
    } else {
        // Handle logo upload
        $logo_path = $business['logo_path'];
        $delete_old_logo = false;
        
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['logo']['type'], $allowed_types)) {
                $error = 'Format logo harus JPG atau PNG.';
            } elseif ($_FILES['logo']['size'] > $max_size) {
                $error = 'Ukuran logo maksimal 2MB.';
            } else {
                $upload_dir = 'assets/uploads/logos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $new_filename = 'logo_' . $business_id . '_' . time() . '.' . $file_extension;
                $new_logo_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $new_logo_path)) {
                    $delete_old_logo = true;
                    $logo_path = $new_logo_path;
                } else {
                    $error = 'Gagal mengupload logo.';
                }
            }
        }
        
        if (empty($error)) {
            // Update business
            $stmt = mysqli_prepare($conn, 
                "UPDATE businesses SET 
                    business_name = ?, 
                    category = ?, 
                    description = ?, 
                    address = ?, 
                    phone = ?, 
                    email = ?, 
                    logo_path = ?
                 WHERE id = ?"
            );
            mysqli_stmt_bind_param($stmt, "sssssssi", 
                $business_name, $category, $description, $address, $phone, $email, $logo_path,
                $business_id
            );
            
            if (mysqli_stmt_execute($stmt)) {
                // Delete old logo if new one was uploaded
                if ($delete_old_logo && !empty($business['logo_path']) && file_exists($business['logo_path'])) {
                    unlink($business['logo_path']);
                }
                
                setFlashMessage('success', 'Profil bisnis berhasil diupdate.');
                redirect('business-profile.php');
            } else {
                $error = 'Terjadi kesalahan saat mengupdate profil bisnis.';
            }
        }
    }
    
    // Reload business data to show updated info
    if (empty($error)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM businesses WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $business_id);
        mysqli_stmt_execute($stmt);
        $business = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Profil Bisnis</h2>
                    <p class="text-muted mb-0">Kelola informasi bisnis Anda</p>
                </div>
            </div>
            
            <?php
            $flash = getFlashMessage();
            if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="row g-4">
                <!-- Business Logo Card -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center p-4">
                            <h6 class="card-title mb-4">Logo Bisnis</h6>
                            
                            <div class="mb-4">
                                <?php if (!empty($business['logo_path']) && file_exists($business['logo_path'])): ?>
                                    <img src="<?= e($business['logo_path']) ?>" 
                                         alt="Business Logo" 
                                         class="img-fluid rounded-3 shadow-sm mb-3"
                                         style="max-width: 200px; max-height: 200px;">
                                <?php else: ?>
                                    <div class="business-logo-placeholder mb-3">
                                        <i class="fas fa-building"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="business-info">
                                <h5 class="mb-1"><?= e($business['business_name']) ?></h5>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-tag me-2"></i><?= e($business['category']) ?>
                                </p>
                                
                                <hr class="my-3">
                                
                                <div class="text-start">
                                    <small class="text-muted d-block mb-1">Dibuat pada:</small>
                                    <p class="mb-2"><?= formatDate($business['created_at']) ?></p>
                                    
                                    <small class="text-muted d-block mb-1">Terakhir diupdate:</small>
                                    <p class="mb-0"><?= formatDate($business['updated_at']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Business Form Card -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Edit Informasi Bisnis
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="business_name" class="form-label">
                                            Nama Bisnis <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="business_name" 
                                               name="business_name" 
                                               placeholder="Nama bisnis Anda"
                                               value="<?= e($business['business_name']) ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="category" class="form-label">
                                            Kategori Bisnis <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Pilih kategori...</option>
                                            <option value="Kreatif/Desain" <?= $business['category'] === 'Kreatif/Desain' ? 'selected' : '' ?>>Kreatif/Desain</option>
                                            <option value="Konsultan" <?= $business['category'] === 'Konsultan' ? 'selected' : '' ?>>Konsultan</option>
                                            <option value="Kebersihan" <?= $business['category'] === 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
                                            <option value="Perbaikan" <?= $business['category'] === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                                            <option value="Lainnya" <?= $business['category'] === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="description" class="form-label">Deskripsi Singkat</label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3"
                                                  placeholder="Jelaskan bisnis Anda dalam beberapa kalimat..."><?= e($business['description']) ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="address" class="form-label">Alamat</label>
                                        <textarea class="form-control" 
                                                  id="address" 
                                                  name="address" 
                                                  rows="2"
                                                  placeholder="Alamat lengkap bisnis"><?= e($business['address']) ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               placeholder="08123456789"
                                               value="<?= e($business['phone']) ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Bisnis</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               placeholder="email@bisnis.com"
                                               value="<?= e($business['email']) ?>">
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="logo" class="form-label">Logo Bisnis</label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="logo" 
                                               name="logo" 
                                               accept="image/jpeg,image/png,image/jpg">
                                        <small class="form-text text-muted">
                                            Format: JPG, PNG. Maksimal 2MB. 
                                            <?php if (!empty($business['logo_path'])): ?>
                                                Upload file baru untuk mengganti logo saat ini.
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="d-flex justify-content-between">
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Statistik Bisnis
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get business stats
                    $stats_query = "SELECT 
                        (SELECT COUNT(*) FROM clients WHERE business_id = $business_id) as total_clients,
                        (SELECT COUNT(*) FROM deals WHERE business_id = $business_id) as total_deals,
                        (SELECT COUNT(*) FROM services WHERE business_id = $business_id AND is_deleted = 0) as total_services,
                        (SELECT COUNT(*) FROM transactions WHERE business_id = $business_id) as total_transactions";
                    $biz_stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
                    ?>
                    
                    <div class="row text-center">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="border-end">
                                <h3 class="text-primary mb-1"><?= $biz_stats['total_clients'] ?></h3>
                                <small class="text-muted">Total Klien</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="border-end">
                                <h3 class="text-success mb-1"><?= $biz_stats['total_deals'] ?></h3>
                                <small class="text-muted">Total Deal</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="border-end">
                                <h3 class="text-info mb-1"><?= $biz_stats['total_services'] ?></h3>
                                <small class="text-muted">Paket Jasa</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning mb-1"><?= $biz_stats['total_transactions'] ?></h3>
                            <small class="text-muted">Transaksi Keuangan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.business-logo-placeholder {
    width: 200px;
    height: 200px;
    margin: 0 auto;
    background: var(--primary-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
}
</style>

<?php include 'includes/footer.php'; ?>
