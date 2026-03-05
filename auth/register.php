<?php
/**
 * Register Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../dashboard.php');
}

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = 'Email sudah terdaftar. Silakan login atau gunakan email lain.';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $full_name, $email, $password_hash);
            
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($conn);
                
                // Set session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $full_name;
                
                // Redirect to business setup
                redirect('../setup-business.php');
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
    <title>Daftar - Jasaku</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #10B981;
            --dark-color: #1F2937;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--primary-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
        }
        
        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        
        .register-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .register-subtitle {
            color: #6B7280;
            margin-bottom: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6B7280;
        }
        
        .password-strength {
            height: 4px;
            background: #E5E7EB;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
        }
        
        .password-strength-weak { width: 33%; background: #EF4444; }
        .password-strength-medium { width: 66%; background: #F59E0B; }
        .password-strength-strong { width: 100%; background: #10B981; }
        
        .btn-register {
            width: 100%;
            padding: 0.875rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            background: var(--primary-color);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }
        
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #E5E7EB;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6B7280;
            font-size: 0.875rem;
            position: relative;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6B7280;
        }
        
        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-home a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-home a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem;
        }
        
        .form-text {
            font-size: 0.875rem;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-logo">
                <div class="logo-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h1 class="register-title">Daftar Akun Baru</h1>
                <p class="register-subtitle">Mulai kelola bisnis Anda dengan Jasaku</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" 
                           class="form-control" 
                           id="full_name" 
                           name="full_name" 
                           placeholder="Nama lengkap Anda"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                           required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="nama@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required>
                    <div class="form-text">Email ini akan digunakan untuk login</div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="position-relative">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Minimal 8 karakter"
                               required>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrength"></div>
                    </div>
                    <div class="form-text">Gunakan kombinasi huruf, angka, dan simbol</div>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                    <div class="position-relative">
                        <input type="password" 
                               class="form-control" 
                               id="password_confirm" 
                               name="password_confirm" 
                               placeholder="Ketik ulang password"
                               required>
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Saya setuju dengan <a href="#" class="text-decoration-none">syarat dan ketentuan</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                </button>
            </form>
            
            <div class="divider">
                <span>atau</span>
            </div>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>
        
        <div class="back-home">
            <a href="../index.php">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('password_confirm');
        
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirm.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password strength meter
        password.addEventListener('input', function() {
            const strengthBar = document.getElementById('passwordStrength');
            const value = this.value;
            
            strengthBar.className = 'password-strength-bar';
            
            if (value.length === 0) {
                strengthBar.style.width = '0';
            } else if (value.length < 6) {
                strengthBar.classList.add('password-strength-weak');
            } else if (value.length < 10) {
                strengthBar.classList.add('password-strength-medium');
            } else {
                strengthBar.classList.add('password-strength-strong');
            }
        });
    </script>
</body>
</html>
