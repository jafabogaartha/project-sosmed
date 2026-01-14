<?php
require_once __DIR__ . '/../config/database_registry.php';

// 1. Ambil semua konten milik Brand tertentu (Untuk Brand Detail)
function get_brand_contents($pdo, $brand_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM content_tasks 
            WHERE brand_id = :bid 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['bid' => $brand_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Error loading contents: " . $e->getMessage());
    }
}

// 2. Ambil Info Brand (Nama, Kode, dll)
function get_brand_detail($pdo, $brand_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = :bid");
        $stmt->execute(['bid' => $brand_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Error loading brand info: " . $e->getMessage());
    }
}

// 3. LOGIC BARU: Ambil Detail Satu Task (Untuk Halaman Task Detail)
function get_task_detail($pdo, $task_id) {
    try {
        // Kita JOIN ke users agar tahu siapa yang buat (Creator Name)
        // Kita JOIN ke brands agar tahu ini konten brand apa
        $stmt = $pdo->prepare("
            SELECT t.*, b.brand_name, b.brand_code, u.full_name as creator_name
            FROM content_tasks t
            JOIN brands b ON t.brand_id = b.id
            LEFT JOIN users u ON t.creator_id = u.id
            WHERE t.id = :id
        ");
        $stmt->execute(['id' => $task_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Error loading task detail: " . $e->getMessage());
    }
}
?>