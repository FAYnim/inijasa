<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <nav class="nav flex-column">
        <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" href="/jasaku/pages/dashboard.php">
            <i class="fas fa-chart-line me-2"></i>Dashboard
        </a>
        <a class="nav-link <?= $currentPage == 'deals.php' || $currentPage == 'deal-form.php' ? 'active' : '' ?>" href="/jasaku/pages/deals.php">
            <i class="fas fa-handshake me-2"></i>Deals
        </a>
        <a class="nav-link <?= $currentPage == 'clients.php' || $currentPage == 'client-form.php' ? 'active' : '' ?>" href="/jasaku/pages/clients.php">
            <i class="fas fa-users me-2"></i>Klien
        </a>
        <a class="nav-link <?= $currentPage == 'services.php' || $currentPage == 'service-form.php' ? 'active' : '' ?>" href="/jasaku/pages/services.php">
            <i class="fas fa-box me-2"></i>Paket Jasa
        </a>
        <a class="nav-link <?= $currentPage == 'finance.php' || $currentPage == 'transaction-form.php' ? 'active' : '' ?>" href="/jasaku/pages/finance.php">
            <i class="fas fa-wallet me-2"></i>Keuangan
        </a>
        <hr class="my-2">
        <a class="nav-link <?= $currentPage == 'business-profile.php' ? 'active' : '' ?>" href="/jasaku/pages/business-profile.php">
            <i class="fas fa-building me-2"></i>Profil Bisnis
        </a>
    </nav>
</div>
