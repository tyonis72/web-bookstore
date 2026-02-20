<?php
$pembeli_id = $_SESSION['user']['id'] ?? null;

// Hitung jumlah item di keranjang
$jumlah_keranjang = 0;
if ($pembeli_id) {
    $q = mysqli_query(
        $conn,
        "SELECT SUM(qty) AS total 
         FROM keranjang 
         WHERE pembeli_id='$pembeli_id'"
    );
    $data = mysqli_fetch_assoc($q);
    $jumlah_keranjang = $data['total'] ?? 0;
}
?>

<aside class="w-64 glass-card min-h-screen flex flex-col">
    <div class="p-6 border-b border-white/10">
        <h2 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
            BookStore
        </h2>
        <p class="text-xs uppercase tracking-widest text-emerald-100/40 font-semibold mt-1">
            Exclusive Buyer
        </p>
    </div>

    <nav class="p-4 flex-1 space-y-3">

        <a href="<?= BASE_URL ?>/pembeli/produk/index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 hover:bg-emerald-500/20 text-emerald-50 group border border-transparent hover:border-emerald-500/30">
            <svg class="w-5 h-5 text-emerald-400 group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="font-medium">Produk</span>
        </a>

        <a href="<?= BASE_URL ?>/pembeli/keranjang/index.php"
            class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 hover:bg-amber-500/20 text-amber-50 group border border-transparent hover:border-amber-500/30">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-400 group-hover:scale-110 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="font-medium">Keranjang</span>
            </div>

            <?php if ($jumlah_keranjang > 0): ?>
                <span
                    class="bg-gradient-to-r from-amber-500 to-orange-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                    <?= $jumlah_keranjang ?>
                </span>
            <?php endif ?>
        </a>

        <a href="<?= BASE_URL ?>/pembeli/pesanan/index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 hover:bg-emerald-500/20 text-emerald-50 group border border-transparent hover:border-emerald-500/30">
            <svg class="w-5 h-5 text-emerald-400 group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="font-medium">Pesanan Saya</span>
        </a>

        <a href="<?= BASE_URL ?>/chat/index.php"
            class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-300 hover:bg-emerald-500/20 text-emerald-50 group border border-transparent hover:border-emerald-500/30">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400 group-hover:rotate-12 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <span class="font-medium">Pesan</span>
            </div>

            <span id="notif-badge"
                class="hidden bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                0
            </span>
        </a>
    </nav>

    <div class="p-4 border-t border-white/10">
        <a href="<?= BASE_URL ?>/pembeli/bantuan/index.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/60 hover:bg-white/5 hover:text-emerald-50 transition-all group mb-2">
            <svg class="w-5 h-5 text-emerald-400/50 group-hover:text-emerald-400 transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">F.A.Q</span>
        </a>

        <a href="<?= BASE_URL ?>/auth/logout.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/20 hover:text-red-300 transition-all border border-transparent hover:border-red-500/30 group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-medium">Keluar</span>
        </a>
    </div>
</aside>