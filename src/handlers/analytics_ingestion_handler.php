<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';

/**
 * HANDLER ANALYTICS INGESTION (FINAL)
 * Fungsi: Menyimpan link postingan TikTok & Instagram, lalu mengunci status ke 'published'
 */

// 1. Proteksi Akses
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_url_tracking'])) {
    
    $task_id = $_POST['task_id'];
    $user_role = $_SESSION['role'];

    // 2. Keamanan Role: Hanya Specialist atau Admin yang boleh melakukan closing/archive
    if ($user_role !== 'specialist' && $user_role !== 'admin') {
        die("Unauthorized access: Your role is not permitted to perform this action.");
    }

    // 3. Ambil data link dan bersihkan spasi
    $url_tiktok = isset($_POST['url_tiktok']) ? trim($_POST['url_tiktok']) : null;
    $url_instagram = isset($_POST['url_instagram']) ? trim($_POST['url_instagram']) : null;

    // 4. Validasi Sederhana: Pastikan setidaknya satu link diisi jika platformnya dicentang
    // Validasi format URL menggunakan filter PHP
    if (!empty($url_tiktok) && !filter_var($url_tiktok, FILTER_VALIDATE_URL)) {
        $msg = "error_invalid_tiktok_url";
    } elseif (!empty($url_instagram) && !filter_var($url_instagram, FILTER_VALIDATE_URL)) {
        $msg = "error_invalid_instagram_url";
    } else {
        try {
            /**
             * 5. EKSEKUSI DATABASE
             * Mengupdate kolom url_tiktok dan url_instagram secara spesifik.
             * Memaksa status menjadi 'published' sebagai tahap akhir workflow.
             */
            $sql = "UPDATE content_tasks 
                    SET url_tiktok = :url_tk, 
                        url_instagram = :url_ig, 
                        status = 'published' 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':url_tk' => !empty($url_tiktok) ? $url_tiktok : null,
                ':url_ig' => !empty($url_instagram) ? $url_instagram : null,
                ':id'     => $task_id
            ]);

            $msg = "content_published";

        } catch (PDOException $e) {
            error_log("Database Error in Analytics Ingestion: " . $e->getMessage());
            die("Critical System Error: Failed to save publication data.");
        }
    }

    /**
     * 6. SMART REDIRECT
     * Mengembalikan user ke halaman sebelumnya dengan membersihkan sisa parameter lama di URL
     */
    if (isset($_SERVER['HTTP_REFERER'])) {
        $clean_referer = preg_replace('/([&?]msg=[^&]*)|([&?]error=[^&]*)/', '', $_SERVER['HTTP_REFERER']);
        $connector = (strpos($clean_referer, '?') !== false) ? "&" : "?";
        header("Location: " . $clean_referer . $connector . "msg=$msg");
    } else {
        header("Location: ../../public/dashboard_analytics.php?msg=$msg");
    }
    exit;

} else {
    // Kick balik jika diakses langsung tanpa melalui form POST
    header("Location: ../../public/dashboard_analytics.php");
    exit;
}