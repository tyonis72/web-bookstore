<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$my_id = $_SESSION['user']['id'];

/**
 * Query untuk mengambil daftar room chat:
 * Mengambil lawan bicara, pesan terakhir, dan jumlah pesan belum dibaca
 */
$sql = "SELECT 
            cr.id AS room_id,
            u.username AS lawan_bicara,
            u.role AS lawan_role,
            (SELECT pesan FROM chat_pesan WHERE chat_room_id = cr.id ORDER BY created_at DESC LIMIT 1) AS pesan_terakhir,
            (SELECT created_at FROM chat_pesan WHERE chat_room_id = cr.id ORDER BY created_at DESC LIMIT 1) AS waktu_terakhir,
            (SELECT COUNT(*) FROM chat_pesan WHERE chat_room_id = cr.id AND pengirim_id != '$my_id' AND is_read = 0) AS unread_count
        FROM chat_room cr
        JOIN users u ON (u.id = cr.pembeli_id OR u.id = cr.penjual_id)
        WHERE (cr.pembeli_id = '$my_id' OR cr.penjual_id = '$my_id')
        AND u.id != '$my_id'
        ORDER BY waktu_terakhir DESC";

$rooms = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox Pesan | Glass Amber Edition</title>
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
            opacity: 0.12;
            animation: float 18s infinite alternate;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(245, 158, 11, 0.4);
            transform: translateX(12px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .unread-badge {
            background: linear-gradient(to right, #f59e0b, #ea580c);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.2);
            border-radius: 10px;
        }
    </style>
</head>

<body class="antialiased min-h-screen">

    <div class="liquid-bg">
        <div class="blob" style="top: 10%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 15%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex">
        <aside class="fixed inset-y-0 left-0 z-50">
            <?php include '../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 ml-64 p-8 lg:p-12">

            <header class="mb-16">
                <h1 class="text-5xl font-black italic uppercase tracking-tighter leading-none">
                    Messaging <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">Hub</span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Pusat Komunikasi Merchant & Customer</p>
            </header>

            <div class="max-w-4xl space-y-5">
                <?php if (mysqli_num_rows($rooms) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($rooms)): ?>
                        <a href="room.php?room_id=<?= $row['room_id'] ?>" class="block group">
                            <div class="glass-card p-6 rounded-[3rem] flex items-center justify-between relative overflow-hidden">

                                <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/15 transition-all"></div>

                                <div class="flex items-center gap-7 relative z-10">
                                    <div class="relative">
                                        <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-950 rounded-2xl flex items-center justify-center text-amber-500 font-black italic text-2xl shadow-inner border border-white/10 group-hover:rotate-3 transition-transform">
                                            <?= strtoupper(substr($row['lawan_bicara'], 0, 1)) ?>
                                        </div>
                                        <?php if ($row['unread_count'] > 0): ?>
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 rounded-full border-4 border-[#020617] animate-pulse"></div>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="text-xl font-black italic uppercase tracking-tighter text-white group-hover:text-amber-400 transition-colors">
                                                <?= htmlspecialchars($row['lawan_bicara']) ?>
                                            </h3>
                                            <span class="text-[8px] px-2 py-0.5 rounded-lg bg-white/5 text-emerald-500/50 font-black uppercase border border-white/10 tracking-widest">
                                                <?= $row['lawan_role'] ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 line-clamp-1 italic font-medium group-hover:text-gray-300 transition-colors">
                                            <?= $row['pesan_terakhir'] ?? 'Belum ada percakapan...' ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-3 relative z-10">
                                    <?php if ($row['waktu_terakhir']): ?>
                                        <span class="text-[9px] font-black text-gray-700 uppercase tracking-widest">
                                            <?= date('H:i', strtotime($row['waktu_terakhir'])) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($row['unread_count'] > 0): ?>
                                        <span class="unread-badge text-white text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-tighter">
                                            <?= $row['unread_count'] ?> Baru
                                        </span>
                                    <?php endif; ?>

                                    <svg class="w-5 h-5 text-white/5 group-hover:text-amber-500 group-hover:translate-x-1 transition-all"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="glass-card p-24 rounded-[4rem] text-center border-dashed border-2 border-white/5">
                        <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8 opacity-20">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black italic uppercase text-gray-700 tracking-widest">Kotak Masuk Kosong</h3>
                        <p class="text-gray-800 text-xs mt-4 uppercase font-bold tracking-[0.2em]">Cari produk dan hubungi penjual untuk memulai chat.</p>
                    </div>
                <?php endif; ?>
            </div>

            <footer class="mt-20 text-center">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">Digital Encryption Verified • BookStore Hub</p>
            </footer>
        </main>
    </div>

</body>

</html>