<?php
session_start();
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/helpers/neobrutalism_ui.php';
require_once __DIR__ . '/../src/handlers/brand_content_loader.php';
check_auth();

if (!isset($_GET['id'])) {
    header("Location: dashboard_analytics.php");
    exit;
}

$brand_id = $_GET['id'];
$brand = get_brand_detail($pdo, $brand_id);
$contents = get_brand_contents($pdo, $brand_id);
$user_role = $_SESSION['role'];

if (!$brand) {
    die("Workspace not found.");
}

/** 
 * LOGIKA DASHBOARD PINTAR
 * Progress: planning, process_vo, editing, waiting_footage
 * Finish Work: ready_upload, uploaded, published
 */
$stats = [
    'total' => count($contents),
    'progress' => 0,
    'finish_work' => 0
];

foreach($contents as $c) {
    if(in_array($c['status'], ['planning', 'process_vo', 'editing', 'waiting_footage'])) {
        $stats['progress']++;
    }
    if(in_array($c['status'], ['ready_upload', 'uploaded', 'published'])) {
        $stats['finish_work']++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brand['brand_name']) ?> - Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
    </style>
</head>
<body class="text-stone-900">
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-8">
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-white border-2 border-stone-900 rounded-2xl flex items-center justify-center text-3xl font-black shadow-[3px_3px_0px_0px_#1c1917]">
                    <?= strtoupper(substr($brand['brand_name'], 0, 1)) ?>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-3xl font-extrabold tracking-tight"><?= htmlspecialchars($brand['brand_name']) ?></h1>
                        <span class="bg-indigo-50 text-indigo-700 border-2 border-stone-900 px-3 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest"><?= htmlspecialchars($brand['brand_code']) ?></span>
                    </div>
                    <p class="text-stone-400 text-xs font-bold uppercase tracking-widest font-medium">Brand Strategy Console</p>
                </div>
            </div>

            <?php if($user_role === 'specialist'): ?>
            <a href="planner_studio.php?brand_id=<?= $brand_id ?>" class="group flex items-center gap-2 bg-yellow-300 border-2 border-stone-900 px-6 py-3 rounded-xl font-bold text-xs shadow-[3px_3px_0px_0px_#1c1917] hover:-translate-y-0.5 transition-all uppercase tracking-widest">
                <i data-lucide="plus" class="w-4 h-4 text-stone-900"></i> New Narrative
            </a>
            <?php endif; ?>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white border-2 border-stone-900 rounded-xl p-5 shadow-[3px_3px_0px_0px_#1c1917]">
                <p class="text-[10px] font-black text-stone-400 uppercase mb-1">Total Narratives</p>
                <h3 class="text-3xl font-black"><?= $stats['total'] ?></h3>
            </div>
            <div class="bg-indigo-50 border-2 border-stone-900 rounded-xl p-5 shadow-[3px_3px_0px_0px_#1c1917]">
                <p class="text-[10px] font-black text-indigo-400 uppercase mb-1 text-indigo-600">Active Production</p>
                <h3 class="text-3xl font-black text-indigo-600"><?= $stats['progress'] ?></h3>
            </div>
            <div class="bg-green-50 border-2 border-stone-900 rounded-xl p-5 shadow-[3px_3px_0px_0px_#1c1917]">
                <p class="text-[10px] font-black text-green-400 uppercase mb-1 text-green-600">Finish Work</p>
                <h3 class="text-3xl font-black text-green-600"><?= $stats['finish_work'] ?></h3>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <div class="mb-6">
            <div class="relative w-full max-w-sm">
                <i data-lucide="search" class="w-4 h-4 absolute left-4 top-3 text-stone-300"></i>
                <input type="text" id="brandSearch" placeholder="Filter by title..." class="w-full pl-10 pr-4 py-2.5 bg-white border-2 border-stone-900 rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-100 outline-none shadow-[2px_2px_0px_0px_#1c1917]">
            </div>
        </div>

        <!-- TABLE CONTAINER -->
        <div class="bg-white border-2 border-stone-900 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_#1c1917]">
            <table class="w-full text-left border-collapse" id="brandTable">
                <thead class="bg-stone-50 border-b-2 border-stone-900 text-stone-400 text-[10px] uppercase tracking-widest font-black">
                    <tr>
                        <th class="p-4">Content Information</th>
                        <th class="p-4 text-center">Channels</th>
                        <th class="p-4">Process Status</th>
                        <th class="p-4">Post Links (Analytics)</th>
                        <th class="p-4 text-right">Execution Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-stone-100">
                    <?php if(count($contents) > 0): ?>
                        <?php foreach($contents as $item): ?>
                        <tr class="hover:bg-stone-50 transition-colors group">
                            
                            <!-- JUDUL -->
                            <td class="p-4">
                                <a href="task_detail.php?id=<?= $item['id'] ?>" class="block group">
                                    <span class="text-sm font-bold text-stone-900 group-hover:text-indigo-600 uppercase tracking-tight transition-colors"><?= htmlspecialchars($item['title']) ?></span>
                                    <div class="text-[9px] font-bold text-stone-300 mt-0.5 uppercase tracking-tighter">ID #<?= $item['id'] ?> • <?= date('d M Y', strtotime($item['created_at'])) ?></div>
                                </a>
                            </td>

                            <!-- CHANNELS -->
                            <td class="p-4 text-center">
                                <div class="flex gap-1.5 justify-center">
                                    <?php if($item['target_tiktok']): ?>
                                        <div class="w-7 h-7 bg-stone-900 text-white rounded flex items-center justify-center border-2 border-stone-900 shadow-[1px_1px_0px_0px_#000]"><i data-lucide="music-2" class="w-3.5 h-3.5"></i></div>
                                    <?php endif; ?>
                                    <?php if($item['target_instagram']): ?>
                                        <div class="w-7 h-7 bg-pink-500 text-white rounded flex items-center justify-center border-2 border-stone-900 shadow-[1px_1px_0px_0px_#000]"><i data-lucide="instagram" class="w-3.5 h-3.5"></i></div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- BADGE STATUS -->
                            <td class="p-4">
                                <?= ui_badge_status($item['status']) ?>
                            </td>

                            <!-- POST LINKS -->
                            <td class="p-4">
                                <?php if($item['status'] === 'published'): ?>
                                    <div class="flex gap-2">
                                        <?php if($item['url_tiktok']): ?>
                                            <a href="<?= htmlspecialchars($item['url_tiktok']) ?>" target="_blank" class="p-1.5 bg-stone-100 border-2 border-stone-900 rounded-lg hover:bg-white transition-all shadow-[1px_1px_0px_0px_#1c1917]"><i data-lucide="music-2" class="w-3 h-3 text-stone-900"></i></a>
                                        <?php endif; ?>
                                        <?php if($item['url_instagram']): ?>
                                            <a href="<?= htmlspecialchars($item['url_instagram']) ?>" target="_blank" class="p-1.5 bg-stone-100 border-2 border-stone-900 rounded-lg hover:bg-white transition-all shadow-[1px_1px_0px_0px_#1c1917]"><i data-lucide="instagram" class="w-3 h-3 text-pink-600"></i></a>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($user_role === 'specialist' && $item['status'] === 'uploaded'): ?>
                                    <form action="../src/handlers/analytics_ingestion_handler.php" method="POST" class="flex flex-col gap-1.5">
                                        <input type="hidden" name="submit_url_tracking" value="1">
                                        <input type="hidden" name="task_id" value="<?= $item['id'] ?>">
                                        
                                        <?php if($item['target_tiktok']): ?>
                                            <input type="url" name="url_tiktok" placeholder="TikTok URL" required class="pl-2 py-1 bg-yellow-50 border-2 border-stone-900 rounded text-[9px] font-bold outline-none w-40">
                                        <?php endif; ?>
                                        
                                        <?php if($item['target_instagram']): ?>
                                            <input type="url" name="url_instagram" placeholder="IG URL" required class="pl-2 py-1 bg-yellow-50 border-2 border-stone-900 rounded text-[9px] font-bold outline-none w-40">
                                        <?php endif; ?>

                                        <button type="submit" class="bg-indigo-600 text-white border-2 border-stone-900 py-1 rounded text-[8px] font-black uppercase hover:bg-indigo-700 shadow-[2px_2px_0px_0px_#1c1917] active:shadow-none">Archive & Post</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-stone-300 text-[9px] font-bold uppercase italic italic">Waiting Upload</span>
                                <?php endif; ?>
                            </td>

                            <!-- DROPDOWN ACTION -->
                            <td class="p-4 text-right">
                                <?php if($item['status'] === 'published'): ?>
                                    <div class="text-[9px] font-black text-green-600 uppercase border-2 border-green-200 bg-green-50 px-3 py-1 rounded-lg inline-block">Finalized</div>
                                <?php else: ?>
                                    <form action="../src/handlers/editorial_pipeline_handler.php" method="POST" class="flex items-center justify-end gap-2">
                                        <input type="hidden" name="quick_status_update" value="1">
                                        <input type="hidden" name="task_id" value="<?= $item['id'] ?>">
                                        <div class="relative">
                                            <select name="new_status" class="appearance-none bg-white border-2 border-stone-900 text-[10px] font-bold uppercase rounded-lg px-3 py-1.5 pr-8 outline-none cursor-pointer shadow-[2px_2px_0px_0px_#1c1917]">
                                                <option value="planning" <?= ($item['status'] == 'planning' ? 'selected' : '') ?>>Planning</option>
                                                <option value="process_vo" <?= ($item['status'] == 'process_vo' ? 'selected' : '') ?>>VO Process</option>
                                                <option value="editing" <?= ($item['status'] == 'editing' ? 'selected' : '') ?>>Editing</option>
                                                <option value="ready_upload" <?= ($item['status'] == 'ready_upload' ? 'selected' : '') ?>>Ready Upload</option>
                                                
                                                <!-- Opsi Uploaded hanya muncul untuk Specialist atau jika status sudah Uploaded -->
                                                <?php if($user_role === 'specialist' || $item['status'] === 'uploaded'): ?>
                                                    <option value="uploaded" <?= ($item['status'] == 'uploaded' ? 'selected' : '') ?>>Uploaded</option>
                                                <?php endif; ?>

                                                <!-- Tambahkan Published agar select tidak bingung jika status sudah di-set oleh handler analytics -->
                                                <option value="published" <?= ($item['status'] == 'published' ? 'selected' : '') ?> disabled>Published</option>
                                            </select>
                                            <i data-lucide="chevron-down" class="w-3 h-3 absolute right-2.5 top-2 text-stone-400 pointer-events-none"></i>
                                        </div>
                                        <button type="submit" class="p-1.5 bg-stone-900 text-white border-2 border-stone-900 rounded-lg hover:bg-stone-700 shadow-[2px_2px_0px_0px_#444] transition-all">
                                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="p-20 text-center text-stone-300 text-[10px] font-black uppercase tracking-widest">Workspace is Empty</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        lucide.createIcons();
        document.getElementById('brandSearch').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let rows = document.querySelector("#brandTable tbody").rows;
            for (let i = 0; i < rows.length; i++) {
                let titleCol = rows[i].cells[0].textContent.toUpperCase();
                rows[i].style.display = titleCol.indexOf(filter) > -1 ? "" : "none";
            }
        });
    </script>
</body>
</html>