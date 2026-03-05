<?php
/**
 * Landing Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasaku - Platform Manajemen Bisnis Jasa</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4F46E5;
            --secondary-color: #06B6D4;
            --success-color: #10B981;
            --dark-color: #1F2937;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.5;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .navbar-brand-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            padding: 6rem 0 4rem;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .btn-hero-primary {
            background: white;
            color: var(--primary-color);
            border: none;
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: var(--primary-color);
        }
        
        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-hero-outline:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .hero-image {
            position: relative;
            z-index: 1;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-image img {
            max-width: 100%;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
        }
        
        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: #F9FAFB;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .section-subtitle {
            font-size: 1.125rem;
            color: #6B7280;
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-icon-1 { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .feature-icon-2 { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .feature-icon-3 { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .feature-icon-4 { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .feature-icon-5 { background: linear-gradient(135deg, #fa709a, #fee140); color: white; }
        .feature-icon-6 { background: linear-gradient(135deg, #30cfd0, #330867); color: white; }
        
        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
        }
        
        .feature-description {
            color: #6B7280;
            line-height: 1.6;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .cta-subtitle {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1.5rem;
        }
        
        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .footer-description {
            opacity: 0.8;
            margin-bottom: 1.5rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: white;
            opacity: 0.8;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
            text-align: center;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .btn-hero {
                width: 100%;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <div class="navbar-brand-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                Jasaku
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light ms-2" href="auth/register.php">Daftar Gratis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center hero-content">
                <div class="col-lg-6">
                    <h1 class="hero-title">Kelola Bisnis Jasamu Tanpa Ribet</h1>
                    <p class="hero-subtitle">
                        Platform all-in-one untuk mengelola klien, deals, keuangan, dan paket jasa dalam satu tempat. 
                        Dibuat khusus untuk Agensi Jasa dan UMKM di Indonesia.
                    </p>
                    <div class="hero-buttons">
                        <a href="auth/register.php" class="btn btn-hero btn-hero-primary">
                            <i class="fas fa-rocket me-2"></i>Mulai Gratis
                        </a>
                        <a href="#features" class="btn btn-hero btn-hero-outline">
                            <i class="fas fa-play-circle me-2"></i>Lihat Fitur
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-image">
                        <svg width="500" height="400" viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                            <!-- Dashboard mockup -->
                            <rect x="50" y="50" width="400" height="300" rx="10" fill="white" opacity="0.95"/>
                            <rect x="50" y="50" width="400" height="50" rx="10" fill="#4F46E5"/>
                            <circle cx="80" cy="75" r="8" fill="white" opacity="0.5"/>
                            <circle cx="100" cy="75" r="8" fill="white" opacity="0.5"/>
                            <circle cx="120" cy="75" r="8" fill="white" opacity="0.5"/>
                            
                            <!-- KPI Cards -->
                            <rect x="70" y="120" width="85" height="70" rx="5" fill="#10B981" opacity="0.8"/>
                            <rect x="165" y="120" width="85" height="70" rx="5" fill="#3B82F6" opacity="0.8"/>
                            <rect x="260" y="120" width="85" height="70" rx="5" fill="#8B5CF6" opacity="0.8"/>
                            <rect x="355" y="120" width="85" height="70" rx="5" fill="#F59E0B" opacity="0.8"/>
                            
                            <!-- Chart -->
                            <rect x="70" y="210" width="260" height="120" rx="5" fill="#F3F4F6"/>
                            <polyline points="90,300 120,280 150,290 180,260 210,270 240,250 270,260 300,240" 
                                      fill="none" stroke="#10B981" stroke-width="3"/>
                            
                            <!-- List -->
                            <rect x="345" y="210" width="95" height="30" rx="3" fill="#F3F4F6"/>
                            <rect x="345" y="250" width="95" height="30" rx="3" fill="#F3F4F6"/>
                            <rect x="345" y="290" width="95" height="30" rx="3" fill="#F3F4F6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Fitur Lengkap untuk Bisnis Jasamu</h2>
            <p class="section-subtitle">Semua yang kamu butuhkan untuk mengelola bisnis jasa dalam satu platform</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-1">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="feature-title">Deal Pipeline</h3>
                        <p class="feature-description">
                            Track semua kesepakatan dari lead sampai closing dengan pipeline yang jelas dan mudah dipahami.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Manajemen Klien</h3>
                        <p class="feature-description">
                            Database klien lengkap dengan histori transaksi, catatan, dan informasi kontak yang terorganisir.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3 class="feature-title">Pencatatan Keuangan</h3>
                        <p class="feature-description">
                            Catat income dan expense dengan mudah, track pembayaran, dan lihat laporan keuangan real-time.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-4">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3 class="feature-title">Paket Jasa</h3>
                        <p class="feature-description">
                            Buat dan kelola berbagai paket jasa dengan harga, deskripsi, dan status yang fleksibel.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-5">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Dashboard Analytics</h3>
                        <p class="feature-description">
                            Visualisasi data bisnis dengan metrics penting dan chart yang mudah dipahami.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-6">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <h3 class="feature-title">Promo & Diskon</h3>
                        <p class="feature-description">
                            Berikan diskon khusus untuk deal tertentu dan track dampaknya terhadap revenue.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Siap Mengembangkan Bisnis Jasamu?</h2>
            <p class="cta-subtitle">Daftar sekarang dan kelola bisnis dengan lebih profesional</p>
            <a href="auth/register.php" class="btn btn-hero btn-hero-primary btn-lg">
                <i class="fas fa-rocket me-2"></i>Mulai Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">Jasaku</div>
                    <p class="footer-description">
                        Platform manajemen bisnis jasa yang membantu agensi dan UMKM mengelola operasional dengan lebih efisien.
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Produk</h5>
                    <ul class="footer-links">
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#">Harga</a></li>
                        <li><a href="#">Tutorial</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Perusahaan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Bantuan</h5>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Dukungan</a></li>
                        <li><a href="#">Privasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; 2026 Jasaku. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
