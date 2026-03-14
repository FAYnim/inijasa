<?php
/**
 * Contact Us Page
 * IniJasa - Platform Manajemen Bisnis Jasa
 */

session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - IniJasa</title>
    
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
                    <?php
else: ?>
                        <a href="auth/login" class="text-decoration-none fw-medium text-dark px-3 py-2 btn-login">Masuk</a>
                        <a href="auth/register" class="btn btn-primary rounded-pill px-4 py-2 cta-btn fw-medium shadow-sm">Coba Gratis</a>
                    <?php
endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section pt-5 mt-5">
        <div class="container px-4 px-lg-5">
            <div class="row pt-5 align-items-center justify-content-center text-center">
                <div class="col-lg-8 hero-content mb-5">
                    <p class="text-primary fw-semibold small tracking-widest text-uppercase mb-3">Hubungi Kami</p>
                    <h1 class="display-3 fw-bold text-dark mb-4">
                        Kami Siap Membantu Anda
                    </h1>
                    <p class="text-muted fs-5 fw-medium mx-auto" style="max-width: 700px;">
                        Punya pertanyaan terkait IniJasa, rencana kerja sama, atau butuh bantuan teknis? Tim kami siap merespons kebutuhan Anda dengan cepat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="py-4 my-3">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 text-center justify-content-center">
                <div class="col-md-4">
                    <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light-subtle hover-lift transition-all">
                        <div class="feature-icon mb-4 mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-envelope text-primary fs-2"></i>
                        </div>
                        <h4 class="fw-bold fs-4 text-dark mb-2">Email</h4>
                        <p class="text-muted fw-medium fs-6 mb-3">Kirimkan email kapan saja</p>
                        <a href="mailto:hello@inijasa.id" class="text-primary fw-semibold text-decoration-none">hello@inijasa.id</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light-subtle hover-lift transition-all">
                        <div class="feature-icon mb-4 mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-headset text-primary fs-2"></i>
                        </div>
                        <h4 class="fw-bold fs-4 text-dark mb-2">Dukungan</h4>
                        <p class="text-muted fw-medium fs-6 mb-3">Bantuan langsung dari tim kami</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">Pusat Bantuan IniJasa</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light-subtle hover-lift transition-all">
                        <div class="feature-icon mb-4 mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-map-marker-alt text-primary fs-2"></i>
                        </div>
                        <h4 class="fw-bold fs-4 text-dark mb-2">Kantor</h4>
                        <p class="text-muted fw-medium fs-6 mb-3">Kunjungi HQ kami</p>
                        <span class="text-dark fw-semibold">Jakarta, Indonesia</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-5 bg-light my-5" id="contact-form">
        <div class="container px-4 px-lg-5 py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="fw-bold text-dark mb-4 text-center">Tinggalkan Pesan</h3>
                            <form action="#" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label fw-medium text-dark">Nama Lengkap</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0" id="nama" placeholder="Masukkan nama Anda" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-medium text-dark">Alamat Email</label>
                                        <input type="email" class="form-control form-control-lg bg-light border-0" id="email" placeholder="nama@email.com" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="subjek" class="form-label fw-medium text-dark">Subjek</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0" id="subjek" placeholder="Topik pesan" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="pesan" class="form-label fw-medium text-dark">Pesan</label>
                                        <textarea class="form-control bg-light border-0" id="pesan" rows="5" placeholder="Tuliskan pesan Anda secara detail di sini..." required></textarea>
                                    </div>
                                    <div class="col-12 mt-4 text-center">
                                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 fw-medium shadow-sm w-100 w-md-auto">Kirim Pesan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
