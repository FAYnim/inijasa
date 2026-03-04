<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

requireLogin();

$currentBusiness = getBusinessById(getCurrentBusinessId());
$businesses = getUserBusinesses(getCurrentUserId());
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' - ' : '' ?>Jasaku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/jasaku/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/jasaku/pages/dashboard.php">
                <i class="fas fa-briefcase me-2"></i>Jasaku
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (count($businesses) > 1): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-building me-1"></i>
                            <?= sanitize($currentBusiness['business_name'] ?? 'Pilih Bisnis') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($businesses as $biz): ?>
                            <li>
                                <a class="dropdown-item <?= $biz['id'] == getCurrentBusinessId() ? 'active' : '' ?>" 
                                   href="/jasaku/includes/switch-business.php?id=<?= $biz['id'] ?>">
                                    <?= sanitize($biz['business_name']) ?>
                                    <?php if ($biz['is_primary']): ?>
                                    <span class="badge bg-secondary ms-1">Primary</span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= sanitize($_SESSION['user_name'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/jasaku/pages/business-profile.php"><i class="fas fa-cog me-2"></i>Profil Bisnis</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/jasaku/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
