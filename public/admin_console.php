<?php
session_start();
require_once __DIR__ . '/../src/config/database_registry.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';

check_auth();
check_role(['admin']);

// Fetch Data
$users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY full_name ASC")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands ORDER BY brand_name ASC")->fetchAll();
$sql_assign = "SELECT ta.user_id, ta.brand_id, u.full_name, u.role, u.avatar_color, b.brand_name, b.brand_code
               FROM team_assignments ta
               JOIN users u ON ta.user_id = u.id
               JOIN brands b ON ta.brand_id = b.id
               ORDER BY u.full_name ASC";
$assignments = $pdo->query($sql_assign)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap'); body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }</style>
</head>
<body class="text-stone-900">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="ml-64 p-8">
        <h1 class="text-3xl font-extrabold mb-8 flex items-center gap-3">
            <i data-lucide="settings" class="w-8 h-8 text-stone-900"></i> Admin Console
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white border-2 border-stone-900 rounded-2xl p-6 shadow-[4px_4px_0px_0px_#1c1917] sticky top-8">
                    <h2 class="text-xl font-black mb-6 border-b-2 border-stone-900 pb-4">Assign Access</h2>
                    <form action="../src/handlers/admin_governance_handler.php" method="POST" class="space-y-4">
                        <input type="hidden" name="assign_user" value="1">
                        <input type="hidden" name="action" value="add">
                        <div>
                            <label class="block font-bold text-xs uppercase mb-2">Team Member</label>
                            <select name="user_id" required class="w-full px-4 py-3 border-2 border-stone-900 rounded-xl font-bold focus:ring-2 focus:ring-blue-200 outline-none cursor-pointer bg-stone-50">
                                <option value="" disabled selected>-- Select --</option>
                                <?php foreach($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= $u['full_name'] ?> (<?= ucfirst($u['role']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-xs uppercase mb-2">Brand Access</label>
                            <select name="brand_id" required class="w-full px-4 py-3 border-2 border-stone-900 rounded-xl font-bold focus:ring-2 focus:ring-blue-200 outline-none cursor-pointer bg-stone-50">
                                <option value="" disabled selected>-- Select --</option>
                                <?php foreach($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['brand_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-3 bg-stone-900 text-white border-2 border-stone-900 rounded-xl font-bold shadow-[2px_2px_0px_0px_#9ca3af] hover:bg-stone-700 transition flex items-center justify-center gap-2">
                            <i data-lucide="lock" class="w-4 h-4"></i> Grant Access
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white border-2 border-stone-900 rounded-2xl overflow-hidden shadow-[4px_4px_0px_0px_#1c1917]">
                    <table class="w-full text-left">
                        <thead class="bg-stone-100 border-b-2 border-stone-900 text-xs uppercase">
                            <tr>
                                <th class="p-4 font-black border-r-2 border-stone-900 w-1/3">Member</th>
                                <th class="p-4 font-black border-r-2 border-stone-900 w-1/3">Assigned Brand</th>
                                <th class="p-4 font-black text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-stone-900 font-bold text-sm">
                            <?php foreach($assignments as $row): ?>
                            <tr class="hover:bg-stone-50">
                                <td class="p-4 border-r-2 border-stone-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full border-2 border-stone-900 <?= $row['avatar_color'] ?>"></div>
                                        <div>
                                            <div><?= $row['full_name'] ?></div>
                                            <div class="text-[10px] text-stone-500 uppercase"><?= $row['role'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-stone-900">
                                    <span class="bg-yellow-100 border border-stone-900 px-2 py-0.5 rounded text-xs">
                                        <?= $row['brand_name'] ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <form action="../src/handlers/admin_governance_handler.php" method="POST" onsubmit="return confirm('Revoke access?');">
                                        <input type="hidden" name="assign_user" value="1">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                        <input type="hidden" name="brand_id" value="<?= $row['brand_id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-white hover:bg-red-500 border border-red-500 hover:border-black px-3 py-1 rounded transition text-xs font-black flex items-center gap-1 mx-auto">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i> Revoke
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>