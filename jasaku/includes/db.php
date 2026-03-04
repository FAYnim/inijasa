<?php
/**
 * Database Connection
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jasaku');

$conn = null;

function getDB() {
    global $conn;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Koneksi database gagal: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

function closeDB() {
    global $conn;
    if ($conn !== null) {
        $conn->close();
        $conn = null;
    }
}
