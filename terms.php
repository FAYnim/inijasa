<?php
/**
 * Terms Page
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat dan Ketentuan - IniJasa</title>
    
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
            <a class="navbar-brand fw-bold text-primary fs-3" href="index">
                IniJasa
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="index#features">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="index#bento">Kelebihan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="index#how-it-works">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="index#pricing">Harga</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="index#faq">FAQ</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Dashboard</a>
                    <?php else: ?>
                        <a href="auth/login" class="text-decoration-none fw-medium text-dark px-3 py-2 btn-login">Masuk</a>
                        <a href="auth/register" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Coba Gratis</a>
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
                    <p class="text-primary fw-semibold small tracking-widest text-uppercase mb-3">Legal</p>
                    <h1 class="display-3 fw-bold text-dark mb-4">
                        Syarat dan Ketentuan
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-5 bg-light my-5">
        <div class="container px-4 px-lg-5 py-3">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                <h4 class="fw-bold mb-3">1. Pengantar</h4>
                <p class="text-muted mb-4">Selamat datang di IniJasa. Dengan mengakses dan menggunakan platform ini, Anda setuju untuk terikat oleh Syarat dan Ketentuan berikut.</p>

                <h4 class="fw-bold mb-3">2. Penggunaan Layanan</h4>
                <p class="text-muted mb-4">Anda setuju untuk menggunakan layanan IniJasa hanya untuk tujuan yang sah dan sesuai dengan semua hukum dan peraturan yang berlaku.</p>

                <h4 class="fw-bold mb-3">3. Akun Pengguna</h4>
                <p class="text-muted mb-4">Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun Anda serta seluruh aktivitas yang terjadi dalam akun tersebut.</p>

                <h4 class="fw-bold mb-3">4. Perubahan Layanan</h4>
                <p class="text-muted mb-0">Kami berhak untuk mengubah, menangguhkan, atau menghentikan layanan (atau bagian apa pun darinya) kapan saja dengan atau tanpa pemberitahuan.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-4 pe-lg-5">
                    <a class="navbar-brand fw-bold text-dark fs-3 mb-3 d-inline-block text-decoration-none d-flex align-items-center gap-2" href="index">
                        <i class="fas fa-briefcase text-primary"></i> IniJasa
                    </a>
                    <p class="text-muted mt-2 fw-medium pe-lg-4">Platform operasi terpadu untuk agensi, freelancer, dan UMKM jasa inovatif di Indonesia.</p>
                </div>
                <div class="col-6 col-md-4 col-lg-2 offset-lg-2">
                    <h6 class="fw-bold mb-4 text-dark mb-4 pb-2 border-bottom border-light-subtle d-inline-block">Solusi</h6>
                    <ul class="list-unstyled text-muted d-flex flex-column gap-3 fw-medium">
                        <li><a href="agensi-kecil" class="text-decoration-none text-muted footer-link">Agensi Kecil</a></li>
                        <li><a href="freelancer" class="text-decoration-none text-muted footer-link">Freelancer</a></li>
                        <li><a href="konsultan" class="text-decoration-none text-muted footer-link">Konsultan</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <h6 class="fw-bold mb-4 text-dark mb-4 pb-2 border-bottom border-light-subtle d-inline-block">Perusahaan</h6>
                    <ul class="list-unstyled text-muted d-flex flex-column gap-3 fw-medium">
                        <li><a href="about" class="text-decoration-none text-muted footer-link">Tentang Kami</a></li>
                        <li><a href="karir" class="text-decoration-none text-muted footer-link">Karir</a></li>
                        <li><a href="contact" class="text-decoration-none text-muted footer-link">Kontak</a></li>
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
                <p class="mb-3 mb-md-0 small fw-medium">&copy; <?php echo date('Y'); ?> IniJasa. All Rights Reserved.</p>
                <div class="d-flex gap-4 small fw-medium">
                    <a href="terms" class="text-decoration-none text-muted footer-link">Terms</a>
                    <a href="privacy" class="text-decoration-none text-muted footer-link">Privacy</a>
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
