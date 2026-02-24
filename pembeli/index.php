<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

check_role('pembeli');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Pembeli - Glass Amber Edition</title>
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

        body {
            background: #020617;
            color: white;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
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

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .amber-glow:hover {
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.1);
        }

        .emerald-glow:hover {
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.1);
        }
    </style>
</head>

<body class="antialiased">

    <div class="liquid-bg">
        <div class="blob" style="top: 10%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">

        <aside class="w-64 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 md:p-12">
            <header class="mb-16">
                <h1 class="text-5xl font-black tracking-tighter uppercase italic">
                    Welcome,
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    </span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Eksplorasi Katalog & Manajemen Pesanan</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                <div class="glass-card p-10 rounded-[3rem] transition-all duration-500 emerald-glow group relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-8 border border-emerald-500/20">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-black italic text-white mb-4 uppercase tracking-tighter">Marketplace</h2>
                        <p class="text-gray-400 text-sm leading-relaxed mb-8">
                            Temukan koleksi literatur terbaik dari berbagai kategori pilihan.
                        </p>
                        <a href="<?= BASE_URL ?>/pembeli/produk/index.php"
                            class="inline-flex items-center px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-emerald-900/40">
                            Mulai Belanja
                        </a>
                    </div>
                </div>

                <div class="glass-card p-10 rounded-[3rem] transition-all duration-500 amber-glow group relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>

                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-8 border border-amber-500/20">
                            <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-black italic text-white mb-4 uppercase tracking-tighter">My Orders</h2>
                        <p class="text-gray-400 text-sm leading-relaxed mb-8">
                            Pantau progres pengiriman dan unduh invoice untuk setiap transaksi.
                        </p>
                        <a href="<?= BASE_URL ?>/pembeli/pesanan/index.php"
                            class="inline-flex items-center px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest shadow-lg shadow-amber-900/40">
                            Riwayat Pesanan
                        </a>
                    </div>
                </div>

            </div>

            <footer class="mt-20 text-center">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">BookStore Digital Authenticity Verified</p>
            </footer>
        </main>

    </div>

</body>

</html>