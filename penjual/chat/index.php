<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual'); 
$my_id = $_SESSION['user']['id'];

// Ambil daftar chat yang masuk ke penjual ini
$sql = "SELECT 
            cr.id AS room_id,
            u.username AS nama_pembeli,
            (SELECT pesan FROM chat_pesan WHERE chat_room_id = cr.id ORDER BY created_at DESC LIMIT 1) AS pesan_terakhir,
            (SELECT created_at FROM chat_pesan WHERE chat_room_id = cr.id ORDER BY created_at DESC LIMIT 1) AS waktu_terakhir,
            (SELECT COUNT(*) FROM chat_pesan WHERE chat_room_id = cr.id AND pengirim_id != '$my_id' AND is_read = 0) AS unread_count
        FROM chat_room cr
        JOIN users u ON u.id = cr.pembeli_id
        WHERE cr.penjual_id = '$my_id'
        ORDER BY waktu_terakhir DESC";

$rooms = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Masuk - Glass Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-30px, 40px); }
            100% { transform: translate(0, 0); }
        }
        .liquid-bg {
            position: fixed; z-index: -1; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at center, #111827 0%, #030712 100%);
        }
        .blob {
            position: absolute; width: 600px; height: 600px; filter: blur(90px);
            border-radius: 50%; opacity: 0.12; animation: float 18s infinite alternate;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="text-gray-100 antialiased overflow-x-hidden">

<div class="liquid-bg">
    <div class="blob" style="top: 15%; right: 15%; background: #10b981;"></div> 
    <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -4s; background: #f59e0b;"></div> 
</div>

<div class="flex min-h-screen">
    <?php include '../../partials/sidebar-penjual.php'; ?>

    <main class="flex-1 p-8 ml-64">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
                Pesan Masuk
            </h1>
            <p class="text-gray-400 text-sm mt-1 italic uppercase tracking-widest">Komunikasi real-time dengan pelanggan Anda</p>
        </div>

        <div class="max-w-4xl space-y-4">
            <?php if (mysqli_num_rows($rooms) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($rooms)): ?>
                    <a href="room.php?room_id=<?= $row['room_id'] ?>" 
                       class="block glass-card p-5 rounded-2xl hover:bg-white/10 hover:border-emerald-500/30 transition-all duration-300 group relative overflow-hidden">
                        
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl font-bold border border-emerald-500/30 shadow-lg group-hover:scale-105 transition-transform">
                                    <?= strtoupper(substr($row['nama_pembeli'], 0, 1)) ?>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">
                                        <?= htmlspecialchars($row['nama_pembeli']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-400 line-clamp-1 italic mt-0.5">
                                        <?= $row['pesan_terakhir'] ?? '<span class="text-gray-600">Belum ada pesan...</span>' ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-3">
                                <?php if ($row['waktu_terakhir']): ?>
                                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-tighter">
                                        <?= date('H:i', strtotime($row['waktu_terakhir'])) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($row['unread_count'] > 0): ?>
                                    <span class="bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.4)] animate-pulse uppercase tracking-wider">
                                        <?= $row['unread_count'] ?> Baru
                                    </span>
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-20 text-center">
                    <div class="w-20 h-20 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/5">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-400">Belum ada percakapan</h3>
                    <p class="text-gray-600 text-sm mt-2">Pesan dari pembeli akan muncul di sini secara otomatis.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>