<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

check_role('penjual');

$penjual_id = $_SESSION['user']['id'];

/* ======================
   TOTAL PRODUK
====================== */
$total_produk = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT id FROM produk 
         WHERE penjual_id='$penjual_id'"
    )
);

/* ======================
   TOTAL STOK
===================== */
$data_stok = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(stok) AS total_stok 
         FROM produk 
         WHERE penjual_id='$penjual_id'"
    )
);
$total_stok = $data_stok['total_stok'] ?? 0;

/* ======================
   PRODUK PENJUAL
====================== */
$produk = mysqli_query(
    $conn,
    "SELECT id, nama, harga, stok, foto
     FROM produk
     WHERE penjual_id='$penjual_id'
     ORDER BY id DESC
     LIMIT 8"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Penjual - Glass Amber Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 30px); }
            100% { transform: translate(0, 0); }
        }
        .liquid-bg {
            position: fixed; z-index: -1; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
        }
        .blob {
            position: absolute; width: 600px; height: 600px; filter: blur(90px);
            border-radius: 50%; opacity: 0.12; animation: float 18s infinite alternate;
        }
    </style>
</head>
<body class="text-gray-100 antialiased overflow-x-hidden">

<div class="liquid-bg">
    <div class="blob" style="top: 15%; right: 15%; background: #f59e0b;"></div> <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div> </div>

<div class="flex min-h-screen">

    <aside class="w-64 border-r border-white/10 bg-white/5 backdrop-blur-2xl">
        <?php include '../partials/sidebar-penjual.php'; ?>
    </aside>

    <main class="flex-1 p-8">

        <div class="mb-10">
            <h1 class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight">
                Dashboard Penjual
            </h1>
            <p class="text-gray-500 mt-2 font-medium">Selamat datang kembali! Berikut adalah ringkasan performa toko Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">

            <div class="group relative overflow-hidden bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl transition-all hover:bg-white/10 hover:border-amber-500/30">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>
                <div class="relative z-10">
                    <p class="text-amber-500 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Koleksi Buku</p>
                    <h2 class="text-5xl font-black text-white italic"><?= $total_produk ?></h2>
                    <p class="text-gray-500 text-xs mt-4">Jumlah produk yang aktif di katalog</p>
                </div>
            </div>

            <div class="group relative overflow-hidden bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl transition-all hover:bg-white/10 hover:border-emerald-500/30">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
                <div class="relative z-10">
                    <p class="text-emerald-500 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Inventaris Total</p>
                    <h2 class="text-5xl font-black text-white italic"><?= number_format($total_stok) ?></h2>
                    <p class="text-gray-500 text-xs mt-4">Total unit barang yang tersedia</p>
                </div>
            </div>

        </div>

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Buku Terbaru</h2>
                <div class="h-1 w-12 bg-amber-500 rounded-full mt-1"></div>
            </div>
            <a href="<?= BASE_URL ?>/penjual/produk.php"
               class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-amber-400 hover:bg-amber-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest backdrop-blur-md">
                Kelola Semua
            </a>
        </div>

        <?php if (mysqli_num_rows($produk) > 0): ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                <div class="group bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-4 transition-all duration-500 hover:-translate-y-3 hover:border-amber-500/40 hover:shadow-[0_30px_60px_-15px_rgba(245,158,11,0.3)]">

                    <div class="relative overflow-hidden rounded-[1.5rem] mb-5 aspect-[3/4]">
                        <?php if ($row['foto']): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/produk/<?= htmlspecialchars($row['foto']) ?>"
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <?php else: ?>
                            <div class="w-full h-full bg-white/5 flex items-center justify-center text-gray-600 italic text-xs">
                                No Cover Art
                            </div>
                        <?php endif ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#020617] via-transparent to-transparent opacity-60"></div>
                        
                        <div class="absolute bottom-3 left-3 right-3">
                             <p class="text-amber-400 font-mono font-black text-sm">
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </p>
                        </div>
                    </div>

                    <h3 class="font-bold text-base truncate text-white group-hover:text-amber-400 transition-colors">
                        <?= htmlspecialchars($row['nama']) ?>
                    </h3>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                        <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Persediaan</span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black <?= $row['stok'] > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' ?>">
                            <?= $row['stok'] ?> Unit
                        </span>
                    </div>

                </div>
            <?php endwhile ?>
        </div>
        <?php else: ?>
            <div class="bg-white/5 backdrop-blur-md border border-white/10 p-20 rounded-[3rem] text-center">
                <div class="w-20 h-20 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-10 w-10 text-amber-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Belum Ada Katalog</h3>
                <p class="text-gray-500 max-w-xs mx-auto text-sm">Mulailah menambahkan koleksi buku Anda untuk mulai berjualan hari ini.</p>
                <a href="<?= BASE_URL ?>/penjual/produk.php" class="inline-block mt-8 px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl font-bold transition-all shadow-lg shadow-amber-900/40">
                    Tambah Produk Pertama
                </a>
            </div>
        <?php endif ?>

    </main>
</div>

</body>
</html>