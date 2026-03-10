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
    <nav class="navbar navbar-expand-lg navbar-marketplace sticky-top py-2 bg-white border-bottom">
        <div class="container-fluid px-4 align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-fire me-2 brand-logo-icon" style="color: var(--primary-color);"></i>
                <span class="fw-bold text-dark fs-4">Jasaku</span>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse d-flex justify-content-between align-items-center w-100 mt-3 mt-lg-0" id="navbarNav">
                <form action="index.php" method="GET" class="search-container mx-auto" style="max-width: 650px; width: 100%;">
                    <div class="input-group search-input-group align-items-center p-1 rounded-pill border hover-shadow-sm bg-white" style="transition: all 0.2s;">
                        <span class="input-group-text border-0 bg-transparent text-muted ps-3 pe-2"><i class="fas fa-map-marker-alt"></i></span>
                        <select class="form-select border-0 bg-transparent text-muted fw-medium shadow-none px-1" style="max-width: 120px;">
                            <option>Jakarta</option>
                            <option>Surabaya</option>
                            <option>Bandung</option>
                        </select>
                        <div class="vr my-2 opacity-25"></div>
                        <input type="text" name="q" class="form-control border-0 shadow-none ps-3 fw-medium text-dark bg-transparent" placeholder="Ketik jasa yang dicari..." value="<?= htmlspecialchars($searchQuery) ?>">
                        <button type="submit" class="btn rounded-pill px-4 py-2 fw-bold text-white shadow-sm" style="background-color: var(--primary-color);"><i class="fas fa-search me-2"></i>Cari</button>
                    </div>
                </form>

                <ul class="navbar-nav align-items-center gap-4 d-none d-lg-flex">
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-semibold d-flex align-items-center" href="#" role="button">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9f/Flag_of_Indonesia.svg" width="22" class="me-2 border rounded-1 shadow-sm" alt="ID"> ID <i class="fas fa-chevron-down ms-1 small text-muted" style="font-size:0.7em;"></i>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-dark hover-primary" href="#"><i class="fas fa-shopping-cart fs-5"></i></a></li>
                    <li class="nav-item"><a class="nav-link text-dark hover-primary" href="#"><i class="far fa-bell fs-5"></i></a></li>
                    <li class="nav-item"><a class="nav-link text-dark hover-primary" href="#"><i class="far fa-envelope fs-5"></i></a></li>
                    <li class="nav-item ms-2">
                        <a href="business.php" class="text-decoration-none">
                            <img src="https://ui-avatars.com/api/?name=User&background=333&color=fff&rounded=true&bold=true" width="36" height="36" alt="User" class="rounded-circle border">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sub Navbar -->
    <div class="sub-navbar border-bottom py-2 bg-white sticky-top d-none d-md-block" style="top: 73px; z-index: 1019;">
        <div class="container-fluid px-4 d-flex align-items-center gap-4 py-1 text-muted fs-6">
            <a href="#" class="text-dark text-decoration-none fw-semibold d-flex align-items-center"><i class="fas fa-list me-2"></i>Kategori <i class="fas fa-chevron-down ms-2 small text-muted"></i></a>
            <div class="vr opacity-25"></div>
            <a href="#" class="text-muted text-decoration-none hover-dark fw-medium">Siap Dikerjakan</a>
            <a href="#" class="text-muted text-decoration-none hover-dark fw-medium">Layanan Prioritas</a>
            <a href="#" class="text-muted text-decoration-none hover-dark fw-medium d-flex align-items-center">Pusat Pembeli <i class="fas fa-chevron-down ms-1 small"></i></a>
            <a href="#" class="text-muted text-decoration-none hover-dark fw-medium d-flex align-items-center">Jual di Jasaku <i class="fas fa-chevron-down ms-1 small"></i></a>
            <a href="#" class="text-muted text-decoration-none hover-dark fw-medium d-flex align-items-center">Bantuan <i class="fas fa-chevron-down ms-1 small"></i></a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4 main-content-area">
        <div class="row">
            <!-- Sidebar Filters -->
            <aside class="col-lg-3 col-xl-2 d-none d-lg-block">
                <div class="filter-sidebar pe-2">
                    <h5 class="fw-bold mb-4 text-dark">Filter</h5>
                    
                    <!-- Filter Group 1 -->
                    <div class="filter-group mb-4 pb-2">
                        <h6 class="fw-bold mb-3 text-dark fs-6">Tipe Penyedia</h6>
                        <div class="form-check mb-2 custom-checkbox">
                            <input class="form-check-input shadow-none" type="checkbox" id="tAssurance">
                            <label class="form-check-label d-flex align-items-center gap-2 text-muted" for="tAssurance">
                                <i class="fas fa-crown text-warning"></i> Jaminan Transaksi
                            </label>
                        </div>
                        <div class="form-check mb-2 custom-checkbox">
                            <input class="form-check-input shadow-none" type="checkbox" id="vSuppliers">
                            <label class="form-check-label d-flex align-items-center gap-2 text-muted" for="vSuppliers">
                                <i class="fas fa-check-circle text-primary"></i> Terverifikasi
                            </label>
                        </div>
                    </div>

                    <!-- Filter Group 2 -->
                    <div class="filter-group mb-4 pb-2">
                        <h6 class="fw-bold mb-3 text-dark fs-6">Tipe Layanan</h6>
                        <div class="form-check mb-3 custom-checkbox orange-check">
                            <input class="form-check-input shadow-none" type="checkbox" id="pReady" checked>
                            <label class="form-check-label text-muted fw-medium" for="pReady">Siap Dikerjakan</label>
                        </div>
                        <div class="form-check mb-2 custom-checkbox orange-check">
                            <input class="form-check-input shadow-none" type="checkbox" id="pPaid" checked>
                            <label class="form-check-label text-muted fw-medium" for="pPaid">Konsultasi Berbayar</label>
                        </div>
                    </div>

                    <!-- Filter Group 3 -->
                    <div class="filter-group mb-4 pb-2">
                        <h6 class="fw-bold mb-3 text-dark fs-6">Pengalaman</h6>
                        <div class="form-check mb-3 custom-checkbox">
                            <input class="form-check-input shadow-none" type="checkbox" id="cNew">
                            <label class="form-check-label text-muted" for="cNew">Baru Bergabung</label>
                        </div>
                        <div class="form-check mb-2 custom-checkbox">
                            <input class="form-check-input shadow-none" type="checkbox" id="cSecond">
                            <label class="form-check-label text-muted" for="cSecond">Berpengalaman</label>
                        </div>
                    </div>

                    <!-- Filter Group 4: Range -->
                    <div class="filter-group mb-4 pb-2 pt-2">
                        <div class="position-relative pb-4">
                            <div class="range-slider-tooltip bg-dark text-white rounded px-2 py-1 small fw-bold position-absolute" style="left: 50%; top:-30px; transform: translateX(-50%);">500</div>
                            <h6 class="fw-bold mb-3 text-dark fs-6 float-start">Min. Pesanan</h6>
                            <div class="clearfix"></div>
                            <input type="range" class="form-range custom-range" min="10" max="1000" value="500">
                            <div class="d-flex justify-content-between text-muted mt-1 fw-medium" style="font-size: 0.8em;">
                                <span>10</span>
                                <span>1000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Group 5: Price -->
                    <div class="filter-group mb-4 pb-2 pt-1">
                        <h6 class="fw-bold mb-3 text-dark fs-6">Harga</h6>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-end-0 text-muted bg-white px-2 ps-3 fw-medium border-light-grey rounded-start-2 shadow-sm text-center" style="width: 40px; font-size: 0.85rem;">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-1 bg-white fw-medium border-light-grey rounded-end-2 shadow-sm" value="100.000">
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-end-0 text-muted bg-white px-2 ps-3 fw-medium border-light-grey rounded-start-2 shadow-sm text-center" style="width: 40px; font-size: 0.85rem;">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-1 bg-white fw-medium border-light-grey rounded-end-2 shadow-sm" value="5.000.000">
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2 mt-3">
                            <button class="btn border-light-grey text-start py-2 text-muted bg-white filter-btn rounded-2 shadow-sm pe-4">Di bawah Rp 500rb</button>
                            <button class="btn border-light-grey text-start py-2 text-muted bg-white filter-btn rounded-2 shadow-sm pe-4">Rp 500rb - Rp 1Jt</button>
                            <button class="btn border-light-grey text-start py-2 text-muted bg-white filter-btn rounded-2 shadow-sm pe-4">Rp 1Jt - Rp 5Jt</button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Product Area -->
            <div class="col-lg-9 col-xl-10">
                <!-- Search Result Header -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-2 border-bottom border-light gap-3">
                    <div class="fw-bold text-dark fs-6">
                        1 - <?= min(16, count($services)) ?> dari <?= count($services) + 7000 ?> hasil untuk <span class="text-primary">"<?= empty($searchQuery) ? 'Semua' : htmlspecialchars($searchQuery) ?>"</span>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-medium fs-6">Urutkan :</span>
                            <select class="form-select form-select-sm border bg-white fw-semibold rounded-2 py-2 px-3 shadow-none text-dark" style="width: 200px; cursor:pointer;">
                                <option>Paling Sesuai</option>
                                <option>Harga: Terendah ke Tertinggi</option>
                                <option>Harga: Tertinggi ke Terendah</option>
                            </select>
                        </div>
                        <button class="btn btn-white rounded-2 px-2 py-1 border border-light-grey shadow-sm view-toggle text-dark fs-5"><i class="fas fa-th-large"></i></button>
                    </div>
                </div>

                <!-- Active Filter Tags -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <span class="filter-tag">Siap Dikerjakan <i class="fas fa-times ms-2 text-muted small hover-primary" style="cursor:pointer;"></i></span>
                    <span class="filter-tag">Konsultasi Berbayar <i class="fas fa-times ms-2 text-muted small hover-primary" style="cursor:pointer;"></i></span>
                    <span class="filter-tag">Harga Minimum <i class="fas fa-times ms-2 text-muted small hover-primary" style="cursor:pointer;"></i></span>
                    <span class="filter-tag">Harga Maksimum <i class="fas fa-times ms-2 text-muted small hover-primary" style="cursor:pointer;"></i></span>
                    <span class="filter-tag">Minimal Pesanan <i class="fas fa-times ms-2 text-muted small hover-primary" style="cursor:pointer;"></i></span>
                    <a href="index.php" class="text-primary text-decoration-none fw-bold ms-3 filter-clear" style="font-size: 0.95rem;">Hapus Semua Filter</a>
                </div>

                <!-- Product Grid -->
                <?php if (count($services) > 0): ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-4 g-4 pt-2">
                        <?php foreach ($services as $index => $service): 
                            // Determine dummy data for UI testing based on ID to make it somewhat consistent
                            $rating = number_format(4 + ($service['id'] % 11) / 10, 1);
                            $sold = ($service['id'] * 7) % 200 + 10;
                            // Adding a dummy original price (strikethrough) slightly higher
                            $origPrice = $service['price'] * (1 + ($service['id'] % 3 + 1) * 0.1);
                            
                            // Icons based on category
                            $icon = 'fa-cogs';
                            $nameLower = strtolower($service['service_name']);
                            if (strpos($nameLower, 'desain') !== false || strpos($nameLower, 'grafik') !== false) $icon = 'fa-pen-nib';
                            if (strpos($nameLower, 'kebersihan') !== false || strpos($nameLower, 'clean') !== false) $icon = 'fa-broom';
                            if (strpos($nameLower, 'konsultan') !== false) $icon = 'fa-comments';
                            if (strpos($nameLower, 'web') !== false || strpos($nameLower, 'app') !== false) $icon = 'fa-laptop-code';
                        ?>
                            <div class="col">
                                <div class="card product-card h-100 bg-white">
                                    <div class="product-img-wrapper position-relative bg-light d-flex align-items-center justify-content-center">
                                        <!-- Placeholder Product visual: A subtle icon since we don't have product images -->
                                        <i class="fas <?= $icon ?> text-muted opacity-25" style="font-size: 5rem;"></i>
                                        
                                        <!-- Keep original logo small at top left -->
                                        <?php if (!empty($service['logo_path']) && file_exists('assets/uploads/logos/' . $service['logo_path'])): ?>
                                            <img src="assets/uploads/logos/<?= htmlspecialchars($service['logo_path']) ?>" alt="Logo" class="position-absolute bg-white rounded shadow-sm p-1" style="width: 32px; height: 32px; top: 10px; left: 10px; object-fit: contain;">
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex align-items-end gap-2 mb-2 w-100 flex-wrap">
                                            <h4 class="product-price mb-0 fw-bold text-dark fs-6" style="white-space: nowrap;">Rp <?= number_format($service['price'], 0, ',', '.') ?></h4>
                                            <span class="text-muted text-decoration-line-through fw-medium" style="font-size: 0.75rem;">Rp <?= number_format($origPrice, 0, ',', '.') ?></span>
                                        </div>
                                        
                                        <h5 class="product-title text-dark fw-bold mb-1 lh-base mt-1" style="font-size: 0.95rem; max-height: 2.8em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; min-height: 2.8em;" ><?= htmlspecialchars($service['service_name']) ?> <span class="fw-normal text-muted">(<?= htmlspecialchars($service['business_name']) ?>)</span></h5>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center product-footer pt-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rating badge text-dark px-2 py-1 rounded-pill fw-bold d-flex align-items-center gap-1" style="background-color: #FFC107;">
                                                    <i class="fas fa-star" style="font-size: 0.7rem;"></i> <?= $rating ?>
                                                </div>
                                                <div class="sold text-muted ms-3 fw-medium" style="font-size: 0.85rem;">
                                                    Terjual <?= $sold ?>
                                                </div>
                                            </div>
                                            <button class="btn btn-action rounded-circle d-flex align-items-center justify-content-center p-0" title="Contact/Action" onclick="alert('Fitur detail MVP')" style="width: 35px; height: 35px; background: var(--primary-color);">
                                                <i class="fas fa-shopping-basket text-white" style="font-size: 0.9rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                        <h4 class="mt-4 text-dark fw-bold">Hasil tidak ditemukan</h4>
                        <p class="text-muted">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                        <?php if (!empty($searchQuery)): ?>
                            <a href="index.php" class="btn btn-primary mt-3 px-4 py-2 rounded-pill fw-medium">Hapus Pencarian</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 mt-auto">
        <div class="container text-center text-muted border-top pt-4">
            <p class="mb-0 fw-medium">&copy; <?= date('Y') ?> Jasaku. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
