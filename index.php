<?php
/**
 * Landing Page Business
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasaku - Platform Manajemen Bisnis Jasa All-in-One</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white py-3 shadow-sm" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand fw-bold text-primary fs-3" href="#">
                Jasaku
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#bento">Kelebihan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#pricing">Harga</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#faq">FAQ</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <a href="auth/login.php" class="text-decoration-none fw-medium text-dark px-3 py-2 btn-login">Masuk</a>
                    <a href="auth/register.php" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Coba Gratis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section pt-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row pt-5 align-items-center">
                <div class="col-lg-6 hero-content pe-lg-5 mb-5 mb-lg-0">
                    <h1 class="display-3 fw-bold hero-title mb-4">
                        Kelola bisnis jasa lebih mudah, otomatis tanpa ribet.
                    </h1>
                    <p class="hero-subtitle mb-5 text-secondary fs-5">
                        Dapatkan pembayaran lebih awal, simpan otomatis seluruh pendapatan Anda. Dari database klien, pipeline sales, hingga keuangan dalam satu dashboard.
                    </p>
                    <div class="hero-cta mb-5 pb-3">
                        <div class="input-group input-group-lg hero-input-group shadow-sm p-1 rounded-pill bg-white border border-light-subtle">
                            <input type="email" class="form-control border-0 bg-transparent ps-4" placeholder="Alamat email Anda">
                            <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" type="button" onclick="window.location.href='auth/register.php'">Mulai Gratis</button>
                        </div>
                    </div>
                    
                    <div class="hero-logos mt-4">
                        <p class="text-muted small fw-semibold mb-3 tracking-widest text-uppercase">Terintegrasi Dengan</p>
                        <div class="d-flex gap-4 align-items-center flex-wrap opacity-50 hero-partner-logos">
                            <i class="fab fa-whatsapp fs-2"></i>
                            <i class="fab fa-google fs-2"></i>
                            <i class="fas fa-money-bill-wave fs-2"></i>
                            <i class="fas fa-calendar-alt fs-2"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 position-relative hero-visual mt-5 mt-lg-0 d-none d-md-block">
                    <div class="hero-graphic position-relative w-100 ps-lg-5">
                        <div class="hero-blob"></div>
                        
                        <!-- Main Dashboard Card -->
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative z-1 mb-4 hero-card-main ms-auto" style="max-width: 450px;">
                            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="bg-primary text-white p-2 rounded align-items-center d-flex justify-content-center" style="width:32px; height:32px;"><i class="fas fa-chart-line fs-6"></i></div>
                                    <span class="fw-semibold text-dark fs-6">Total Pendapatan</span>
                                </div>
                                <h2 class="fw-bold fs-1 mb-0 text-dark">Rp 128.500.000</h2>
                                <p class="text-success small fw-medium mt-1"><i class="fas fa-arrow-up"></i> 12.5% dibanding bulan lalu</p>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <svg class="img-fluid rounded w-100" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="paint0_linear" x1="200" y1="0" x2="200" y2="200" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#0d6efd" stop-opacity="0.25"/>
                                            <stop offset="1" stop-color="#0d6efd" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M0 160H400" stroke="#F1F5F9" stroke-width="1.5" stroke-dasharray="4 4"/>
                                    <path d="M0 120H400" stroke="#F1F5F9" stroke-width="1.5" stroke-dasharray="4 4"/>
                                    <path d="M0 80H400" stroke="#F1F5F9" stroke-width="1.5" stroke-dasharray="4 4"/>
                                    <path d="M0 40H400" stroke="#F1F5F9" stroke-width="1.5" stroke-dasharray="4 4"/>
                                    <path d="M0 150 C 40 150, 70 130, 100 120 C 130 110, 160 50, 200 80 C 240 110, 270 60, 300 70 C 340 80, 370 20, 400 20 L 400 200 L 0 200 Z" fill="url(#paint0_linear)"/>
                                    <path d="M0 150 C 40 150, 70 130, 100 120 C 130 110, 160 50, 200 80 C 240 110, 270 60, 300 70 C 340 80, 370 20, 400 20" stroke="#0d6efd" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="100" cy="120" r="4.5" fill="white" stroke="#0d6efd" stroke-width="2.5"/>
                                    <circle cx="200" cy="80" r="4.5" fill="white" stroke="#0d6efd" stroke-width="2.5"/>
                                    <circle cx="300" cy="70" r="4.5" fill="white" stroke="#0d6efd" stroke-width="2.5"/>
                                    <circle cx="400" cy="20" r="6.5" fill="#0d6efd" stroke="white" stroke-width="3"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Floating Notification Card -->
                        <div class="card border-0 shadow-lg px-4 py-3 rounded-4 position-absolute hero-card-float" style="width: max-content;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                    <i class="fas fa-check fs-5"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold text-dark fs-6">Invoice Lunas</p>
                                    <p class="mb-0 text-muted small fw-medium">PT. Inovasi (Rp 15.000.000)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section / Light rounded block -->
    <section class="features-section py-5 my-5" id="features">
        <div class="container px-4 px-lg-5">
            <div class="bg-light feature-wrapper p-4 p-md-5">
                <div class="text-center mb-5 pb-3">
                    <p class="text-primary fw-semibold small tracking-widest text-uppercase">Fitur Unggulan</p>
                    <h2 class="display-6 fw-bold text-dark mb-3">Pengalaman yang tumbuh sesuai skala Anda.</h2>
                    <p class="text-muted fs-5 max-w-2xl mx-auto">Sistem terintegrasi mulai dari menerima klien hingga mengelola neraca laba-rugi bisnis.</p>
                </div>
                <div class="row g-4 g-lg-5 text-center text-md-start">
                    <div class="col-md-4 feature-box">
                        <div class="feature-icon mb-4 fs-1">
                            <i class="fas fa-box text-primary"></i>
                        </div>
                        <h4 class="fw-bold fs-5 text-dark mb-3">Manajemen Paket Jasa</h4>
                        <p class="text-muted fw-medium">Buat dan kelola service packages dengan harga transparan, memudahkan penawaran & pembuatan invoice.</p>
                    </div>
                    <div class="col-md-4 feature-box">
                        <div class="feature-icon mb-4 fs-1">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <h4 class="fw-bold fs-5 text-dark mb-3">Database Klien Lengkap</h4>
                        <p class="text-muted fw-medium">CRM sederhana untuk mengelola profil, history dan catatan per klien secara terstruktur dan rapi.</p>
                    </div>
                    <div class="col-md-4 feature-box">
                        <div class="feature-icon mb-4 fs-1">
                            <i class="fas fa-shield-alt text-primary"></i>
                        </div>
                        <h4 class="fw-bold fs-5 text-dark mb-3">Keamanan Optimal</h4>
                        <p class="text-muted fw-medium">Data proyek dan keuangan Anda dicatat dengan aman dan bisa diexport kapan saja untuk backup.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Grid Section -->
    <section class="bento-section py-5 my-5" id="bento">
        <div class="container px-4 px-lg-5">
            <div class="text-center mb-5 pb-3">
                <p class="text-primary fw-semibold small tracking-widest text-uppercase">Statistik & Kinerja</p>
                <h2 class="display-5 fw-bold text-dark">Mengapa mereka memilih Jasaku</h2>
            </div>
            
            <div class="bento-grid">
                <!-- Large Top Left -->
                <div class="bento-card bg-light p-5 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold text-primary mb-3" style="font-size: 5rem; letter-spacing: -0.05em;">100+</h2>
                    <h4 class="fw-bold text-dark mb-0 fs-3">Bisnis jasa sudah beroperasi di Jasaku</h4>
                </div>
                
                <!-- Top Right Square -->
                <div class="bento-card bg-light p-5 position-relative flex-column d-flex justify-content-center">
                    <h4 class="fw-bold text-dark mb-4 fs-4 position-relative z-1">Monitor Profitabilitas<br>secara Realtime</h4>
                    <div class="bento-visual mt-auto">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary shadow text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px;"><i class="fas fa-chart-line fs-4"></i></div>
                            <div class="flex-grow-1 border-top border-2 border-dashed border-secondary opacity-50"></div>
                            <div class="bg-dark shadow text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px;"><i class="fas fa-wallet fs-4"></i></div>
                        </div>
                    </div>
                </div>
                
                <!-- Bottom Span Full -->
                <div class="bento-card bento-wide bg-light p-0 d-flex flex-column flex-md-row align-items-center overflow-hidden">
                    <div class="p-5 flex-grow-1">
                        <h4 class="fw-bold text-dark mb-3 fs-3">Tanpa kehilangan momentum</h4>
                        <p class="text-muted fs-5 fw-medium mb-0 max-w-md">Kendalikan deal pipeline Anda secara mulus. Pantau tahapan setiap transaksi dari prospek awal hingga selesai dibayar.</p>
                    </div>
                    <div class="flex-shrink-0 bento-bottom-img text-center pt-5 pt-md-0 pe-md-5 w-100" style="max-width: 500px;">
                        <svg class="img-fluid bento-image shadow-sm w-100" viewBox="0 0 800 400" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-top-left-radius: 12px; border-top-right-radius: 12px; border: 1px solid rgba(0,0,0,0.05); border-bottom:0; background: #ffffff;">
                            <defs>
                                <linearGradient id="bar_gradient" x1="0" y1="0" x2="0" y2="360" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#0d6efd"/>
                                    <stop offset="1" stop-color="#0d6efd" stop-opacity="0.4"/>
                                </linearGradient>
                            </defs>
                            <path d="M50 360H770" stroke="#E2E8F0" stroke-width="2"/>
                            <path d="M50 280H770" stroke="#F1F5F9" stroke-width="2" stroke-dasharray="6 6"/>
                            <path d="M50 200H770" stroke="#F1F5F9" stroke-width="2" stroke-dasharray="6 6"/>
                            <path d="M50 120H770" stroke="#F1F5F9" stroke-width="2" stroke-dasharray="6 6"/>
                            <path d="M50 40H770" stroke="#F1F5F9" stroke-width="2" stroke-dasharray="6 6"/>
                            
                            <text x="40" y="365" font-family="Inter, sans-serif" font-size="13" fill="#94A3B8" text-anchor="end">0</text>
                            <text x="40" y="285" font-family="Inter, sans-serif" font-size="13" fill="#94A3B8" text-anchor="end">25M</text>
                            <text x="40" y="205" font-family="Inter, sans-serif" font-size="13" fill="#94A3B8" text-anchor="end">50M</text>
                            <text x="40" y="125" font-family="Inter, sans-serif" font-size="13" fill="#94A3B8" text-anchor="end">75M</text>
                            <text x="40" y="45" font-family="Inter, sans-serif" font-size="13" fill="#94A3B8" text-anchor="end">100M</text>
                            
                            <rect x="90" y="240" width="48" height="120" rx="6" fill="#E2E8F0"/>
                            <rect x="186" y="200" width="48" height="160" rx="6" fill="#E2E8F0"/>
                            <rect x="282" y="160" width="48" height="200" rx="6" fill="#E2E8F0"/>
                            <rect x="378" y="140" width="48" height="220" rx="6" fill="#E2E8F0"/>
                            <rect x="474" y="90" width="48" height="270" rx="6" fill="#E2E8F0"/>
                            <rect x="570" y="70" width="48" height="290" rx="6" fill="#E2E8F0"/>
                            <rect x="666" y="30" width="48" height="330" rx="6" fill="url(#bar_gradient)"/>
                        
                            <path d="M114 200 L 210 160 L 306 140 L 402 100 L 498 70 L 594 40 L 690 10" stroke="#F59E0B" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            
                            <rect x="625" y="15" width="130" height="44" rx="8" fill="#1E293B" opacity="0.95"/>
                            <text x="690" y="43" font-family="Inter, sans-serif" font-size="16" font-weight="600" fill="white" text-anchor="middle">Rp 92.5M</text>
                            <circle cx="690" cy="10" r="7" fill="#F59E0B" stroke="white" stroke-width="3"/>
                            
                            <text x="114" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Jan</text>
                            <text x="210" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Feb</text>
                            <text x="306" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Mar</text>
                            <text x="402" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Apr</text>
                            <text x="498" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Mei</text>
                            <text x="594" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="500" fill="#64748B" text-anchor="middle">Jun</text>
                            <text x="690" y="390" font-family="Inter, sans-serif" font-size="14" font-weight="700" fill="#0d6efd" text-anchor="middle">Jul</text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dark Steps Section (How it works) -->
    <section class="dark-steps-section py-5 my-5 text-white bg-primary" id="how-it-works">
        <div class="container px-4 px-lg-5 py-5">
            <div class="row mb-5 pb-3">
                <div class="col-lg-6">
                    <p class="text-white fw-semibold small tracking-widest text-uppercase opacity-75">Cara Kerja</p>
                    <h2 class="display-5 fw-bold text-white lh-sm">Maksimalkan keuntungan dengan manajemen terpusat.</h2>
                </div>
            </div>
            
            <div class="row g-4 steps-row">
                <div class="col-md-4">
                    <div class="step-card-dark p-4 p-lg-5 h-100 position-relative overflow-hidden group hover-effect">
                        <div class="step-bg d-none d-lg-block"></div>
                        <h1 class="display-3 fw-bold mb-4 opacity-25 font-monospace z-1 position-relative text-white">1</h1>
                        <h4 class="fw-bold text-white mb-3 fs-3 z-1 position-relative">Setup akun</h4>
                        <p class="text-white-50 fw-medium mb-0 z-1 position-relative fs-6 lh-lg">Daftar secara gratis lalu sesuaikan profil bisnis dan daftar paket jasa yang Anda sediakan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card-dark p-4 p-lg-5 h-100 position-relative overflow-hidden group hover-effect">
                        <div class="step-bg d-none d-lg-block"></div>
                        <h1 class="display-3 fw-bold mb-4 opacity-25 font-monospace z-1 position-relative text-white">2</h1>
                        <h4 class="fw-bold text-white mb-3 fs-3 z-1 position-relative">Kelola deal</h4>
                        <p class="text-white-50 fw-medium mb-0 z-1 position-relative fs-6 lh-lg">Buat penawaran baru dan pindahkan status secara mudah dari "Prospek" menjadi "Deal Won".</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card-dark p-4 p-lg-5 h-100 position-relative overflow-hidden group hover-effect">
                        <div class="step-bg d-none d-lg-block"></div>
                        <h1 class="display-3 fw-bold mb-4 opacity-25 font-monospace z-1 position-relative text-white">3</h1>
                        <h4 class="fw-bold text-white mb-3 fs-3 z-1 position-relative">Pantau hasil</h4>
                        <p class="text-white-50 fw-medium mb-0 z-1 position-relative fs-6 lh-lg">Setiap invoice yang lunas otomatis masuk ke pencatatan transaksi masuk bisnis Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission / Trust Section -->
    <section class="mission-section py-5 my-5">
        <div class="container px-4 px-lg-5 text-center">
            <p class="text-primary fw-semibold small tracking-widest text-uppercase mb-3">Visi Kami</p>
            <h2 class="display-5 fw-bold text-dark mb-4">Kami mendukung perusahaan jasa bertumbuh</h2>
            <p class="text-muted fs-5 mx-auto mb-5 fw-medium" style="max-width: 650px;">
                Fokus Jasaku adalah memudahkan operasional pengelola agensi, konsultan, dan penyedia UMKM jasa agar bisa lebih fokus mengekskalasi bisnis.
            </p>
            
            <div class="row justify-content-center g-4 mt-2 stat-row">
                <div class="col-6 col-md-3">
                    <h2 class="display-4 fw-bold text-dark mb-1">24%</h2>
                    <p class="text-muted fw-semibold mb-0 font-monospace small">Potensi Pertumbuhan</p>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="display-4 fw-bold text-dark mb-1">150K</h2>
                    <p class="text-muted fw-semibold mb-0 font-monospace small">Transaksi Dicatat</p>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="display-4 fw-bold text-dark mb-1">10+</h2>
                    <p class="text-muted fw-semibold mb-0 font-monospace small">Bulan Dukungan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section py-5 my-5 bg-light" id="pricing">
        <div class="container px-4 px-lg-5 py-5">
            <div class="text-center mb-5 pb-4">
                <p class="text-primary fw-semibold small tracking-widest text-uppercase">Pilih Paket</p>
                <h2 class="display-5 fw-bold text-dark">Harga bersahabat,<br>untuk semua kalangan</h2>
            </div>
            
            <div class="row justify-content-center g-4 g-lg-5 align-items-center px-lg-5">
                <div class="col-md-5">
                    <div class="card pricing-card bg-white border-0 p-4 p-lg-5 h-100 d-flex flex-column hover-lift">
                        <div class="mb-5">
                            <h3 class="fw-bold fs-3 text-dark mb-3">Starter</h3>
                            <div class="price-block d-flex align-items-baseline gap-2 mb-4">
                                <span class="fs-1 fw-bold text-dark">Rp0</span>
                                <span class="text-muted fw-medium">/bulan</span>
                            </div>
                            <p class="text-muted fw-medium">Sempurna untuk freelancer atau yang baru memulai bisnis jasa.</p>
                        </div>
                        <a href="auth/register.php" class="text-decoration-none fw-bold fs-5 text-dark mt-auto d-flex align-items-center gap-2 group mb-4">
                            Mulai Gratis <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </a>
                        <ul class="list-unstyled text-muted fw-medium fs-6 d-flex flex-column gap-3 mb-0 pt-4 border-top">
                            <li><i class="fas fa-check text-dark me-3 opacity-75"></i>1 lisensi bisnis</li>
                            <li><i class="fas fa-check text-dark me-3 opacity-75"></i>Maksimal 50 klien</li>
                            <li><i class="fas fa-check text-dark me-3 opacity-75"></i>Fitur Pipeline & Laporan Standar</li>
                            <li class="opacity-50 text-decoration-line-through"><i class="fas fa-times text-dark me-3"></i>Priority Support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5">
                    <!-- Premium card -->
                    <div class="card pricing-card pricing-premium border-0 p-4 p-lg-5 h-100 d-flex flex-column position-relative overflow-hidden shadow-lg hover-lift group">
                        <!-- BG accent blob -->
                        <div class="position-absolute end-0 bottom-0 ms-auto blob-premium"></div>
                        
                        <div class="mb-5 position-relative z-1">
                            <span class="badge bg-white text-secondary px-3 py-2 rounded-pill fw-bold small text-uppercase mb-3 shadow-sm d-inline-block">Paling Diminati</span>
                            <h3 class="fw-bold fs-3 text-white mb-3">Premium</h3>
                            <div class="price-block d-flex align-items-baseline gap-2 mb-4">
                                <span class="fs-1 fw-bold text-white">Rp99k</span>
                                <span class="text-white-50 fw-medium">/bulan</span>
                            </div>
                            <p class="text-white-50 fw-medium">Cocok untuk agensi dengan tim dan proyek tak terbatas.</p>
                        </div>
                        <a href="auth/register.php" class="text-decoration-none fw-bold fs-5 text-white mt-auto d-flex align-items-center gap-2 mb-4 position-relative z-1 btn-premium-cta">
                            Coba Premium <i class="fas fa-arrow-right"></i>
                        </a>
                        <ul class="list-unstyled text-white font-medium fs-6 d-flex flex-column gap-3 mb-0 pt-4 position-relative z-1 border-top border-white border-opacity-25">
                            <li><i class="fas fa-check text-accent me-3"></i>Unlimited bisnis & klien</li>
                            <li><i class="fas fa-check text-accent me-3"></i>Unlimited deal pipeline</li>
                            <li><i class="fas fa-check text-accent me-3"></i>Laporan Laba/Rugi lengkap</li>
                            <li><i class="fas fa-check text-accent me-3"></i>Export data CSV kapan saja</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5 my-5" id="faq">
        <div class="container px-4 px-lg-5">
            <div class="text-center mb-5 pb-3">
                <h2 class="display-6 fw-bold text-dark">FAQ</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion accordion-flush custom-accordion" id="accordionFaq">
                        <div class="accordion-item border-0 mb-3 bg-light rounded-4 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-transparent fw-bold text-dark collapsed p-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Apakah data saya aman?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                                <div class="accordion-body px-4 pb-4 pt-0 text-muted fw-medium fs-6">
                                    Ya, kami menggunakan enkripsi dan backup otomatis untuk memastikan keamanan data bisnis Anda. Anda bebas mengexport data kapan saja.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3 bg-light rounded-4 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-transparent fw-bold text-dark collapsed p-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Bisa digunakan lewat handphone?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                                <div class="accordion-body px-4 pb-4 pt-0 text-muted fw-medium fs-6">
                                    Tentu, platform kami dirancang fully-responsive dan bisa diakses via browser mobile kapan saja, di mana saja.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3 bg-light rounded-4 overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-transparent fw-bold text-dark collapsed p-4 fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Bagaimana jika saya punya lebih dari 1 bisnis?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                                <div class="accordion-body px-4 pb-4 pt-0 text-muted fw-medium fs-6">
                                    Dengan paket Premium, satu akun bisa mengelola multiple bisnis (multitenant). Cukup switch antar bisnis dengan mudah dari dropdown dashboard utama.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section banner -->
    <section class="final-cta py-5 px-3 mb-5">
        <div class="container px-4 px-lg-5 bg-primary cta-banner position-relative overflow-hidden text-white" style="border-radius: 2rem;">
            <div class="cta-decor position-absolute d-none d-lg-block"></div>
            
            <div class="row align-items-center py-5 px-lg-4 position-relative z-1 text-center text-lg-start">
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <p class="small fw-bold tracking-widest text-uppercase text-white-50 mb-3">Siap berkembang?</p>
                    <h2 class="display-5 fw-bold mb-0 lh-sm">Tingkatkan efektivitas pembayaran operasional Anda.</h2>
                </div>
                <div class="col-lg-5 text-lg-end d-flex gap-3 justify-content-center justify-content-lg-end flex-wrap">
                    <a href="auth/register.php" class="btn border-0 rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm cta-banner-btn" style="background-color: var(--accent); color: white;">Get Started</a>
                    <a href="#how-it-works" class="btn btn-outline-light rounded-pill px-4 py-3 fw-bold fs-5 d-none d-sm-inline-flex align-items-center gap-2">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4 pe-lg-5">
                    <a class="navbar-brand fw-bold text-dark fs-3 mb-3 d-inline-block text-decoration-none d-flex align-items-center gap-2" href="#">
                        <i class="fas fa-briefcase text-primary"></i> Jasaku
                    </a>
                    <p class="text-muted mt-2 fw-medium pe-lg-4">Platform operasi terpadu untuk agensi, freelancer, dan UMKM jasa inovatif di Indonesia.</p>
                </div>
                <div class="col-6 col-md-4 col-lg-2 offset-lg-2">
                    <h6 class="fw-bold mb-4 text-dark mb-4 pb-2 border-bottom border-light-subtle d-inline-block">Solusi</h6>
                    <ul class="list-unstyled text-muted d-flex flex-column gap-3 fw-medium">
                        <li><a href="#" class="text-decoration-none text-muted footer-link">Agensi Kecil</a></li>
                        <li><a href="#" class="text-decoration-none text-muted footer-link">Freelancer</a></li>
                        <li><a href="#" class="text-decoration-none text-muted footer-link">Konsultan</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 class="fw-bold mb-4 text-dark mb-4 pb-2 border-bottom border-light-subtle d-inline-block">Perusahaan</h6>
                    <ul class="list-unstyled text-muted d-flex flex-column gap-3 fw-medium">
                        <li><a href="about.php" class="text-decoration-none text-muted footer-link">Tentang Kami</a></li>
                        <li><a href="#" class="text-decoration-none text-muted footer-link">Karir</a></li>
                        <li><a href="contact.php" class="text-decoration-none text-muted footer-link">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-2 mt-4 mt-lg-0">
                    <h6 class="fw-bold mb-4 text-dark d-none d-md-block">&nbsp;</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-dark bg-light hover-bg-primary rounded-circle d-flex align-items-center justify-content-center social-link transition-all" style="width: 40px; height: 40px;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-dark bg-light hover-bg-primary rounded-circle d-flex align-items-center justify-content-center social-link transition-all" style="width: 40px; height: 40px;"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-dark bg-light hover-bg-primary rounded-circle d-flex align-items-center justify-content-center social-link transition-all" style="width: 40px; height: 40px;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted border-top border-light-subtle pt-4 mt-5">
                <p class="mb-3 mb-md-0 small fw-medium">&copy; <?php echo date('Y'); ?> Jasaku. All Rights Reserved.</p>
                <div class="d-flex gap-4 small fw-medium">
                    <a href="#" class="text-decoration-none text-muted footer-link">Terms</a>
                    <a href="#" class="text-decoration-none text-muted footer-link">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple sticky navbar effect
        window.addEventListener('scroll', function() {
            var navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-sm');
                navbar.style.paddingTop = '1rem';
                navbar.style.paddingBottom = '1rem';
            } else {
                navbar.classList.remove('shadow-sm');
                navbar.style.paddingTop = '1.5rem';
                navbar.style.paddingBottom = '1.5rem';
            }
        });
    </script>
</body>
</html>
