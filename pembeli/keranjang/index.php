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
    <title>Keranjang Belanja - Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #064e3b 0%, #022c22 50%, #78350f 100%);
            background-attachment: fixed;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .qty-btn {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .qty-btn:hover {
            background: rgba(16, 185, 129, 0.3);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>

<body class="min-h-screen">

    <div class="flex min-h-screen">

        <?php include '../../partials/sidebar-pembeli.php'; ?>

        <main class="flex-1 p-8">
            <header class="mb-10">
                <h1 class="text-3xl font-black tracking-tight">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
                        Keranjang Belanja
                    </span>
                </h1>
                <p class="text-emerald-100/50 mt-1 italic text-sm">Tinjau item pilihan Anda sebelum melakukan pembayaran.</p>
            </header>

            <?php if (mysqli_num_rows($keranjang) === 0): ?>
                <div class="glass-card p-12 rounded-[2rem] text-center">
                    <div class="text-6xl mb-4 text-emerald-100/10 italic font-black uppercase tracking-widest">Kosong</div>
                    <p class="text-emerald-100/50">Keranjang kamu masih kosong.</p>
                    <a href="../produk/index.php" class="mt-6 inline-block text-emerald-400 hover:text-emerald-300 font-bold tracking-widest text-xs uppercase underline">Mulai Belanja →</a>
                </div>
            <?php else: ?>

                <div class="glass-card rounded-[2rem] overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 border-b border-white/10 uppercase text-xs tracking-widest text-emerald-400 font-bold">
                            <tr>
                                <th class="p-5">Produk</th>
                                <th class="p-5">Harga</th>
                                <th class="p-5 text-center">Qty</th>
                                <th class="p-5">Subtotal</th>
                                <th class="p-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php while ($row = mysqli_fetch_assoc($keranjang)):
                                $subtotal = $row['harga'] * $row['qty'];
                                $total += $subtotal;
                            ?>
                                <tr class="hover:bg-white/5 transition-all duration-300">
                                    <td class="p-5 flex items-center gap-4">
                                        <?php if ($row['foto']): ?>
                                            <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>"
                                                class="w-20 h-20 object-cover rounded-2xl border border-white/10">
                                        <?php else: ?>
                                            <div class="w-20 h-20 bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-100/20 text-xs">No Pic</div>
                                        <?php endif ?>
                                        <div>
                                            <p class="font-bold text-emerald-50"><?= htmlspecialchars($row['nama']) ?></p>
                                            <p class="text-xs text-emerald-100/40 mt-1">Penjual: <?= htmlspecialchars($row['penjual']) ?></p>
                                        </div>
                                    </td>
                                    <td class="p-5 text-emerald-100/70 font-medium">Rp <?= number_format($row['harga']) ?></td>
                                    <td class="p-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="updateQty(<?= $row['keranjang_id'] ?>, -1, <?= $row['stok'] ?>)"
                                                class="qty-btn w-8 h-8 rounded-lg flex items-center justify-center text-emerald-300">-</button>

                                            <span id="qty-<?= $row['keranjang_id'] ?>" class="font-black text-emerald-50 min-w-[20px] text-center"><?= $row['qty'] ?></span>

                                            <button onclick="updateQty(<?= $row['keranjang_id'] ?>, 1, <?= $row['stok'] ?>)"
                                                class="qty-btn w-8 h-8 rounded-lg flex items-center justify-center text-emerald-300">+</button>
                                        </div>
                                    </td>
                                    <td class="p-5 font-black text-amber-400 text-lg">Rp <?= number_format($subtotal) ?></td>
                                    <td class="p-5 text-center">
                                        <a href="hapus.php?id_item=<?= $row['keranjang_id'] ?>"
                                            onclick="return confirm('Hapus dari keranjang?')"
                                            class="text-red-400/60 hover:text-red-400 transition-colors">
                                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex flex-col md:flex-row justify-between items-center glass-card p-8 rounded-[2rem] gap-6">
                    <div>
                        <p class="text-emerald-100/40 text-xs uppercase tracking-widest font-bold mb-1">Total Estimasi Pembayaran</p>
                        <p class="text-4xl font-black text-amber-400 leading-none">
                            Rp <?= number_format($total) ?>
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <a href="../produk/index.php"
                            class="px-8 py-4 bg-white/5 border border-white/10 text-emerald-100 rounded-2xl font-bold hover:bg-white/10 transition-all active:scale-95">
                            Lanjut Belanja
                        </a>
                        <a href="../checkout/index.php"
                            class="px-10 py-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white rounded-2xl font-black shadow-lg shadow-emerald-900/40 transition-all active:scale-95 flex items-center gap-2">
                            CHECKOUT
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </main>
    </div>

    <script>
        // Logic JavaScript (sama seperti sebelumnya)
        function updateQty(idItem, delta, stokMax = 999) {
            const qtyElement = document.getElementById(`qty-${idItem}`);
            if (!qtyElement) return;

            let currentQty = parseInt(qtyElement.innerText.trim());
            if (isNaN(currentQty)) currentQty = 1;
            let newQty = currentQty + delta;

            if (newQty < 1) {
                if (confirm('Hapus produk ini dari keranjang?')) {
                    window.location.href = `hapus.php?id_item=${idItem}`;
                }
                return;
            }

            if (delta > 0 && newQty > stokMax) {
                alert('Maaf, stok tidak mencukupi. Maksimal pembelian adalah ' + stokMax);
                return;
            }

            fetch('update-qty.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id_item=${encodeURIComponent(idItem)}&qty=${encodeURIComponent(newQty)}`
                })
                .then(async response => {
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(errorText || 'Server Error');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                    }
                })
                .catch(error => {
                    console.error('Debug Error:', error);
                    alert('Kesalahan Koneksi ke Server.');
                });
        }
    </script>
</body>

</html>