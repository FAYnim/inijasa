<?php
/**
 * Logout Handler
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

session_start();

// Destroy all session data
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to landing page
header('Location: ../index.php');
exit();
?>
