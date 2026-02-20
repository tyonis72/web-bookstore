<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('penjual');

$penjual_id = $_SESSION['user']['id'];

/* =========================
   AMBIL PRODUK PENJUAL (Lengkap dengan Modal & Margin)
========================= */
$produk = mysqli_query(
    $conn,
    "SELECT 
        p.*, 
        k.nama AS nama_kategori
     FROM produk p
     LEFT JOIN kategori k ON p.kategori_id = k.id
     WHERE p.penjual_id='$penjual_id'
     ORDER BY p.id DESC"
);

/* =========================
   AMBIL KATEGORI
========================= */
$kategori = mysqli_query(
    $conn,
    "SELECT id, nama FROM kategori ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Produk Saya - Liquid Glass Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 30px); }
            100% { transform: translate(0, 0); }
        }

        body {
            background: radial-gradient(circle at top left, #0f172a 0%, #020617 100%);
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .blob {
            position: fixed; width: 600px; height: 600px; filter: blur(90px);
            border-radius: 50%; opacity: 0.1; animation: float 15s infinite alternate; z-index: -1;
        }
    </style>
</head>

<body class="text-gray-100 antialiased">

    <div class="blob" style="top: 10%; right: 15%; background: #f59e0b;"></div>
    <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar-penjual.php'; ?>

        <main class="flex-1 p-8 lg:p-12">

            <div class="flex justify-between items-end mb-10">
                <div>
                    <h1 class="text-4xl font-black tracking-tighter italic uppercase">
                        Katalog <span class="text-amber-400">Produk</span>
                    </h1>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-[0.3em] mt-2">Inventory Management System</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-2xl shadow-xl shadow-amber-900/20 transition-all font-bold flex items-center gap-3 uppercase text-xs tracking-widest">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Tambah Koleksi
                </button>
            </div>

            <div class="max-w-4xl">
                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-6 p-4 glass-card border-red-500/30 text-red-400 rounded-2xl flex items-center gap-3 text-sm font-bold">
                        <span>⚠️</span>
                        <span><?= $_GET['error'] === 'upload_gagal' ? 'Gagal: Cek izin folder uploads/produk' : 'Gagal memproses data.' ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="mb-6 p-4 glass-card border-emerald-500/30 text-emerald-400 rounded-2xl flex items-center gap-3 text-sm font-bold">
                        <span>✅</span>
                        <span>Aksi berhasil diselesaikan.</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5 text-gray-500 text-[10px] uppercase font-black tracking-[0.2em]">
                            <th class="p-6">Info Produk</th>
                            <th class="p-6">Harga Jual</th>
                            <th class="p-6">Profit/Margin</th>
                            <th class="p-6 text-center">Stok</th>
                            <th class="p-6 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="p-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-20 rounded-xl overflow-hidden bg-black/40 border border-white/10 group-hover:scale-105 transition-transform">
                                            <?php if ($row['foto']): ?>
                                                <img src="<?= BASE_URL ?>/public/uploads/produk/<?= htmlspecialchars($row['foto']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-600 italic">No Pic</div>
                                            <?php endif ?>
                                        </div>
                                        <div>
                                            <p class="font-black text-emerald-50 tracking-tight leading-none mb-1 uppercase italic"><?= htmlspecialchars($row['nama']) ?></p>
                                            <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest"><?= $row['nama_kategori'] ?? 'Tanpa Kategori' ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="text-amber-400 font-black text-sm tracking-tighter">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                                </td>
                                <td class="p-6">
                                    <div class="flex flex-col">
                                        <span class="text-emerald-400 font-bold text-sm">Rp <?= number_format($row['margin'], 0, ',', '.') ?></span>
                                        <span class="text-[9px] text-gray-500 uppercase font-black">Mdl: <?= number_format($row['modal'], 0, ',', '.') ?></span>
                                    </div>
                                </td>
                                <td class="p-6 text-center">
                                    <span class="px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?= $row['stok'] > 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' ?>">
                                        <?= $row['stok'] ?> Unit
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button onclick='openEditModal(<?= json_encode($row) ?>)'
                                            class="p-3 glass-card hover:bg-emerald-500/20 text-gray-400 hover:text-emerald-400 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <?php if ($row['stok'] == 0): ?>
                                            <a href="<?= BASE_URL ?>/controllers/ProdukController.php?delete=<?= $row['id'] ?>"
                                                onclick="return confirm('Hapus permanen?')"
                                                class="p-3 glass-card hover:bg-red-500/20 text-gray-400 hover:text-red-400 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-xl rounded-[3rem] p-10 relative">
            <h2 id="modalTitle" class="text-2xl font-black mb-8 text-amber-400 tracking-tighter uppercase italic"></h2>

            <form action="<?= BASE_URL ?>/controllers/ProdukController.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="id" id="id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Nama Produk</label>
                        <input name="nama" id="nama" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-white font-bold" placeholder="Contoh: Dilan 1990" required>
                    </div>

                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Harga Jual</label>
                        <input name="harga" id="harga" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-amber-400 font-mono font-bold" placeholder="0" required>
                    </div>

                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Stok Unit</label>
                        <input name="stok" id="stok" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-white font-bold" placeholder="0" required>
                    </div>

                    <div>
                        <label class="text-[10px] text-amber-500/50 uppercase font-black tracking-widest mb-2 block ml-1">Harga Modal</label>
                        <input name="modal" id="modal_input" type="number" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-white font-mono" placeholder="0">
                    </div>

                    <div>
                        <label class="text-[10px] text-emerald-500/50 uppercase font-black tracking-widest mb-2 block ml-1">Margin Profit</label>
                        <input name="margin" id="margin" type="number" class="w-full bg-white/10 border border-white/10 rounded-2xl p-4 text-emerald-400 font-mono font-black" readonly placeholder="Otomatis">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Kategori</label>
                    <select name="kategori_id" id="kategori_id" class="w-full bg-[#020617] border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-white font-bold appearance-none cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        <?php mysqli_data_seek($kategori, 0); while ($k = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama']) ?></option>
                        <?php endwhile ?>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500 text-white text-sm leading-relaxed" placeholder="Tuliskan sinopsis atau detail buku..."></textarea>
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Cover Produk</label>
                    <input type="file" name="foto" class="text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-amber-500/10 file:text-amber-500 hover:file:bg-amber-500/20 cursor-pointer">
                </div>

                <div class="flex justify-end gap-4 pt-6">
                    <button type="button" onclick="closeModal()" class="px-8 py-3 bg-white/5 hover:bg-white/10 rounded-2xl transition-all text-xs font-black uppercase tracking-widest border border-white/10">Batal</button>
                    <button name="save" class="px-8 py-3 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white rounded-2xl shadow-2xl transition-all text-xs font-black uppercase tracking-widest">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const id = document.getElementById('id');
        const nama = document.getElementById('nama');
        const harga = document.getElementById('harga');
        const stok = document.getElementById('stok');
        const kategori_id = document.getElementById('kategori_id');
        const modal_input = document.getElementById('modal_input');
        const margin = document.getElementById('margin');
        const deskripsi = document.getElementById('deskripsi');

        // Fungsi Hitung Margin Otomatis
        function calculateMargin() {
            const valHarga = parseFloat(harga.value) || 0;
            const valModal = parseFloat(modal_input.value) || 0;
            margin.value = valHarga - valModal;
        }

        harga.addEventListener('input', calculateMargin);
        modal_input.addEventListener('input', calculateMargin);

        function openAddModal() {
            modalTitle.innerText = 'Tambah Koleksi Baru';
            modal.querySelector('form').reset();
            id.value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(data) {
            modalTitle.innerText = 'Modifikasi Data Produk';
            id.value = data.id;
            nama.value = data.nama;
            harga.value = data.harga;
            stok.value = data.stok;
            kategori_id.value = data.kategori_id;
            modal_input.value = data.modal;
            margin.value = data.margin;
            deskripsi.value = data.deskripsi;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        window.onclick = (e) => { if (e.target == modal) closeModal(); }
    </script>
</body>
</html>