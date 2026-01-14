<?php
session_start();
require_once __DIR__ . '/../src/handlers/workspace_loader.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';

// Pastikan sudah login
check_auth();

// Ambil data brand sesuai akses user (Logic dari workspace_loader)
$my_brands = get_my_brands($pdo, $_SESSION['user_id'], $_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Workspace - StudioSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
    </style>
</head>
<body class="text-stone-900">
    
    <!-- LOAD SIDEBAR NAVIGATION -->
    <?php include 'layouts/sidebar.php'; ?>

    <!-- MAIN CONTENT AREA -->
    <main class="ml-64 p-8">
        
        <!-- HEADER SECTION -->
        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-1 uppercase">Brand Workspaces</h1>
                <p class="text-stone-500 font-medium text-sm">Select a brand to manage its content strategy and production.</p>
            </div>
            <div class="flex items-center gap-2 bg-white border-2 border-stone-900 px-4 py-2 rounded-xl shadow-[3px_3px_0px_0px_#1c1917]">
                <i data-lucide="briefcase" class="w-4 h-4 text-indigo-600"></i>
                <span class="text-xs font-bold uppercase tracking-widest"><?= count($my_brands) ?> Assigned Brands</span>
            </div>
        </header>

        <!-- BRAND GRID AREA -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            
            <?php if(count($my_brands) > 0): ?>
                <?php foreach($my_brands as $brand): ?>
                    
                    <!-- BRAND CARD -->
                    <a href="brand_detail.php?id=<?= $brand['id'] ?>" 
                       class="group bg-white border-2 border-stone-900 rounded-3xl p-6 shadow-[6px_6px_0px_0px_#1c1917] hover:translate-y-[-4px] hover:shadow-[10px_10px_0px_0px_#1c1917] transition-all relative overflow-hidden flex flex-col items-center text-center">
                        
                        <!-- BRAND CODE BADGE -->
                        <div class="absolute top-0 right-0 bg-yellow-300 border-l-2 border-b-2 border-stone-900 px-3 py-1 font-black text-[10px] uppercase tracking-widest">
                            <?= htmlspecialchars($brand['brand_code']) ?>
                        </div>

                        <!-- BRAND ICON (INITIAL) -->
                        <div class="w-20 h-20 bg-indigo-50 border-2 border-stone-900 rounded-2xl flex items-center justify-center text-3xl font-black mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <?= strtoupper(substr($brand['brand_name'], 0, 1)) ?>
                        </div>

                        <!-- BRAND INFO -->
                        <h3 class="text-xl font-extrabold leading-tight text-stone-900 mb-2">
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </h3>
                        
                        <div class="mt-4 pt-4 border-t-2 border-stone-50 w-full">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400 group-hover:text-indigo-600 transition-colors">
                                Open Workspace <i data-lucide="arrow-right" class="w-3 h-3 inline-block ml-1"></i>
                            </span>
                        </div>
                    </a>

                <?php endforeach; ?>
            <?php else: ?>
                
                <!-- EMPTY STATE -->
                <div class="col-span-full py-24 border-2 border-dashed border-stone-300 rounded-[2rem] flex flex-col items-center justify-center bg-stone-50/50">
                    <div class="w-16 h-16 bg-white border-2 border-stone-900 rounded-2xl flex items-center justify-center mb-4 text-stone-300">
                        <i data-lucide="search-x" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-stone-400">No Brands Assigned</h3>
                    <p class="text-sm font-medium text-stone-400 mt-1 uppercase tracking-widest">Contact administrator for access</p>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <!-- INITIALIZE ICONS -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>