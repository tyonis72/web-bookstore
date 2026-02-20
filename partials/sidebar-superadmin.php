<?php
// sidebar-superadmin.php dengan style Liquid Glass
?>
<aside
    class="w-72 min-h-screen flex flex-col justify-between p-6 sticky top-0 border-r border-white/5 bg-white/[0.02] backdrop-blur-2xl">

    <div>
        <div class="px-4 py-8 mb-6 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black tracking-tighter italic uppercase text-white leading-none">
                        <?= APP_NAME ?>
                    </h2>
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-[0.2em] mt-1">Superadmin</p>
                </div>
            </div>
        </div>

        <nav class="space-y-2">
            <?php
            // Logika sederhana untuk menandai link aktif
            $current_page = $_SERVER['PHP_SELF'];
            ?>

            <a href="<?= BASE_URL ?>/admin/superadmin/index.php"
                class="flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all font-bold text-xs uppercase tracking-widest group <?= strpos($current_page, 'index.php') !== false ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:bg-white/5 hover:text-indigo-400' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="<?= BASE_URL ?>/admin/superadmin/penjual.php"
                class="flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all font-bold text-xs uppercase tracking-widest group <?= strpos($current_page, 'penjual.php') !== false ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:bg-white/5 hover:text-indigo-400' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 1.34 5 3s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
                Daftar Penjual
            </a>

            <a href="<?= BASE_URL ?>/admin/superadmin/pembeli.php"
                class="flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all font-bold text-xs uppercase tracking-widest group <?= strpos($current_page, 'pembeli.php') !== false ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:bg-white/5 hover:text-indigo-400' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Daftar Pembeli
            </a>

            <a href="<?= BASE_URL ?>/admin/superadmin/kategori.php"
                class="flex items-center gap-4 px-5 py-3.5 rounded-2xl transition-all font-bold text-xs uppercase tracking-widest group <?= strpos($current_page, 'kategori.php') !== false ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-500 hover:bg-white/5 hover:text-indigo-400' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Kategori Buku
            </a>
        </nav>
    </div>

    <div class="space-y-4">
        <a href="pusat-bantuan.php"
            class="block px-5 py-4 rounded-2xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.07] transition-all group">
            <p class="text-[8px] font-black uppercase tracking-[0.2em] text-indigo-400 mb-2 italic">Support Center</p>
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] font-bold text-gray-300 group-hover:text-white uppercase tracking-widest transition-colors">Pusat
                    Bantuan & FAQ</span>
                <svg class="w-3 h-3 text-indigo-500 group-hover:translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>

        <a href="<?= BASE_URL ?>/auth/logout.php" onclick="return confirm('Akhiri sesi superadmin?')"
            class="flex items-center justify-center gap-3 w-full px-4 py-3.5 bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 rounded-2xl transition-all font-black text-[10px] uppercase tracking-[0.2em]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout Session
        </a>
    </div>
</aside>