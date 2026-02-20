<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual');
$penjual_id = $_SESSION['user']['id'];

$pesanan = mysqli_query(
    $conn,
    "SELECT p.*, u.username AS pembeli
     FROM transaksi p
     JOIN users u ON p.pembeli_id = u.id
     WHERE p.penjual_id='$penjual_id'
     ORDER BY p.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Masuk - Glass Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>

<body class="text-gray-100 antialiased overflow-x-hidden">

    <div class="liquid-bg">
        <div class="blob" style="top: 15%; right: 15%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -4s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">
        <?php include '../../partials/sidebar-penjual.php'; ?>

        <main class="flex-1 p-8">

            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">
                    Pesanan Masuk
                </h1>
                <p class="text-gray-400 text-sm mt-1 italic uppercase tracking-widest">Kelola transaksi dan pengiriman Anda</p>
            </div>

            <div class="max-w-4xl">
                <?php if (isset($_GET['error']) && $_GET['error'] == 'stok_habis'): ?>
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 backdrop-blur-md rounded-2xl flex items-center gap-3 animate-pulse">
                        <span class="text-xl">⚠️</span>
                        <span>Stok produk tidak mencukupi. Pesanan tidak dapat disetujui.</span>
                    </div>
                <?php endif ?>

                <?php if (isset($_GET['success']) && $_GET['success'] == 'approved'): ?>
                    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 backdrop-blur-md rounded-2xl flex items-center gap-3">
                        <span class="text-xl">✅</span>
                        <span>Pesanan berhasil disetujui dan stok otomatis diperbarui.</span>
                    </div>
                <?php endif ?>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-gray-400 text-xs uppercase tracking-widest">
                            <th class="p-5 font-semibold">ID Transaksi</th>
                            <th class="p-5 font-semibold">Nama Pembeli</th>
                            <th class="p-5 font-semibold">Total Bayar</th>
                            <th class="p-5 font-semibold">Status & Pengiriman</th>
                            <th class="p-5 font-semibold">bukti pembayaran</th>
                            <th class="p-5 font-semibold text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5 font-mono text-amber-400 font-bold">
                                    #<?= $row['id'] ?>
                                </td>

                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-bold border border-emerald-500/30">
                                            <?= strtoupper(substr($row['pembeli'], 0, 1)) ?>
                                        </div>
                                        <span class="text-white/90 font-medium"><?= htmlspecialchars($row['pembeli']) ?></span>
                                    </div>
                                </td>

                                <td class="p-5 text-gray-300 font-medium">
                                    Rp <?= number_format($row['total'], 0, ',', '.') ?>
                                </td>

                                <td class="p-5">
                                    <form action="<?= BASE_URL ?>/controllers/PesananController.php" method="POST" class="flex flex-col gap-2 w-full max-w-[200px]">
                                        <input type="hidden" name="transaksi_id" value="<?= $row['id'] ?>">

                                        <select name="status"
                                            onchange="this.form.submit()"
                                            class="text-[10px] font-bold uppercase rounded-lg border-white/10 p-2 focus:ring-2 focus:ring-amber-500 cursor-pointer transition-all
                                            <?php
                                            if ($row['status'] == 'menunggu') echo 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                                            elseif ($row['status'] == 'approve') echo 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                            elseif ($row['status'] == 'tolak') echo 'bg-red-500/10 text-red-400 border-red-500/20';
                                            else echo 'bg-gray-500/10 text-gray-400 border-gray-500/20';
                                            ?>">
                                            <?php
                                            $statusList = ['menunggu', 'approve', 'tolak', 'refund'];
                                            foreach ($statusList as $st):
                                            ?>
                                                <option value="<?= $st ?>" class="bg-gray-900 text-white" <?= $row['status'] == $st ? 'selected' : '' ?>>
                                                    <?= strtoupper($st) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>

                                        <?php if ($row['status'] == 'approve'): ?>
                                            <button type="button"
                                                onclick="openResiModal('<?= $row['id'] ?>', '<?= $row['jasa_pengiriman'] ?? '' ?>', '<?= $row['resi'] ?? '' ?>', '<?= $row['link_lacak'] ?? '' ?>', '<?= $row['status'] ?>')"
                                                class="w-full text-[10px] p-2 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 hover:bg-emerald-500/30 transition-all text-left flex justify-between items-center">
                                                <span><?= $row['resi'] ? 'Edit Resi' : 'Input Resi' ?></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                        <?php endif ?>
                                    </form>
                                </td>

                                <td class="p-5">
                                    <?php if ($row['bukti_transfer']): ?>
                                        <a href="<?= BASE_URL ?>/public/uploads/bukti_bayar/<?= $row['bukti_transfer'] ?>" target="_blank" class="inline-flex items-center px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-emerald-500/20 hover:border-emerald-500/30 transition-all">
                                            Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-[10px] italic">Tidak ada bukti</span>
                                    <?php endif ?>
                                </td>

                                <td class="p-5 text-right">
                                    <a href="detail.php?id=<?= $row['id'] ?>"
                                        class="inline-flex items-center px-4 py-2 bg-white/5 border border-white/10 text-gray-300 text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-white/15 hover:text-white transition-all group-hover:border-amber-500/50">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>

        </main>

        <div id="resiModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeResiModal()"></div>

            <div class="relative bg-gray-900 border border-white/20 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-6 border-b border-white/10 bg-white/5">
                    <h3 class="text-xl font-bold text-amber-400">Informasi Pengiriman</h3>
                    <p class="text-gray-400 text-xs">Update data ekspedisi untuk transaksi #<span id="display_id"></span></p>
                </div>

                <form action="<?= BASE_URL ?>/controllers/PesananController.php" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="action" value="update_resi">
                    <input type="hidden" name="transaksi_id" id="modal_transaksi_id">
                    <input type="hidden" name="status" id="modal_status">

                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1 ml-1">Nama Ekspedisi</label>
                        <input type="text" name="ekspedisi" id="modal_ekspedisi" required placeholder="Contoh: JNE, J&T, SiCepat"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1 ml-1">Nomor Resi</label>
                        <input type="text" name="resi" id="modal_resi" required placeholder="Masukkan nomor resi..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1 ml-1">URL Tracking (Opsional)</label>
                        <input type="url" name="url_tracking" id="modal_url" placeholder="https://cekresi.com/..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 transition-all">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="closeResiModal()"
                            class="flex-1 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-emerald-500 text-black font-bold hover:opacity-90 transition-all">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openResiModal(id, ekspedisi, resi, url, status) {
            const modal = document.getElementById('resiModal');
            document.getElementById('modal_transaksi_id').value = id;
            document.getElementById('display_id').innerText = id;
            document.getElementById('modal_ekspedisi').value = ekspedisi;
            document.getElementById('modal_resi').value = resi;
            document.getElementById('modal_url').value = url;
            document.getElementById('modal_status').value = status;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResiModal() {
            const modal = document.getElementById('resiModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Menutup modal dengan tombol Esc
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeResiModal();
        });
    </script>
</body>

</html>