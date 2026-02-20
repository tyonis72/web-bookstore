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
    <title>Katalog Produk | BookStore Emerald</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: radial-gradient(circle at top left, #064e3b 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(16, 185, 129, 0.4);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(16, 185, 129, 0.1);
        }

        .img-container {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
        }

        .img-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: skewX(-25deg);
            transition: 0.7s;
        }

        .glass-card:hover .img-container::after {
            left: 150%;
        }
    </style>
</head>

<body class="min-h-screen flex">

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../../partials/sidebar-pembeli.php'; ?>
    </aside>

    <main class="flex-1 p-8 lg:p-12 ml-64 transition-all duration-500">

        <header class="mb-12 animate-in fade-in duration-700">
            <h1 class="text-4xl font-black italic uppercase tracking-tighter">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-200">
                    Koleksi Produk
                </span>
            </h1>
            <p class="text-emerald-100/40 mt-2 font-medium italic text-sm tracking-wide">
                Eksplorasi literatur premium dalam genggaman Anda.
            </p>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                <div class="glass-card p-5 rounded-[2.5rem] flex flex-col group">

                    <div class="img-container shadow-2xl">
                        <?php if ($row['foto']): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>"
                                class="h-60 w-full object-cover rounded-3xl transition-all duration-700 group-hover:scale-110">
                        <?php else: ?>
                            <div
                                class="h-60 w-full bg-emerald-950/40 rounded-3xl flex items-center justify-center italic text-emerald-100/10 uppercase tracking-widest text-xs">
                                No Cover Image
                            </div>
                        <?php endif ?>
                    </div>

                    <div class="mt-6 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3
                                class="font-black italic uppercase text-lg text-white group-hover:text-emerald-400 transition-colors leading-tight">
                                <?= htmlspecialchars($row['nama']) ?>
                            </h3>
                        </div>

                        <div class="flex items-center justify-between py-2 border-y border-white/5 my-3">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-emerald-100/30">
                                Seller: <?= htmlspecialchars($row['penjual']) ?>
                            </span>

                            <a href="<?= BASE_URL ?>/chat/mulai_chat.php?penjual_id=<?= $row['penjual_id'] ?>"
                                class="flex items-center gap-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 px-3 py-1.5 rounded-full transition-all group/chat">
                                <svg class="w-3.5 h-3.5 group-hover/chat:rotate-12 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                <span class="text-[9px] font-black uppercase">Chat</span>
                            </a>
                        </div>

                        <div class="mb-6">
                            <p class="text-[10px] text-emerald-500/60 font-black uppercase tracking-widest mb-1">Price</p>
                            <span class="text-2xl font-black italic text-emerald-400">
                                Rp <?= number_format($row['harga']) ?>
                            </span>
                        </div>

                        <div class="mt-auto flex flex-col gap-3">
                            <a href="detail.php?id=<?= $row['id'] ?>"
                                class="block text-center bg-white/5 hover:bg-white/10 text-white border border-white/10 font-black italic uppercase text-[10px] tracking-widest py-4 rounded-2xl transition-all active:scale-95 shadow-lg">
                                View Details
                            </a>

                            <form action="keranjang_tambah.php" method="POST">
                                <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black italic uppercase text-[10px] tracking-widest py-4 rounded-2xl shadow-xl shadow-emerald-900/40 transition-all flex items-center justify-center gap-3 group/btn active:scale-95">
                                    <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
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

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fade-in 0.8s ease-out forwards;
        }
    </style>
</body>

</html>