<?php
session_start();
require_once __DIR__ . '/../src/config/database_registry.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';
check_auth();

$uid = $_SESSION['user_id'];

// Mengambil task dengan JOIN ke tabel Brands agar Nama Brand muncul
$sql = "SELECT t.*, b.brand_name, b.brand_code 
        FROM content_tasks t
        JOIN brands b ON t.brand_id = b.id
        WHERE t.creator_id = ? AND t.status = 'waiting_footage' 
        ORDER BY t.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$uid]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Queue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap'); body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }</style>
</head>
<body class="text-stone-900">
    <?php include 'layouts/sidebar.php'; ?>
    
    <main class="ml-64 p-8">
        <h1 class="text-3xl font-extrabold mb-6 flex items-center gap-3">
            <i data-lucide="camera" class="w-8 h-8 text-red-500"></i> Production Queue
        </h1>
        
        <?php foreach($tasks as $task): ?>
            <div class="bg-white border-2 border-stone-900 rounded-2xl p-6 mb-6 shadow-[4px_4px_0px_0px_#1c1917] flex justify-between items-center">
                <div>
                    <!-- Badge Brand -->
                    <span class="bg-red-100 border border-stone-900 px-2 py-0.5 rounded text-[10px] font-bold uppercase mb-2 inline-block tracking-wide">
                        <?= $task['brand_name'] ?>
                    </span>
                    
                    <h3 class="text-xl font-extrabold"><?= $task['title'] ?></h3>
                    <p class="text-xs font-bold text-stone-400 mt-1 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i> Created: <?= date('d M Y', strtotime($task['created_at'])) ?>
                    </p>
                </div>
                
                <form action="../src/handlers/production_workflow_handler.php" method="POST" class="flex gap-3 items-end">
                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">New Footage Link</label>
                        <input type="url" name="link_raw_source" required placeholder="https://drive..." class="px-3 py-2 border-2 border-stone-900 rounded-lg font-bold text-sm w-64 focus:ring-2 focus:ring-green-200 outline-none">
                    </div>
                    <button type="submit" class="bg-green-400 border-2 border-stone-900 px-4 py-2 rounded-lg font-bold text-sm shadow-[2px_2px_0px_0px_#1c1917] hover:-translate-y-0.5 transition flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Resolve
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
        
        <?php if(count($tasks) === 0): ?>
            <div class="text-center py-12 border-2 border-dashed border-stone-300 rounded-xl">
                <div class="inline-flex justify-center items-center w-12 h-12 bg-stone-100 rounded-full mb-3">
                    <i data-lucide="thumbs-up" class="w-6 h-6 text-stone-400"></i>
                </div>
                <p class="font-bold text-stone-400">Queue is empty. Good job!</p>
            </div>
        <?php endif; ?>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>