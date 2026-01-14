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

$task = get_task_detail($pdo, $_GET['id']);
$user_role = $_SESSION['role'];

if (!$task) {
    die("Narrative data not found.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Narasi - <?= htmlspecialchars($task['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
        .font-mono-custom { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="text-stone-900 pb-10 text-sm"> <!-- Base font diperkecil -->
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-6"> <!-- Padding dikurangi -->
        <!-- TOP NAVIGATION -->
        <div class="mb-6 flex justify-between items-center">
            <a href="brand_detail.php?id=<?= $task['brand_id'] ?>" class="inline-flex items-center gap-1.5 font-bold text-stone-400 hover:text-stone-900 transition-colors uppercase text-[10px] tracking-widest">
                <i data-lucide="chevron-left" class="w-3 h-3"></i> Balik ke Workspace
            </a>
            <div class="flex items-center gap-3">
                <span class="text-[9px] font-black text-stone-400 uppercase tracking-widest">Status Saat Ini:</span>
                <?= ui_badge_status($task['status']) ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"> <!-- Gap diperkecil -->
            
            <!-- MAIN CONTENT AREA (Left) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- HEADER INFO CARD -->
                <div class="bg-white border-2 border-stone-900 rounded-2xl p-6 shadow-[4px_4px_0px_0px_#1c1917]">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-indigo-100 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest">
                            <?= htmlspecialchars($task['brand_name'] ?? 'General') ?>
                        </span>
                        <span class="text-[9px] font-bold text-stone-300 uppercase tracking-widest">Dibuat <?= date('d M Y', strtotime($task['created_at'])) ?></span>
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight mb-3 uppercase leading-tight"><?= htmlspecialchars($task['title']) ?></h1>
                    <div class="flex items-center gap-2 text-stone-500">
                        <div class="w-5 h-5 rounded-md border border-stone-900 bg-stone-100 flex items-center justify-center">
                            <i data-lucide="user" class="w-3 h-3 text-stone-900"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-tight">Strategist: <?= htmlspecialchars($task['creator_name'] ?? 'System') ?></span>
                    </div>
                </div>

                <!-- TIKTOK SCRIPT SECTION -->
                <?php if($task['target_tiktok']): ?>
                <div class="bg-white border-2 border-stone-900 rounded-2xl shadow-[4px_4px_0px_0px_#1c1917] overflow-hidden">
                    <div class="bg-stone-900 text-white p-3.5 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i data-lucide="music-2" class="w-4 h-4"></i>
                            <span class="font-black text-[10px] uppercase tracking-[0.15em]">Script Voiceover TikTok</span>
                        </div>
                        <button onclick="copyToClipboard('script_tt')" class="bg-stone-700 hover:bg-stone-600 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all active:scale-95 flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3 h-3"></i> Copy Script
                        </button>
                    </div>
                    <div class="p-5">
                        <textarea id="script_tt" readonly class="w-full h-48 bg-transparent outline-none font-mono-custom text-xs leading-relaxed text-stone-600 resize-none" placeholder="Belum ada script."><?= htmlspecialchars($task['script_tiktok']) ?></textarea>
                    </div>
                </div>
                <?php endif; ?>

                <!-- INSTAGRAM CAPTION SECTION -->
                <?php if($task['target_instagram']): ?>
                <div class="bg-white border-2 border-stone-900 rounded-2xl shadow-[4px_4px_0px_0px_#1c1917] overflow-hidden">
                    <div class="bg-pink-50 border-b-2 border-stone-900 p-3.5 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i data-lucide="instagram" class="w-4 h-4 text-pink-600"></i>
                            <span class="font-black text-[10px] text-pink-700 uppercase tracking-[0.15em]">Caption Feed Instagram</span>
                        </div>
                        <button onclick="copyToClipboard('caption_ig')" class="bg-white border border-stone-900 px-3 py-1.5 rounded-lg text-[9px] font-black text-pink-600 uppercase tracking-widest transition-all shadow-[2px_2px_0px_0px_#1c1917] hover:translate-y-[-1px] active:translate-y-0 flex items-center gap-1.5">
                            <i data-lucide="copy" class="w-3 h-3"></i> Copy Caption
                        </button>
                    </div>
                    <div class="p-5">
                        <textarea id="caption_ig" readonly class="w-full h-48 bg-transparent outline-none font-mono-custom text-xs leading-relaxed text-stone-600 resize-none" placeholder="Belum ada caption."><?= htmlspecialchars($task['caption_instagram']) ?></textarea>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- SIDEBAR CONTROLS (Right) -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- STATUS UPDATE PANEL -->
                <div class="bg-white border-2 border-stone-900 rounded-2xl p-6 shadow-[4px_4px_0px_0px_#1c1917]">
                    <h3 class="font-black text-[10px] uppercase text-stone-400 tracking-[0.15em] mb-4">Kontrol Workflow</h3>
                    
                    <form action="../src/handlers/editorial_pipeline_handler.php" method="POST" class="space-y-3">
                        <input type="hidden" name="update_status" value="1">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        
                        <div class="relative">
                            <label class="block text-[9px] font-black uppercase text-stone-500 mb-1.5 ml-1">Ganti Status</label>
                            <select name="new_status" class="appearance-none w-full bg-stone-50 border border-stone-900 rounded-xl px-4 py-3 font-bold text-xs outline-none cursor-pointer focus:ring-2 focus:ring-indigo-100">
                                <option value="planning" <?= $task['status'] == 'planning' ? 'selected' : '' ?>>Proses Planning</option>
                                <option value="process_vo" <?= $task['status'] == 'process_vo' ? 'selected' : '' ?>>Proses VO</option>
                                <option value="editing" <?= $task['status'] == 'editing' ? 'selected' : '' ?>>Proses Editing</option>
                                <option value="ready_upload" <?= $task['status'] == 'ready_upload' ? 'selected' : '' ?>>Siap Upload</option>
                                <?php if($user_role === 'specialist'): ?>
                                    <option value="uploaded" <?= $task['status'] == 'uploaded' ? 'selected' : '' ?>>Sudah Uploaded</option>
                                <?php endif; ?>
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-9 text-stone-400 pointer-events-none"></i>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-stone-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_#444] hover:bg-stone-800 transition-all active:translate-y-1 active:shadow-none">
                            Update Status
                        </button>
                    </form>
                </div>

                <!-- ANALYTICS ARCHIVE -->
                <?php if($task['status'] === 'uploaded' && $user_role === 'specialist'): ?>
                <div class="bg-yellow-100 border-2 border-stone-900 rounded-2xl p-6 shadow-[4px_4px_0px_0px_#1c1917]">
                    <h3 class="font-black text-[10px] uppercase text-stone-900 tracking-[0.15em] mb-3">Close Task & Archive</h3>
                    <p class="text-[10px] font-bold text-stone-600 mb-4 leading-relaxed">Tempel link konten yang sudah tayang untuk arsip data.</p>
                    
                    <form action="../src/handlers/analytics_ingestion_handler.php" method="POST" class="space-y-3">
                        <input type="hidden" name="submit_url_tracking" value="1">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        
                        <?php if($task['target_tiktok']): ?>
                            <input type="url" name="url_tiktok" placeholder="Link TikTok..." required class="w-full px-3 py-2.5 bg-white border border-stone-900 rounded-lg text-[10px] font-bold outline-none focus:ring-2 focus:ring-yellow-400">
                        <?php endif; ?>

                        <?php if($task['target_instagram']): ?>
                            <input type="url" name="url_instagram" placeholder="Link Instagram..." required class="w-full px-3 py-2.5 bg-white border border-stone-900 rounded-lg text-[10px] font-bold outline-none focus:ring-2 focus:ring-yellow-400">
                        <?php endif; ?>

                        <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_#1e1b4b] hover:bg-indigo-700 transition-all">
                            Arsipkan Konten
                        </button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        function copyToClipboard(elementId) {
            const textarea = document.getElementById(elementId);
            textarea.select();
            document.execCommand('copy');
            
            const btn = event.currentTarget;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" class="w-3 h-3"></i> Copied!';
            lucide.createIcons();
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                lucide.createIcons();
            }, 2000);
        }
    </script>
</body>
</html>