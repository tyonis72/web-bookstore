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
$detail = mysqli_query(
    $conn,
    "
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
    <title>Detail Order #<?= $pesanan['id'] ?> | Glass Amber Edition</title>
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

        .btn-refund {
            background: linear-gradient(to right, #ef4444, #991b1b);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-refund:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>

<body class="antialiased pb-20">

    <div class="liquid-bg">
        <div class="blob" style="top: 5%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 lg:p-12 md:ml-64">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-16 gap-6">
                <header>
                    <h1 class="text-5xl font-black italic uppercase tracking-tighter leading-none">
                        Order <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">#<?= $pesanan['id'] ?></span>
                    </h1>
                    <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                    <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Detail Transaksi Digital Terverifikasi</p>
                </header>
                <a href="index.php" class="px-8 py-3 glass-card rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition-all border border-white/10">
                    ← Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <div class="lg:col-span-2 space-y-10">

                    <div class="glass-card rounded-[3.5rem] p-10 overflow-hidden relative group">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl transition-all"></div>

                        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-500 mb-10 flex items-center gap-4">
                            Purchased Items <span class="h-[1px] w-12 bg-emerald-500/30"></span>
                        </h2>

                        <div class="space-y-8">
                            <?php
                            mysqli_data_seek($detail, 0);
                            while ($row = mysqli_fetch_assoc($detail)):
                                $subtotal = $row['harga'] * $row['qty'];
                            ?>
                                <div class="flex items-center gap-8 p-6 rounded-[2rem] bg-white/5 border border-white/5 transition-all hover:bg-white/10 group/item">
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-black/40 border border-white/10 flex-shrink-0">
                                        <img src="<?= BASE_URL ?>/public/uploads/produk/<?= $row['foto'] ?>" class="w-full h-full object-cover group-hover/item:scale-110 transition duration-700">
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-black italic uppercase text-lg text-white tracking-tight mb-1"><?= $row['nama'] ?></h3>
                                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">
                                            <?= $row['qty'] ?> Unit <span class="mx-2 text-white/10">|</span> Rp <?= number_format($row['harga']) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-black text-amber-400 italic tracking-tighter">Rp <?= number_format($subtotal) ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <?php if ($pesanan['bukti_tranfer']): ?>
                        <div class="glass-card rounded-[3.5rem] p-10">
                            <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500 mb-8">Transaction Voucher</h2>
                            <div class="max-w-xs rounded-3xl overflow-hidden border border-white/10 bg-black/20 p-2 shadow-2xl">
                                <img src="<?= BASE_URL ?>/public/uploads/bukti_bayar/<?= $pesanan['bukti_tranfer'] ?>" class="w-full rounded-2xl hover:scale-105 transition duration-500 cursor-zoom-in">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-10">

                    <div class="glass-card rounded-[3.5rem] p-10 border-l-8 border-l-amber-500">
                        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-gray-600 mb-4">Transaction Status</p>

                        <?php $status_cek = strtolower($pesanan['status']); ?>
                        <div class="inline-block px-6 py-2 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] 
                            <?= ($status_cek == 'pending') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : (($status_cek == 'dikirim' || $status_cek == 'approve') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white/5 text-gray-400 border border-white/10') ?>">
                            <?= $pesanan['status'] ?>
                        </div>

                        <?php if (in_array($status_cek, ['dikirim', 'approve', 'approved'])): ?>
                            <div class="mt-10">
                                <a href="refund.php?id=<?= $pesanan['id'] ?>" class="btn-refund block w-full text-center py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] text-white">
                                    Request Refund
                                </a>
                                <p class="text-[8px] text-gray-600 mt-4 text-center font-bold uppercase italic leading-relaxed tracking-widest">
                                    Buku tidak sesuai? <br> Ajukan klaim sekarang.
                                </p>
                            </div>
                        <?php elseif ($status_cek == 'pending_refund'): ?>
                            <div class="mt-8 p-6 rounded-[2rem] bg-amber-500/5 border border-amber-500/10 text-center">
                                <div class="text-amber-500 mb-2">⏳</div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-amber-400">Refund in Process</p>
                                <p class="text-[8px] text-gray-600 mt-2 uppercase font-bold tracking-tighter italic">Menunggu konfirmasi penjual</p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-10 pt-10 border-t border-white/5">
                            <p class="text-[9px] font-black uppercase tracking-[0.4em] text-gray-600 mb-3">Waybill / Resi</p>
                            <p class="font-mono text-xs tracking-[0.2em] text-emerald-400 bg-emerald-500/5 p-3 rounded-xl border border-emerald-500/10 text-center uppercase">
                                <?= $pesanan['resi'] ? $pesanan['resi'] : '📦 Processing Ship' ?>
                            </p>
                        </div>
                    </div>

                    <div class="glass-card rounded-[3.5rem] p-10">
                        <h2 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-600 mb-8">Shipping Info</h2>
                        <div class="space-y-8">
                            <div>
                                <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-2">Merchant</p>
                                <p class="text-lg font-black italic tracking-tighter">🏪 <?= htmlspecialchars($pesanan['nama_toko']) ?></p>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest mb-2">Destination</p>
                                <p class="text-xs text-gray-400 leading-relaxed italic font-medium">
                                    "<?= htmlspecialchars($pesanan['alamat_pengiriman']) ?>"
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card rounded-[3.5rem] p-10 bg-gradient-to-br from-amber-500/10 to-transparent border-none">
                        <p class="text-[9px] font-black uppercase tracking-[0.4em] text-amber-500 mb-3">Final Investment</p>
                        <p class="text-4xl font-black text-white italic tracking-tighter">Rp <?= number_format($pesanan['total']) ?></p>
                        <p class="text-[8px] text-gray-700 mt-4 font-black uppercase tracking-widest italic leading-none">*Price includes VAT & Service Tax</p>
                    </div>
                </div>

            </div>

            <footer class="mt-20 text-center">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">BookStore Transaction Authenticity Verified</p>
            </footer>
        </main>
    </div>

</body>

</html>