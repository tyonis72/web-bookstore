<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual');
$penjual_id = $_SESSION['user']['id'];

// Filter Bulan dan Tahun
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// 1. Query Data Card (Total Penjualan, Keuntungan, Produk Terjual)
$query_stats = mysqli_query($conn, "
    SELECT 
        SUM(total) as total_penjualan,
        COUNT(*) as total_transaksi,
        SUM(total * 0.1) as estimasi_keuntungan -- Contoh: Keuntungan 10%
    FROM transaksi 
    WHERE penjual_id = '$penjual_id' 
    AND status = 'approve'
    AND MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun'
");
$stats = mysqli_fetch_assoc($query_stats);

// 2. Query Data Grafik (Harian dalam bulan tersebut)
$grafik_data = mysqli_query($conn, "
    SELECT DAY(created_at) as tgl, SUM(total * 0.1) as untung 
    FROM transaksi 
    WHERE penjual_id = '$penjual_id' 
    AND status = 'approve'
    AND MONTH(created_at) = '$bulan' 
    AND YEAR(created_at) = '$tahun'
    GROUP BY DAY(created_at)
");

$days = [];
$profits = [];
while ($row = mysqli_fetch_assoc($grafik_data)) {
    $days[] = $row['tgl'];
    $profits[] = $row['untung'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Glass Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes float {
            0% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-30px, 40px);
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
            background: radial-gradient(circle at center, #111827 0%, #030712 100%);
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
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
                color: black;
            }

            .glass-card {
                border: 1px solid #ccc;
                backdrop-filter: none;
                background: white;
                color: black;
            }

            .flex-1 {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="text-gray-100 antialiased overflow-x-hidden">

    <div class="liquid-bg no-print">
        <div class="blob" style="top: 10%; right: 10%; background: #10b981;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; background: #3b82f6;"></div>
    </div>

    <div class="flex min-h-screen">
        <aside class="no-print">
            <?php include '../../partials/sidebar-penjual.php'; ?>
        </aside>

        <main class="flex-1 p-8 ml-64 transition-all duration-500">

            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1
                        class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-blue-400">
                        Laporan Penjualan
                    </h1>
                    <p class="text-gray-400 text-sm mt-1 italic uppercase tracking-widest">Analisis performa toko Anda
                    </p>
                </div>

                <div class="flex gap-3 no-print">
                    <form class="flex gap-2">
                        <select name="bulan"
                            class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm outline-none focus:border-emerald-500 transition-all">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= sprintf('%02d', $m) ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="tahun"
                            class="bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-sm outline-none focus:border-emerald-500 transition-all">
                            <?php for ($y = date('Y'); $y >= 2023; $y--): ?>
                                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-500 px-4 py-2 rounded-xl text-sm font-bold transition-all">Filter</button>
                    </form>
                    <button onclick="window.print()"
                        class="bg-white/10 hover:bg-white/20 border border-white/10 px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all">
                    </div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Total Penjualan</p>
                    <h3 class="text-2xl font-bold text-white">Rp
                        <?= number_format($stats['total_penjualan'] ?? 0, 0, ',', '.') ?></h3>
                </div>

                <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all">
                    </div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Estimasi Keuntungan</p>
                    <h3 class="text-2xl font-bold text-emerald-400">Rp
                        <?= number_format($stats['estimasi_keuntungan'] ?? 0, 0, ',', '.') ?></h3>
                </div>

                <div class="glass-card p-6 rounded-3xl relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all">
                    </div>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">Transaksi Berhasil</p>
                    <h3 class="text-2xl font-bold text-white"><?= $stats['total_transaksi'] ?? 0 ?> <span
                            class="text-sm font-normal text-gray-500 italic">Order</span></h3>
                </div>
            </div>

            <div class="glass-card p-8 rounded-3xl mb-8">
                <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                    <span class="w-2 h-6 bg-emerald-500 rounded-full"></span>
                    Grafik Keuntungan Harian
                </h3>
                <canvas id="profitChart" height="100"></canvas>
            </div>

        </main>
    </div>

    <script>
        // Data dari PHP ke JavaScript
        const labels = <?= json_encode($days) ?>;
        const dataProfits = <?= json_encode($profits) ?>;

        const ctx = document.getElementById('profitChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.map(d => 'Tgl ' + d),
                datasets: [{
                    label: 'Keuntungan (Rp)',
                    data: dataProfits,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af' }
                    }
                }
            }
        });
    </script>

</body>

</html>