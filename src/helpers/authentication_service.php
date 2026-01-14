<?php
// 1. Wajib memulai session di baris paling atas
session_start();

// 2. Sesuaikan path koneksi database Anda
require_once '../config/database_registry.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dan bersihkan whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 3. Cari User berdasarkan email
    // Pastikan variabel koneksi database Anda bernama $pdo. 
    // Jika namanya $conn atau $db, sesuaikan di sini.
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Verifikasi Password
    // Kita MENGGANTI operator '===' dengan fungsi password_verify()
    // Ini akan mencocokkan input password user dengan hash di database
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Login Berhasil: Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role']; // admin, editor, atau specialist
        $_SESSION['avatar_color'] = $user['avatar_color'];

        // 5. Redirect Logika
        if ($user['role'] === 'admin') {
            header("Location: ../../public/admin_console.php");
        } elseif ($user['role'] === 'editor') {
            header("Location: ../../public/brand_hub.php");
        } else {
            // Untuk Specialist (Seivi)
            header("Location: ../../public/brand_hub.php");
        }
        exit;

    } else {
        // Login Gagal (Email salah atau Password salah)
        header("Location: ../../public/index.php?error=invalid_login");
        exit;
    }
} else {
    // Jika akses bukan via POST, tendang balik
    header("Location: ../../public/index.php");
    exit;
}
?>