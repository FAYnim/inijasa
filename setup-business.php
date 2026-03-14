<?php
/**
 * Business Setup Page (First-time Setup)
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

require_once 'includes/db.php';
require_once 'includes/functions.php';

// Require login
requireLogin();

$user_id = getCurrentUserId();
$error = '';
$success = '';

// Check if user already has a business
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM businesses WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$has_business = mysqli_fetch_assoc($result)['count'] > 0;

// If already has business, redirect to dashboard
if ($has_business && !isset($_GET['add'])) {
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
        $logo_path = null;
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
                $new_filename = 'logo_' . $user_id . '_' . time() . '.' . $file_extension;
                $logo_path = $upload_dir . $new_filename;
                
                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
                    $error = 'Gagal mengupload logo.';
                    $logo_path = null;
                }
            }
        }
        
        if (empty($error)) {
            // Set as primary if this is first business
            $is_primary = !$has_business ? 1 : 0;
            
            // Insert business
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO businesses (user_id, business_name, category, description, address, phone, email, logo_path, is_primary) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "isssssssi", 
                $user_id, $business_name, $category, $description, $address, $phone, $email, $logo_path, $is_primary
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $business_id = mysqli_insert_id($conn);
                $_SESSION['business_id'] = $business_id;
                
                setFlashMessage('success', 'Bisnis berhasil dibuat! Selamat datang di IniJasa.');
                redirect('dashboard.php');
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Bisnis - IniJasa</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #FF6B35;
            --primary-hover: #E55A2A;
            --dark-color: #0A2342;
        }
        
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--dark-color) 0%, #1B3A5F 100%);
            min-height: 100vh;
            padding: 3rem 1rem;
        }
        
        .setup-container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .setup-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .setup-header {
            background: var(--primary-color);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .setup-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .setup-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .setup-subtitle {
            opacity: 0.95;
            font-size: 1.125rem;
        }
        
        .setup-body {
            padding: 3rem 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-label .required {
            color: #EF4444;
        }
        
        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.15);
        }
        
        .logo-upload {
            border: 2px dashed #D1D5DB;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .logo-upload:hover {
            border-color: var(--primary-color);
            background: #F9FAFB;
        }
        
        .logo-upload-icon {
            font-size: 3rem;
            color: #9CA3AF;
            margin-bottom: 1rem;
        }
        
        .logo-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto;
            display: none;
        }
        
        .logo-preview img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.125rem;
            border-radius: 12px;
            background: var(--primary-color);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.4);
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6B7280;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 4px;
            background: #E5E7EB;
            border-radius: 2px;
        }
        
        .step.active {
            background: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <div class="setup-header">
                <div class="setup-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h1 class="setup-title">Setup Bisnis</h1>
                <p class="setup-subtitle">Lengkapi informasi bisnis Anda untuk melanjutkan</p>
            </div>
            
            <div class="setup-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="step-indicator">
                    <div class="step active"></div>
                    <div class="step"></div>
                    <div class="step"></div>
                </div>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="business_name" class="form-label">
                                Nama Bisnis <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="business_name" 
                                   name="business_name" 
                                   placeholder="Contoh: IniJasa Digital Agency"
                                   value="<?= htmlspecialchars($_POST['business_name'] ?? '') ?>"
                                   required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="category" class="form-label">
                                Kategori Bisnis <span class="required">*</span>
                            </label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Pilih kategori...</option>
                                <option value="Kreatif/Desain" <?= ($_POST['category'] ?? '') === 'Kreatif/Desain' ? 'selected' : '' ?>>Kreatif/Desain</option>
                                <option value="Konsultan" <?= ($_POST['category'] ?? '') === 'Konsultan' ? 'selected' : '' ?>>Konsultan</option>
                                <option value="Kebersihan" <?= ($_POST['category'] ?? '') === 'Kebersihan' ? 'selected' : '' ?>>Kebersihan</option>
                                <option value="Perbaikan" <?= ($_POST['category'] ?? '') === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                                <option value="Lainnya" <?= ($_POST['category'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Deskripsi Singkat</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Ceritakan sedikit tentang bisnis Anda..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="form-text">Opsional, tapi membantu Anda mengingat bisnis ini</div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="phone" class="form-label">Nomor Telepon Bisnis</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   placeholder="08123456789"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">Email Bisnis</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="info@bisnis.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      placeholder="Alamat lengkap bisnis Anda..."><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="col-md-12 mb-4">
                            <label class="form-label">Logo Bisnis</label>
                            <div class="logo-upload" onclick="document.getElementById('logo').click()">
                                <div class="logo-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <p class="mb-1"><strong>Klik untuk upload logo</strong></p>
                                <p class="form-text mb-0">JPG atau PNG, maksimal 2MB</p>
                                <input type="file" 
                                       class="d-none" 
                                       id="logo" 
                                       name="logo" 
                                       accept="image/jpeg,image/png,image/jpg"
                                       onchange="previewLogo(this)">
                            </div>
                            <div class="logo-preview" id="logoPreview">
                                <img src="" alt="Logo Preview" id="logoPreviewImg">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-check-circle me-2"></i>Simpan & Lanjutkan
                            </button>
                        </div>
                    </div>
                </form>
                
                <?php if ($has_business): ?>
                    <div class="text-center mt-3">
                        <a href="dashboard.php" class="text-muted">Lewati & ke Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('logoPreviewImg').src = e.target.result;
                    document.getElementById('logoPreview').style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
