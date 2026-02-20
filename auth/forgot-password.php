<?php require_once '../config/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | <?= APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 30px); }
            100% { transform: translate(0, 0); }
        }
        .liquid-bg {
            position: fixed; z-index: -1; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
        }
        .blob {
            position: absolute; width: 600px; height: 600px; filter: blur(90px);
            border-radius: 50%; opacity: 0.15; animation: float 18s infinite alternate;
        }
    </style>
</head>
<body class="text-gray-100 antialiased min-h-screen flex items-center justify-center p-4">

<div class="liquid-bg">
    <div class="blob" style="top: 10%; right: 15%; background: #f59e0b;"></div>
    <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>
</div>

<div class="w-full max-w-md">
    <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden group">
        
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-700"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 bg-amber-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-500/20">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight">
                Reset Password
            </h2>
            <p class="text-gray-500 text-sm mt-3 font-medium leading-relaxed">
                Masukkan email Anda untuk menerima instruksi pemulihan akun.
            </p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 backdrop-blur-md rounded-2xl text-xs text-center font-bold tracking-wide">
                ✨ Link reset password telah dikirim ke email Anda.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 backdrop-blur-md rounded-2xl text-xs text-center font-bold tracking-wide">
                ❌ Email tidak ditemukan dalam sistem kami.
            </div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php" method="POST" class="space-y-6 relative z-10">
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-[0.2em] mb-2 block ml-1">Registered Email</label>
                <input type="email" name="email" 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all placeholder:text-gray-700 shadow-inner" 
                       placeholder="yourname@domain.com" required>
            </div>

            <button type="submit" name="forgot" 
                    class="w-full py-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] shadow-xl shadow-amber-900/40 transition-all active:scale-[0.98]">
                Kirim Link Reset
            </button>
        </form>

        <div class="mt-10 text-center relative z-10">
            <a href="login.php" class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-amber-400 transition-colors font-bold uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Login
            </a>
        </div>
    </div>
</div>

</body>
</html>