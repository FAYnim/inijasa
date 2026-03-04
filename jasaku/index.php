<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    if (userHasBusiness(getCurrentUserId())) {
        header('Location: /jasaku/pages/dashboard.php');
    } else {
        header('Location: /jasaku/auth/setup-business.php');
    }
} else {
    header('Location: /jasaku/auth/login.php');
}
exit;
