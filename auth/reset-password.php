<?php
require_once '../config/app.php';
// Token biasanya diambil dari URL: reset-password.php?token=xyz
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Set New Password | <?= APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-20px, 30px);
            }

            100% {
                transform: translate(0, 0);
            }
        }

        .liquid-bg {
            position: fixed;
            z-index: -1;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            filter: blur(90px);
            border-radius: 50%;
            opacity: 0.15;
            animation: float 18s infinite alternate;
        }
    </style>
</head>

<body class="text-gray-100 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="liquid-bg">
        <div class="blob" style="top: 10%; right: 15%; background: #10b981;"></div>
        <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #f59e0b;"></div>
    </div>

    <div class="w-full max-w-md">
        <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden group">

            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>

            <div class="text-center mb-8 relative z-10">
                <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-emerald-500/20">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400 tracking-tight">
                    New Password
                </h2>
                <p class="text-gray-500 text-sm mt-3 font-medium">
                    Masukkan password baru yang kuat untuk mengamankan kembali akun Anda.
                </p>
            </div>

            <form action="../controllers/AuthController.php" method="POST" class="space-y-5 relative z-10">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-[0.2em] mb-2 block ml-1">New Password</label>
                    <input type="password" name="password"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500/50 text-white transition-all placeholder:text-gray-700 shadow-inner"
                        placeholder="••••••••" required>
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-[0.2em] mb-2 block ml-1">Confirm Password</label>
                    <input type="password" name="confirm_password"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500/50 text-white transition-all placeholder:text-gray-700 shadow-inner"
                        placeholder="••••••••" required>
                </div>

                <button type="submit" name="update_password"
                    class="w-full py-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] shadow-xl shadow-emerald-900/40 transition-all active:scale-[0.98]">
                    Update Password
                </button>
            </form>

            <div class="mt-8 text-center relative z-10">
                <a href="login.php" class="text-xs text-gray-500 hover:text-emerald-400 transition-colors font-bold uppercase tracking-widest">
                    I remember my password
                </a>
            </div>
        </div>
    </div>

</body>

</html>