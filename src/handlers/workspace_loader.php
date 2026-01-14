<?php
// PERBAIKAN: Gunakan __DIR__ agar path tidak error
require_once __DIR__ . '/../config/database_registry.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Gunakan path absolut untuk redirect juga biar aman
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fungsi Helper untuk mengambil brand (Langsung dieksekusi)
function get_my_brands($pdo, $user_id, $role) {
    try {
        if ($role === 'admin') {
            /**
             * UPDATE LOGIC ADMIN:
             * Hanya menampilkan brand yang statusnya 'active' di Sidebar/Lobby.
             * Brand inactive hanya akan muncul di menu 'Brand Settings' (Admin Console).
             */
            $stmt = $pdo->query("SELECT * FROM brands WHERE status = 'active' ORDER BY brand_name ASC");
        } else {
            /**
             * UPDATE LOGIC USER:
             * Hanya menampilkan brand yang:
             * 1. Ditugaskan ke user tersebut (JOIN team_assignments)
             * 2. Statusnya 'active'
             */
            $stmt = $pdo->prepare("
                SELECT b.* FROM brands b
                JOIN team_assignments ta ON b.id = ta.brand_id
                WHERE ta.user_id = :uid AND b.status = 'active'
                ORDER BY b.brand_name ASC
            ");
            $stmt->execute(['uid' => $user_id]);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Error Loading Workspace: " . $e->getMessage());
    }
}
?>