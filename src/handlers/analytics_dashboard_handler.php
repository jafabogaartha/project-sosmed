<?php
require_once __DIR__ . '/../config/database_registry.php';

function get_full_analytics($pdo, $user_id, $role) {
    
    // 1. FILTER QUERY BERDASARKAN ROLE
    $where_main = "";
    $where_platform = "";
    
    if ($role === 'specialist') {
        $where_main = "WHERE creator_id = $user_id";
        $where_platform = "WHERE creator_id = $user_id";
    } elseif ($role === 'editor') {
        $where_main = "WHERE brand_id IN (SELECT brand_id FROM team_assignments WHERE user_id = $user_id)";
        $where_platform = $where_main;
    } 

    // 2. STATS UTAMA (FIXED: Menambahkan perhitungan in_progress)
    $sql_main = "SELECT 
        COUNT(*) as total,
        -- PERBAIKAN DI SINI: Menghitung total tugas yang sedang berjalan
        COUNT(CASE WHEN status IN ('planning', 'process_vo', 'editing', 'waiting_footage') THEN 1 END) as in_progress,
        
        -- Pecahan status
        COUNT(CASE WHEN status = 'planning' THEN 1 END) as planning,
        COUNT(CASE WHEN status = 'process_vo' THEN 1 END) as vo,
        COUNT(CASE WHEN status = 'editing' THEN 1 END) as editing,
        COUNT(CASE WHEN status = 'ready_upload' THEN 1 END) as ready,
        COUNT(CASE WHEN status = 'uploaded' THEN 1 END) as uploaded,
        COUNT(CASE WHEN status = 'published' THEN 1 END) as published
        FROM content_tasks $where_main";
        
    $main_stats = $pdo->query($sql_main)->fetch();

    // 3. PLATFORM SPLIT
    $sql_platform = "SELECT 
        SUM(target_tiktok) as total_tiktok,
        SUM(target_instagram) as total_instagram
        FROM content_tasks $where_platform";
    $platforms = $pdo->query($sql_platform)->fetch();

    // 4. BRAND PERFORMANCE
    $brand_join = "";
    if ($role !== 'admin') {
        $brand_join = "JOIN team_assignments ta ON b.id = ta.brand_id AND ta.user_id = $user_id";
    }
    
    $sql_brands = "SELECT b.id, b.brand_name, b.brand_code, COUNT(t.id) as task_count 
        FROM brands b 
        $brand_join
        LEFT JOIN content_tasks t ON b.id = t.brand_id 
        GROUP BY b.id 
        ORDER BY task_count DESC";
    $brand_stats = $pdo->query($sql_brands)->fetchAll();

    // 5. KPI LEADERBOARD (Khusus Admin)
    $team_stats = [];
    if ($role === 'admin') {
        $specs = $pdo->query("SELECT u.full_name, u.avatar_color, COUNT(t.id) as score 
            FROM users u LEFT JOIN content_tasks t ON u.id = t.creator_id 
            WHERE u.role = 'specialist' GROUP BY u.id ORDER BY score DESC LIMIT 5")->fetchAll();

        $eds = $pdo->query("SELECT u.full_name, u.avatar_color, COUNT(t.id) as score 
            FROM users u LEFT JOIN content_tasks t ON u.id = t.editor_id 
            WHERE u.role = 'editor' AND t.status IN ('ready_upload', 'uploaded', 'published') 
            GROUP BY u.id ORDER BY score DESC LIMIT 5")->fetchAll();
            
        $team_stats = ['specs' => $specs, 'editors' => $eds];
    }

    return [
        'main' => $main_stats,
        'platforms' => $platforms,
        'brands' => $brand_stats,
        'team' => $team_stats,
        'weekly' => [] // Placeholder untuk grafik mingguan jika diperlukan
    ];
}