<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

$pembeli_id = $_SESSION['user']['id'];

// Ambil isi keranjang pembeli
$keranjang = mysqli_query(
    $conn,
    "SELECT k.id AS keranjang_id, k.qty,
            p.id AS produk_id, p.nama, p.harga, p.stok, p.foto,
            u.username AS penjual
     FROM keranjang k
     JOIN produk p ON k.produk_id = p.id
     JOIN users u ON p.penjual_id = u.id
     WHERE k.pembeli_id = '$pembeli_id'"
);

$total = 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja | Glass Amber Edition</title>
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .qty-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .qty-btn:hover {
            background: rgba(245, 158, 11, 0.2);
            border-color: rgba(245, 158, 11, 0.4);
            color: #f59e0b;
            transform: scale(1.1);
        }

        .btn-checkout {
            background: linear-gradient(to right, #f59e0b, #d97706);
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
        }
    </style>
</head>

<body class="antialiased pb-20">

    <div class="liquid-bg">
        <div class="blob" style="top: 10%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">

        <aside class="w-64 fixed inset-y-0 left-0 z-50 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 lg:p-12 md:ml-64">
            <header class="mb-16">
                <h1 class="text-5xl font-black italic uppercase tracking-tighter leading-none">
                    Shopping <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">Cart</span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Tinjau Item Pilihan Sebelum Checkout</p>
            </header>

            <?php if (mysqli_num_rows($keranjang) === 0): ?>
                <div class="glass-card p-20 rounded-[3.5rem] text-center border-dashed border-2 border-white/5">
                    <div class="text-8xl mb-6 opacity-10 italic font-black uppercase tracking-widest select-none">Void</div>
                    <p class="text-gray-500 font-bold uppercase tracking-widest text-xs">Keranjang Anda Masih Kosong</p>
                    <a href="../produk/index.php" class="mt-10 inline-flex items-center gap-3 px-8 py-4 bg-white/5 border border-white/10 rounded-2xl text-amber-400 font-black tracking-widest text-[10px] uppercase hover:bg-white/10 transition-all">
                        Mulai Jelajah Produk →
                    </a>
                </div>
            <?php else: ?>

                <div class="glass-card rounded-[3rem] overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th class="p-8 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Product Details</th>
                                <th class="p-8 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Unit Price</th>
                                <th class="p-8 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em] text-center">Quantity</th>
                                <th class="p-8 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Subtotal</th>
                                <th class="p-8 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em] text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php while ($row = mysqli_fetch_assoc($keranjang)):
                                $subtotal = $row['harga'] * $row['qty'];
                                $total += $subtotal;
                            ?>
                                <tr class="hover:bg-white/5 transition-all duration-300 group">
                                    <td class="p-8 flex items-center gap-6">
                                        <div class="relative w-24 h-24 flex-shrink-0">
                                            <div class="absolute -inset-2 bg-amber-500/10 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                                            <?php if ($row['foto']): ?>
                                                <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>"
                                                    class="relative w-full h-full object-cover rounded-2xl border border-white/10 shadow-2xl">
                                            <?php else: ?>
                                                <div class="relative w-full h-full bg-black/40 rounded-2xl flex items-center justify-center text-[10px] uppercase font-black text-gray-700 italic border border-white/10">No Pic</div>
                                            <?php endif ?>
                                        </div>
                                        <div>
                                            <p class="font-black italic uppercase text-lg text-white tracking-tighter"><?= htmlspecialchars($row['nama']) ?></p>
                                            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mt-1">Merchant: <?= htmlspecialchars($row['penjual']) ?></p>
                                        </div>
                                    </td>
                                    <td class="p-8">
                                        <span class="text-xs font-bold text-gray-400">Rp <?= number_format($row['harga']) ?></span>
                                    </td>
                                    <td class="p-8">
                                        <div class="flex items-center justify-center gap-4">
                                            <button onclick="updateQty(<?= $row['keranjang_id'] ?>, -1, <?= $row['stok'] ?>)"
                                                class="qty-btn w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold">-</button>

                                            <span id="qty-<?= $row['keranjang_id'] ?>" class="font-black italic text-xl text-white min-w-[30px] text-center"><?= $row['qty'] ?></span>

                                            <button onclick="updateQty(<?= $row['keranjang_id'] ?>, 1, <?= $row['stok'] ?>)"
                                                class="qty-btn w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold">+</button>
                                        </div>
                                    </td>
                                    <td class="p-8">
                                        <span class="font-black text-amber-400 italic text-xl tracking-tighter">Rp <?= number_format($subtotal) ?></span>
                                    </td>
                                    <td class="p-8 text-center">
                                        <a href="hapus.php?id_item=<?= $row['keranjang_id'] ?>"
                                            onclick="return confirm('Hapus item ini dari koleksi keranjang?')"
                                            class="inline-flex p-3 bg-red-500/5 border border-red-500/10 text-red-500/40 hover:text-red-500 hover:bg-red-500/20 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-12 flex flex-col lg:flex-row justify-between items-center glass-card p-10 rounded-[3.5rem] gap-8 border-l-8 border-l-amber-500">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-black tracking-[0.3em] mb-2 italic">Total Investment Amount</p>
                        <p class="text-5xl font-black text-white italic tracking-tighter">
                            <span class="text-amber-500">Rp</span> <?= number_format($total) ?>
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-5 w-full lg:w-auto">
                        <a href="../produk/index.php"
                            class="px-10 py-5 bg-white/5 border border-white/10 text-white rounded-[1.5rem] font-black uppercase text-[10px] tracking-widest hover:bg-white/10 transition-all text-center">
                            Continue Browsing
                        </a>
                        <a href="../checkout/index.php"
                            class="btn-checkout px-12 py-5 text-white rounded-[1.5rem] font-black uppercase text-[10px] tracking-[0.2em] transition-all active:scale-95 flex items-center justify-center gap-3">
                            Proceed to Checkout
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                </div>

                <footer class="mt-20 text-center">
                    <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">Digital Cart Integrity Secured</p>
                </footer>

            <?php endif; ?>

        </main>
    </div>

    <script>
        function updateQty(idItem, delta, stokMax = 999) {
            const qtyElement = document.getElementById(`qty-${idItem}`);
            if (!qtyElement) return;

            let currentQty = parseInt(qtyElement.innerText.trim());
            let newQty = currentQty + delta;

            if (newQty < 1) {
                if (confirm('Hapus produk ini dari keranjang?')) {
                    window.location.href = `hapus.php?id_item=${idItem}`;
                }
                return;
            }

            if (delta > 0 && newQty > stokMax) {
                alert('Stok limit reached. Maksimal: ' + stokMax);
                return;
            }

            // Tampilkan efek loading sederhana (opsional)
            qtyElement.style.opacity = "0.3";

            fetch('update-qty.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id_item=${encodeURIComponent(idItem)}&qty=${encodeURIComponent(newQty)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Error'));
                        qtyElement.style.opacity = "1";
                    }
                })
                .catch(() => {
                    alert('Server Error');
                    qtyElement.style.opacity = "1";
                });
        }
    </script>
</body>

</html>