<?php
/**
 * Service Form Page (Create/Edit Service Package)
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

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

    // Pre-validate image if provided
    $new_image_file = null;
    $image_upload_error = '';
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024;
        if (!in_array($_FILES['service_image']['type'], $allowed_types)) {
            $image_upload_error = 'Format gambar harus JPG atau PNG.';
        } elseif ($_FILES['service_image']['size'] > $max_size) {
            $image_upload_error = 'Ukuran gambar maksimal 2MB.';
        } else {
            $new_image_file = $_FILES['service_image'];
        }
    }

    // Validation
    if (!empty($image_upload_error)) {
        $error = $image_upload_error;
    } elseif (empty($service_name)) {
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
                // Determine image path for update
                $image_path = $service['image_path'];
                $delete_old_image = false;

                if ($new_image_file) {
                    $uploaded_path = uploadServiceImage($new_image_file, $service_id);
                    if ($uploaded_path) {
                        $delete_old_image = !empty($service['image_path']);
                        $image_path = $uploaded_path;
                    } else {
                        $error = 'Gagal mengupload gambar.';
                    }
                } elseif (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
                    $delete_old_image = !empty($service['image_path']);
                    $image_path = null;
                }

                if (empty($error)) {
                    // Update service
                    $stmt = mysqli_prepare($conn, 
                        "UPDATE services SET 
                            service_name = ?, 
                            description = ?, 
                            price = ?, 
                            status = ?,
                            image_path = ?
                         WHERE id = ? AND business_id = ?"
                    );
                    mysqli_stmt_bind_param($stmt, "ssdssii", 
                        $service_name, $description, $price, $status, $image_path,
                        $service_id, $business_id
                    );
                    
                    if (mysqli_stmt_execute($stmt)) {
                        if ($delete_old_image) {
                            deleteServiceImage($service['image_path']);
                        }
                        setFlashMessage('success', 'Paket jasa berhasil diupdate.');
                        redirect('services.php');
                    } else {
                        $error = 'Gagal mengupdate paket jasa.';
                    }
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
                    $new_service_id = mysqli_insert_id($conn);

                    // Upload image if provided
                    if ($new_image_file) {
                        $uploaded_path = uploadServiceImage($new_image_file, $new_service_id);
                        if ($uploaded_path) {
                            $img_stmt = mysqli_prepare($conn, "UPDATE services SET image_path = ? WHERE id = ?");
                            mysqli_stmt_bind_param($img_stmt, "si", $uploaded_path, $new_service_id);
                            mysqli_stmt_execute($img_stmt);
                        }
                    }

                    setFlashMessage('success', 'Paket jasa berhasil ditambahkan.');
                    redirect('services.php');
                } else {
                    $error = 'Gagal menambahkan paket jasa.';
                }
            }
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
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
                    <form method="POST" action="" enctype="multipart/form-data">
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

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Gambar Paket Jasa</label>
                                <?php
                                $has_current_image = $is_edit && !empty($service['image_path']) && file_exists($service['image_path']);
                                ?>
                                <div class="service-image-upload-area" id="imageUploadArea"
                                     onclick="document.getElementById('service_image').click()">
                                    <div id="imageEmptyState"<?= $has_current_image ? ' class="d-none"' : '' ?>>
                                        <i class="fas fa-image fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-1 small">Klik untuk upload gambar</p>
                                        <p class="text-muted mb-0" style="font-size:11px;">JPG, PNG &middot; Maks 2MB</p>
                                    </div>
                                    <img src="<?= $has_current_image ? e($service['image_path']) : '' ?>"
                                         id="imagePreview" alt="Preview Gambar"
                                         class="service-image-preview<?= $has_current_image ? '' : ' d-none' ?>">
                                </div>
                                <input type="file" id="service_image" name="service_image"
                                       accept="image/jpeg,image/png,image/jpg" class="d-none">
                                <input type="hidden" name="delete_image" id="deleteImageInput" value="0">
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            onclick="event.stopPropagation(); document.getElementById('service_image').click()">
                                        <i class="fas fa-upload me-1"></i>
                                        <span id="uploadBtnText"><?= $has_current_image ? 'Ganti Gambar' : 'Upload Gambar' ?></span>
                                    </button>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm<?= $has_current_image ? '' : ' d-none' ?>"
                                            id="removeImageBtn" onclick="removeServiceImage()">
                                        <i class="fas fa-trash me-1"></i>Hapus Gambar
                                    </button>
                                </div>
                                <small class="form-text text-muted mt-1 d-block">Gambar akan ditampilkan di daftar paket jasa</small>
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

<script>
document.getElementById('service_image').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('imagePreview').src = e.target.result;
        document.getElementById('imagePreview').classList.remove('d-none');
        document.getElementById('imageEmptyState').classList.add('d-none');
        document.getElementById('removeImageBtn').classList.remove('d-none');
        document.getElementById('uploadBtnText').textContent = 'Ganti Gambar';
        document.getElementById('deleteImageInput').value = '0';
    };
    reader.readAsDataURL(file);
});

function removeServiceImage() {
    document.getElementById('imagePreview').src = '';
    document.getElementById('imagePreview').classList.add('d-none');
    document.getElementById('imageEmptyState').classList.remove('d-none');
    document.getElementById('removeImageBtn').classList.add('d-none');
    document.getElementById('service_image').value = '';
    document.getElementById('uploadBtnText').textContent = 'Upload Gambar';
    document.getElementById('deleteImageInput').value = '1';
}
</script>

<?php include 'includes/footer.php'; ?>
