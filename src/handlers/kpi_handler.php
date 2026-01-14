<?php
require_once __DIR__ . '/../config/database_registry.php';

function get_team_performance_report($pdo) {
    // 1. Data Performa Specialist (Berdasarkan jumlah narasi yang dibuat)
    $spec_query = "SELECT u.full_name, u.avatar_color, COUNT(t.id) as total_work
                   FROM users u
                   LEFT JOIN content_tasks t ON u.id = t.creator_id
                   WHERE u.role = 'specialist'
                   GROUP BY u.id
                   ORDER BY total_work DESC";
    $specialists = $pdo->query($spec_query)->fetchAll();

    // 2. Data Performa Editor (Berdasarkan jumlah status Siap Upload/Uploaded)
    $editor_query = "SELECT u.full_name, u.avatar_color, 
                     COUNT(CASE WHEN t.status IN ('ready_upload', 'uploaded') THEN 1 END) as total_finished
                     FROM users u
                     LEFT JOIN content_tasks t ON u.id = t.editor_id
                     WHERE u.role = 'editor'
                     GROUP BY u.id
                     ORDER BY total_finished DESC";
    $editors = $pdo->query($editor_query)->fetchAll();

    return [
        'specialists' => $specialists,
        'editors' => $editors
    ];
}