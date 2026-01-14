<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';
require_once __DIR__ . '/../helpers/scraper_helper.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refresh_metrics'])) {
    
    $task_id = $_POST['task_id'];
    
    // 1. Ambil URL dari database
    $stmt = $pdo->prepare("SELECT url_tiktok, url_instagram FROM content_tasks WHERE id = ?");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    $tt_data = ['views' => 0, 'likes' => 0, 'comments' => 0, 'shares' => 0];
    $ig_data = ['views' => 0, 'likes' => 0, 'comments' => 0];

    // 2. Jalankan Scraper TikTok
    if (!empty($task['url_tiktok'])) {
        $scraped = scrapeTikTok($task['url_tiktok']);
        if ($scraped) $tt_data = $scraped;
    }

    // 3. Jalankan Scraper Instagram
    if (!empty($task['url_instagram'])) {
        $scraped = scrapeInstagram($task['url_instagram']);
        // Merge hasil scrape (jika ada)
        if ($scraped) {
            if(isset($scraped['likes'])) $ig_data['likes'] = $scraped['likes'];
            if(isset($scraped['comments'])) $ig_data['comments'] = $scraped['comments'];
            if(isset($scraped['views'])) $ig_data['views'] = $scraped['views'];
        }
    }

    // 4. Update Database
    try {
        $sql = "UPDATE content_tasks SET 
                tt_views = ?, tt_likes = ?, tt_comments = ?, tt_shares = ?,
                ig_views = ?, ig_likes = ?, ig_comments = ?,
                last_scraped_at = NOW()
                WHERE id = ?";
        
        $update = $pdo->prepare($sql);
        $update->execute([
            $tt_data['views'], $tt_data['likes'], $tt_data['comments'], $tt_data['shares'],
            $ig_data['views'], $ig_data['likes'], $ig_data['comments'],
            $task_id
        ]);

        // --- PERBAIKAN LOGIC REDIRECT DI SINI ---
        $referer = $_SERVER['HTTP_REFERER'] ?? '../../public/analytics_performance.php';
        
        // Cek apakah URL sebelumnya sudah punya parameter query (?)
        if (strpos($referer, '?') !== false) {
            $connector = "&"; // Jika sudah ada ?, pakai &
        } else {
            $connector = "?"; // Jika belum ada, pakai ?
        }

        // Redirect dengan URL yang valid
        header("Location: " . $referer . $connector . "msg=data_refreshed");
        exit;
        // ----------------------------------------

    } catch (PDOException $e) {
        die("Error Updating Analytics: " . $e->getMessage());
    }
}
?>