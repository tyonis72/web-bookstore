<?php
if (!isset($_SESSION['user']))
    exit;
?>

<aside
    class="w-64 flex flex-col justify-between border-r border-white/10 bg-white/5 backdrop-blur-2xl h-screen sticky top-0">
    <div>
        <div class="p-6 border-b border-white/10">
            <div
                class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400 tracking-tighter">
                BookStore
            </div>
            <div class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-semibold mt-1">
                Panel Penjual
            </div>
        </div>

        <nav class="p-4 space-y-3 mt-4">
            <?php
            // Mendapatkan nama file saat ini untuk class 'active'
            $current_page = basename($_SERVER['PHP_SELF']);
            ?>

            <a href="<?= BASE_URL ?>/penjual/dashboard.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= $current_page == 'dashboard.php' ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <span
                    class="p-2 rounded-lg <?= $current_page == 'dashboard.php' ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="<?= BASE_URL ?>/penjual/produk.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= $current_page == 'produk.php' ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <span
                    class="p-2 rounded-lg <?= $current_page == 'produk.php' ? 'bg-purple-500/20 text-purple-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <span class="font-medium">Produk</span>
            </a>

            <a href="<?= BASE_URL ?>/penjual/pesanan/index.php"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($_SERVER['PHP_SELF'], 'pesanan') !== false ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?>">
                <span
                    class="p-2 rounded-lg <?= strpos($_SERVER['PHP_SELF'], 'pesanan') !== false ? 'bg-pink-500/20 text-pink-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </span>
                <span class="font-medium">Pesanan</span>
            </a>

            <a href="<?= BASE_URL ?>/penjual/laporan/index.php"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($_SERVER['PHP_SELF'], 'laporan') !== false ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?> mb-1">

                <div class="flex items-center gap-3">
                    <span
                        class="p-2 rounded-lg <?= strpos($_SERVER['PHP_SELF'], 'laporan') !== false ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                    <span class="font-medium">Laporan Penjualan</span>
                </div>

                <svg class="w-3 h-3 text-gray-600 group-hover:text-white transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="<?= BASE_URL ?>/penjual/chat/index.php"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 group <?= strpos($_SERVER['PHP_SELF'], 'chat') !== false ? 'bg-white/10 text-white border border-white/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' ?> mb-1">

                <div class="flex items-center gap-3">
                    <span
                        class="p-2 rounded-lg <?= strpos($_SERVER['PHP_SELF'], 'chat') !== false ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-800 text-gray-500 group-hover:text-white' ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </span>
                    <span class="font-medium">Pesan Masuk</span>
                </div>

                <?php
                $my_id = $_SESSION['user']['id'];
                $notif_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM chat_pesan cp 
                            JOIN chat_room cr ON cp.chat_room_id = cr.id 
                            WHERE cr.penjual_id = '$my_id' AND cp.pengirim_id != '$my_id' AND cp.is_read = 0");
                $notif_data = mysqli_fetch_assoc($notif_query);
                if ($notif_data['total'] > 0):
                    ?>
                    <span
                        class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[10px] font-black text-white shadow-lg shadow-emerald-500/40 <?= strpos($_SERVER['PHP_SELF'], 'chat') === false ? 'animate-bounce' : '' ?>">
                        <?= $notif_data['total'] ?>
                    </span>
                <?php endif; ?>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-white/10 bg-white/5">
        <div class="flex items-center gap-3 mb-4 px-2">
            <div
                class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500 to-purple-500 flex items-center justify-center text-xs font-bold shadow-lg">
                <?= strtoupper(substr($_SESSION['user']['nama'] ?? '', 0, 1)) ?>
            </div>
            <a href="<?= BASE_URL ?>/penjual/profile/profile.php" class="flex-1 truncate group">
                <div class="truncate">
                    <p class="text-sm font-semibold text-white truncate group-hover:text-emerald-400 transition-colors">
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    </p>
                    <p
                        class="text-[10px] text-gray-500 group-hover:text-gray-400 transition-colors flex items-center gap-1">
                        Penjual
                        <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </p>
                </div>
            </a>
        </div>
        <a href="<?= BASE_URL ?>/auth/logout.php"
            class="block text-center px-4 py-2.5 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl transition-all duration-300 text-sm font-semibold">
            Logout System
        </a>
    </div>
</aside>