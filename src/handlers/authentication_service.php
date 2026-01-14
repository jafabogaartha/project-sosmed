<?php
require_once __DIR__ . '/../config/database_registry.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Cari User
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verifikasi Password (Simple check, gunakan password_verify di production)
    if ($user && $user['password_hash'] === $password) {
        
        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['avatar_color'] = $user['avatar_color'];

        // Redirect sesuai Role
        if ($user['role'] === 'admin') {
            header("Location: ../../public/admin_console.php");
        } elseif ($user['role'] === 'editor') {
            header("Location: ../../public/editor_workbench.php");
        } else {
            // Specialist
            header("Location: ../../public/brand_hub.php");
        }
        exit;
    } else {
        // Gagal
        header("Location: ../../public/index.php?error=invalid_login");
        exit;
    }
}
?>