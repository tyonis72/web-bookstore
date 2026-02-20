<?php require_once '../config/app.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | <?= APP_NAME ?></title>
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
    <div class="blob" style="top: 10%; right: 15%; background: #f59e0b;"></div> <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div> </div>

<div class="w-full max-w-md">
    <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden group">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all duration-700"></div>

        <div class="text-center mb-8 relative z-10">
            <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight">
                Welcome Back
            </h2>
            <p class="text-gray-500 text-sm mt-2 font-medium">Silahkan masuk ke akun <?= APP_NAME ?></p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 backdrop-blur-md rounded-2xl text-sm text-center flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Email atau password salah
            </div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php" method="POST" class="space-y-5 relative z-10">
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Email Address</label>
                <input type="email" name="email" 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all placeholder:text-gray-700 shadow-inner" 
                       placeholder="name@example.com" required>
            </div>

            <div>
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Password</label>
                <input type="password" name="password" 
                       class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all placeholder:text-gray-700 shadow-inner" 
                       placeholder="••••••••" required>
            </div>

            <div class="flex justify-end">
                <a href="forgot-password.php" class="text-xs text-amber-500/80 hover:text-amber-400 transition-colors font-bold">Forgot Password?</a>
            </div>

            <button type="submit" name="login" 
                    class="w-full py-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-amber-900/20 transition-all active:scale-[0.98]">
                Login Now
            </button>
        </form>

        <div class="mt-8 text-center relative z-10">
            <p class="text-gray-500 text-xs">
                Don't have an account? 
                <a href="register.php" class="text-emerald-400 hover:text-emerald-300 font-black transition-colors ml-1 uppercase tracking-wider">Register</a>
            </p>
        </div>
    </div>
    
    <div class="mt-8 text-center">
        <a href="../index.php" class="text-gray-600 hover:text-gray-400 text-xs transition-colors">
            &larr; Back to Home
        </a>
    </div>
</div>

</body>
</html>