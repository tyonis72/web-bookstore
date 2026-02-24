<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

// Pastikan ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}

$id_produk = mysqli_real_escape_string($conn, $_GET['id']);

// Query - Menarik detail produk dan info penjual
$query = mysqli_query($conn, "
    SELECT p.*, u.username as nama_toko 
    FROM produk p 
    JOIN users u ON p.penjual_id = u.id 
    WHERE p.id = '$id_produk'
");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Produk tidak ditemukan.");
}

$produk = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produk['nama']) ?> | Glass Amber Edition</title>
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
        }

        .btn-amber {
            background: linear-gradient(to right, #f59e0b, #d97706);
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
            transition: all 0.3s ease;
        }

        .btn-amber:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(245, 158, 11, 0.6);
        }
    </style>
</head>

<body class="antialiased">

    <div class="liquid-bg">
        <div class="blob" style="top: 15%; right: 15%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">
        <aside class="w-64 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 md:p-12">
            <nav class="mb-12 text-[10px] font-black uppercase tracking-[0.3em] text-gray-500">
                <a href="../dashboard/index.php" class="hover:text-amber-400 transition-colors">Catalog</a>
                <span class="mx-3 text-white/10">/</span>
                <span class="text-amber-500 italic"><?= htmlspecialchars($produk['nama']) ?></span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

                <div class="relative group">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-amber-500/20 to-emerald-500/20 rounded-[4rem] blur-2xl opacity-50 group-hover:opacity-80 transition duration-1000"></div>
                    <div class="relative glass-card rounded-[3.5rem] overflow-hidden aspect-[4/5] flex items-center justify-center p-12">
                        <?php if ($produk['foto']): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $produk['foto'] ?>"
                                class="max-h-full w-full object-contain drop-shadow-[0_35px_35px_rgba(0,0,0,0.6)] transform group-hover:scale-105 transition duration-700"
                                alt="<?= htmlspecialchars($produk['nama']) ?>">
                        <?php else: ?>
                            <div class="text-gray-600 italic text-sm">No Image Available</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-col h-full py-4">
                    <div class="mb-8">
                        <span class="px-5 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-2xl">
                            In Stock: <?= $produk['stok'] ?> Units
                        </span>
                        <h1 class="text-5xl md:text-6xl font-black mt-6 leading-[0.9] tracking-tighter italic uppercase text-white">
                            <?= htmlspecialchars($produk['nama']) ?>
                        </h1>
                    </div>

                    <div class="mb-10">
                        <p class="text-[10px] text-gray-500 uppercase font-black tracking-[0.4em] mb-2">Investment</p>
                        <p class="text-5xl font-black text-amber-400 italic">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                    </div>

                    <div class="glass-card p-6 rounded-[2rem] mb-10 flex items-center gap-5 border-l-4 border-l-emerald-500">
                        <div class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-2xl border border-white/10 shadow-inner">
                            🏪
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest mb-1">Verified Merchant</p>
                            <p class="font-bold text-lg text-white tracking-tight"><?= htmlspecialchars($produk['nama_toko']) ?></p>
                        </div>
                    </div>

                    <div class="mb-12">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-500 mb-6 flex items-center gap-3">
                            Description <span class="h-[1px] w-12 bg-emerald-500/30"></span>
                        </h3>
                        <div class="text-gray-400 leading-relaxed text-sm font-medium italic opacity-80">
                            <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <form action="keranjang_tambah.php" method="POST" class="flex flex-col sm:flex-row gap-5">
                            <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">

                            <div class="relative w-full sm:w-32 group">
                                <input type="number" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>"
                                    class="w-full py-4 bg-white/5 border border-white/10 rounded-2xl text-center font-black text-amber-400 focus:border-amber-500 focus:bg-white/10 outline-none transition-all">
                                <span class="absolute -top-3 left-4 px-2 bg-[#0a1122] text-[8px] font-black uppercase text-gray-500">Qty</span>
                            </div>

                            <button type="submit" class="flex-1 btn-amber py-4 rounded-2xl font-black uppercase tracking-[0.2em] text-[11px] flex items-center justify-center gap-4 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Add to Cart
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>

</html>