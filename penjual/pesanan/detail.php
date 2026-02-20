<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual');

$penjual_id = $_SESSION['user']['id'];
$id = (int) ($_GET['id'] ?? 0);

/* =========================
   AMBIL DATA PESANAN
========================= */
$pesanan = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT 
        t.*,
        u.username AS nama_pembeli,
        u.alamat AS alamat_pembeli
     FROM transaksi t
     JOIN users u ON t.pembeli_id = u.id
     WHERE t.id='$id'
     AND t.penjual_id='$penjual_id'
     LIMIT 1"
));

if (!$pesanan) {
    die('Pesanan tidak ditemukan');
}

/* =========================
   DETAIL PRODUK
========================= */
$detail = mysqli_query($conn,
    "SELECT 
        d.*,
        p.nama,
        p.foto
     FROM transaksi_detail d
     JOIN produk p ON d.produk_id = p.id
     WHERE d.transaksi_id='$id'"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Detail #<?= $id ?> - Glass Edition</title>
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
<body class="text-gray-100 antialiased min-h-screen p-4 md:p-8">

<div class="liquid-bg">
    <div class="blob" style="top: 10%; right: 15%; background: #f59e0b;"></div>
    <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>
</div>

<div class="max-w-4xl mx-auto">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="index.php" 
               class="p-3 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:border-amber-500/50 transition-all group">
                <svg class="w-6 h-6 text-amber-500 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight">
                    Detail Pesanan
                </h1>
                <p class="text-gray-500 font-mono text-sm uppercase tracking-widest">ID Transaksi #<?= $id ?></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
             <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-tighter border
                <?= $pesanan['status']=='menunggu' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : '' ?>
                <?= $pesanan['status']=='approve' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : '' ?>
                <?= $pesanan['status']=='tolak' ? 'bg-red-500/10 text-red-400 border-red-500/20' : '' ?>">
                Status: <?= $pesanan['status'] ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
                
                <h2 class="text-amber-500 font-black uppercase tracking-widest text-[10px] mb-4">Informasi Pengiriman</h2>
                
                <div class="space-y-4 relative z-10">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold">Nama Penerima</p>
                        <p class="text-white font-semibold"><?= htmlspecialchars($pesanan['nama_pembeli']) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold">Alamat Lengkap</p>
                        <p class="text-gray-300 text-sm leading-relaxed italic">"<?= nl2br(htmlspecialchars($pesanan['alamat_pembeli'])) ?>"</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold">Waktu Transaksi</p>
                        <p class="text-gray-300 text-sm"><?= date('d F Y, H:i', strtotime($pesanan['created_at'])) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase font-bold">Nomor Resi</p>
                        <p class="font-mono text-emerald-400"><?= $pesanan['resi'] ? htmlspecialchars($pesanan['resi']) : 'Belum Tersedia' ?></p>
                    </div>
                </div>
            </div>

            <?php if ($pesanan['status'] === 'menunggu'): ?>
            <div class="bg-white/5 border border-white/10 p-6 rounded-[2rem] space-y-3">
                <h2 class="text-white font-bold text-sm mb-2 text-center">Konfirmasi Pesanan?</h2>
                <a href="<?= BASE_URL ?>/controllers/PesananController.php?approve=<?= $id ?>"
                   class="flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-900/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Terima Pesanan
                </a>
                <a href="<?= BASE_URL ?>/controllers/PesananController.php?reject=<?= $id ?>"
                   onclick="return confirm('Yakin ingin menolak pesanan ini?')"
                   class="flex items-center justify-center gap-2 w-full py-3 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 rounded-xl font-bold transition-all">
                    Tolak Pesanan
                </a>
            </div>
            <?php endif ?>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-white/5 flex justify-between items-center">
                    <h2 class="font-bold text-white tracking-tight">Item yang Dibeli</h2>
                    <span class="text-xs text-gray-500 font-mono italic">Total Item: <?= mysqli_num_rows($detail) ?></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 text-[10px] uppercase tracking-[0.2em] text-gray-400">
                            <tr>
                                <th class="p-5">Produk</th>
                                <th class="p-5 text-center">Qty</th>
                                <th class="p-5 text-right">Harga</th>
                                <th class="p-5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <?php $total = 0; ?>
                            <?php while ($d = mysqli_fetch_assoc($detail)): 
                                $subtotal = $d['qty'] * $d['harga'];
                                $total += $subtotal;
                            ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5">
                                    <span class="text-white font-medium group-hover:text-amber-400 transition-colors">
                                        <?= htmlspecialchars($d['nama']) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <span class="px-2 py-1 bg-white/5 rounded-lg border border-white/10 text-gray-300 font-mono">
                                        <?= $d['qty'] ?>
                                    </span>
                                </td>
                                <td class="p-5 text-right text-gray-400">Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                                <td class="p-5 text-right font-bold text-white italic">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            </tr>
                            <?php endwhile ?>
                        </tbody>
                        <tfoot class="bg-white/5">
                            <tr class="font-black text-lg">
                                <td colspan="3" class="p-6 text-right text-amber-500 uppercase tracking-widest text-xs">Total Pembayaran</td>
                                <td class="p-6 text-right text-amber-400 font-mono italic">
                                    Rp <?= number_format($total, 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>