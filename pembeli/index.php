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
    <title>Dashboard Pembeli - Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Gradient Background */
        body {
            background: linear-gradient(135deg, #064e3b 0%, #022c22 50%, #78350f 100%);
            background-attachment: fixed;
        }

        /* Liquid Glass Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        /* Emerald & Amber Glow */
        .glow-emerald {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        }

        .glow-amber {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
        }

        /* Liquid Animation */
        .liquid-bg {
            position: fixed;
            z-index: -1;
            top: -50%;
            left: -50%;
            right: -50%;
            bottom: -50%;
            width: 200%;
            height: 200%;
            background: transparent url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            opacity: 0.1;
        }
    </style>
</head>

<body class="text-white">

    <div class="liquid-bg"></div>

    <div class="flex min-h-screen">

        <aside class="w-64 glass-card border-r-0 m-4 rounded-3xl overflow-hidden hidden md:block">
            <?php include '../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8">
            <header class="mb-10">
                <h1 class="text-4xl font-extrabold tracking-tight">
                    Selamat Datang,
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    </span>
                </h1>
                <p class="text-emerald-100/60 mt-2 italic">Akses dashboard belanja eksklusif Anda hari ini.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="glass-card p-8 rounded-3xl glow-emerald hover:scale-[1.02] transition-all duration-300 group">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-6 border border-emerald-500/30 group-hover:bg-emerald-500/40 transition-colors">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h2 class="font-bold text-2xl text-emerald-50">Lihat Produk</h2>
                    <p class="text-emerald-100/50 mt-3 leading-relaxed">
                        Jelajahi koleksi buku premium dari kurasi penjual terbaik kami.
                    </p>
                    <a href="<?= BASE_URL ?>/pembeli/produk/index.php"
                        class="inline-flex items-center mt-6 px-6 py-2.5 bg-emerald-600/20 hover:bg-emerald-600/40 text-emerald-300 rounded-xl border border-emerald-500/30 transition-all font-medium">
                        Lihat Produk
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div class="glass-card p-8 rounded-3xl glow-amber hover:scale-[1.02] transition-all duration-300 group">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-2xl flex items-center justify-center mb-6 border border-amber-500/30 group-hover:bg-amber-500/40 transition-colors">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="font-bold text-2xl text-amber-50">Pesanan Saya</h2>
                    <p class="text-amber-100/50 mt-3 leading-relaxed">
                        Pantau status pengiriman dan riwayat belanja Anda secara real-time.
                    </p>
                    <a href="<?= BASE_URL ?>/pembeli/pesanan/index.php"
                        class="inline-flex items-center mt-6 px-6 py-2.5 bg-amber-600/20 hover:bg-amber-600/40 text-amber-300 rounded-xl border border-amber-500/30 transition-all font-medium">
                        Lihat Pesanan
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

            </div>
        </main>

    </div>

</body>

</html>