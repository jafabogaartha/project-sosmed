<?php
session_start();
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/handlers/analytics_dashboard_handler.php';
check_auth();

$role = $_SESSION['role'];
$data = get_full_analytics($pdo, $_SESSION['user_id'], $role);
$counts = $data['main'];

// Helper untuk greeting - Diterjemahkan
$hour = date('H');
if ($hour < 12) $greeting = "Selamat Pagi";
elseif ($hour < 15) $greeting = "Selamat Siang";
elseif ($hour < 18) $greeting = "Selamat Sore";
else $greeting = "Selamat Malam";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - StudioSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
    </style>
</head>
<body class="text-stone-900 text-sm"> <!-- Perkecil base font size -->
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-6"> <!-- Padding dikurangi -->
        
        <!-- HEADER WITH GREETING -->
        <header class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.15em] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                        Workspace <?= ucfirst($role) ?>
                    </span>
                </div>
                <!-- Font Size judul dikurangi -->
                <h1 class="text-2xl font-extrabold tracking-tight text-stone-900">
                    <?= $greeting ?>, <span class="text-stone-400"><?= explode(' ', $_SESSION['full_name'])[0] ?></span>
                </h1>
                <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mt-0.5">Berikut ringkasan produksi konten kamu.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-white border border-stone-900 px-4 py-2 rounded-xl shadow-[3px_3px_0px_0px_#1c1917]">
                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black uppercase tracking-wider"><?= date('l, d F Y') ?></span>
            </div>
        </header>

        <!-- MAIN STATS GRID (Disesuaikan ukurannya) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            <!-- CARD 1: TOTAL -->
            <div class="bg-white border border-stone-900 rounded-2xl p-5 shadow-[4px_4px_0px_0px_#1c1917] relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-16 h-16 bg-stone-100 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-3">
                        <div class="p-2 bg-stone-900 rounded-lg text-white">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase bg-stone-100 px-1.5 py-0.5 rounded border border-stone-200">Total</span>
                    </div>
                    <h3 class="text-3xl font-black text-stone-900 mb-0.5"><?= $counts['total'] ?></h3>
                    <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Total Narasi</p>
                </div>
            </div>

            <!-- CARD 2: PRODUCTION FLOW -->
            <div class="bg-blue-50 border border-stone-900 rounded-2xl p-5 shadow-[4px_4px_0px_0px_#1c1917] hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="loader" class="w-4 h-4 text-blue-600 animate-spin-slow"></i>
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">On Progress</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center border-b border-blue-100 pb-1.5">
                        <span class="text-[11px] font-bold text-stone-600">Planning</span>
                        <span class="text-xs font-black bg-white border border-stone-900 px-1.5 rounded"><?= $counts['planning'] ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-blue-100 pb-1.5">
                        <span class="text-[11px] font-bold text-stone-600">Voiceover</span>
                        <span class="text-xs font-black bg-white border border-stone-900 px-1.5 rounded"><?= $counts['vo'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] font-bold text-stone-600">Editing</span>
                        <span class="text-xs font-black bg-white border border-stone-900 px-1.5 rounded"><?= $counts['editing'] ?></span>
                    </div>
                </div>
            </div>

            <!-- CARD 3: FINISH WORK -->
            <div class="bg-green-50 border border-stone-900 rounded-2xl p-5 shadow-[4px_4px_0px_0px_#1c1917] hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600"></i>
                    <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Selesai</span>
                </div>
                <div class="flex items-end gap-1.5 mb-2">
                    <h3 class="text-3xl font-black text-stone-900"><?= $counts['published'] ?></h3>
                    <span class="text-[10px] font-bold text-green-600 mb-1">Tayang</span>
                </div>
                <div class="w-full bg-white h-2 rounded-full border border-stone-900 overflow-hidden">
                    <?php 
                        $total = $counts['total'] ?: 1;
                        $pub_percent = ($counts['published'] / $total) * 100;
                    ?>
                    <div class="bg-green-400 h-full" style="width: <?= $pub_percent ?>%"></div>
                </div>
                <p class="text-[9px] font-bold text-stone-400 mt-1.5 text-right"><?= round($pub_percent) ?>% Completion</p>
            </div>

            <!-- CARD 4: PLATFORM DISTRIBUTION -->
            <div class="bg-indigo-50 border border-stone-900 rounded-2xl p-5 shadow-[4px_4px_0px_0px_#1c1917] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-center gap-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1 bg-stone-900 rounded text-white"><i data-lucide="music-2" class="w-3 h-3"></i></div>
                        <span class="text-[10px] font-black uppercase">TikTok</span>
                    </div>
                    <span class="text-md font-black"><?= $data['platforms']['total_tiktok'] ?? 0 ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1 bg-pink-500 rounded text-white border border-stone-900"><i data-lucide="instagram" class="w-3 h-3"></i></div>
                        <span class="text-[10px] font-black uppercase">Instagram</span>
                    </div>
                    <span class="text-md font-black"><?= $data['platforms']['total_instagram'] ?? 0 ?></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- BRAND PERFORMANCE GRID -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-black text-[10px] uppercase tracking-widest flex items-center gap-2 text-stone-500">
                        <i data-lucide="briefcase" class="w-4 h-4 text-stone-900"></i> 
                        <?= $role === 'admin' ? 'Performa Global Brand' : 'Brand Workspace Kamu' ?>
                    </h3>
                </div>

                <?php if(!empty($data['brands'])): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach($data['brands'] as $brand): ?>
                            <a href="brand_detail.php?id=<?= $brand['id'] ?? '' ?>" class="bg-white border border-stone-900 rounded-xl p-3.5 hover:bg-yellow-50 transition-all group relative overflow-hidden">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[8px] font-black bg-stone-100 px-1.5 py-0.5 rounded border border-stone-200 uppercase text-stone-500 group-hover:bg-yellow-200 group-hover:text-stone-900 transition-colors">
                                        <?= $brand['brand_code'] ?>
                                    </span>
                                    <?php if($brand['task_count'] > 0): ?>
                                        <div class="flex h-1.5 w-1.5">
                                            <span class="animate-ping absolute inline-flex h-1.5 w-1.5 rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <h4 class="text-[11px] font-extrabold text-stone-900 truncate mb-1"><?= $brand['brand_name'] ?></h4>
                                <div class="flex items-end gap-1">
                                    <span class="text-xl font-black text-stone-900 group-hover:text-indigo-600 transition-colors"><?= $brand['task_count'] ?></span>
                                    <span class="text-[8px] font-bold text-stone-400 mb-0.5">Konten</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white border border-dashed border-stone-300 rounded-xl p-8 text-center">
                        <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Belum ada data brand</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- LEADERBOARD (Right Side) -->
            <?php if($role === 'admin'): ?>
            <div class="lg:col-span-1 space-y-4">
                <h3 class="font-black text-[10px] uppercase tracking-widest flex items-center gap-2 text-stone-500">
                    <i data-lucide="award" class="w-4 h-4 text-stone-900"></i> Top Performers Tim
                </h3>

                <div class="bg-white border border-stone-900 rounded-2xl p-5 shadow-[3px_3px_0px_0px_#1c1917]">
                    
                    <!-- Specialist Section -->
                    <div class="flex items-center gap-2 mb-4 border-b border-stone-100 pb-2">
                        <span class="text-[9px] font-black uppercase text-indigo-600">Specialists</span>
                        <span class="text-[9px] font-bold text-stone-300">|</span>
                        <span class="text-[9px] font-bold uppercase text-stone-400">Narasi</span>
                    </div>

                    <div class="space-y-3">
                        <?php foreach($data['team']['specs'] as $index => $spec): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-5 h-5 flex items-center justify-center text-[8px] font-black bg-stone-100 rounded text-stone-400">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg border border-stone-900 <?= $spec['avatar_color'] ?>"></div>
                                        <span class="text-[10px] font-bold text-stone-700"><?= $spec['full_name'] ?></span>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-stone-900"><?= $spec['score'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="my-4 border-t border-dashed border-stone-200"></div>

                    <!-- Editor Section -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[9px] font-black uppercase text-green-600">Editors</span>
                        <span class="text-[9px] font-bold text-stone-300">|</span>
                        <span class="text-[9px] font-bold uppercase text-stone-400">Final Video</span>
                    </div>

                    <div class="space-y-3">
                        <?php foreach($data['team']['editors'] as $index => $editor): ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-5 h-5 flex items-center justify-center text-[8px] font-black bg-stone-100 rounded text-stone-400">
                                        <?= $index + 1 ?>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg border border-stone-900 <?= $editor['avatar_color'] ?>"></div>
                                        <span class="text-[10px] font-bold text-stone-700"><?= $editor['full_name'] ?></span>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-stone-900"><?= $editor['score'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
            <?php else: ?>
            
            <!-- SIDE WIDGET (For Non-Admin) -->
            <div class="lg:col-span-1">
                <div class="bg-yellow-300 border border-stone-900 rounded-2xl p-6 shadow-[3px_3px_0px_0px_#1c1917] h-full flex flex-col justify-between">
                    <div>
                        <i data-lucide="zap" class="w-8 h-8 text-stone-900 mb-3"></i>
                        <h3 class="text-xl font-black uppercase leading-tight mb-2">Tetap semangat berkarya!</h3>
                        <p class="text-[11px] font-bold text-stone-700">Kontribusi kamu sangat berarti untuk growth brand ini.</p>
                    </div>
                    <div class="mt-6">
                        <div class="text-[9px] font-black uppercase tracking-widest text-stone-900 mb-1">Fokus Saat Ini</div>
                        <div class="text-md font-black bg-white border border-stone-900 inline-block px-3 py-1.5 rounded-xl shadow-[2px_2px_0px_0px_#1c1917]">
                            <?= $counts['in_progress'] ?> Konten Aktif
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>