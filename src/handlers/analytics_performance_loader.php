<?php
require_once __DIR__ . '/../config/database_registry.php';

function get_performance_data($pdo, $user_id, $role) {
    /**
     * PERBAIKAN BUG AMBIGUOUS COLUMN:
     * Menggunakan alias 't.' untuk merujuk spesifik ke tabel content_tasks.
     * Ini mencegah bentrok dengan kolom 'status' milik tabel brands.
     */
    
    // 1. Definisikan Filter (WHERE) dengan alias 't'
    $where = "WHERE t.status = 'published'";
    
    if ($role === 'specialist') {
        $where .= " AND t.creator_id = $user_id";
    } elseif ($role === 'editor') {
        $where .= " AND t.editor_id = $user_id";
    }
    // Admin melihat semua data

    // 2. Ambil Summary Total (Total Views & Likes)
    // Perhatikan penambahan alias 't' setelah nama tabel content_tasks
    $sql_sum = "SELECT 
                SUM(t.tt_views) + SUM(t.ig_views) as total_reach,
                SUM(t.tt_likes) + SUM(t.ig_likes) as total_engagement
                FROM content_tasks t 
                $where";
    
    $summary = $pdo->query($sql_sum)->fetch();

    // 3. Ambil Daftar Konten (Diurutkan dari Views Tertinggi)
    // Join dengan brands aman karena kita sudah pakai 't.status' di $where
    $sql_list = "SELECT t.*, b.brand_name, 
                 (IFNULL(t.tt_views, 0) + IFNULL(t.ig_views, 0)) as popularity_score
                 FROM content_tasks t
                 JOIN brands b ON t.brand_id = b.id
                 $where
                 ORDER BY popularity_score DESC";
    
    $contents = $pdo->query($sql_list)->fetchAll();

    return [
        'summary' => $summary,
        'contents' => $contents
    ];
}
?>