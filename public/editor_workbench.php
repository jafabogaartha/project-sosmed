<?php
session_start();
require_once __DIR__ . '/../src/config/database_registry.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/helpers/neobrutalism_ui.php';
check_auth();

$uid = $_SESSION['user_id'];

/**
 * LOGIC DATA:
 * Mengambil semua tugas dari brand yang ditugaskan ke User ini.
 * Mengurutkan dari yang terbaru diperbarui.
 */
$sql = "SELECT t.*, b.brand_name, b.brand_code 
        FROM content_tasks t
        JOIN team_assignments ta ON t.brand_id = ta.brand_id
        JOIN brands b ON t.brand_id = b.id
        WHERE ta.user_id = :uid
        ORDER BY t.updated_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $uid]);
$tasks = $stmt->fetchAll();

// Hitung jumlah untuk label tombol filter
$count_active = 0;
$count_done = 0;
$count_published = 0;

foreach($tasks as $t) {
    if(in_array($t['status'], ['planning', 'process_vo', 'editing', 'waiting_footage'])) $count_active++;
    if(in_array($t['status'], ['ready_upload', 'uploaded'])) $count_done++;
    if($t['status'] === 'published') $count_published++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workbench - StudioSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
        
        .filter-btn.active {
            background-color: #1c1917;
            color: white;
            box-shadow: none;
            transform: translateY(2px);
        }
    </style>
</head>
<body class="text-stone-900">
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-8">
        <!-- HEADER & FILTERS -->
        <header class="mb-10 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight uppercase italic">Production Workbench</h1>
                <p class="text-stone-400 font-bold text-xs uppercase tracking-[0.2em] mt-1">Manage and track content lifecycle</p>
            </div>

            <!-- TAB FILTER SYSTEM -->
            <div class="flex flex-wrap bg-white border-2 border-stone-900 rounded-2xl p-1.5 shadow-[4px_4px_0px_0px_#1c1917]">
                <button onclick="filterTasks('active', this)" class="filter-btn active px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> In Production (<?= $count_active ?>)
                </button>
                <button onclick="filterTasks('done', this)" class="filter-btn px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Ready & Uploaded (<?= $count_done ?>)
                </button>
                <button onclick="filterTasks('published', this)" class="filter-btn px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2">
                    <i data-lucide="archive" class="w-3.5 h-3.5"></i> Final Published (<?= $count_published ?>)
                </button>
            </div>
        </header>

        <!-- CARD GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="taskContainer">
            <?php if(count($tasks) > 0): ?>
                <?php foreach($tasks as $task): 
                    // Menentukan kategori filter
                    if(in_array($task['status'], ['planning', 'process_vo', 'editing', 'waiting_footage'])) $category = 'active';
                    elseif(in_array($task['status'], ['ready_upload', 'uploaded'])) $category = 'done';
                    else $category = 'published';
                ?>
                    <div class="task-card bg-white border-2 border-stone-900 rounded-3xl p-7 shadow-[5px_5px_0px_0px_#1c1917] flex flex-col transition-all hover:translate-y-[-2px]" data-category="<?= $category ?>">
                        
                        <!-- Top Meta -->
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex flex-col gap-1">
                                <span class="bg-indigo-50 text-indigo-700 border-2 border-indigo-100 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest inline-block w-fit">
                                    <?= htmlspecialchars($task['brand_name']) ?>
                                </span>
                                <span class="text-[9px] font-bold text-stone-300 uppercase italic">#ID-<?= $task['id'] ?></span>
                            </div>
                            <div class="flex gap-1.5">
                                <?php if($task['target_tiktok']): ?>
                                    <div class="w-7 h-7 bg-stone-900 text-white rounded-lg flex items-center justify-center border-2 border-stone-900 shadow-[1px_1px_0px_0px_#000]"><i data-lucide="music-2" class="w-3.5 h-3.5"></i></div>
                                <?php endif; ?>
                                <?php if($task['target_instagram']): ?>
                                    <div class="w-7 h-7 bg-pink-50 flex items-center justify-center text-pink-600 rounded-lg border-2 border-pink-100 shadow-[1px_1px_0px_0px_#ec4899]"><i data-lucide="instagram" class="w-3.5 h-3.5"></i></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Content Title -->
                        <div class="mb-8 flex-1">
                            <h3 class="text-lg font-black leading-tight uppercase mb-2">
                                <a href="task_detail.php?id=<?= $task['id'] ?>" class="hover:text-indigo-600 transition-colors"><?= htmlspecialchars($task['title']) ?></a>
                            </h3>
                            <p class="text-[9px] font-bold text-stone-400 uppercase tracking-[0.2em]"><?= date('M d, Y', strtotime($task['created_at'])) ?></p>
                        </div>

                        <!-- Status Badge Area -->
                        <div class="mb-8 p-4 bg-stone-50 border-2 border-dashed border-stone-200 rounded-2xl">
                            <div class="text-[8px] font-black text-stone-400 uppercase tracking-widest mb-2">Current Process</div>
                            <?= ui_badge_status($task['status']) ?>
                        </div>

                        <!-- Footer Action -->
                        <div class="pt-6 border-t-2 border-stone-50">
                            <?php if($category !== 'published'): ?>
                                <form action="../src/handlers/editorial_pipeline_handler.php" method="POST" class="flex w-full gap-2">
                                    <input type="hidden" name="quick_status_update" value="1">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    
                                    <div class="relative flex-1">
                                        <select name="new_status" class="appearance-none w-full bg-white border-2 border-stone-900 rounded-xl px-3 py-2 text-[10px] font-black uppercase outline-none cursor-pointer hover:bg-stone-50">
                                            <option value="planning" <?= ($task['status'] == 'planning' ? 'selected' : '') ?>>Planning</option>
                                            <option value="process_vo" <?= ($task['status'] == 'process_vo' ? 'selected' : '') ?>>Proses VO</option>
                                            <option value="editing" <?= ($task['status'] == 'editing' ? 'selected' : '') ?>>Editing</option>
                                            <option value="ready_upload" <?= ($task['status'] == 'ready_upload' ? 'selected' : '') ?>>Ready Upload</option>
                                            <?php if($_SESSION['role'] === 'specialist'): ?>
                                                <option value="uploaded" <?= ($task['status'] == 'uploaded' ? 'selected' : '') ?>>Uploaded</option>
                                            <?php endif; ?>
                                        </select>
                                        <i data-lucide="chevron-down" class="w-3 h-3 absolute right-3 top-2.5 text-stone-400 pointer-events-none"></i>
                                    </div>
                                    
                                    <button type="submit" class="bg-stone-900 text-white border-2 border-stone-900 px-3 py-2 rounded-xl shadow-[2px_2px_0px_0px_#444] hover:bg-stone-700 transition-all flex items-center justify-center">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Published Style: Link view -->
                                <div class="flex gap-2">
                                    <?php if($task['url_tiktok']): ?>
                                        <a href="<?= $task['url_tiktok'] ?>" target="_blank" class="flex-1 py-2 bg-stone-900 text-white border-2 border-stone-900 rounded-xl text-[9px] font-black uppercase text-center shadow-[2px_2px_0px_0px_#444] hover:translate-y-[-1px] transition-all">TikTok Link</a>
                                    <?php endif; ?>
                                    <?php if($task['url_instagram']): ?>
                                        <a href="<?= $task['url_instagram'] ?>" target="_blank" class="flex-1 py-2 bg-white text-pink-600 border-2 border-pink-200 rounded-xl text-[9px] font-black uppercase text-center shadow-[2px_2px_0px_0px_#ec4899] hover:translate-y-[-1px] transition-all">IG Link</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-24 text-center border-2 border-dashed border-stone-200 rounded-[3rem] bg-stone-50/50">
                    <i data-lucide="inbox" class="w-12 h-12 text-stone-200 mx-auto mb-4"></i>
                    <p class="text-xs font-black text-stone-300 uppercase tracking-[0.3em]">Vault is Empty</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        lucide.createIcons();

        function filterTasks(category, btn) {
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filter cards with subtle transition
            const cards = document.querySelectorAll('.task-card');
            cards.forEach(card => {
                if (card.dataset.category === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Default Filter
        window.onload = () => {
            const activeBtn = document.querySelector('.filter-btn.active');
            filterTasks('active', activeBtn);
        };
    </script>
</body>
</html>