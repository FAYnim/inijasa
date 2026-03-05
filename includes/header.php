<?php
$business_name = 'Jasaku';
$user_name = 'User';

if (isset($_SESSION['business_id']) && isset($conn)) {
    $business_id = $_SESSION['business_id'];
    $stmt = mysqli_prepare($conn, "SELECT business_name FROM businesses WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $business_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $business_name = $row['business_name'];
    }
}

if (isset($_SESSION['user_id']) && isset($conn)) {
    $user_id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT full_name FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $user_name = $row['full_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?>Jasaku</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4F46E5;
            --secondary-color: #06B6D4;
            --success-color: #10B981;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --dark-color: #1F2937;
            --light-color: #F9FAFB;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-color);
        }
        
        .topbar {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 1.5rem;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .business-switcher {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--light-color);
            border-radius: 8px;
            border: 1px solid #E5E7EB;
            cursor: pointer;
        }
        
        .business-switcher:hover {
            background: #E5E7EB;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .user-profile:hover {
            background: var(--light-color);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 72px;
            padding: 2rem;
            min-height: calc(100vh - 72px);
        }
        
        @media (max-width: 768px) {
            .topbar {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="business-switcher">
                <i class="fas fa-briefcase"></i>
                <span><?= e($business_name) ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
        
        <div class="topbar-right">
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user_name, 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline"><?= e($user_name) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="business-profile.php"><i class="fas fa-cog me-2"></i>Pengaturan Bisnis</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
