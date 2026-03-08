<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle search query
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// Build SQL to fetch active services
$sql = "SELECT s.id, s.service_name, s.price, s.description, b.business_name, b.logo_path, b.id as business_id 
        FROM services s 
        JOIN businesses b ON s.business_id = b.id 
        WHERE s.status = 'Active' AND s.is_deleted = 0";

$params = [];
$types = "";

if (!empty($searchQuery)) {
    $sql .= " AND (s.service_name LIKE ? OR s.description LIKE ? OR b.business_name LIKE ?)";
    $likeQuery = "%" . $searchQuery . "%";
    $params = [$likeQuery, $likeQuery, $likeQuery];
    $types = "sss";
}

$sql .= " ORDER BY s.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$services = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Colors for placeholders if no logo
$placeholderColors = ['#FF6B35', '#2563EB', '#10B981', '#F59E0B', '#8B5CF6'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jasaku Marketplace - Temukan Jasa Apapun</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Page CSS -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-marketplace sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-briefcase me-2"></i>Jasaku
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="btn btn-login-outline" href="business.php">Bisnis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Jasaku Marketplace<br>Temukan Jasa Apapun</h1>
                    <p class="hero-subtitle">Mulai dari jasa desain, kebersihan, hingga konsultan. Semua ada di sini.</p>
                    
                    <form action="index.php" method="GET" class="search-container mt-4">
                        <i class="fas fa-search ms-3 text-muted"></i>
                        <input type="text" name="q" class="form-control search-input" placeholder="Ketik jasa yang Anda butuhkan..." value="<?= htmlspecialchars($searchQuery) ?>">
                        <button type="submit" class="btn search-btn">Cari Jasa</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Catalog Section -->
    <section class="catalog-section container">
        <?php if (!empty($searchQuery)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Hasil Pencarian: "<?= htmlspecialchars($searchQuery) ?>"</h2>
                <a href="index.php" class="text-decoration-none text-muted"><i class="fas fa-times me-1"></i>Reset</a>
            </div>
        <?php else: ?>
            <h2 class="section-title text-center">Inspirasi Jasa Pilihan</h2>
        <?php endif; ?>

        <?php if (count($services) > 0): ?>
            <div class="row g-4">
                <?php foreach ($services as $index => $service): 
                    // Generate random consistent color based on service ID
                    $colorIndex = $service['id'] % count($placeholderColors);
                    $bgColor = $placeholderColors[$colorIndex];
                    $businessInitials = strtoupper(substr($service['business_name'], 0, 1));
                    
                    // Simple logic to pick an icon based on category/name
                    $icon = 'fa-cogs';
                    $nameLower = strtolower($service['service_name']);
                    if (strpos($nameLower, 'desain') !== false || strpos($nameLower, 'grafik') !== false) $icon = 'fa-pen-nib';
                    if (strpos($nameLower, 'kebersihan') !== false || strpos($nameLower, 'clean') !== false) $icon = 'fa-broom';
                    if (strpos($nameLower, 'konsultan') !== false) $icon = 'fa-comments';
                    if (strpos($nameLower, 'web') !== false || strpos($nameLower, 'app') !== false) $icon = 'fa-laptop-code';
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="service-card">
                            <!-- Dynamic colorful placeholder -->
                            <div class="service-placeholder-img" style="background-color: <?= $bgColor ?>;">
                                <i class="fas <?= $icon ?>"></i>
                                
                                <div class="service-logo-overlay">
                                    <?php if (!empty($service['logo_path']) && file_exists('assets/uploads/logos/' . $service['logo_path'])): ?>
                                        <img src="assets/uploads/logos/<?= htmlspecialchars($service['logo_path']) ?>" alt="<?= htmlspecialchars($service['business_name']) ?>">
                                    <?php else: ?>
                                        <span class="initial"><?= $businessInitials ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="service-body">
                                <div class="business-name">
                                    <i class="fas fa-building me-1"></i> <?= htmlspecialchars($service['business_name']) ?>
                                </div>
                                <h3 class="service-title"><?= htmlspecialchars($service['service_name']) ?></h3>
                                <p class="service-desc text-truncate" style="max-height: 4.5em; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    <?= htmlspecialchars(empty($service['description']) ? 'Jasa profesional dari ' . $service['business_name'] : $service['description']) ?>
                                </p>
                                
                                <div class="service-footer">
                                    <div class="service-price">
                                        Rp <?= number_format($service['price'], 0, ',', '.') ?>
                                    </div>
                                    <button class="btn btn-detail" onclick="alert('Fitur detail jasa MVP (Out of Scope untuk view page, bisa dialihkan ke WhatsApp owner atau ke halaman contact nantinya).')">Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-box-open empty-icon"></i>
                <h3>Tidak ada jasa yang ditemukan</h3>
                <p>Maaf, kami tidak dapat menemukan jasa yang sesuai dengan pencarian Anda.</p>
                <?php if (!empty($searchQuery)): ?>
                    <a href="index.php" class="btn btn-login-outline mt-3">Lihat Semua Jasa</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-4 mt-5 border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; <?= date('Y') ?> Jasaku. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
