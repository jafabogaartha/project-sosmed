<?php
require_once __DIR__ . '/../../src/handlers/workspace_loader.php';
$sidebar_brands = get_my_brands($pdo, $_SESSION['user_id'], $_SESSION['role']);
$current_page = basename($_SERVER['PHP_SELF']);

// Fungsi Helper untuk class menu aktif (Soft Neobrutalism Style)
function is_active($page, $current_page) {
    return $page === $current_page 
        ? 'bg-stone-100 border-stone-900 shadow-[3px_3px_0px_0px_#1c1917]' 
        : 'border-transparent hover:border-stone-900 hover:bg-stone-50 hover:shadow-[3px_3px_0px_0px_#1c1917]';
}
?>

<aside class="w-64 bg-white border-r-2 border-stone-900 h-screen fixed left-0 top-0 overflow-y-auto z-50 flex flex-col">
    <!-- Header Logo -->
    <div class="p-6 border-b-2 border-stone-900">
        <div class="flex items-center gap-2 text-indigo-600 mb-1">
            <i data-lucide="layers" class="w-6 h-6"></i>
            <span class="font-extrabold text-xl tracking-tight text-stone-900 uppercase">MNA <span class="text-indigo-600">Content</span></span>
        </div>
        <!-- Content Management -> Manajemen Konten -->
        <p class="text-[10px] font-black text-stone-400 tracking-[0.2em] uppercase">Manajemen Konten</p>
    </div>

    <!-- User Profile Card -->
    <div class="p-5 border-b-2 border-stone-900 bg-stone-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl border-2 border-stone-900 <?= $_SESSION['avatar_color'] ?> flex items-center justify-center shadow-[2px_2px_0px_0px_#1c1917]">
                <span class="font-bold text-white text-xs"><?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?></span>
            </div>
            <div class="overflow-hidden">
                <div class="font-bold text-sm text-stone-900 truncate"><?= $_SESSION['full_name'] ?></div>
                <div class="text-[10px] font-black uppercase text-stone-500 tracking-wider bg-white border border-stone-300 px-2 py-0.5 rounded mt-1 inline-block"><?= $_SESSION['role'] ?></div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Main Console -> Menu Utama -->
        <div class="px-2 mb-2 text-[10px] font-black uppercase tracking-widest text-stone-400">Menu Utama</div>
        
        <!-- Dashboard (All Roles) -->
        <a href="dashboard_analytics.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('dashboard_analytics.php', $current_page) ?>">
            <!-- Overview -> Ringkasan -->
            <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Ringkasan
        </a>

        <!-- Performance Monitor (All Roles - New Feature) -->
        <a href="analytics_performance.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('analytics_performance.php', $current_page) ?>">
            <!-- Data Monitor -> Monitor Data -->
            <i data-lucide="bar-chart-big" class="w-4 h-4"></i> Monitor Data
        </a>

        <!-- Admin Only Menus -->
        <?php if($_SESSION['role'] === 'admin'): ?>
        <!-- Administration -> Administrasi -->
        <div class="mt-4 px-2 mb-2 text-[10px] font-black uppercase tracking-widest text-stone-400">Administrasi</div>
        <a href="admin_console.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('admin_console.php', $current_page) ?>">
            <!-- Team Manager -> Manajemen Tim -->
            <i data-lucide="user-cog" class="w-4 h-4"></i> Manajemen Tim
        </a>
        <a href="admin_brand_settings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('admin_brand_settings.php', $current_page) ?>">
            <!-- Brand Inventory -> Inventaris Brand -->
            <i data-lucide="briefcase" class="w-4 h-4"></i> Inventaris Brand
        </a>
        <a href="team_performance.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('team_performance.php', $current_page) ?>">
            <!-- KPI Report -> Laporan KPI -->
            <i data-lucide="line-chart" class="w-4 h-4"></i> Laporan KPI
        </a>
        <?php endif; ?>

        <!-- Specialist Only Menus -->
        <?php if($_SESSION['role'] === 'specialist'): ?>
        <!-- Production -> Produksi -->
        <div class="mt-4 px-2 mb-2 text-[10px] font-black uppercase tracking-widest text-stone-400">Produksi</div>
        <a href="production_deck.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('production_deck.php', $current_page) ?>">
            <!-- Production Queue -> Antrean Produksi -->
            <i data-lucide="camera" class="w-4 h-4"></i> Antrean Produksi
        </a>
        <a href="insight_vault.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('insight_vault.php', $current_page) ?>">
            <!-- Insight Vault -> Gudang Insight -->
            <i data-lucide="archive" class="w-4 h-4"></i> Gudang Insight
        </a>
        <?php endif; ?>

        <!-- Editor Only Menus -->
        <?php if($_SESSION['role'] === 'editor'): ?>
        <!-- Post-Production -> Pasca-Produksi -->
        <div class="mt-4 px-2 mb-2 text-[10px] font-black uppercase tracking-widest text-stone-400">Pasca-Produksi</div>
        <a href="editor_workbench.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 <?= is_active('editor_workbench.php', $current_page) ?>">
            <!-- Workbench -> Meja Kerja -->
            <i data-lucide="scissors" class="w-4 h-4"></i> Meja Kerja
        </a>
        <?php endif; ?>

        <!-- Workspace Brands Section -->
        <!-- My Workspaces -> Ruang Kerja Saya -->
        <div class="mt-8 px-2 mb-2 text-[10px] font-black uppercase tracking-widest text-stone-400">Ruang Kerja Saya</div>
        
        <?php foreach($sidebar_brands as $brand): ?>
            <?php 
                // Cek apakah halaman ini sedang aktif untuk highlight brand
                $is_brand_active = (isset($_GET['id']) && $_GET['id'] == $brand['id'] && ($current_page == 'brand_detail.php' || $current_page == 'planner_studio.php' || $current_page == 'task_detail.php'));
                $active_class = $is_brand_active ? 'bg-indigo-50 border-stone-900 shadow-[3px_3px_0px_0px_#1c1917]' : 'border-transparent hover:border-stone-900 hover:bg-stone-50 hover:shadow-[3px_3px_0px_0px_#1c1917]';
            ?>
            <a href="brand_detail.php?id=<?= $brand['id'] ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl border-2 transition-all text-sm font-bold text-stone-700 mb-1 <?= $active_class ?>">
                <span class="truncate pr-2"><?= $brand['brand_name'] ?></span>
                <span class="text-[9px] font-black bg-stone-100 px-1.5 py-0.5 rounded border border-stone-300 text-stone-500 uppercase"><?= $brand['brand_code'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Logout Area -->
    <div class="p-4 border-t-2 border-stone-900 bg-stone-50">
        <a href="logout.php" class="flex items-center gap-2 text-stone-600 font-bold text-xs hover:text-red-600 transition-colors justify-center uppercase tracking-widest">
            <!-- Log Out -> Keluar -->
            <i data-lucide="log-out" class="w-3 h-3"></i> Keluar
        </a>
    </div>
</aside>

<!-- Inisialisasi Lucide Icons -->
<script>
    lucide.createIcons();
</script>