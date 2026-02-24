<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

$pembeli_id = $_SESSION['user']['id'];

// Ambil semua pesanan pembeli
$pesanan = mysqli_query(
    $conn,
    "SELECT * FROM transaksi 
     WHERE pembeli_id='$pembeli_id'
     ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan | Glass Amber Edition</title>
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

        /* Liquid Background Decor */
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
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 10px;
        }

        .status-badge {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 4px 12px;
            border-radius: 8px;
            border-width: 1px;
        }
    </style>
</head>

<body class="antialiased">

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
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">
                        Orders History
                    </span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Arsip Belanja & Dokumen Digital</p>
            </header>

            <div class="glass-card rounded-[3rem] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">No</th>
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Purchase Date</th>
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Total Amount</th>
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Current Status</th>
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em]">Tracking</th>
                            <th class="p-6 font-black text-amber-500 text-[10px] uppercase tracking-[0.2em] text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if (mysqli_num_rows($pesanan) > 0): ?>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($pesanan)):
                                // Logic warna status
                                $status_class = "text-gray-400 bg-white/5 border-white/10";
                                if ($row['status'] == 'pending')
                                    $status_class = "text-orange-400 bg-orange-500/10 border-orange-500/20";
                                elseif ($row['status'] == 'approve' || $row['status'] == 'approved' || $row['status'] == 'selesai')
                                    $status_class = "text-emerald-400 bg-emerald-500/10 border-emerald-500/20";
                                elseif ($row['status'] == 'dikirim')
                                    $status_class = "text-blue-400 bg-blue-500/10 border-blue-500/20";
                                elseif ($row['status'] == 'tolak' || $row['status'] == 'ditolak')
                                    $status_class = "text-red-400 bg-red-500/10 border-red-500/20";
                            ?>
                                <tr class="hover:bg-white/5 transition-all duration-300 group">
                                    <td class="p-6 text-emerald-100/30 font-mono text-xs italic"><?= str_pad($no++, 2, "0", STR_PAD_LEFT) ?></td>
                                    <td class="p-6 text-white text-xs font-bold"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                    <td class="p-6 font-black text-amber-400 italic text-lg tracking-tighter">
                                        Rp <?= number_format($row['total']) ?>
                                    </td>
                                    <td class="p-6">
                                        <span class="status-badge <?= $status_class ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-6">
                                        <?php if (!empty($row['resi'])): ?>
                                            <div class="flex flex-col">
                                                <span class="text-[8px] font-black text-gray-600 uppercase mb-1">Waybill No.</span>
                                                <span class="font-mono text-[10px] text-emerald-400 bg-emerald-500/5 px-2 py-1 rounded border border-emerald-500/10 w-fit">
                                                    <?= htmlspecialchars($row['resi']) ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-white/10 italic text-[10px] tracking-widest uppercase">Processing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="detail.php?id=<?= $row['id'] ?>"
                                                class="px-5 py-2.5 bg-white/5 border border-white/10 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-white/10 transition-all">
                                                Detail
                                            </a>

                                            <?php if (in_array($row['status'], ['approve', 'approved', 'selesai', 'dikirim'])): ?>
                                                <a href="cetak_invoice.php?id=<?= $row['id'] ?>" target="_blank"
                                                    class="px-5 py-2.5 bg-amber-600/20 border border-amber-500/50 text-amber-400 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all shadow-lg shadow-amber-900/20">
                                                    Invoice
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-24 text-center">
                                    <div class="flex flex-col items-center opacity-20">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="1.5" />
                                        </svg>
                                        <p class="font-black italic uppercase tracking-[0.3em] text-xs text-white">No Transactions Found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <footer class="mt-20 text-center">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">Digital Ledger Verified Authenticity</p>
            </footer>
        </main>
    </div>

</body>

</html>