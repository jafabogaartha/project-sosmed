<?php
require_once __DIR__ . '/../config/database_registry.php';
require_once __DIR__ . '/../helpers/security_guard.php';

check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /**
     * HANDLER PEMBARUAN STATUS (WORKFLOW ONLY)
     * Digunakan melalui dropdown di Brand Detail atau tombol di Workbench
     */
    if (isset($_POST['quick_status_update']) || isset($_POST['update_status'])) {
        $task_id = $_POST['task_id'];
        $new_status = $_POST['new_status'];
        $current_user_id = $_SESSION['user_id'];
        $current_role = $_SESSION['role'];

        try {
            /**
             * SECURITY & ROLE VALIDATION
             * Memastikan Editor tidak bisa melompat ke status 'uploaded' 
             * karena itu adalah hak akses Specialist.
             */
            if ($new_status === 'uploaded' && $current_role !== 'specialist') {
                die("Akses Ditolak: Hanya Specialist yang dapat menandai konten sebagai Uploaded.");
            }

            /**
             * LOGIC KPI (Key Performance Indicator)
             * Status yang dianggap sebagai fase eksekusi oleh Editor.
             * Jika status diubah ke fase ini oleh user dengan role 'editor', 
             * maka sistem mencatatnya sebagai penanggung jawab (editor_id).
             */
            $execution_phases = ['process_vo', 'editing', 'ready_upload'];

            if (in_array($new_status, $execution_phases) && $current_role === 'editor') {
                // Update status dan kunci Editor ID untuk KPI
                $stmt = $pdo->prepare("UPDATE content_tasks SET status = ?, editor_id = ? WHERE id = ?");
                $stmt->execute([$new_status, $current_user_id, $task_id]);
            } else {
                /**
                 * Untuk status lainnya (planning) atau jika dilakukan oleh Specialist (uploaded),
                 * kita hanya memperbarui statusnya saja tanpa mengubah editor_id yang sudah ada.
                 */
                $stmt = $pdo->prepare("UPDATE content_tasks SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $task_id]);
            }
            
            $msg = "status_updated";
            
            /**
             * SMART REDIRECT
             * Mengembalikan user ke halaman sebelumnya dengan feedback sukses.
             */
            if (isset($_SERVER['HTTP_REFERER'])) {
                // Membersihkan parameter pesan lama agar URL tidak menumpuk
                $clean_referer = preg_replace('/([&?]msg=[^&]*)|([&?]error=[^&]*)/', '', $_SERVER['HTTP_REFERER']);
                $connector = (strpos($clean_referer, '?') !== false) ? "&" : "?";
                header("Location: " . $clean_referer . $connector . "msg=$msg");
            } else {
                header("Location: ../../public/dashboard_analytics.php?msg=$msg");
            }
            exit;

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}