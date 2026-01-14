<?php
session_start();
require_once __DIR__ . '/../src/helpers/security_guard.php';
require_once __DIR__ . '/../src/config/database_registry.php';
check_auth();
check_role(['admin']);

// Ambil SEMUA brand (termasuk yang inactive) untuk admin kelola
$stmt = $pdo->query("SELECT * FROM brands ORDER BY brand_name ASC");
$all_brands = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Brand Management - Admin</title>
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
        <header class="mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight uppercase italic">Brand Inventory</h1>
            <p class="text-stone-500 font-medium">Aktifkan atau nonaktifkan visibilitas brand di seluruh sistem.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($all_brands as $brand): ?>
                <div class="bg-white border-2 border-stone-900 rounded-[2rem] p-6 shadow-[5px_5px_0px_0px_#1c1917] flex flex-col justify-between transition-all <?= $brand['status'] === 'inactive' ? 'opacity-60 grayscale' : '' ?>">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-stone-50 border-2 border-stone-900 rounded-2xl flex items-center justify-center font-black text-xl">
                                <?= strtoupper(substr($brand['brand_name'], 0, 1)) ?>
                            </div>
                            <span class="text-[10px] font-black px-2 py-1 rounded border-2 border-stone-900 uppercase <?= $brand['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $brand['status'] ?>
                            </span>
                        </div>
                        <h3 class="text-lg font-black uppercase tracking-tight mb-1"><?= htmlspecialchars($brand['brand_name']) ?></h3>
                        <p class="text-[10px] font-bold text-stone-400 tracking-widest uppercase">Code: <?= $brand['brand_code'] ?></p>
                    </div>

                    <div class="mt-8 pt-4 border-t-2 border-stone-50">
                        <form action="../src/handlers/brand_management_handler.php" method="POST">
                            <input type="hidden" name="brand_id" value="<?= $brand['id'] ?>">
                            <input type="hidden" name="current_status" value="<?= $brand['status'] ?>">
                            <input type="hidden" name="toggle_brand_status" value="1">
                            
                            <?php if($brand['status'] === 'active'): ?>
                                <button type="submit" class="w-full py-2 bg-white border-2 border-stone-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 hover:text-red-600 transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="eye-off" class="w-3 h-3"></i> Deactivate Brand
                                </button>
                            <?php else: ?>
                                <button type="submit" class="w-full py-2 bg-stone-900 text-white border-2 border-stone-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-stone-800 transition-all flex items-center justify-center gap-2 shadow-[3px_3px_0px_0px_#444]">
                                    <i data-lucide="eye" class="w-3 h-3"></i> Activate Brand
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>