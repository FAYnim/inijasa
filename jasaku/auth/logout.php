<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

// Clear all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to login
header('Location: /jasaku/auth/login.php');
exit;
