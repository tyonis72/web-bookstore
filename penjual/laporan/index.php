<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual');
$penjual_id = $_SESSION['user']['id'];

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// 1. Query Statistik
$query_stats = mysqli_query($conn, "
    SELECT 
        SUM(total) as total_penjualan,
        COUNT(*) as total_transaksi,
        SUM(total * 0.1) as estimasi_keuntungan 
    FROM transaksi 
    WHERE penjual_id = '$penjual_id' 
    AND status = 'approve'
    AND MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun'
");
$stats = mysqli_fetch_assoc($query_stats);

// 2. Query Data Grafik
$grafik_data = mysqli_query($conn, "
    SELECT DAY(created_at) as tgl, SUM(total * 0.1) as untung 
    FROM transaksi 
    WHERE penjual_id = '$penjual_id' 
    AND status = 'approve'
    AND MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun'
    GROUP BY DAY(created_at)
    ORDER BY tgl ASC
");

$days = [];
$profits = [];
while ($row = mysqli_fetch_assoc($grafik_data)) {
    $days[] = $row['tgl'];
    $profits[] = (float)$row['untung'];
}

// Default jika kosong agar Chart tidak error
if (empty($days)) {
    $days = [date('d')];
    $profits = [0];
}

// 3. Query Detail Transaksi
$detail_transaksi = mysqli_query($conn, "
    SELECT t.*, u.username as pembeli
    FROM transaksi t
    JOIN users u ON t.pembeli_id = u.id
    WHERE t.penjual_id = '$penjual_id' 
    AND t.status IN ('approve', 'pending_refund', 'refunded')
    AND MONTH(t.created_at) = '$bulan' 
    AND YEAR(t.created_at) = '$tahun'
    ORDER BY t.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan & Refund - Glass Amber Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="text-gray-100 antialiased overflow-x-hidden">

    <div class="liquid-bg">
        <div class="blob" style="top: 15%; right: 15%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">
        <aside class="w-64 border-r border-white/10 bg-white/5 backdrop-blur-2xl">
            <?php include '../../partials/sidebar-penjual.php'; ?>
        </aside>

        <main class="flex-1 p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
                <div>
                    <h1 class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight uppercase">
                        Sales Intelligence
                    </h1>
                    <p class="text-gray-500 mt-2 font-medium italic">Analisa pendapatan dan manajemen pengembalian dana.</p>
                </div>
                <form class="flex gap-2 bg-white/5 p-2 rounded-2xl border border-white/10">
                    <select name="bulan" class="bg-transparent px-3 py-1 text-xs outline-none cursor-pointer">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option class="text-black" value="<?= sprintf('%02d', $m) ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 px-5 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Apply</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="glass-card p-8 rounded-[2rem] border-t-2 border-amber-500/20">
                    <p class="text-amber-500 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Total Penjualan</p>
                    <h2 class="text-3xl font-black italic">Rp <?= number_format($stats['total_penjualan'] ?? 0, 0, ',', '.') ?></h2>
                </div>
                <div class="glass-card p-8 rounded-[2rem] border-t-2 border-emerald-500/20">
                    <p class="text-emerald-500 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Profit (10%)</p>
                    <h2 class="text-3xl font-black italic text-emerald-400">Rp <?= number_format($stats['estimasi_keuntungan'] ?? 0, 0, ',', '.') ?></h2>
                </div>
                <div class="glass-card p-8 rounded-[2rem] border-t-2 border-blue-500/20">
                    <p class="text-blue-500 font-black uppercase tracking-[0.2em] text-[10px] mb-2">Pesanan Sukses</p>
                    <h2 class="text-3xl font-black italic"><?= $stats['total_transaksi'] ?? 0 ?></h2>
                </div>
            </div>

            <div class="glass-card p-8 rounded-[2.5rem] mb-12">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Growth Performance</h3>
                <canvas id="profitChart" height="90"></canvas>
            </div>

            <div class="glass-card rounded-[2.5rem] overflow-hidden">
                <div class="p-6 border-b border-white/5 bg-white/5">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-white">Log Transaksi Terkini</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] uppercase font-black tracking-widest text-gray-500 border-b border-white/5">
                                <th class="p-6">Waktu</th>
                                <th class="p-6">Pembeli</th>
                                <th class="p-6">Total</th>
                                <th class="p-6">Status</th>
                                <th class="p-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php while ($row = mysqli_fetch_assoc($detail_transaksi)): ?>
                                <tr class="hover:bg-white/5 transition-all">
                                    <td class="p-6 text-xs text-gray-500"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td class="p-6 text-xs font-bold uppercase tracking-widest text-white"><?= htmlspecialchars($row['pembeli']) ?></td>
                                    <td class="p-6 text-xs font-black text-amber-400">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                    <td class="p-6">
                                        <?php if ($row['status'] == 'approve'): ?>
                                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[9px] font-black rounded-lg uppercase">Success</span>
                                        <?php elseif ($row['status'] == 'pending_refund'): ?>
                                            <span class="px-3 py-1 bg-amber-500/10 text-amber-400 text-[9px] font-black rounded-lg uppercase animate-pulse">Refund Req</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-red-500/10 text-red-400 text-[9px] font-black rounded-lg uppercase">Refunded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-6 text-right">
                                        <?php if ($row['status'] == 'pending_refund'): ?>
                                            <button onclick="openRefundModal('<?= $row['id'] ?>', '<?= addslashes($row['alasan_refund']) ?>')"
                                                class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-[10px] font-black rounded-xl uppercase transition-all shadow-lg shadow-amber-900/40">
                                                Review
                                            </button>
                                        <?php else: ?>
                                            <span class="text-[9px] text-gray-600 font-bold uppercase">Locked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="refundModal" class="hidden fixed inset-0 z-[999] flex items-center justify-center bg-black/90 backdrop-blur-md p-6">
        <div class="glass-card w-full max-w-lg rounded-[3rem] p-10 shadow-2xl">
            <h2 class="text-3xl font-black italic text-amber-400 mb-6 uppercase tracking-tighter">Refund Review</h2>

            <div class="bg-white/5 rounded-2xl p-6 mb-8 border border-white/10">
                <p class="text-[10px] text-amber-500 font-black uppercase mb-2 tracking-widest">Alasan Pembeli:</p>
                <p id="modalAlasan" class="text-sm text-gray-300 italic leading-relaxed"></p>
            </div>

            <form action="../../controllers/RefundController.php" method="POST">
                <input type="hidden" name="id_transaksi" id="inputOrderId">
                <div id="rejectSection" class="hidden mb-6">
                    <label class="text-[10px] text-red-400 font-black uppercase mb-2 block tracking-widest">Alasan Penolakan:</label>
                    <textarea name="alasan_tolak" id="textTolak" class="w-full bg-white/10 border border-white/20 rounded-2xl p-4 text-sm text-white focus:outline-none focus:border-red-500" placeholder="Berikan alasan..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4" id="mainButtons">
                    <button type="button" onclick="showRejectInput()" class="py-4 border border-red-500/50 text-red-500 rounded-2xl text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition-all">Tolak</button>
                    <button type="submit" name="approve_refund" class="py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/40">Approve</button>
                </div>

                <button type="submit" name="reject_refund" id="confirmReject" class="hidden w-full py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase shadow-lg shadow-red-900/40">Konfirmasi Penolakan</button>
            </form>

            <button onclick="closeRefundModal()" class="w-full mt-6 text-[10px] text-gray-500 font-bold uppercase hover:text-white">Batal</button>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('profitChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($days) ?>.map(d => 'Tgl ' + d),
                datasets: [{
                    label: 'Profit',
                    data: <?= json_encode($profits) ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        grid: {
                            color: 'rgba(255,255,255,0.05)'
                        },
                        ticks: {
                            color: '#4b5563'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#4b5563'
                        }
                    }
                }
            }
        });

        function openRefundModal(id, alasan) {
            document.getElementById('inputOrderId').value = id;
            document.getElementById('modalAlasan').innerText = alasan;
            document.getElementById('refundModal').classList.remove('hidden');
        }

        function closeRefundModal() {
            document.getElementById('refundModal').classList.add('hidden');
            document.getElementById('rejectSection').classList.add('hidden');
            document.getElementById('confirmReject').classList.add('hidden');
            document.getElementById('mainButtons').classList.remove('hidden');
        }

        function showRejectInput() {
            document.getElementById('rejectSection').classList.remove('hidden');
            document.getElementById('confirmReject').classList.remove('hidden');
            document.getElementById('mainButtons').classList.add('hidden');
            document.getElementById('textTolak').required = true;
        }
    </script>
</body>

</html>