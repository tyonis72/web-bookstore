<?php
if (!isset($_SESSION['user'])) {
    exit;
}

$pembeli_id = $_SESSION['user']['id'];
$current_page = $_SERVER['PHP_SELF'];

// 1. Hitung jumlah item di keranjang
$q_cart = mysqli_query($conn, "SELECT SUM(qty) AS total FROM keranjang WHERE pembeli_id='$pembeli_id'");
$data_cart = mysqli_fetch_assoc($q_cart);
$jumlah_keranjang = $data_cart['total'] ?? 0;

// 2. Cek apakah ada refund yang baru saja disetujui (Status: refunded)
$q_refund = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE pembeli_id = '$pembeli_id' AND status = 'refunded'");
$refund_notif = mysqli_fetch_assoc($q_refund);
?>

<aside class="w-64 flex flex-col justify-between border-r border-white/10 bg-[#020617]/80 backdrop-blur-2xl h-screen sticky top-0 z-40">
    <div>
        <div class="p-6 border-b border-white/10">
            <div class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tighter">
                BookStore
            </div>
            <div class="text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold mt-1">
                Buyer Command Center
            </div>
        </div>

        <nav class="p-4 space-y-2 mt-4">

            <a href="<?= BASE_URL ?>/pembeli/produk/index.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($current_page, '/produk/') !== false ? 'bg-white/10 text-white border border-white/20 shadow-xl shadow-black/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <span class="p-2 rounded-lg <?= strpos($current_page, '/produk/') !== false ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <span class="text-xs font-black uppercase tracking-widest">Katalog Produk</span>
            </a>

            <a href="<?= BASE_URL ?>/pembeli/keranjang/index.php"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($current_page, '/keranjang/') !== false ? 'bg-white/10 text-white border border-white/20 shadow-xl shadow-black/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-lg <?= strpos($current_page, '/keranjang/') !== false ? 'bg-amber-500/20 text-amber-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    <span class="text-xs font-black uppercase tracking-widest">Keranjang</span>
                </div>
                <?php if ($jumlah_keranjang > 0): ?>
                    <span class="bg-gradient-to-r from-amber-500 to-orange-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-amber-900/40">
                        <?= $jumlah_keranjang ?>
                    </span>
                <?php endif ?>
            </a>

            <a href="<?= BASE_URL ?>/pembeli/pesanan/index.php"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($current_page, '/pesanan/') !== false ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-lg <?= strpos($current_page, '/pesanan/') !== false ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </span>
                    <span class="text-xs font-black uppercase tracking-widest">Pesanan Saya</span>
                </div>
                <?php if ($refund_notif['total'] > 0): ?>
                    <span class="flex h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                <?php endif; ?>
            </a>

            <a href="<?= BASE_URL ?>/chat/index.php"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($current_page, '/chat/') !== false ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <div class="flex items-center gap-3">
                    <span class="p-2 rounded-lg <?= strpos($current_page, '/chat/') !== false ? 'bg-purple-500/20 text-purple-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </span>
                    <span class="text-xs font-black uppercase tracking-widest">Inbox Chat</span>
                </div>
                <?php
                // Hitung chat belum dibaca khusus untuk pembeli
                $chat_notif_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM chat_pesan cp 
                            JOIN chat_room cr ON cp.chat_room_id = cr.id 
                            WHERE cr.pembeli_id = '$pembeli_id' AND cp.pengirim_id != '$pembeli_id' AND cp.is_read = 0");
                $chat_notif = mysqli_fetch_assoc($chat_notif_query);
                if ($chat_notif['total'] > 0):
                ?>
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[9px] font-black text-white shadow-lg shadow-emerald-500/40">
                        <?= $chat_notif['total'] ?>
                    </span>
                <?php endif; ?>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-white/10 bg-white/5">
        <div class="flex items-center gap-3 mb-4 px-2">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-emerald-500 flex items-center justify-center text-xs font-black shadow-lg text-white">
                <?= strtoupper(substr($_SESSION['user']['username'] ?? 'P', 0, 1)) ?>
            </div>
            <div class="flex-1 truncate">
                <p class="text-xs font-black text-white truncate uppercase tracking-tighter">
                    <?= htmlspecialchars($_SESSION['user']['username']) ?>
                </p>
                <p class="text-[8px] text-amber-500 font-black uppercase tracking-widest flex items-center gap-1">
                    Premium Buyer
                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                </p>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/auth/logout.php"
            class="block text-center px-4 py-3 bg-red-500/10 hover:bg-red-600 text-red-500 hover:text-white border border-red-500/20 rounded-2xl transition-all duration-300 text-[10px] font-black uppercase tracking-[0.2em]">
            TERMINATE SESSION
        </a>
    </div>
</aside>