<?php
require_once '../config/database_registry.php';
require_once '../helpers/security_guard.php';

check_auth();

// Digunakan saat Spec melunasi hutang syuting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $link_raw = $_POST['link_raw_source'];

    try {
        $stmt = $pdo->prepare("UPDATE content_tasks SET link_raw_source = ?, status = 'ready_to_edit' WHERE id = ?");
        $stmt->execute([$link_raw, $task_id]);

        header("Location: ../../public/production_deck.php?msg=moved_to_editor");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>