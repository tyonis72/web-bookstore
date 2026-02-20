<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

$pembeli_id = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$pesanan_id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data pesanan & Nama Toko (Penjual)
$query_pesanan = mysqli_query($conn, "
    SELECT t.*, u.username as nama_toko 
    FROM transaksi t
    JOIN users u ON t.penjual_id = u.id
    WHERE t.id='$pesanan_id' AND t.pembeli_id='$pembeli_id'
");

$pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$pesanan) {
    header("Location: index.php?error=not_found");
    exit;
}

// Ambil detail produk
$detail = mysqli_query($conn, "
    SELECT d.*, p.nama, p.foto 
    FROM transaksi_detail d
    JOIN produk p ON d.produk_id = p.id
    WHERE d.transaksi_id='$pesanan_id'"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Order Detail #<?= $pesanan['id'] ?> | Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #064e3b 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen pb-10">

    <div class="flex">
        <?php include '../../partials/sidebar-pembeli.php'; ?>

        <main class="flex-1 p-6 md:p-12">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter italic">ORDER <span class="text-emerald-400">#<?= $pesanan['id'] ?></span></h1>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-gray-500 font-bold">Detail Transaksi Anda</p>
                </div>
                <a href="index.php" class="px-6 py-2 glass-card rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-white/10 transition-all">
                    ← Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="glass-card rounded-[2.5rem] p-8">
                        <h2 class="text-sm font-black uppercase tracking-widest text-emerald-500 mb-6 flex items-center gap-2">
                            <span>📦</span> Item Pesanan
                        </h2>
                        
                        <div class="space-y-6">
                            <?php 
                            $total_calc = 0;
                            while ($row = mysqli_fetch_assoc($detail)): 
                                $subtotal = $row['harga'] * $row['qty'];
                                $total_calc += $subtotal;
                            ?>
                            <div class="flex items-center gap-6 p-4 rounded-3xl bg-white/5 border border-white/5">
                                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-900 border border-white/10">
                                    <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-emerald-50"><?= $row['nama'] ?></h3>
                                    <p class="text-xs text-gray-500 font-bold"><?= $row['qty'] ?> x Rp <?= number_format($row['harga']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-emerald-400">Rp <?= number_format($subtotal) ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <?php if($pesanan['bukti_tranfer']): ?>
                    <div class="glass-card rounded-[2.5rem] p-8">
                        <h2 class="text-sm font-black uppercase tracking-widest text-amber-500 mb-6">Bukti Pembayaran</h2>
                        <div class="max-w-xs rounded-2xl overflow-hidden border border-white/10">
                            <img src="<?= BASE_URL ?>/public/uploads/bukti_bayar/<?= $pesanan['bukti_tranfer'] ?>" class="w-full hover:scale-110 transition duration-500 cursor-zoom-in">
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-6">
                    <div class="glass-card rounded-[2.5rem] p-8 border-l-4 border-emerald-500">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Status Pesanan</p>
                        <div class="inline-block px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                            <?= $pesanan['status'] == 'pending' ? 'bg-amber-500/20 text-amber-400' : 'bg-emerald-500/20 text-emerald-400' ?>">
                            <?= $pesanan['status'] ?>
                        </div>
                        
                        <hr class="my-6 border-white/5">
                        
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Nomor Resi</p>
                        <p class="font-mono text-sm tracking-widest text-emerald-100">
                            <?= $pesanan['resi'] ? $pesanan['resi'] : '📦 Menunggu Pengiriman' ?>
                        </p>
                    </div>

                    <div class="glass-card rounded-[2.5rem] p-8">
                        <h2 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Informasi Pengiriman</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-emerald-500/50 uppercase">Toko Penjual</p>
                                <p class="text-sm font-bold">🏪 <?= $pesanan['nama_toko'] ?></p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-emerald-500/50 uppercase">Alamat Tujuan</p>
                                <p class="text-xs text-gray-400 leading-relaxed italic">
                                    "<?= $pesanan['alamat_pengiriman'] ?>"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-[2.5rem] p-8 bg-gradient-to-br from-emerald-500/10 to-transparent">
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500 mb-2">Total Pembayaran</p>
                        <p class="text-3xl font-black text-white italic">Rp <?= number_format($pesanan['total']) ?></p>
                        <p class="text-[9px] text-gray-500 mt-2 font-bold uppercase tracking-tighter italic">*Harga sudah termasuk PPN</p>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>