<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('superadmin');

// HITUNG DATA
$total_penjual = mysqli_num_rows(
    mysqli_query($conn, "SELECT id FROM users WHERE role='penjual'")
);

$total_pembeli = mysqli_num_rows(
    mysqli_query($conn, "SELECT id FROM users WHERE role='pembeli'")
);

$total_kategori = mysqli_num_rows(
    mysqli_query($conn, "SELECT id FROM kategori")
);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Superadmin Panel | Liquid Glass</title>
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
            background: radial-gradient(circle at top right, #1e1b4b 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-5px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .blob {
            position: fixed;
            width: 500px;
            height: 500px;
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.15;
            animation: float 20s infinite alternate;
            z-index: -1;
        }
    </style>
</head>

<body class="antialiased overflow-x-hidden">

    <div class="blob" style="top: -10%; right: -5%; background: #4f46e5;"></div>
    <div class="blob" style="bottom: -10%; left: -5%; background: #06b6d4; animation-delay: -7s;"></div>

    <div class="flex min-h-screen">

        <?php include __DIR__ . '../../../partials/sidebar-superadmin.php'; ?>

        <main class="flex-1 p-8 lg:p-12">

            <div class="mb-12">
                <h1 class="text-4xl font-black tracking-tighter italic uppercase">
                    Control <span class="text-cyan-400">Center</span>
                </h1>
                <p class="text-indigo-300/50 text-xs font-bold uppercase tracking-[0.3em] mt-2">Sistem Manajemen Utama
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="glass-card rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 text-indigo-500/10 group-hover:text-indigo-500/20 transition-colors">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-2">Total
                        Vendor/Penjual</p>
                    <h2 class="text-5xl font-black italic tracking-tighter text-white">
                        <?= $total_penjual ?>
                    </h2>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Aktif dalam
                            sistem</span>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 text-cyan-500/10 group-hover:text-cyan-500/20 transition-colors">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 1.34 5 3s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-cyan-400 mb-2">Total Pelanggan</p>
                    <h2 class="text-5xl font-black italic tracking-tighter text-white">
                        <?= $total_pembeli ?>
                    </h2>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Terverifikasi</span>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 text-emerald-500/10 group-hover:text-emerald-500/20 transition-colors">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                        </svg>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-2">Klasifikasi Buku
                    </p>
                    <h2 class="text-5xl font-black italic tracking-tighter text-white">
                        <?= $total_kategori ?>
                    </h2>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Kategori
                            Tersedia</span>
                    </div>
                </div>

            </div>

        </main>

    </div>

</body>

</html>