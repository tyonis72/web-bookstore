<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

$my_id = $_SESSION['user']['id'];

/**
 * Query untuk mengambil daftar room chat:
 * 1. Mencari room di mana user terlibat (sebagai pembeli atau penjual)
 * 2. Mengambil nama lawan bicara
 * 3. Mengambil pesan terakhir dan menghitung pesan yang belum dibaca
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
    <title>Inbox Pesan | Liquid Emerald</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #064e3b 0%, #020617 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.1);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(16, 185, 129, 0.05);
            border-color: rgba(16, 185, 129, 0.3);
            transform: translateX(10px);
        }
    </style>
</head>

<body class="flex text-emerald-50">

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../partials/sidebar-pembeli.php'; ?>
    </aside>

    <main class="flex-1 ml-64 p-8 lg:p-12">

        <header class="mb-12">
            <h1 class="text-5xl font-black italic uppercase tracking-tighter">
                Inbox <span class="text-emerald-400">Pesan</span>
            </h1>
            <div class="flex items-center gap-4 mt-3">
                <div class="h-[2px] w-12 bg-emerald-500/50"></div>
                <p class="text-emerald-500/40 font-bold uppercase tracking-[0.3em] text-[10px]">
                    Private Conversations
                </p>
            </div>
        </header>

        <div class="max-w-4xl space-y-4">
            <?php if (mysqli_num_rows($rooms) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($rooms)): ?>
                    <a href="room.php?room_id=<?= $row['room_id'] ?>" class="block group">
                        <div class="glass-card p-6 rounded-[2.5rem] flex items-center justify-between relative overflow-hidden">

                            <div
                                class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 blur-3xl group-hover:bg-emerald-500/10 transition-all">
                            </div>

                            <div class="flex items-center gap-6 relative z-10">
                                <div
                                    class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 font-black text-xl shadow-inner border border-emerald-500/10 group-hover:scale-110 transition-transform">
                                    <?= strtoupper(substr($row['lawan_bicara'], 0, 1)) ?>
                                </div>

                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3
                                            class="text-xl font-black italic uppercase tracking-tight text-white group-hover:text-emerald-400 transition-colors">
                                            <?= htmlspecialchars($row['lawan_bicara']) ?>
                                        </h3>
                                        <span
                                            class="text-[9px] px-2 py-0.5 rounded-full bg-white/5 text-emerald-500/50 font-black uppercase border border-white/5">
                                            <?= $row['lawan_role'] ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-emerald-100/40 mt-1 line-clamp-1 italic font-medium">
                                        <?= $row['pesan_terakhir'] ?? 'Belum ada pesan...' ?>
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2 relative z-10">
                                <?php if ($row['waktu_terakhir']): ?>
                                    <span class="text-[10px] font-bold text-emerald-500/30 uppercase tracking-tighter">
                                        <?= date('H:i', strtotime($row['waktu_terakhir'])) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($row['unread_count'] > 0): ?>
                                    <span
                                        class="bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-full animate-pulse shadow-[0_0_15px_rgba(16,185,129,0.5)]">
                                        <?= $row['unread_count'] ?> BARU
                                    </span>
                                <?php endif; ?>

                                <svg class="w-5 h-5 text-emerald-500/20 group-hover:text-emerald-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="glass-card p-20 rounded-[3rem] text-center border-dashed border-emerald-500/20">
                    <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-emerald-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black italic uppercase text-white/50">Belum ada percakapan</h3>
                    <p class="text-emerald-100/20 text-sm mt-2">Mulai chat dengan penjual melalui halaman produk.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>

</html>