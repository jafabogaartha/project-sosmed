<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';

check_auth();
check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_brand_status'])) {
    $brand_id = $_POST['brand_id'];
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === 'active') ? 'inactive' : 'active';

    try {
        $stmt = $pdo->prepare("UPDATE brands SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $brand_id]);

        header("Location: ../../public/admin_brand_settings.php?msg=status_updated");
        exit;
    } catch (PDOException $e) {
        die("Error updating brand status: " . $e->getMessage());
    }
}