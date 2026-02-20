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
    <title>Riwayat Pesanan - Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Base Background matching Dashboard */
        body {
            background: linear-gradient(135deg, #064e3b 0%, #022c22 50%, #78350f 100%);
            background-attachment: fixed;
            color: white;
        }

        /* Liquid Glass Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        /* Custom Scrollbar for dark theme */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen">

    <div class="flex min-h-screen">

        <?php include '../../partials/sidebar-pembeli.php'; ?>

        <main class="flex-1 p-8">
            <header class="mb-10">
                <h1 class="text-3xl font-black tracking-tight text-white">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
                        Riwayat Pesanan
                    </span>
                </h1>
                <p class="text-emerald-100/50 mt-1 italic text-sm">Pantau status transaksi dan koleksi buku Anda.</p>
            </header>

            <div class="glass-card rounded-[2rem] overflow-hidden border border-white/10">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider">No</th>
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider">Tanggal</th>
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider">Total Belanja
                            </th>
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider">Status</th>
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider">No. Resi</th>
                            <th class="p-5 font-bold text-emerald-400 text-sm uppercase tracking-wider text-center">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if (mysqli_num_rows($pesanan) > 0): ?>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($pesanan)):
                                // Styling Status Badge Liquid
                                $status_class = "text-gray-300 bg-white/5 border-white/10";
                                if ($row['status'] == 'pending')
                                    $status_class = "text-orange-400 bg-orange-500/10 border-orange-500/20";
                                elseif ($row['status'] == 'approved')
                                    $status_class = "text-emerald-400 bg-emerald-500/10 border-emerald-500/20";
                                elseif ($row['status'] == 'dikirim')
                                    $status_class = "text-blue-400 bg-blue-500/10 border-blue-500/20";
                                elseif ($row['status'] == 'ditolak')
                                    $status_class = "text-red-400 bg-red-500/10 border-red-500/20";
                                ?>
                                <tr class="hover:bg-white/5 transition-all duration-300 group">
                                    <td class="p-5 text-emerald-100/70 font-mono"><?= str_pad($no++, 2, "0", STR_PAD_LEFT) ?>
                                    </td>
                                    <td class="p-5 text-emerald-50 font-medium">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="p-5 font-black text-amber-400 text-lg">
                                        Rp <?= number_format($row['total']) ?>
                                    </td>
                                    <td class="p-5">
                                        <span
                                            class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border <?= $status_class ?> shadow-[0_0_15px_rgba(0,0,0,0.2)]">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <?php
                                        // Gunakan 'tolak' sesuai dengan isi di PesananController.php
                                        if ($row['status'] == 'tolak' || $row['status'] == 'ditolak'): ?>
                                            <span class="text-white-400/20 italic text-sm">-</span>

                                            <?php
                                            // Cek jika resi ada (dan status bukan ditolak)
                                        elseif (!empty($row['resi'])): ?>
                                            <span
                                                class="font-mono text-sm text-emerald-300/80 bg-emerald-500/5 px-3 py-1 rounded-lg border border-emerald-500/10 italic">
                                                <?= $row['resi'] ?>
                                            </span>

                                            <?php
                                            // Jika masih menunggu (status pending/approve tapi resi belum input)
                                        else: ?>
                                            <span class="text-white/20 italic text-sm">Menunggu...</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-5 text-center">
                                        <a href="detail.php?id=<?= $row['id'] ?>"
                                            class="inline-flex items-center justify-center bg-gradient-to-br from-emerald-500 to-emerald-700 text-white px-6 py-2 rounded-xl text-xs font-bold hover:from-emerald-400 hover:to-emerald-600 transition-all shadow-lg shadow-emerald-900/40 active:scale-95">
                                            DETAIL
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mb-4 border border-white/10 text-white/20 text-3xl italic">
                                            ?
                                        </div>
                                        <p class="text-emerald-100/30 italic">Belum ada jejak transaksi ditemukan.</p>
                                        <a href="<?= BASE_URL ?>/pembeli/produk/index.php"
                                            class="mt-6 px-8 py-3 bg-white/5 border border-emerald-500/30 text-emerald-400 rounded-2xl hover:bg-emerald-500/10 transition-all font-bold tracking-widest text-xs">
                                            MULAI BELANJA →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>