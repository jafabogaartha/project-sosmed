<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap'); body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#FDFBF7] flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md bg-white border-2 border-stone-900 rounded-2xl shadow-[6px_6px_0px_0px_#1c1917] p-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-100 rounded-xl border-2 border-stone-900 mb-4 text-indigo-600">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Welcome Back</h1>
            <p class="text-stone-500 font-medium text-sm mt-1">Enter your credentials to access the workspace.</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-50 border-2 border-red-500 text-red-700 p-3 rounded-xl mb-6 flex items-center gap-3 text-sm font-bold">
                <i data-lucide="alert-circle" class="w-5 h-5"></i> Invalid Credentials
            </div>
        <?php endif; ?>

        <form action="../src/handlers/authentication_service.php" method="POST" class="space-y-5">
            <input type="hidden" name="login_attempt" value="1">
            <div>
                <label class="block font-bold text-xs uppercase mb-1.5 text-stone-700">Work Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-stone-50 border-2 border-stone-900 rounded-xl font-bold focus:bg-white focus:ring-2 focus:ring-indigo-100 outline-none transition" placeholder="user@company.com">
            </div>
            <div>
                <label class="block font-bold text-xs uppercase mb-1.5 text-stone-700">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 bg-stone-50 border-2 border-stone-900 rounded-xl font-bold focus:bg-white focus:ring-2 focus:ring-indigo-100 outline-none transition" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white border-2 border-stone-900 rounded-xl font-bold text-sm shadow-[4px_4px_0px_0px_#1c1917] hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_#1c1917] active:translate-y-0 active:shadow-[2px_2px_0px_0px_#1c1917] transition-all flex items-center justify-center gap-2">
                Sign In <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>