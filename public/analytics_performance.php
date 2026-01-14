<?php
session_start();
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/helpers/neobrutalism_ui.php';
// Gunakan path handler yang sudah kita perbaiki logic-nya
require_once __DIR__ . '/../src/handlers/analytics_performance_loader.php'; 
check_auth();

// Ambil Data Statistik (Logic 'get_performance_data' ada di handler)
$data = get_performance_data($pdo, $_SESSION['user_id'], $_SESSION['role']);
$summary = $data['summary'];
$contents = $data['contents'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Monitor - StudioSync</title>
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
        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight uppercase italic">Performance Monitor</h1>
                <p class="text-stone-500 font-bold text-xs uppercase tracking-widest mt-1">Live data from published content</p>
            </div>
            <button onclick="location.reload()" class="flex items-center gap-2 bg-white border-2 border-stone-900 px-4 py-2 rounded-xl shadow-[3px_3px_0px_0px_#1c1917] hover:bg-stone-50 transition-all font-bold text-xs uppercase tracking-wider">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reload Page
            </button>
        </header>

        <!-- SUMMARY CARDS (BIG NUMBERS) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <!-- Total Reach -->
            <div class="bg-indigo-600 text-white border-2 border-stone-900 rounded-[2rem] p-8 shadow-[6px_6px_0px_0px_#1c1917] relative overflow-hidden group">
                <i data-lucide="eye" class="w-24 h-24 absolute -right-4 -top-4 text-indigo-500 opacity-50 group-hover:scale-110 transition-transform"></i>
                <div class="relative z-10">
                    <p class="text-indigo-200 font-black text-xs uppercase tracking-[0.2em] mb-2">Total Estimated Reach</p>
                    <h2 class="text-6xl font-black tracking-tighter"><?= number_format($summary['total_reach'] ?? 0) ?></h2>
                    <p class="mt-2 text-sm font-bold text-indigo-200">Views across TikTok & Instagram</p>
                </div>
            </div>

            <!-- Total Engagement -->
            <div class="bg-pink-500 text-white border-2 border-stone-900 rounded-[2rem] p-8 shadow-[6px_6px_0px_0px_#1c1917] relative overflow-hidden group">
                <i data-lucide="heart" class="w-24 h-24 absolute -right-4 -top-4 text-pink-400 opacity-50 group-hover:scale-110 transition-transform"></i>
                <div class="relative z-10">
                    <p class="text-pink-200 font-black text-xs uppercase tracking-[0.2em] mb-2">Total Engagement</p>
                    <h2 class="text-6xl font-black tracking-tighter"><?= number_format($summary['total_engagement'] ?? 0) ?></h2>
                    <p class="mt-2 text-sm font-bold text-pink-200">Likes & Interactions</p>
                </div>
            </div>
        </div>

        <!-- DATA TABLE -->
        <div class="bg-white border-2 border-stone-900 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_#1c1917]">
            <div class="p-6 border-b-2 border-stone-900 bg-stone-50 flex justify-between items-center">
                <h3 class="font-black text-sm uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="trophy" class="w-4 h-4 text-yellow-600"></i> Top Performing Content
                </h3>
                <span class="text-[10px] font-bold text-stone-400 uppercase">Sorted by Popularity</span>
            </div>

            <table class="w-full text-left">
                <thead class="bg-stone-100 border-b-2 border-stone-900 text-[10px] font-black uppercase tracking-widest text-stone-500">
                    <tr>
                        <th class="p-5">Content</th>
                        <th class="p-5 text-center">TikTok Metrics</th>
                        <th class="p-5 text-center">Instagram Metrics</th>
                        <th class="p-5 text-right">Data Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-stone-100 font-bold text-sm">
                    <?php if(count($contents) > 0): ?>
                        <?php foreach($contents as $item): ?>
                        <tr class="hover:bg-yellow-50/50 transition-colors group">
                            
                            <!-- Content Info -->
                            <td class="p-5">
                                <span class="text-[9px] font-black bg-stone-200 px-2 py-0.5 rounded text-stone-600 uppercase mb-1 inline-block"><?= $item['brand_name'] ?></span>
                                <div class="block text-stone-900 text-base font-extrabold uppercase leading-tight"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="text-[10px] text-stone-400 mt-1 uppercase tracking-wider">
                                    Last Scraped: <?= $item['last_scraped_at'] ? date('d M H:i', strtotime($item['last_scraped_at'])) : 'Never' ?>
                                </div>
                            </td>

                            <!-- TikTok Metrics -->
                            <td class="p-5 text-center">
                                <?php if($item['url_tiktok']): ?>
                                    <div class="inline-block bg-stone-900 text-white border-2 border-stone-900 rounded-xl p-3 min-w-[100px] shadow-[2px_2px_0px_0px_#ccc]">
                                        <div class="flex justify-center mb-1"><i data-lucide="music-2" class="w-3 h-3"></i></div>
                                        <div class="text-lg font-black"><?= number_format($item['tt_views']) ?></div>
                                        <div class="text-[9px] text-stone-400 uppercase">Views</div>
                                        <div class="mt-2 pt-2 border-t border-stone-700 flex justify-center gap-3 text-[10px]">
                                            <span class="flex items-center gap-1"><i data-lucide="heart" class="w-2 h-2"></i> <?= $item['tt_likes'] ?></span>
                                            <span class="flex items-center gap-1"><i data-lucide="message-circle" class="w-2 h-2"></i> <?= $item['tt_comments'] ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-stone-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Instagram Metrics -->
                            <td class="p-5 text-center">
                                <?php if($item['url_instagram']): ?>
                                    <div class="inline-block bg-white text-stone-900 border-2 border-pink-200 rounded-xl p-3 min-w-[100px] shadow-[2px_2px_0px_0px_#fbcfe8]">
                                        <div class="flex justify-center mb-1"><i data-lucide="instagram" class="w-3 h-3 text-pink-500"></i></div>
                                        <!-- Note: IG API limit views, usually likes -->
                                        <div class="text-lg font-black text-pink-600"><?= number_format($item['ig_likes']) ?></div>
                                        <div class="text-[9px] text-pink-300 uppercase">Likes</div>
                                        <div class="mt-2 pt-2 border-t border-pink-100 flex justify-center gap-3 text-[10px] text-pink-400">
                                            <span class="flex items-center gap-1"><i data-lucide="message-circle" class="w-2 h-2"></i> <?= $item['ig_comments'] ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-stone-300 text-xs">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Action -->
                            <td class="p-5 text-right align-middle">
                                <form action="../src/handlers/analytics_refresh_handler.php" method="POST">
                                    <input type="hidden" name="refresh_metrics" value="1">
                                    <input type="hidden" name="task_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="group flex items-center gap-2 bg-yellow-300 border-2 border-stone-900 px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider shadow-[3px_3px_0px_0px_#1c1917] hover:translate-y-[-1px] hover:shadow-[4px_4px_0px_0px_#1c1917] active:translate-y-[1px] active:shadow-none transition-all ml-auto">
                                        <i data-lucide="refresh-cw" class="w-3 h-3 group-hover:rotate-180 transition-transform duration-500"></i>
                                        Sync Data
                                    </button>
                                </form>
                                <div class="mt-2 flex justify-end gap-2">
                                    <?php if($item['url_tiktok']): ?>
                                        <a href="<?= htmlspecialchars($item['url_tiktok']) ?>" target="_blank" class="text-stone-300 hover:text-stone-900"><i data-lucide="external-link" class="w-4 h-4"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="p-20 text-center text-stone-300 font-bold uppercase tracking-widest">No Published Content Yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>