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
    <title>Secure Checkout | Glass Amber Edition</title>
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
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            border-color: #f59e0b;
            background: rgba(255, 255, 255, 0.05);
            outline: none;
        }

        .btn-checkout {
            background: linear-gradient(to right, #10b981, #059669);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>

<body class="antialiased pb-20">

    <div class="liquid-bg">
        <div class="blob" style="top: 5%; right: 10%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 10%; left: 5%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <div class="flex min-h-screen">
        <aside class="w-64 fixed inset-y-0 left-0 z-50 border-r border-white/10 bg-white/5 backdrop-blur-2xl hidden md:block">
            <?php include '../../partials/sidebar-pembeli.php'; ?>
        </aside>

        <main class="flex-1 p-8 lg:p-12 md:ml-64">
            <header class="mb-16">
                <h1 class="text-5xl font-black italic uppercase tracking-tighter leading-none">
                    Secure <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400">Checkout</span>
                </h1>
                <div class="h-1 w-20 bg-amber-500 rounded-full mt-4"></div>
                <p class="text-gray-500 mt-6 font-bold uppercase tracking-[0.3em] text-[10px]">Verifikasi Alamat & Pembayaran Multi-Merchant</p>
            </header>

            <form action="proses.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <div class="lg:col-span-2 space-y-10">

                    <section class="glass-card p-10 rounded-[3.5rem] relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl"></div>
                        <h2 class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                            <span class="p-2 bg-emerald-500/10 rounded-lg">📍</span> Delivery Destination
                        </h2>
                        <div class="space-y-6">
                            <div class="group">
                                <label class="text-[9px] font-black uppercase text-gray-600 ml-4 mb-2 block">Recipient Name</label>
                                <input type="text" name="nama_penerima" value="<?= $pembeli['username'] ?>" class="input-glass w-full p-5 rounded-[1.5rem] text-sm font-medium italic" placeholder="Nama Penerima" required>
                            </div>
                            <div class="group">
                                <label class="text-[9px] font-black uppercase text-gray-600 ml-4 mb-2 block">Full Address</label>
                                <textarea name="alamat_lengkap" rows="3" class="input-glass w-full p-5 rounded-[1.5rem] text-sm font-medium italic" required><?= $pembeli['alamat'] ?></textarea>
                            </div>
                        </div>
                    </section>

                    <?php foreach ($groups as $id_penjual => $data):
                        $total_toko = 0;
                    ?>
                        <section class="glass-card p-10 rounded-[3.5rem] border-l-8 border-l-amber-500 relative group transition-all duration-500">
                            <div class="flex flex-col xl:flex-row gap-10">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-xl shadow-inner border border-white/10">🏪</div>
                                        <div>
                                            <h3 class="text-xl font-black italic uppercase tracking-tighter"><?= $data['toko']['nama'] ?></h3>
                                            <p class="text-[9px] text-amber-500/60 font-black uppercase tracking-widest">Authorized Merchant Hub</p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <?php foreach ($data['produk'] as $item):
                                            $total_toko += $item['subtotal'];
                                            $grand_total += $item['subtotal'];
                                        ?>
                                            <div class="flex justify-between items-center p-4 rounded-2xl bg-white/5 border border-white/5 group-hover:bg-white/10 transition-colors">
                                                <div>
                                                    <p class="text-white font-black italic uppercase text-sm tracking-tight"><?= $item['nama_produk'] ?></p>
                                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1"><?= $item['qty'] ?> Unit <span class="mx-2 text-white/10">|</span> Rp <?= number_format($item['harga']) ?></p>
                                                </div>
                                                <span class="font-black text-white italic">Rp <?= number_format($item['subtotal']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="mt-10 p-6 bg-emerald-500/5 rounded-[2rem] border border-emerald-500/10 flex items-center justify-between">
                                        <div>
                                            <p class="text-[9px] text-gray-600 uppercase font-black tracking-widest mb-1">Merchant Subtotal</p>
                                            <p class="text-2xl font-black text-emerald-400 italic tracking-tighter">Rp <?= number_format($total_toko) ?></p>
                                        </div>
                                        <div class="text-3xl opacity-20">💰</div>
                                    </div>
                                </div>

                                <div class="w-full xl:w-72 flex flex-col items-center xl:border-l border-white/10 xl:pl-10">
                                    <p class="text-[9px] text-gray-600 uppercase font-black tracking-[0.3em] mb-6">Payment Gateway</p>
                                    <div class="relative group/qris">
                                        <div class="absolute -inset-2 bg-white rounded-3xl blur opacity-0 group-hover/qris:opacity-10 transition duration-500"></div>
                                        <div class="relative bg-white p-3 rounded-[2rem] shadow-2xl mb-6">
                                            <?php if ($data['toko']['qris']): ?>
                                                <img src="<?= BASE_URL ?>/public/uploads/qris/<?= $data['toko']['qris'] ?>" class="w-44 h-44 object-contain">
                                            <?php else: ?>
                                                <div class="w-44 h-44 flex items-center justify-center bg-gray-100 rounded-2xl p-6 text-center">
                                                    <p class="text-[8px] text-gray-400 font-black uppercase leading-relaxed">QRIS belum dikonfigurasi oleh penjual</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <label class="w-full group/upload">
                                        <input type="file" name="bukti_<?= $id_penjual ?>" class="hidden" id="file_<?= $id_penjual ?>" required onchange="updateFileName(<?= $id_penjual ?>)">
                                        <div class="w-full py-4 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black uppercase text-center cursor-pointer hover:bg-amber-500 hover:text-white transition-all tracking-[0.2em] group-active/upload:scale-95" id="label_<?= $id_penjual ?>">
                                            Upload Receipt
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>

                <div class="lg:col-span-1">
                    <div class="glass-card p-10 rounded-[3.5rem] sticky top-10 border-t-8 border-t-amber-500">
                        <h2 class="text-[10px] font-black text-gray-600 uppercase tracking-[0.4em] mb-10 text-center">Checkout Summary</h2>

                        <div class="mb-10 text-center">
                            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-3">Grand Investment</p>
                            <p class="text-5xl font-black text-white italic tracking-tighter leading-none">
                                Rp <?= number_format($grand_total) ?>
                            </p>
                            <p class="text-[8px] text-gray-700 mt-6 font-black uppercase tracking-widest italic leading-relaxed">
                                *Total akumulasi dari <?= count($groups) ?> merchant yang berbeda.
                            </p>
                        </div>

                        <button type="submit" class="btn-checkout w-full py-5 text-white rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[11px] transition-all active:scale-95 flex items-center justify-center gap-3">
                            Confirm All Orders
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div class="mt-8 pt-8 border-t border-white/5 flex flex-col items-center gap-4">
                            <div class="flex items-center gap-2 text-emerald-500/40">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-[9px] font-black uppercase tracking-widest">End-to-End Encryption</span>
                            </div>
                            <p class="text-[8px] text-gray-800 font-bold uppercase text-center tracking-tighter leading-relaxed">
                                Dengan menekan tombol, Anda menyetujui <br> Syarat & Ketentuan Liquid Glass.
                            </p>
                        </div>
                    </div>
                </div>

            </form>

            <footer class="mt-20 text-center">
                <p class="text-[9px] text-gray-600 font-black uppercase tracking-[0.5em]">Global Transaction Authority Verified</p>
            </footer>
        </main>
    </div>

    <script>
        function updateFileName(id) {
            const input = document.getElementById('file_' + id);
            const label = document.getElementById('label_' + id);
            if (input.files.length > 0) {
                label.innerHTML = `<span class="text-emerald-400">✅ RECEIVED:</span> ${input.files[0].name.substring(0, 10)}...`;
                label.classList.add('border-emerald-500', 'bg-emerald-500/10');
                label.classList.remove('hover:bg-amber-500');
            }
        }
    </script>

</body>

</html>