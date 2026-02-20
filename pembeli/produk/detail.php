<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

// Pastikan ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);

// Query - sesuaikan p.judul atau p.nama sesuai database Anda
$query = mysqli_query($conn, "
    SELECT p.*, u.username as nama_toko 
    FROM produk p 
    JOIN users u ON p.penjual_id = u.id 
    WHERE p.id = '$id_produk'
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Produk tidak ditemukan atau query error: " . mysqli_error($conn));
}

$produk = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $produk['nama'] ?> | Detail Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top right, #064e3b 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
        }
        .glass-container {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .btn-gradient {
            background: linear-gradient(to right, #10b981, #059669);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        }
    </style>
</head>
<body class="min-h-screen pb-20">

    <div class="flex">
        <?php include '../../partials/sidebar-pembeli.php'; ?>

        <main class="flex-1 p-6 md:p-12">
            <nav class="mb-8 text-xs font-bold uppercase tracking-widest text-emerald-500/60">
                <a href="../dashboard/index.php" class="hover:text-emerald-400">Katalog</a> 
                <span class="mx-2">/</span> 
                <span class="text-white"><?= $produk['nama'] ?></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-amber-500 rounded-[3rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                    <div class="relative glass-container rounded-[3rem] overflow-hidden aspect-square flex items-center justify-center p-8">
                        <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $produk['foto'] ?>" 
                             class="max-h-full object-contain drop-shadow-2xl transform group-hover:scale-105 transition duration-700" 
                             alt="<?= $produk['nama'] ?>">
                    </div>
                </div>

                <div class="flex flex-col">
                    <div class="mb-6">
                        <span class="px-4 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-widest rounded-full">
                            Stok: <?= $produk['stok'] ?> Unit
                        </span>
                        <h1 class="text-4xl md:text-5xl font-black mt-4 leading-tight tracking-tighter">
                            <?= $produk['nama'] ?>
                        </h1>
                    </div>

                    <div class="mb-8">
                        <p class="text-sm text-gray-400 uppercase font-bold tracking-widest mb-1">Harga Terbaik</p>
                        <p class="text-4xl font-black text-emerald-400">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    </div>

                    <div class="glass-container p-6 rounded-3xl mb-8 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gray-700 to-gray-900 overflow-hidden border border-white/10">
                            <?php if($produk['foto_profil']): ?>
                                <img src="<?= BASE_URL ?>/public/uploads/profil/<?= $produk['foto_profil'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-xl">🏪</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-tighter">Penjual</p>
                            <p class="font-bold text-white"><?= $produk['nama_toko'] ?></p>
                        </div>
                    </div>

                    <div class="mb-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-500 mb-4">Deskripsi Produk</h3>
                        <div class="text-gray-300 leading-relaxed text-sm space-y-4">
                            <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                        </div>
                    </div>

                    <div class="mt-auto flex flex-col sm:flex-row gap-4">
                        <form action="keranjang_tambah.php" method="POST" class="flex-1 flex gap-4">
                            <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                            <div class="w-24">
                                <input type="number" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>" 
                                       class="w-full h-full bg-white/5 border border-white/10 rounded-2xl text-center font-bold focus:border-emerald-500 outline-none">
                            </div>
                            <button type="submit" class="flex-1 btn-gradient py-4 rounded-2xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Masukkan Keranjang
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>