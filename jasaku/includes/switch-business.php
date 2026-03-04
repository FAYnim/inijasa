<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

requireLogin();

$businessId = $_GET['id'] ?? null;

if ($businessId && hasPermission($businessId)) {
    setCurrentBusiness($businessId);
}

header('Location: /jasaku/pages/dashboard.php');
exit;
