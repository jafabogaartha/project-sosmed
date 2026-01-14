<?php
session_start();
require_once __DIR__ . '/../src/config/database_registry.php';
require_once __DIR__ . '/../src/helpers/security_guard.php';
check_auth();

// Validasi Parameter ID Brand
if (!isset($_GET['brand_id'])) {
    header("Location: dashboard_analytics.php");
    exit;
}

$brand_id = $_GET['brand_id'];

// Ambil Informasi Brand
$stmt = $pdo->prepare("SELECT brand_name, brand_code FROM brands WHERE id = ?");
$stmt->execute([$brand_id]);
$brand = $stmt->fetch();

if (!$brand) {
    die("Brand workspace not found.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Studio - <?= htmlspecialchars($brand['brand_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFBF7; }
        .font-mono-custom { font-family: 'JetBrains Mono', monospace; }
        
        /* Custom Checkbox Style - Ukuran disesuaikan */
        .platform-checkbox:checked + div {
            background-color: #EEF2FF;
            border-color: #18181B;
            box-shadow: 2px 2px 0px 0px #18181B; /* Shadow lebih kecil */
        }
    </style>
</head>
<body class="text-stone-900 pb-10 text-sm"> <!-- Base font size diperkecil -->
    <?php include 'layouts/sidebar.php'; ?>

    <main class="ml-64 p-6"> <!-- Padding main dikurangi -->
        <div class="max-w-4xl mx-auto"> <!-- Container diperkecil dari 5xl ke 4xl -->
            
            <!-- HEADER BREADCRUMB (Lebih Rapat) -->
            <div class="mb-5 flex justify-between items-center">
                <a href="brand_detail.php?id=<?= $brand_id ?>" class="inline-flex items-center gap-1.5 font-bold text-stone-400 hover:text-stone-900 transition-colors uppercase text-[10px] tracking-widest">
                    <i data-lucide="chevron-left" class="w-3 h-3"></i> Balik ke Workspace
                </a>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black text-stone-400 uppercase tracking-widest">Workspace Saat Ini:</span>
                    <span class="px-2 py-0.5 bg-white border border-stone-900 rounded-md text-[9px] font-black shadow-[1px_1px_0px_0px_#1c1917] uppercase">
                        <?= htmlspecialchars($brand['brand_name']) ?>
                    </span>
                </div>
            </div>
            
            <!-- FORM CONTAINER (Padding & Shadow Diperkecil) -->
            <form action="../src/handlers/campaign_orchestrator.php" method="POST" class="bg-white border border-stone-900 rounded-2xl shadow-[5px_5px_0px_0px_#1c1917] overflow-hidden transition-all">
                <input type="hidden" name="brand_id" value="<?= $brand_id ?>">

                <!-- SECTION HEADER (Compact) -->
                <div class="bg-indigo-600 text-white p-6 border-b border-stone-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-300 rounded-xl border border-stone-900 shadow-[2px_2px_0px_0px_#1c1917] flex items-center justify-center">
                            <i data-lucide="pen-tool" class="w-5 h-5 text-stone-900"></i>
                        </div>
                        <div>
                            <!-- Ukuran text dikurangi -->
                            <h1 class="text-xl font-extrabold tracking-tight uppercase">Narasi Content</h1>
                            <p class="text-indigo-200 font-bold text-[10px] uppercase tracking-[0.15em]">Drafting strategi konten</p>
                        </div>
                    </div>
                </div>

                <!-- BODY FORM (Space-y dikurangi dari 10 ke 6) -->
                <div class="p-6 space-y-6">
                    
                    <!-- 1. JUDUL KONTEN -->
                    <div class="space-y-2">
                        <label class="block font-black text-[10px] uppercase tracking-[0.15em] text-stone-400">Headline Konten</label>
                        <!-- Menambahkan id="headline_input" -->
                        <input type="text" name="title" id="headline_input" required 
                               class="w-full px-4 py-3 bg-stone-50 border border-stone-900 rounded-xl font-bold text-sm outline-none focus:bg-white focus:ring-2 focus:ring-indigo-100 transition-all placeholder:text-stone-300 shadow-[2px_2px_0px_0px_#f5f5f4]" 
                               placeholder="Input judul konten utama di sini...">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 2. PILIH PLATFORM -->
                        <div class="space-y-2">
                            <label class="block font-black text-[10px] uppercase tracking-[0.15em] text-stone-400">Channel Distribusi</label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="checkbox" name="target_tiktok" value="1" checked class="platform-checkbox hidden">
                                    <div class="p-3 border border-stone-200 rounded-xl transition-all flex flex-col items-center gap-1 hover:border-stone-900 group">
                                        <i data-lucide="music-2" class="w-4 h-4 text-stone-400 group-hover:text-stone-900"></i>
                                        <span class="font-black text-[9px] uppercase tracking-widest text-stone-400 group-hover:text-stone-900">TikTok</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="checkbox" name="target_instagram" value="1" checked class="platform-checkbox hidden">
                                    <div class="p-3 border border-stone-200 rounded-xl transition-all flex flex-col items-center gap-1 hover:border-stone-900 group">
                                        <i data-lucide="instagram" class="w-4 h-4 text-stone-400 group-hover:text-pink-600"></i>
                                        <span class="font-black text-[9px] uppercase tracking-widest text-stone-400 group-hover:text-pink-600">Instagram</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- 3. WORKFLOW MODE -->
                        <div class="space-y-2">
                            <label class="block font-black text-[10px] uppercase tracking-[0.15em] text-stone-400">Mode Produksi</label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="production_mode" value="ready_stock" checked class="peer hidden">
                                    <div class="p-3 border border-stone-200 rounded-xl peer-checked:bg-green-50 peer-checked:border-stone-900 peer-checked:shadow-[2px_2px_0px_0px_#18181b] transition-all text-center font-bold text-[9px] uppercase tracking-widest flex flex-col items-center gap-1">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i> Stock Video
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="production_mode" value="need_take" class="peer hidden">
                                    <div class="p-3 border border-stone-200 rounded-xl peer-checked:bg-red-50 peer-checked:border-stone-900 peer-checked:shadow-[2px_2px_0px_0px_#18181b] transition-all text-center font-bold text-[9px] uppercase tracking-widest flex flex-col items-center gap-1 text-stone-400 peer-checked:text-red-600">
                                        <i data-lucide="camera" class="w-4 h-4"></i> Butuh Take
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SCRIPTING AREA -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                        <!-- TikTok Script -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="block font-black text-[10px] uppercase tracking-[0.15em] text-stone-400">Script TikTok</label>
                                <span class="bg-stone-900 text-white text-[9px] px-1.5 py-0.5 rounded font-black uppercase">Voiceover</span>
                            </div>
                            <!-- Rows dikurangi -->
                            <textarea name="script_tiktok" id="script_tiktok" rows="6" 
                                      class="w-full p-4 bg-stone-50 border border-stone-900 rounded-xl font-mono-custom text-xs leading-relaxed outline-none focus:bg-white focus:ring-2 focus:ring-indigo-50 transition-all shadow-inner" 
                                      placeholder="Tulis script voiceover di sini..."></textarea>
                        </div>

                        <!-- Instagram Caption -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="block font-black text-[10px] uppercase tracking-[0.15em] text-stone-400">Caption Instagram</label>
                                <button type="button" onclick="copyScript()" class="text-[9px] font-black text-indigo-600 hover:underline uppercase tracking-widest flex items-center gap-1 transition-all">
                                    <i data-lucide="copy" class="w-3 h-3"></i> Sync dari TikTok
                                </button>
                            </div>
                            <textarea name="caption_instagram" id="caption_instagram" rows="6" 
                                      class="w-full p-4 bg-stone-50 border border-stone-900 rounded-xl font-mono-custom text-xs leading-relaxed outline-none focus:bg-white focus:ring-2 focus:ring-pink-50 transition-all shadow-inner" 
                                      placeholder="Drafting caption Instagram di sini..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- FORM FOOTER (Compact) -->
                <div class="p-6 bg-stone-50 border-t border-stone-900 flex justify-between items-center">
                    <p class="text-[9px] font-bold text-stone-400 uppercase max-w-xs leading-relaxed">
                        Data bakal diproses & di-assign sesuai mode produksi.
                    </p>
                    <button type="submit" class="group flex items-center gap-2 bg-stone-900 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-[3px_3px_0px_0px_#4b5563] hover:bg-stone-800 hover:-translate-y-0.5 active:translate-y-0 active:shadow-none transition-all duration-200">
                        Create & Process <i data-lucide="send" class="w-3 h-3 group-hover:translate-x-0.5 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="assets/js/platform_interaction.js"></script>
    <script>
        lucide.createIcons();

        // --- LOGIKA OTOMATIS HURUF KAPITAL DI DEPAN ---
        const headlineInput = document.getElementById('headline_input');

        headlineInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Logika: Pecah per kata, besarkan huruf pertama tiap kata, gabungkan kembali
            // Contoh: "makan nasi goreng" -> "Makan Nasi Goreng"
            let words = value.split(' ');
            let capitalizedWords = words.map(word => {
                return word.charAt(0).toUpperCase() + word.slice(1);
            });
            
            e.target.value = capitalizedWords.join(' ');
        });
        
        // Fitur Sync Script TikTok ke Instagram
        function copyScript() {
            const tiktok = document.getElementById('script_tiktok').value;
            const igField = document.getElementById('caption_instagram');
            
            if(!tiktok) {
                alert('Script TikTok masih kosong, belum bisa di-sync.');
                return;
            }
            
            igField.value = tiktok;
            
            // Visual Feedback
            igField.classList.add('ring-2', 'ring-green-100');
            setTimeout(() => {
                igField.classList.remove('ring-2', 'ring-green-100');
            }, 1000);
        }
    </script>
</body>
</html>