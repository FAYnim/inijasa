<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Profil Bisnis';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$businessId = getCurrentBusinessId();
$business = getBusinessById($businessId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = trim($_POST['business_name'] ?? '');
    $category = $_POST['category'] ?? 'Lainnya';
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($businessName)) {
        $error = 'Nama bisnis wajib diisi';
    } else {
        $data = [
            'business_name' => $businessName,
            'category' => $category,
            'description' => $description,
            'address' => $address,
            'phone' => $phone,
            'email' => $email
        ];
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, ['jpg', 'jpeg', 'png'])) {
                $fileName = 'logo_' . $businessId . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    $data['logo_path'] = '/jasaku/assets/uploads/' . $fileName;
                }
            }
        }
        
        $result = updateBusiness($businessId, $data);
        if ($result['success']) {
            $success = 'Profil bisnis berhasil diupdate';
            $business = getBusinessById($businessId);
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="main-content">
    <div class="page-header">
        <h2>Profil Bisnis</h2>
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
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Nama Bisnis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="business_name" name="business_name" required value="<?= sanitize($business['business_name'] ?? '') ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Kategori Bisnis</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="Kreatif/Desain" <?= ($business['category'] ?? '') === 'Kreatif/Desain' ? 'selected' : '' ?>>Kreatif/Desain</option>
                                    <option value="Konsultan" <?= ($business['category'] ?? '') === 'Konsultan' ? 'selected' : '' ?>>Konsultan</option>
                                    <option value="Kebersihan" <?= ($business['category'] ?? '') === 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
                                    <option value="Perbaikan" <?= ($business['category'] ?? '') === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                                    <option value="Lainnya" <?= ($business['category'] ?? 'Lainnya') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Bisnis</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($business['email'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= sanitize($business['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= sanitize($business['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= sanitize($business['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Bisnis</label>
                            <div class="text-center mb-3">
                                <?php if (!empty($business['logo_path'])): ?>
                                    <img src="<?= sanitize($business['logo_path']) ?>" alt="Logo" class="img-thumbnail" style="max-width: 150px;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; margin: 0 auto;">
                                        <i class="fas fa-image text-muted" style="font-size: 48px;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png">
                            <div class="form-text">Max 2MB, format JPG atau PNG</div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
