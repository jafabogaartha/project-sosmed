<?php
// Tampilkan Error untuk Debugging (Hapus saat production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // User default XAMPP
define('DB_PASS', '');          // Password default XAMPP (kosong)
define('DB_NAME', 'db_project_sosmed');

// Base URL (Sesuaikan nama folder)
define('BASE_URL', 'http://localhost/project-sosmed/public/');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Setting Error Mode ke Exception (Biar kalau error langsung muncul pesannya)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    die("❌ KONEKSI DATABASE GAGAL: " . $e->getMessage());
}

// Start Session Otomatis
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>