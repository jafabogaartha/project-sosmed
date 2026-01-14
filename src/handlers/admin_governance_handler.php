<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';
check_auth();
check_role(['admin']); // Hanya Admin

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user'])) {
    
    $user_id = $_POST['user_id'];
    $brand_id = $_POST['brand_id'];
    $action = $_POST['action']; // 'add' or 'remove'

    try {
        if ($action === 'add') {
            // Tambah Assignment (Ignore jika duplikat)
            $stmt = $pdo->prepare("INSERT IGNORE INTO team_assignments (user_id, brand_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $brand_id]);
        } elseif ($action === 'remove') {
            // Hapus Assignment
            $stmt = $pdo->prepare("DELETE FROM team_assignments WHERE user_id = ? AND brand_id = ?");
            $stmt->execute([$user_id, $brand_id]);
        }
        
        header("Location: ../../public/admin_console.php?status=success");
        
    } catch (PDOException $e) {
        die("Gagal mengatur tim: " . $e->getMessage());
    }
}
?>