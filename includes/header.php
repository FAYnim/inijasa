<?php
$business_name = 'IniJasa';
$user_name = 'User';

if (isset($_SESSION['business_id']) && isset($conn)) {
    $business_id = $_SESSION['business_id'];
    
    // Generate daily notifications
    if (function_exists('generate_daily_notifications')) {
        generate_daily_notifications($conn, $business_id);
    }
    
    // Fetch unread notifications
    $unread_notifs = [];
    $unread_count = 0;
    if (function_exists('get_unread_notifications')) {
        $unread_notifs = get_unread_notifications($conn, $business_id, 5);
        $unread_count = get_unread_notification_count($conn, $business_id);
    }

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
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?>IniJasa</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #E55A2A;
            --success-color: #059669;
            --danger-color: #EF4444;
            --warning-color: #F59E0B;
            --dark-color: #0A2342;
            --light-color: #FAFAFA;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        
        /* Notifications */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
            color: var(--dark-color);
            transition: color 0.2s;
        }
        
        .notification-bell:hover {
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: var(--danger-color);
            color: white;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 10px;
            line-height: 1;
        }
        
        .notification-menu {
            width: 320px;
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .notification-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #E5E7EB;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #F9FAFB;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #E5E7EB;
            display: flex;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: background 0.2s;
        }
        
        .notification-item:hover {
            background: var(--light-color);
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notif-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.85rem;
        }
        
        .notif-icon.deal_stale { background: #FEF3C7; color: #D97706; }
        .notif-icon.overdue_payment { background: #FEE2E2; color: #DC2626; }
        .notif-icon.close_date_passed { background: #E0E7FF; color: #4F46E5; }
        
        .notif-content {
            flex-grow: 1;
            font-size: 0.85rem;
            line-height: 1.3;
        }
        
        .notif-time {
            font-size: 0.7rem;
            color: #6B7280;
            margin-top: 0.35rem;
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
            <div class="business-switcher" data-bs-toggle="modal" data-bs-target="#businessSwitcherModal">
                <i class="fas fa-briefcase"></i>
                <span id="currentBusinessName"><?= e($business_name) ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
        
        <div class="topbar-right">
            <?php if (isset($business_id)): ?>
            <div class="dropdown">
                <div class="notification-bell" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="far fa-bell fs-5"></i>
                    <?php if (isset($unread_count) && $unread_count > 0): ?>
                    <span class="notification-badge" id="notifBadge"><?= $unread_count > 99 ? '99+' : $unread_count ?></span>
                    <?php endif; ?>
                </div>
                <div class="dropdown-menu dropdown-menu-end notification-menu">
                    <div class="notification-header">
                        <span class="fw-semibold">Notifikasi</span>
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                        <a href="#" class="text-primary text-decoration-none small" id="markAllRead">Tandai dibaca</a>
                        <?php endif; ?>
                    </div>
                    <div class="notification-list" id="notifList">
                        <?php if (empty($unread_notifs)): ?>
                        <div class="p-3 text-center text-muted small">
                            Tidak ada notifikasi baru
                        </div>
                        <?php else: ?>
                            <?php foreach ($unread_notifs as $notif): 
                                $icon = '';
                                $link = '#';
                                if ($notif['type'] == 'deal_stale' || $notif['type'] == 'close_date_passed') {
                                    $icon = '<i class="fas fa-exclamation-circle"></i>';
                                    $link = "deal-detail?id=" . $notif['related_id'];
                                } elseif ($notif['type'] == 'overdue_payment') {
                                    $icon = '<i class="fas fa-file-invoice"></i>';
                                    $link = "invoices";
                                }
                            ?>
                            <a href="<?= htmlspecialchars($link) ?>" class="notification-item">
                                <div class="notif-icon <?= htmlspecialchars($notif['type']) ?>">
                                    <?= $icon ?>
                                </div>
                                <div class="notif-content">
                                    <div class="notif-message"><?= htmlspecialchars($notif['message']) ?></div>
                                    <div class="notif-time"><?= date('d M Y, H:i', strtotime($notif['created_at'])) ?></div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user_name, 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline"><?= e($user_name) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="business-profile"><i class="fas fa-cog me-2"></i>Pengaturan Bisnis</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markAllBtn = document.getElementById('markAllRead');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    fetch('api/mark-notifications-read.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI
                            const badge = document.getElementById('notifBadge');
                            if (badge) badge.remove();
                            markAllBtn.remove();
                            document.getElementById('notifList').innerHTML = '<div class="p-3 text-center text-muted small">Tidak ada notifikasi baru</div>';
                        }
                    })
                    .catch(err => console.error('Error marking notifications read:', err));
                });
            }
        });
    </script>
