<?php
session_start();
require_once __DIR__ . '/../src/config/database_registry.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/helpers/neobrutalism_ui.php';
check_auth();

$uid = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

/**
 * LOGIC:
 * Menampilkan konten yang sudah masuk tahap penyelesaian:
 * ready_upload, uploaded, atau published.
 */
$stmt = $pdo->prepare("
    SELECT t.*, b.brand_name 
    FROM content_tasks t
    JOIN brands b ON t.brand_id = b.id
    WHERE t.creator_id = ? AND t.status IN ('ready_upload', 'uploaded', 'published') 
    ORDER BY t.updated_at DESC
");
$stmt->execute([$uid]);
$archives = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insight Vault - Archive</title>
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
        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Insight Vault</h1>
                <p class="text-stone-500 font-medium">Arsip konten dan pelacakan link publikasi.</p>
            </div>
            <div class="text-xs font-bold bg-white border-2 border-stone-900 px-4 py-2 rounded-xl shadow-[3px_3px_0px_0px_#1c1917] flex items-center gap-2">
                <i data-lucide="archive" class="w-4 h-4 text-purple-500"></i> Total <?= count($archives) ?> Archived
            </div>
        </header>

        <div class="bg-white border-2 border-stone-900 rounded-3xl overflow-hidden shadow-[6px_6px_0px_0px_#1c1917]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-stone-50 border-b-2 border-stone-900 text-[10px] font-black uppercase tracking-[0.2em] text-stone-400">
                    <tr>
                        <th class="p-5">Narrative Information</th>
                        <th class="p-5">Workflow Status</th>
                        <th class="p-5">Media Links</th>
                        <th class="p-5 text-right">Analytics Integration</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-stone-100 font-bold text-sm">
                    <?php if(count($archives) > 0): ?>
                        <?php foreach($archives as $item): ?>
                        <tr class="hover:bg-purple-50/30 transition-colors group">
                            <!-- INFO -->
                            <td class="p-5">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-indigo-600 uppercase mb-1"><?= htmlspecialchars($item['brand_name']) ?></span>
                                    <a href="task_detail.php?id=<?= $item['id'] ?>" class="text-stone-900 hover:text-indigo-600 transition-colors">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </a>
                                    <span class="text-[9px] text-stone-300 mt-1 uppercase">Updated: <?= date('d M Y', strtotime($item['updated_at'])) ?></span>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="p-5">
                                <?= ui_badge_status($item['status']) ?>
                            </td>

                            <!-- MEDIA/FILE LINKS -->
                            <td class="p-5">
                                <?php if($item['status'] === 'published'): ?>
                                    <div class="flex gap-2">
                                        <?php if($item['url_tiktok']): ?>
                                            <a href="<?= htmlspecialchars($item['url_tiktok']) ?>" target="_blank" class="w-8 h-8 bg-stone-900 text-white rounded-lg flex items-center justify-center border-2 border-stone-900 shadow-[2px_2px_0px_0px_#000] hover:translate-y-[-1px] transition-all">
                                                <i data-lucide="music-2" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($item['url_instagram']): ?>
                                            <a href="<?= htmlspecialchars($item['url_instagram']) ?>" target="_blank" class="w-8 h-8 bg-pink-100 text-pink-600 rounded-lg flex items-center justify-center border-2 border-pink-200 shadow-[2px_2px_0px_0px_#ec4899] hover:translate-y-[-1px] transition-all">
                                                <i data-lucide="instagram" class="w-4 h-4"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10px] text-stone-300 italic">Awaiting Publication</span>
                                <?php endif; ?>
                            </td>

                            <!-- ANALYTICS/ACTION -->
                            <td class="p-5 text-right">
                                <?php if($item['status'] === 'uploaded' && $user_role === 'specialist'): ?>
                                    <!-- Form Input URL untuk Closing -->
                                    <form action="../src/handlers/analytics_ingestion_handler.php" method="POST" class="flex flex-col gap-2 items-end">
                                        <input type="hidden" name="submit_url_tracking" value="1">
                                        <input type="hidden" name="task_id" value="<?= $item['id'] ?>">
                                        
                                        <div class="flex flex-col gap-1.5">
                                            <?php if($item['target_tiktok']): ?>
                                                <input type="url" name="url_tiktok" placeholder="TikTok Link..." required class="border-2 border-stone-900 rounded-lg px-3 py-1.5 text-[10px] font-bold w-48 bg-yellow-50 outline-none focus:bg-white shadow-[2px_2px_0px_0px_#1c1917]">
                                            <?php endif; ?>
                                            
                                            <?php if($item['target_instagram']): ?>
                                                <input type="url" name="url_instagram" placeholder="Instagram Link..." required class="border-2 border-stone-900 rounded-lg px-3 py-1.5 text-[10px] font-bold w-48 bg-yellow-50 outline-none focus:bg-white shadow-[2px_2px_0px_0px_#1c1917]">
                                            <?php endif; ?>
                                        </div>

                                        <button type="submit" class="bg-indigo-600 text-white border-2 border-stone-900 px-4 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-700 shadow-[2px_2px_0px_0px_#1e1b4b] active:shadow-none transition-all">
                                            Finalize Archive
                                        </button>
                                    </form>
                                <?php elseif($item['status'] === 'published'): ?>
                                    <div class="flex items-center justify-end gap-2 text-green-600 font-black text-[10px] uppercase">
                                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Data Tracked
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">In Progress</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-20 text-center">
                                <div class="w-16 h-16 bg-stone-50 border-2 border-stone-900 border-dashed rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="folder-search" class="w-8 h-8 text-stone-200"></i>
                                </div>
                                <p class="text-stone-400 text-[10px] font-black uppercase tracking-[0.2em]">No archives found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>