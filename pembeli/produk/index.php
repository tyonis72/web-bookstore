<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

// Ambil data produk beserta info penjual_id untuk fitur chat
$produk = mysqli_query(
    $conn,
    "SELECT p.id, p.nama, p.harga, p.stok, p.foto, p.penjual_id, u.username AS penjual
     FROM produk p
     JOIN users u ON p.penjual_id = u.id
     WHERE p.stok > 0
     ORDER BY p.id DESC"
);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk | Glass Amber Edition</title>

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
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(245, 158, 11, 0.4);
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), 0 0 20px rgba(245, 158, 11, 0.1);
        }

        .img-container {
            position: relative;
            overflow: hidden;
            border-radius: 2rem;
            background: rgba(0, 0, 0, 0.2);
        }

        /* Shine Effect */
        .img-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: skewX(-25deg);
            transition: 0.8s;
        }

        .glass-card:hover .img-container::after {
            left: 150%;
        }

        .btn-amber {
            background: linear-gradient(to right, #f59e0b, #d97706);
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
        }
    </style>
</head>

<body class="min-h-screen antialiased">

    <div class="liquid-bg">
        <div class="blob" style="top: 10%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 lg:p-12 md:ml-64">

            <header class="mb-16">
                <h1 class="text-5xl font-black italic uppercase tracking-tighter leading-none">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">
                        Book Catalog
                    </span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Premium Curated Literature Selection</p>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-10">
                <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                    <div class="glass-card p-6 rounded-[3rem] flex flex-col group relative overflow-hidden">

                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/15 transition-all"></div>

                        <div class="img-container shadow-2xl aspect-[3/4] flex items-center justify-center">
                            <?php if ($row['foto']): ?>
                                <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>"
                                    class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="italic text-gray-700 text-[10px] uppercase tracking-widest">No Cover</div>
                            <?php endif ?>
                        </div>

                        <div class="mt-8 flex flex-col flex-1 relative z-10">
                            <h3 class="font-black italic uppercase text-xl text-white group-hover:text-amber-400 transition-colors leading-none tracking-tighter mb-4">
                                <?= htmlspecialchars($row['nama']) ?>
                            </h3>

                            <div class="flex items-center justify-between py-3 border-y border-white/5 mb-6">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black uppercase tracking-widest text-gray-500">Merchant</span>
                                    <span class="text-[10px] font-bold text-emerald-400 uppercase">@<?= htmlspecialchars($row['penjual']) ?></span>
                                </div>

                                <a href="<?= BASE_URL ?>/chat/mulai_chat.php?penjual_id=<?= $row['penjual_id'] ?>"
                                    class="p-2 bg-white/5 hover:bg-amber-500/20 text-white hover:text-amber-400 rounded-xl border border-white/10 transition-all group/chat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </a>
                            </div>

                            <div class="mb-8">
                                <p class="text-[9px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1">Current Price</p>
                                <span class="text-3xl font-black italic text-amber-400 tracking-tighter">
                                    Rp <?= number_format($row['harga']) ?>
                                </span>
                            </div>

                            <div class="mt-auto flex flex-col gap-4">
                                <a href="detail.php?id=<?= $row['id'] ?>"
                                    class="block text-center bg-white/5 hover:bg-white/10 text-white border border-white/10 font-black italic uppercase text-[10px] tracking-[0.2em] py-4 rounded-2xl transition-all">
                                    View Collection
                                </a>

                                <form action="keranjang_tambah.php" method="POST">
                                    <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
                                    <button type="submit"
                                        class="w-full btn-amber text-white font-black italic uppercase text-[10px] tracking-[0.2em] py-4 rounded-2xl transition-all flex items-center justify-center gap-3 active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Add To Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile ?>
            </div>
        </main>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-card {
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) backwards;
        }

        <?php for ($i = 1; $i <= 20; $i++): ?>.glass-card:nth-child(<?= $i ?>) {
            animation-delay: <?= $i * 0.1 ?>s;
        }

        <?php endfor; ?>
    </style>
</body>

</html>