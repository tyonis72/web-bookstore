<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');
$pembeli_id = $_SESSION['user']['id'];

// Ambil riwayat transaksi milik pembeli ini
$query = mysqli_query($conn, "
    SELECT t.*, 
    (SELECT COUNT(*) FROM detail_transaksi WHERE transaksi_id = t.id) as total_item
    FROM transaksi t 
    WHERE t.pembeli_id = '$pembeli_id' 
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>My Orders | Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top right, #0f172a 0%, #020617 100%);
            color: white;
            min-height: screen;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="antialiased p-8">

    <div class="max-w-5xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-black tracking-tighter italic uppercase">My <span class="text-indigo-400">Orders</span></h1>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-2">Pantau status pesanan dan pengembalian Anda</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 p-4 glass-card border-emerald-500/30 text-emerald-400 rounded-2xl text-xs font-black uppercase tracking-widest animate-pulse">
                Permintaan refund telah dikirim. Menunggu verifikasi penjual.
            </div>
        <?php endif; ?>

        <div class="grid gap-6">
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="glass-card rounded-[2rem] p-8 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex gap-6 items-center">
                        <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center border border-white/10">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-1">TX-ID: #<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></p>
                            <h3 class="text-lg font-black italic uppercase"><?= $row['total_item'] ?> Items Ordered</h3>
                            <p class="text-xs text-indigo-300/50"><?= date('d F Y', strtotime($row['created_at'])) ?></p>
                        </div>
                    </div>

                    <div class="text-center md:text-right">
                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-1">Total Payment</p>
                        <h3 class="text-xl font-black text-white">Rp <?= number_format($row['total'], 0, ',', '.') ?></h3>
                    </div>

                    <div class="flex flex-col gap-3 w-full md:w-auto">
                        <?php if ($row['status'] == 'approve'): ?>
                            <span class="px-4 py-2 bg-emerald-500/10 text-emerald-400 text-[10px] font-black rounded-xl uppercase text-center border border-emerald-500/20">Completed</span>
                            <a href="refund.php?id=<?= $row['id'] ?>" class="px-4 py-2 bg-white/5 hover:bg-red-500 hover:text-white text-gray-400 text-[10px] font-black rounded-xl uppercase text-center transition-all">Request Refund</a>
                        <?php elseif ($row['status'] == 'pending_refund'): ?>
                            <span class="px-4 py-2 bg-amber-500/10 text-amber-400 text-[10px] font-black rounded-xl uppercase text-center border border-amber-500/20 italic">Refund Processing</span>
                        <?php elseif ($row['status'] == 'refunded'): ?>
                            <span class="px-4 py-2 bg-red-500/10 text-red-400 text-[10px] font-black rounded-xl uppercase text-center border border-red-500/20">Order Refunded</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>