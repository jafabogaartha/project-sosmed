<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ... (Kode pengambilan variabel $_POST biarkan sama) ...
    $brand_id = $_POST['brand_id'];
    $title = $_POST['title'];
    $target_tiktok = isset($_POST['target_tiktok']) ? 1 : 0;
    $target_instagram = isset($_POST['target_instagram']) ? 1 : 0;
    $script_tiktok = $_POST['script_tiktok'] ?? '';
    $caption_ig = $_POST['caption_instagram'] ?? '';
    $production_mode = $_POST['production_mode']; 
    $link_raw = $_POST['link_raw_source'] ?? '';
    
    // --- PERBAIKAN LOGIC STATUS BARU ---
    // Jika 'Need Take', masuk ke Planning.
    // Jika 'Ready Stock', langsung masuk ke Proses Editing.
    
    if ($production_mode === 'need_take') {
        $status = 'planning'; 
    } else {
        $status = 'planning'; 
    }
    // -----------------------------------

    try {
        $sql = "INSERT INTO content_tasks 
                (brand_id, creator_id, title, target_tiktok, target_instagram, 
                 script_tiktok, caption_instagram, production_mode, status, link_raw_source) 
                VALUES 
                (:bid, :cid, :title, :tk, :ig, :script, :caption, :mode, :status, :link)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'bid' => $brand_id,
            'cid' => $_SESSION['user_id'],
            'title' => $title,
            'tk' => $target_tiktok,
            'ig' => $target_instagram,
            'script' => $script_tiktok,
            'caption' => $caption_ig,
            'mode' => $production_mode,
            'status' => $status, // Ini sekarang berisi 'planning' atau 'editing'
            'link' => $link_raw
        ]);

        header("Location: ../../public/planner_studio.php?brand_id=$brand_id&msg=saved");

    } catch (PDOException $e) {
        die("Gagal menyimpan plan: " . $e->getMessage());
    }
}
?>