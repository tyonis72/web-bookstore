<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

$id_transaksi = $_GET['id'];
$pembeli_id = $_SESSION['user']['id'];

// Pastikan transaksi ini milik pembeli yang login dan statusnya sudah 'approve'
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id_transaksi' AND pembeli_id = '$pembeli_id' AND status = 'approve'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: index.php?error=not_found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Request Refund | Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: #020617;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="p-8 flex justify-center items-center min-h-screen">
    <div class="glass-card w-full max-w-lg rounded-[2.5rem] p-10 shadow-2xl">
        <h2 class="text-2xl font-black uppercase italic tracking-tighter text-emerald-400 mb-2">Request Refund</h2>
        <p class="text-gray-500 text-xs mb-8 uppercase font-bold tracking-widest">Transaction #TX-<?= $id_transaksi ?></p>

        <form action="../../controllers/RefundController.php" method="POST" class="space-y-6">
            <input type="hidden" name="id_transaksi" value="<?= $id_transaksi ?>">

            <div>
                <label class="text-[10px] text-gray-400 uppercase font-black tracking-widest block mb-2">Alasan Pengembalian</label>
                <textarea name="alasan" required
                    class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500 text-sm transition-all h-32"
                    placeholder="Jelaskan alasan Anda (misal: Buku rusak, salah judul, dll)"></textarea>
            </div>

            <div class="flex gap-3">
                <a href="index.php" class="flex-1 text-center py-4 bg-white/5 hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Cancel</a>
                <button type="submit" name="ajukan_refund"
                    class="flex-2 px-8 py-4 bg-red-600 hover:bg-red-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg shadow-red-900/20">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</body>

</html>