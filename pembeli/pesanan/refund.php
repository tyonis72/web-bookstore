<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');
$id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Request Refund</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#020617] text-white flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white/5 border border-white/10 p-10 rounded-[2.5rem] shadow-2xl">
        <h2 class="text-2xl font-black uppercase italic tracking-tighter text-red-500 mb-6">Ajukan Refund</h2>

        <form action="../../controllers/RefundController.php" method="POST" class="space-y-6">
            <input type="hidden" name="id_transaksi" value="<?= $id ?>">

            <div>
                <label class="text-[10px] text-gray-400 uppercase font-black tracking-widest block mb-2">Alasan Pengembalian</label>
                <textarea name="alasan" required
                    class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-red-500 text-sm transition-all h-40"
                    placeholder="Contoh: Paket rusak saat sampai, halaman buku ada yang hilang..."></textarea>
            </div>

            <button type="submit" name="ajukan_refund"
                class="w-full py-4 bg-red-600 hover:bg-red-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all">
                Kirim Permintaan Refund
            </button>
            <a href="detail.php?id=<?= $id ?>" class="block text-center text-[10px] text-gray-500 uppercase font-bold tracking-widest">Batal</a>
        </form>
    </div>
</body>

</html>