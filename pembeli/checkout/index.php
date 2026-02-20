<?php
require_once '../../config/app.php'; 
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

$pembeli_id = $_SESSION['user']['id'];

// 1. Ambil data pembeli
$pembeli = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT username, alamat FROM users WHERE id='$pembeli_id'"
));

// 2. Ambil keranjang dan GROUP berdasarkan id_penjual
// Kita join ke tabel users untuk mendapatkan nama toko (username penjual) dan QRIS-nya
$query_keranjang = mysqli_query($conn, "
    SELECT k.*, p.nama as nama_produk, p.harga, p.penjual_id, u.username as nama_toko, u.foto_qris 
    FROM keranjang k
    JOIN produk p ON k.produk_id = p.id
    JOIN users u ON p.penjual_id = u.id
    WHERE k.pembeli_id = '$pembeli_id'
");

$groups = [];
while ($row = mysqli_fetch_assoc($query_keranjang)) {
    $row['subtotal'] = $row['harga'] * $row['qty'];
    $groups[$row['penjual_id']]['toko'] = [
        'nama' => $row['nama_toko'],
        'qris' => $row['foto_qris']
    ];
    $groups[$row['penjual_id']]['produk'][] = $row;
}

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Multi-Merchant Checkout | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">
    <?php include '../../partials/sidebar-pembeli.php'; ?>

    <main class="flex-1 p-8">
        <header class="mb-10 text-center md:text-left">
            <h1 class="text-4xl font-black tracking-tighter">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-amber-400">
                    SECURE CHECKOUT
                </span>
            </h1>
            <p class="text-gray-500 mt-2 uppercase tracking-[0.2em] text-[10px] font-bold">Pesanan Anda dikelompokkan per penjual</p>
        </header>

        <form action="proses.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <div class="lg:col-span-2 space-y-10">
                
                <section class="glass-card p-8 rounded-[2.5rem]">
                    <h2 class="text-sm font-black text-emerald-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <span>📍</span> Alamat Pengiriman
                    </h2>
                    <div class="space-y-4">
                        <input type="text" name="nama_penerima" value="<?= $pembeli['username'] ?>" class="input-glass w-full p-4 rounded-2xl text-sm" placeholder="Nama Penerima" required>
                        <textarea name="alamat_lengkap" rows="2" class="input-glass w-full p-4 rounded-2xl text-sm" required><?= $pembeli['alamat'] ?></textarea>
                    </div>
                </section>

                <?php foreach ($groups as $id_penjual => $data): 
                    $total_toko = 0;
                ?>
                <section class="glass-card p-8 rounded-[3rem] border-l-4 border-amber-500/50 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl"></div>
                    
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <h3 class="text-lg font-black text-white mb-6 uppercase flex items-center gap-3">
                                <span class="text-amber-500">🏪</span> <?= $data['toko']['nama'] ?>
                            </h3>
                            
                            <div class="space-y-4">
                                <?php foreach ($data['produk'] as $item): 
                                    $total_toko += $item['subtotal'];
                                    $grand_total += $item['subtotal'];
                                ?>
                                <div class="flex justify-between items-center text-sm border-b border-white/5 pb-3">
                                    <div>
                                        <p class="text-emerald-50 font-medium"><?= $item['nama_produk'] ?></p>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter"><?= $item['qty'] ?> Unit x Rp <?= number_format($item['harga']) ?></p>
                                    </div>
                                    <span class="font-bold text-emerald-400">Rp <?= number_format($item['subtotal']) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-6 p-4 bg-emerald-500/5 rounded-2xl border border-emerald-500/10">
                                <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Subtotal Toko</p>
                                <p class="text-xl font-black text-emerald-400">Rp <?= number_format($total_toko) ?></p>
                            </div>
                        </div>

                        <div class="w-full md:w-64 flex flex-col items-center border-t md:border-t-0 md:border-l border-white/10 pt-6 md:pt-0 md:pl-8">
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-4">Scan QRIS Toko</p>
                            <div class="bg-white p-2 rounded-2xl mb-4 shadow-xl">
                                <?php if ($data['toko']['qris']): ?>
                                    <img src="<?= BASE_URL ?>/public/uploads/qris/<?= $data['toko']['qris'] ?>" class="w-40 h-40 object-contain">
                                <?php else: ?>
                                    <div class="w-40 h-40 flex items-center justify-center bg-gray-100 text-[8px] text-gray-400 font-bold uppercase text-center p-4">QRIS Belum Tersedia</div>
                                <?php endif; ?>
                            </div>
                            
                            <label class="w-full">
                                <input type="file" name="bukti_<?= $id_penjual ?>" class="hidden" id="file_<?= $id_penjual ?>" required onchange="updateFileName(<?= $id_penjual ?>)">
                                <div class="w-full py-3 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase text-center cursor-pointer hover:bg-white/10 transition-all tracking-widest" id="label_<?= $id_penjual ?>">
                                    Upload Bukti Bayar
                                </div>
                            </label>
                        </div>
                    </div>
                </section>
                <?php endforeach; ?>
            </div>

            <div class="lg:col-span-1">
                <div class="glass-card p-8 rounded-[2.5rem] sticky top-8 text-center">
                    <h2 class="text-sm font-black text-gray-500 uppercase tracking-[0.3em] mb-6">Total Pembayaran</h2>
                    
                    <div class="mb-8">
                        <p class="text-4xl font-black text-amber-400 tracking-tighter">Rp <?= number_format($grand_total) ?></p>
                        <p class="text-[9px] text-gray-500 uppercase font-bold mt-2 italic tracking-widest">*Sudah termasuk semua toko</p>
                    </div>

                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-emerald-900/40 transition-all active:scale-95">
                        Konfirmasi Semua Pesanan
                    </button>

                    <div class="mt-6 flex items-center justify-center gap-2 text-emerald-500/50">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Secure Encrypted Payment</span>
                    </div>
                </div>
            </div>

        </form>
    </main>
</div>

<script>
function updateFileName(id) {
    const input = document.getElementById('file_' + id);
    const label = document.getElementById('label_' + id);
    if (input.files.length > 0) {
        label.innerText = "✅ " + input.files[0].name.substring(0, 15) + "...";
        label.classList.add('border-emerald-500', 'text-emerald-400', 'bg-emerald-500/10');
    }
}
</script>

</body>
</html>