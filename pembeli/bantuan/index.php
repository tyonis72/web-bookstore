<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Bantuan | Liquid Emerald</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #064e3b 0%, #020617 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.1);
        }
        summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="flex text-emerald-50">

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../../partials/sidebar-pembeli.php'; ?>
    </aside>

    <main class="flex-1 ml-64 p-8 lg:p-12 min-h-screen">
        
        <div class="mb-16 animate-in fade-in duration-700">
            <h1 class="text-6xl font-black italic uppercase tracking-tighter leading-none">
                Pusat <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200 drop-shadow-sm">
                    Bantuan
                </span>
            </h1>
            <div class="flex items-center gap-4 mt-4">
                <div class="h-[2px] w-12 bg-emerald-500/50"></div>
                <p class="text-emerald-500/50 font-bold uppercase tracking-[0.4em] text-[10px]">
                    Support & Information Center
                </p>
            </div>
        </div>

        <div class="max-w-5xl space-y-6">

            <details class="group glass-card rounded-[2.5rem] overflow-hidden">
                <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                    <div class="flex items-center gap-8">
                        <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 shadow-inner group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black italic uppercase tracking-tight text-white">Panduan Belanja</h3>
                            <p class="text-[10px] text-emerald-500/40 font-bold uppercase tracking-widest mt-1">Langkah demi langkah transaksi</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full border border-emerald-500/20 flex items-center justify-center group-open:rotate-180 transition-all duration-500">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </summary>
                <div class="px-12 pb-10 pt-4 text-emerald-100/60 text-sm leading-relaxed border-t border-emerald-500/10 bg-emerald-900/10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
                        <div class="space-y-4">
                            <p class="flex gap-4"><b class="text-emerald-400 font-black italic">01.</b> Cari buku melalui katalog Produk yang tersedia.</p>
                            <p class="flex gap-4"><b class="text-emerald-400 font-black italic">02.</b> Masukkan ke Keranjang dan tentukan jumlah pesanan.</p>
                        </div>
                        <div class="space-y-4">
                            <p class="flex gap-4"><b class="text-emerald-400 font-black italic">03.</b> Masuk ke menu Keranjang untuk memulai Checkout.</p>
                            <p class="flex gap-4"><b class="text-emerald-400 font-black italic">04.</b> Pilih alamat dan selesaikan pembayaran sesuai tagihan.</p>
                        </div>
                    </div>
                </div>
            </details>

            <details class="group glass-card rounded-[2.5rem] overflow-hidden">
                <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                    <div class="flex items-center gap-8">
                        <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 shadow-inner group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black italic uppercase tracking-tight text-white">Logistik & Pengiriman</h3>
                            <p class="text-[10px] text-emerald-500/40 font-bold uppercase tracking-widest mt-1">Status dan estimasi waktu</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full border border-emerald-500/20 flex items-center justify-center group-open:rotate-180 transition-all duration-500">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </summary>
                <div class="px-12 pb-10 pt-4 text-emerald-100/60 text-sm leading-relaxed border-t border-emerald-500/10 bg-emerald-900/10">
                    <p class="mb-4">Kami bekerja sama dengan ekspedisi terbaik (J&T, JNE, SiCepat) dengan estimasi:</p>
                    <div class="flex gap-4">
                        <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20 flex-1">
                            <p class="font-black italic text-emerald-400 uppercase text-[10px]">Layanan Reguler</p>
                            <p class="text-white text-lg font-bold">2 - 4 Hari Kerja</p>
                        </div>
                        <div class="p-4 bg-emerald-500/10 rounded-xl border border-emerald-500/20 flex-1">
                            <p class="font-black italic text-emerald-400 uppercase text-[10px]">Layanan Kilat</p>
                            <p class="text-white text-lg font-bold">1 - 2 Hari Kerja</p>
                        </div>
                    </div>
                </div>
            </details>

            <details class="group glass-card rounded-[2.5rem] overflow-hidden">
                <summary class="flex items-center justify-between p-8 cursor-pointer list-none">
                    <div class="flex items-center gap-8">
                        <div class="w-14 h-14 bg-red-500/20 rounded-2xl flex items-center justify-center text-red-400 shadow-inner group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black italic uppercase tracking-tight text-white">Kebijakan Retur</h3>
                            <p class="text-[10px] text-red-500/40 font-bold uppercase tracking-widest mt-1">Syarat pengembalian barang</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full border border-red-500/20 flex items-center justify-center group-open:rotate-180 transition-all duration-500">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </summary>
                <div class="px-12 pb-10 pt-4 text-emerald-100/60 text-sm leading-relaxed border-t border-emerald-500/10 bg-red-950/10">
                    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl mb-4">
                        <p class="text-red-300 font-black italic uppercase text-[11px]">⚠️ Wajib Video Unboxing</p>
                        <p class="mt-1">Klaim barang rusak atau kurang tidak akan diproses tanpa bukti video unboxing dari awal membuka paket.</p>
                    </div>
                </div>
            </details>

        </div>
    </main>
</body>
</html>