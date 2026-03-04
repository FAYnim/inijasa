<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$error = '';
$success = '';

// Check if user already has a business
if (userHasBusiness(getCurrentUserId())) {
    header('Location: /jasaku/pages/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessName = trim($_POST['business_name'] ?? '');
    $category = $_POST['category'] ?? 'Lainnya';
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validation
    if (empty($businessName)) {
        $error = 'Nama bisnis wajib diisi';
    } else {
        $data = [
            'business_name' => $businessName,
            'category' => $category,
            'description' => $description,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'logo_path' => null
        ];
        
        $result = createBusiness(getCurrentUserId(), $data);
        if ($result['success']) {
            $_SESSION['business_id'] = $result['business_id'];
            header('Location: /jasaku/pages/dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Bisnis - Jasaku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .brand {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <div class="brand d-inline-block">
                        <i class="fas fa-briefcase me-2"></i>Jasaku
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white pt-4 pb-0">
                        <h4 class="mb-1">Setup Bisnis Pertama Anda</h4>
                        <p class="text-muted">Lengkapi informasi bisnis Anda untuk mulai menggunakan Jasaku</p>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= sanitize($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_name" class="form-label">Nama Bisnis <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="business_name" name="business_name" required value="<?= sanitize($_POST['business_name'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori Bisnis</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="Kreatif/Desain" <?= ($_POST['category'] ?? '') === 'Kreatif/Desain' ? 'selected' : '' ?>>Kreatif/Desain</option>
                                        <option value="Konsultan" <?= ($_POST['category'] ?? '') === 'Konsultan' ? 'selected' : '' ?>>Konsultan</option>
                                        <option value="Kebersihan" <?= ($_POST['category'] ?? '') === 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
                                        <option value="Perbaikan" <?= ($_POST['category'] ?? '') === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                                        <option value="Lainnya" <?= ($_POST['category'] ?? 'Lainnya') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Singkat</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= sanitize($_POST['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="2"><?= sanitize($_POST['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?= sanitize($_POST['phone'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Bisnis</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($_POST['email'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="logo" class="form-label">Logo Bisnis</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png">
                                <div class="form-text">Max 2MB, format JPG atau PNG</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Simpan & Mulai
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
