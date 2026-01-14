<?php
session_start();
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/handlers/kpi_handler.php';
check_auth();
check_role(['admin']);

$report = get_team_performance_report($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Team Performance - KPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap'); body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }</style>
</head>
<body class="text-stone-900">
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-8">
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight">Team Performance Metrics</h1>
            <p class="text-stone-500 font-medium">Laporan produktivitas divisi sosial media dan konten.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            
            <!-- SEKSI KPI SPECIALIST -->
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-indigo-100 border-2 border-stone-900 rounded-lg text-indigo-600">
                        <i data-lucide="pen-tool" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-wide">Social Media Specialist</h2>
                </div>

                <div class="bg-white border-2 border-stone-900 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_#1c1917]">
                    <table class="w-full text-left">
                        <thead class="bg-stone-50 border-b-2 border-stone-900">
                            <tr>
                                <th class="p-4 text-xs font-black uppercase text-stone-400">Member</th>
                                <th class="p-4 text-xs font-black uppercase text-stone-400 text-center">Narratives Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-stone-100">
                            <?php foreach($report['specialists'] as $spec): ?>
                            <tr>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full border-2 border-stone-900 <?= $spec['avatar_color'] ?> flex items-center justify-center text-white font-bold text-xs">
                                            <?= substr($spec['full_name'], 0, 1) ?>
                                        </div>
                                        <span class="font-bold text-sm"><?= $spec['full_name'] ?></span>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-4 py-1 bg-stone-100 border-2 border-stone-900 rounded-lg font-black text-sm shadow-[2px_2px_0px_0px_#1c1917]">
                                        <?= $spec['total_work'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SEKSI KPI CONTENT EDITOR -->
            <section>
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-green-100 border-2 border-stone-900 rounded-lg text-green-600">
                        <i data-lucide="video" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-wide">Content Editor</h2>
                </div>

                <div class="bg-white border-2 border-stone-900 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_#1c1917]">
                    <table class="w-full text-left">
                        <thead class="bg-stone-50 border-b-2 border-stone-900">
                            <tr>
                                <th class="p-4 text-xs font-black uppercase text-stone-400">Member</th>
                                <th class="p-4 text-xs font-black uppercase text-stone-400 text-center">Finished Videos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-stone-100">
                            <?php foreach($report['editors'] as $editor): ?>
                            <tr>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full border-2 border-stone-900 <?= $editor['avatar_color'] ?> flex items-center justify-center text-white font-bold text-xs">
                                            <?= substr($editor['full_name'], 0, 1) ?>
                                        </div>
                                        <span class="font-bold text-sm"><?= $editor['full_name'] ?></span>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-4 py-1 bg-green-100 border-2 border-stone-900 rounded-lg font-black text-sm shadow-[2px_2px_0px_0px_#1c1917]">
                                        <?= $editor['total_finished'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-4 bg-indigo-50 border-2 border-dashed border-indigo-200 rounded-xl">
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Parameter:</p>
                    <p class="text-xs font-medium text-indigo-600">Dihitung berdasarkan jumlah konten dengan status "Siap Upload" atau "Uploaded".</p>
                </div>
            </section>

        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>