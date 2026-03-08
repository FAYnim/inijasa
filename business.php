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
    <meta name="description" content="Jasaku - Platform manajemen bisnis jasa all-in-one untuk agensi & UMKM Indonesia. Kelola klien, pipeline sales, hingga keuangan dalam satu dashboard.">
    <meta property="og:title" content="Jasaku - Platform Manajemen Bisnis Jasa All-in-One">
    <meta property="og:description" content="Dari database klien, pipeline sales, hingga pencatatan keuangan — kelola semuanya tanpa ribet dalam satu dashboard.">
    <meta property="og:type" content="website">
    <title>Jasaku - Platform Manajemen Bisnis Jasa All-in-One</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;1,9..144,600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="brand-text">Jasaku</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary cta-btn" href="auth/register.php">Coba Gratis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="hero-background"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-animate="fade-up">
                    <div class="hero-badge">Untuk Agensi & UMKM Indonesia</div>
                    <h1 class="hero-title">Platform Manajemen Bisnis Jasa All-in-One</h1>
                    <p class="hero-subtitle">Dari database klien, pipeline sales, hingga pencatatan keuangan — kelola semuanya tanpa ribet dalam satu dashboard.</p>
                    <div class="hero-cta">
                        <a href="auth/register.php" class="btn btn-primary btn-lg">Daftar Gratis</a>
                        <a href="#how-it-works" class="btn btn-outline-secondary btn-lg">Lihat Cara Kerja</a>
                    </div>
                </div>
                <div class="col-lg-6" data-animate="fade-left">
                    <div class="hero-image-wrapper">
                        <img src="https://placehold.co/1200x600/0A2342/FFFFFF?text=Dashboard+Jasaku" alt="Dashboard Jasaku" class="img-fluid hero-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logo Cloud -->
    <section class="logo-cloud-section" id="integrations">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title-small">Terintegrasi dengan Tools yang Anda Gunakan</h2>
                <p class="section-subtitle-small">Atau gunakan sebagai standalone platform</p>
            </div>
            <div class="logo-cloud">
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=WhatsApp" alt="WhatsApp">
                </div>
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=Calendar" alt="Google Calendar">
                </div>
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=Midtrans" alt="Midtrans">
                </div>
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=Xendit" alt="Xendit">
                </div>
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=Email" alt="Email">
                </div>
                <div class="logo-item">
                    <img src="https://placehold.co/100x40/999999/FFFFFF?text=API" alt="API">
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works-section" id="how-it-works">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Cara Kerja</h2>
                <p class="section-subtitle">Mulai kelola bisnis Anda dalam 3 langkah sederhana</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-animate="fade-up" data-delay="0">
                    <div class="step-card">
                        <div class="step-number">01</div>
                        <h3 class="step-title">Daftar & Setup Bisnis</h3>
                        <p class="step-description">Buat akun, setup profil bisnis Anda dalam 2 menit</p>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="100">
                    <div class="step-card">
                        <div class="step-number">02</div>
                        <h3 class="step-title">Input Klien & Deal</h3>
                        <p class="step-description">Tambahkan klien dan buat pipeline deal Anda</p>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="200">
                    <div class="step-card">
                        <div class="step-number">03</div>
                        <h3 class="step-title">Kelola & Terima Pembayaran</h3>
                        <p class="step-description">Tracking progress sampai uang masuk rekening</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Semua yang Anda Butuhkan untuk Mengelola Bisnis Jasa</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-box"></i></div>
                        <h3 class="feature-title">Manajemen Paket Jasa</h3>
                        <p class="feature-description">Buat dan kelola service packages dengan harga transparan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <h3 class="feature-title">Database Klien Lengkap</h3>
                        <p class="feature-description">CRM sederhana dengan history dan catatan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-briefcase"></i></div>
                        <h3 class="feature-title">Pipeline Sales</h3>
                        <p class="feature-description">Tracking deal dari Lead sampai Won/Lost</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-coins"></i></div>
                        <h3 class="feature-title">Pencatatan Keuangan</h3>
                        <p class="feature-description">Pemasukan & pengeluaran terintegrasi dengan deal</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h3 class="feature-title">Dashboard Analytics</h3>
                        <p class="feature-description">Monitor performa bisnis secara real-time</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-animate="fade-up" data-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-building"></i></div>
                        <h3 class="feature-title">Multi-Business Ready</h3>
                        <p class="feature-description">Kelola beberapa bisnis dalam satu akun</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshot Preview -->
    <section class="screenshot-section" id="preview">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Dashboard yang Sederhana, Powerful</h2>
                <p class="section-subtitle">Semua data bisnis Anda dalam satu pandangan</p>
            </div>
            <div class="screenshot-wrapper" data-animate="fade-up">
                <img src="https://placehold.co/1400x800/0A2342/FFFFFF?text=Tampilan+Dashboard+Jasaku" alt="Tampilan Dashboard Jasaku" class="img-fluid screenshot-main">
            </div>
            <div class="row g-4 mt-4">
                <div class="col-md-4" data-animate="fade-up" data-delay="0">
                    <div class="screenshot-thumb">
                        <img src="https://placehold.co/400x300/FF6B35/FFFFFF?text=Pipeline+View" alt="Pipeline View" class="img-fluid">
                        <p class="thumb-caption">Pipeline Management</p>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="100">
                    <div class="screenshot-thumb">
                        <img src="https://placehold.co/400x300/FF6B35/FFFFFF?text=Finance+View" alt="Finance View" class="img-fluid">
                        <p class="thumb-caption">Financial Overview</p>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="200">
                    <div class="screenshot-thumb">
                        <img src="https://placehold.co/400x300/FF6B35/FFFFFF?text=Clients+View" alt="Clients View" class="img-fluid">
                        <p class="thumb-caption">Client Database</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Dipercaya oleh Pemilik Bisnis Jasa di Seluruh Indonesia</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-animate="fade-up" data-delay="0">
                    <div class="testimonial-card">
                        <div class="quote-icon">"</div>
                        <p class="testimonial-text">Pipeline deal membantu tim kami tracking 15+ proyek aktif tanpa kehilangan momentum.</p>
                        <div class="testimonial-author">
                            <img src="https://placehold.co/60x60/0A2342/FFFFFF?text=AW" alt="Andi Wijaya" class="author-avatar">
                            <div>
                                <div class="author-name">Andi Wijaya</div>
                                <div class="author-role">Owner, Kreasi Digital</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="100">
                    <div class="testimonial-card">
                        <div class="quote-icon">"</div>
                        <p class="testimonial-text">Sebelumnya catatan di buku, sekarang semua otomatis. Bisa lihat untung rugi tiap bulan.</p>
                        <div class="testimonial-author">
                            <img src="https://placehold.co/60x60/0A2342/FFFFFF?text=SM" alt="Sari Mulyani" class="author-avatar">
                            <div>
                                <div class="author-name">Sari Mulyani</div>
                                <div class="author-role">Owner, Salon Cantik</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-animate="fade-up" data-delay="200">
                    <div class="testimonial-card">
                        <div class="quote-icon">"</div>
                        <p class="testimonial-text">Client CRM-nya simpel tapi lengkap. History deals per klien sangat membantu follow-up.</p>
                        <div class="testimonial-author">
                            <img src="https://placehold.co/60x60/0A2342/FFFFFF?text=BS" alt="Budi Santoso" class="author-avatar">
                            <div>
                                <div class="author-name">Budi Santoso</div>
                                <div class="author-role">Business Consultant</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section (Hidden) -->
    <section class="pricing-section" id="pricing" style="display: none;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Pilih Paket yang Sesuai untuk Bisnis Anda</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="pricing-card">
                        <h3 class="pricing-tier">Starter</h3>
                        <div class="pricing-price">Rp0<span>/bulan</span></div>
                        <ul class="pricing-features">
                            <li>1 bisnis</li>
                            <li>50 klien maksimal</li>
                            <li>Fitur dasar</li>
                        </ul>
                        <a href="/auth/register.php" class="btn btn-outline-primary w-100">Mulai Gratis</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">Paling Populer</div>
                        <h3 class="pricing-tier">Pro</h3>
                        <div class="pricing-price">Rp99rb<span>/bulan</span></div>
                        <ul class="pricing-features">
                            <li>Unlimited bisnis & klien</li>
                            <li>Semua fitur</li>
                            <li>Priority support</li>
                        </ul>
                        <a href="/auth/register.php" class="btn btn-primary w-100">Upgrade Pro</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card">
                        <h3 class="pricing-tier">Business</h3>
                        <div class="pricing-price">Rp299rb<span>/bulan</span></div>
                        <ul class="pricing-features">
                            <li>Multi-user (segera hadir)</li>
                            <li>API access</li>
                            <li>Dedicated support</li>
                        </ul>
                        <a href="/auth/register.php" class="btn btn-outline-primary w-100">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-list">
                        <div class="faq-item" data-animate="fade-up" data-delay="0">
                            <h3 class="faq-question">Apakah data saya aman?</h3>
                            <p class="faq-answer">Ya, kami menggunakan enkripsi dan backup otomatis untuk memastikan keamanan data bisnis Anda.</p>
                        </div>
                        <div class="faq-item" data-animate="fade-up" data-delay="100">
                            <h3 class="faq-question">Bisa digunakan di HP?</h3>
                            <p class="faq-answer">Ya, platform responsive dan bisa diakses via browser mobile kapan saja, di mana saja.</p>
                        </div>
                        <div class="faq-item" data-animate="fade-up" data-delay="200">
                            <h3 class="faq-question">Bagaimana jika saya punya lebih dari 1 bisnis?</h3>
                            <p class="faq-answer">Satu akun bisa kelola multiple bisnis. Switch antar bisnis dengan mudah dari dashboard utama.</p>
                        </div>
                        <div class="faq-item" data-animate="fade-up" data-delay="0">
                            <h3 class="faq-question">Apakah ada biaya setup?</h3>
                            <p class="faq-answer">Tidak ada. Daftar gratis dan langsung bisa digunakan tanpa biaya setup atau biaya tersembunyi.</p>
                        </div>
                        <div class="faq-item" data-animate="fade-up" data-delay="100">
                            <h3 class="faq-question">Bisa export data?</h3>
                            <p class="faq-answer">Ya, semua data bisa di-export ke format Excel/CSV kapan saja untuk kebutuhan analisis atau backup.</p>
                        </div>
                        <div class="faq-item" data-animate="fade-up" data-delay="200">
                            <h3 class="faq-question">Bagaimana sistem pembayaran?</h3>
                            <p class="faq-answer">Saat ini manual recording. Integrasi payment gateway (Midtrans, Xendit) segera hadir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta-section" id="final-cta">
        <div class="cta-background"></div>
        <div class="container">
            <div class="text-center">
                <h2 class="cta-title">Siap Mengelola Bisnis Jasa Anda dengan Lebih Profesional?</h2>
                <p class="cta-subtitle">Daftar gratis sekarang. Tidak perlu kartu kredit.</p>
                <a href="auth/register.php" class="btn btn-primary btn-lg cta-main-btn">Daftar Gratis Sekarang</a>
                <p class="cta-trust">Sudah digunakan oleh 100+ pemilik bisnis jasa di Indonesia</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="footer-copyright">© 2026 Jasaku. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link">Terms</a>
                    <a href="#" class="footer-link">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/landing.js"></script>
</body>
</html>
