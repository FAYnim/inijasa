<?php
/**
 * About Us Page
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Jasaku</title>
    
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
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white py-3 shadow-sm" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand fw-bold text-primary fs-3" href="business.php">
                Jasaku
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="business.php#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="business.php#bento">Kelebihan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="business.php#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="business.php#pricing">Harga</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="business.php#faq">FAQ</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Dashboard</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="text-decoration-none fw-medium text-dark px-3 py-2 btn-login">Masuk</a>
                        <a href="auth/register.php" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Coba Gratis</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section pt-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row pt-5 align-items-center justify-content-center text-center">
                <div class="col-lg-8 hero-content mb-5">
                    <p class="text-primary fw-semibold small tracking-widest text-uppercase mb-3">Cerita Kami</p>
                    <h1 class="display-3 fw-bold text-dark mb-4">
                        Merevolusi Cara Bisnis Jasa Bekerja
                    </h1>
                    <p class="text-muted fs-5 fw-medium mx-auto" style="max-width: 700px;">
                        Jasaku hadir untuk memberikan solusi operasional yang efisien, transparan, dan terintegrasi bagi para penyedia jasa di Indonesia—dari freelancer hingga agensi profesional.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story / Content Section -->
    <section class="py-5 bg-light my-5" id="story">
        <div class="container px-4 px-lg-5 py-5">
            <div class="row align-items-center g-5 mb-5 pb-5">
                <div class="col-lg-6">
                    <img src="https://placehold.co/800x600/F8F9FA/CBD5E1?text=Perjalanan+Jasaku" alt="Perjalanan Jasaku" class="img-fluid rounded-4 shadow-lg w-100 border border-light-subtle">
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h2 class="display-5 fw-bold text-dark mb-4">Latar Belakang Kami</h2>
                    <p class="text-muted fs-5 mb-4 lh-lg">
                        Berawal dari pengalaman mengelola agensi jasa, kami menyadari betapa rumitnya menyelaraskan proses operasional, penjualan, dan penagihan klien tanpa sistem yang saling terhubung. Alat-alat yang ada sering kali terlalu mahal, rumit, atau tidak sesuai dengan alur kerja (workflow) penyedia jasa.
                    </p>
                    <p class="text-muted fs-5 mb-0 lh-lg">
                        Atas dasar itulah Jasaku dibangun. Sebuah platform <em>all-in-one</em> yang dirancang secara spesifik, mengedepankan kesederhanaan, namun memiliki skalabilitas tinggi untuk memenuhi kebutuhan bisnis jasa masa kini yang serba dinamis.
                    </p>
                </div>
            </div>

            <div class="row mt-5 pt-3 g-4 align-items-stretch">
                <!-- Visi -->
                <div class="col-md-6">
                    <div class="bento-card bg-white p-5 h-100 d-flex flex-column justify-content-center text-center hover-lift position-relative overflow-hidden group">
                        <div class="mb-4 position-relative z-1">
                            <i class="fas fa-rocket text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3 fs-2 text-dark position-relative z-1">Visi Kami</h3>
                        <p class="text-muted fs-5 fw-medium mb-0 position-relative z-1">Menjadi ekosistem digital terbaik dan terpercaya yang memampukan setiap bisnis jasa di Indonesia untuk tumbuh tanpa batas.</p>
                    </div>
                </div>
                <!-- Misi -->
                <div class="col-md-6">
                    <div class="bento-card bg-primary text-white p-5 h-100 d-flex flex-column justify-content-center text-center hover-lift position-relative overflow-hidden group">
                        <div class="step-bg d-none d-lg-block" style="opacity: 0.1"></div>
                        <div class="mb-4 position-relative z-1">
                            <i class="fas fa-bullseye text-white" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold mb-3 fs-2 text-white position-relative z-1">Misi Kami</h3>
                        <p class="text-white-50 fs-5 fw-medium mb-0 position-relative z-1">Menyederhanakan kompleksitas operasional, meningkatkan transparansi keuangan, dan mengotomatisasi alur kerja agar Anda bisa fokus melayani klien.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support / Value section -->
    <section class="py-5 my-5">
        <div class="container px-4 px-lg-5">
            <div class="text-center mb-5 pb-3">
                <p class="text-primary fw-semibold small tracking-widest text-uppercase">Nilai Utama</p>
                <h2 class="display-5 fw-bold text-dark">Mengapa Jasaku Berbeda</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4 feature-box p-4">
                    <div class="feature-icon mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 2.5rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-handshake text-primary"></i>
                    </div>
                    <h4 class="fw-bold fs-4 text-dark mb-3">Kolaboratif</h4>
                    <p class="text-muted fw-medium fs-6">Kami membangun Jasaku tidak hanya untuk kami, tetapi dirancang bersama masukan dari ratusan praktisi bisnis jasa nyata.</p>
                </div>
                <div class="col-md-4 feature-box p-4">
                    <div class="feature-icon mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 2.5rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lock text-primary"></i>
                    </div>
                    <h4 class="fw-bold fs-4 text-dark mb-3">Keamanan Data</h4>
                    <p class="text-muted fw-medium fs-6">Privasi dan keamanan data finansial maupun klien Anda adalah prioritas tertinggi kami melalui standar enkripsi industri.</p>
                </div>
                <div class="col-md-4 feature-box p-4">
                    <div class="feature-icon mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 2.5rem; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lightbulb text-primary"></i>
                    </div>
                    <h4 class="fw-bold fs-4 text-dark mb-3">Inovasi Berkelanjutan</h4>
                    <p class="text-muted fw-medium fs-6">Kami secara rutin merilis pembaruan fitur baru untuk memastikan tools yang Anda gunakan selalu relevan dengan tren masa kini.</p>
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
                    <p class="small fw-bold tracking-widest text-uppercase text-white-50 mb-3">Mari Bertumbuh Bersama</p>
                    <h2 class="display-5 fw-bold mb-0 lh-sm">Jadilah bagian dari perjalanan Jasaku.</h2>
                </div>
                <div class="col-lg-5 text-lg-end d-flex gap-3 justify-content-center justify-content-lg-end flex-wrap">
                    <a href="auth/register.php" class="btn border-0 rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm cta-banner-btn" style="background-color: var(--accent); color: white;">Mulai Gratis Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4 pe-lg-5">
                    <a class="navbar-brand fw-bold text-dark fs-3 mb-3 d-inline-block text-decoration-none d-flex align-items-center gap-2" href="business.php">
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
