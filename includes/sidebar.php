<?php
/**
 * Sidebar Navigation
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: white;
        border-right: 1px solid #E5E7EB;
        z-index: 101;
        overflow-y: auto;
    }
    
    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--dark-color);
    }
    
    .sidebar-logo-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-color);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .sidebar-logo-text {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }
    
    .sidebar-nav {
        padding: 1rem 0;
    }
    
    .nav-section {
        padding: 1rem 1.5rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6B7280;
        letter-spacing: 0.05em;
    }
    
    .nav-item {
        list-style: none;
        margin: 0;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: #4B5563;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    .nav-link:hover {
        background: var(--light-color);
        color: var(--primary-color);
    }
    
    .nav-link.active {
        background: rgba(255, 107, 53, 0.1);
        color: var(--primary-color);
        border-right: 3px solid var(--primary-color);
    }
    
    .nav-link i {
        width: 20px;
        text-align: center;
    }
    
    .sidebar-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem 1.5rem;
        border-top: 1px solid #E5E7EB;
        background: white;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h1 class="sidebar-logo-text">Jasaku</h1>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">Menu Utama</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="deals.php" class="nav-link <?= $current_page == 'deals.php' ? 'active' : '' ?>">
                    <i class="fas fa-handshake"></i>
                    <span>Deals</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="clients.php" class="nav-link <?= $current_page == 'clients.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Klien</span>
                </a>
            </li>
        </ul>
        
        <div class="nav-section">Layanan & Keuangan</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="services.php" class="nav-link <?= $current_page == 'services.php' ? 'active' : '' ?>">
                    <i class="fas fa-box"></i>
                    <span>Paket Jasa</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="finance.php" class="nav-link <?= $current_page == 'finance.php' ? 'active' : '' ?>">
                    <i class="fas fa-wallet"></i>
                    <span>Keuangan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="invoices.php" class="nav-link <?= in_array($current_page, ['invoices.php', 'invoice-form.php', 'invoice-detail.php']) ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Invoice</span>
                </a>
            </li>
        </ul>
        
        <div class="nav-section">Pengaturan</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="business-profile.php" class="nav-link <?= $current_page == 'business-profile.php' ? 'active' : '' ?>">
                    <i class="fas fa-building"></i>
                    <span>Profil Bisnis</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
