<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_transaksi = $_GET['id'];
$user_id = $_SESSION['user']['id'];

// Ambil data transaksi & nama pengguna
// Kita ubah u.nama menjadi u.username sesuai dengan data yang ada di session Anda
$query = mysqli_query($conn, "
    SELECT t.*, u.username as nama_pembeli, p.username as nama_toko
    FROM transaksi t
    JOIN users u ON t.pembeli_id = u.id
    JOIN users p ON t.penjual_id = p.id
    WHERE t.id = '$id_transaksi' AND t.pembeli_id = '$user_id'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Transaksi tidak ditemukan.");
}

// Ambil detail item buku (Asumsi ada tabel detail_transaksi atau relasi ke produk)
// Jika Anda belum punya tabel detail, kita ambil dari nama produk di transaksi (jika simpan per item)
// Di sini saya contohkan pengambilan data produk sederhana
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $data['id'] ?> - BookStore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #020617;
            color: white;
        }

        .invoice-box {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media print {

            /* Memastikan latar belakang putih bersih dan teks hitam pekat */
            body {
                background: white !important;
                color: black !important;
                margin: 0;
                padding: 0;
            }

            /* Menghilangkan tombol Kembali dan tombol Print secara mutlak */
            .no-print {
                display: none !important;
            }

            /* Menghilangkan efek glassmorphism agar rapi di kertas */
            .invoice-box {
                border: none !important;
                background: white !important;
                backdrop-filter: none !important;
                box-shadow: none !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
            }

            /* Memastikan teks berwarna (amber/emerald) tercetak hitam agar kontras */
            .text-amber-400,
            .text-emerald-400,
            .text-white,
            .font-black {
                color: black !important;
            }

            /* Memberikan border tipis pada tabel agar tidak melayang */
            .bg-white\/5 {
                background: transparent !important;
                border-bottom: 1px solid #eee !important;
            }

            /* Menyembunyikan dekorasi lingkaran (blob) saat print */
            .blur-3xl {
                display: none !important;
            }
        }
    </style>
    
</head>

<body class="p-4 md:p-10">

    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-8 no-print">
            <a href="index.php" class="text-xs font-bold text-gray-500 hover:text-white flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" />
                </svg> Kembali
            </a>
            <button onclick="window.print()" class="px-6 py-2 bg-amber-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-amber-500 transition-all shadow-lg shadow-amber-900/40">
                Print Invoice
            </button>
        </div>

        <div class="invoice-box rounded-[3rem] p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl"></div>

            <div class="flex justify-between items-start mb-12 relative z-10">
                <div>
                    <h1 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tighter uppercase">
                        Invoice
                    </h1>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.3em] mt-1">Official Purchase Receipt</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-black italic">BookStore</h2>
                    <p class="text-xs text-gray-500">ID Transaksi: #TX-<?= $data['id'] ?></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-12 border-b border-white/5 pb-12 text-sm">
                <div>
                    <p class="text-[9px] font-black uppercase text-amber-500 tracking-widest mb-2">Ditagihkan Kepada:</p>
                    <p class="font-bold text-white text-base"><?= htmlspecialchars($data['nama_pembeli']) ?></p>
                    <p class="text-gray-500 text-xs mt-1">Customer Pelanggan Setia</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-black uppercase text-emerald-500 tracking-widest mb-2">Nama Toko:</p>
                    <p class="font-bold text-white text-base"><?= htmlspecialchars($data['nama_toko']) ?></p>
                </div>
            </div>

            <div class="mb-12">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-500 border-b border-white/5">
                            <th class="py-4">Deskripsi Produk</th>
                            <th class="py-4 text-center">Jumlah</th>
                            <th class="py-4 text-right">Harga</th>
                            <th class="py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr class="text-sm">
                            <td class="py-6">
                                <p class="font-bold text-white">Pembelian Koleksi Buku</p>
                                <p class="text-[10px] text-gray-500 italic mt-1 uppercase">Terima kasih telah berbelanja</p>
                            </td>
                            <td class="py-6 text-center font-bold">1</td>
                            <td class="py-6 text-right text-gray-400">Rp <?= number_format($data['total'], 0, ',', '.') ?></td>
                            <td class="py-6 text-right font-black text-amber-400">Rp <?= number_format($data['total'], 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end relative z-10">
                <div class="w-full md:w-64 space-y-3">
                    <div class="flex justify-between text-xs text-gray-500 font-bold uppercase tracking-widest">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($data['total'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-xs text-emerald-500 font-bold uppercase tracking-widest">
                        <span>Pajak (0%)</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="h-[1px] bg-white/10 my-4"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Total Bayar</span>
                        <span class="text-2xl font-black italic text-white">Rp <?= number_format($data['total'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-20 pt-8 border-t border-white/5 text-center">

                <div class="flex justify-center mt-4">
                    <div class="px-4 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                        <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest">Status: Paid & Approved</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

</body>

</html>