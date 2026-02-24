<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('superadmin');

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Classification | Liquid Glass</title>
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
            background: radial-gradient(circle at top right, #1e1b4b 0%, #020617 100%);
            background-attachment: fixed;
            color: white;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .blob {
            position: fixed;
            width: 500px;
            height: 500px;
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s infinite alternate;
            z-index: -1;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
    </style>
</head>

<body class="antialiased overflow-x-hidden">

    <div class="blob" style="top: 15%; right: 10%; background: #10b981;"></div>
    <div class="blob" style="bottom: 10%; left: 5%; background: #4f46e5;"></div>

    <div class="flex min-h-screen">
        <?php include __DIR__ . '../../../partials/sidebar-superadmin.php'; ?>

        <main class="flex-1 p-8 lg:p-12">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h1 class="text-4xl font-black tracking-tighter italic uppercase">
                        Book <span class="text-emerald-400">Categories</span>
                    </h1>
                    <p class="text-indigo-300/50 text-xs font-bold uppercase tracking-[0.3em] mt-2">Klasifikasi Data Katalog Utama</p>
                </div>
                <button onclick="openAddModal()"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl shadow-xl shadow-emerald-900/20 transition-all font-black text-[10px] uppercase tracking-widest flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    New Category
                </button>
            </div>

            <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
                <div class="mb-8 animate-in fade-in slide-in-from-top-4 duration-500">
                    <?php
                    $msg = "";
                    $color = "";
                    $icon = "";

                    if (isset($_GET['error'])) {
                        $color = "red";
                        $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';

                        switch ($_GET['error']) {
                            case 'duplicate':
                                $msg = "Gagal: Nama kategori sudah digunakan dalam sistem.";
                                break;
                            case 'kategori_dipakai':
                                $msg = "Akses Ditolak: Kategori tidak bisa dihapus karena masih terhubung dengan produk.";
                                break;
                            default:
                                $msg = "Terjadi kesalahan sistem.";
                        }
                    } else {
                        $color = "emerald";
                        $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

                        switch ($_GET['success']) {
                            case 'add':
                                $msg = "Kategori baru berhasil ditambahkan.";
                                break;
                            case 'edit':
                                $msg = "Label kategori berhasil diperbarui.";
                                break;
                            case 'delete':
                                $msg = "Data kategori telah berhasil dihapus.";
                                break;
                        }
                    }
                    ?>
                    <div class="bg-<?= $color ?>-500/10 border border-<?= $color ?>-500/20 text-<?= $color ?>-400 px-6 py-4 rounded-2xl flex items-center gap-4">
                        <div class="p-2 bg-<?= $color ?>-500/20 rounded-lg"><?= $icon ?></div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">System Message</span>
                            <span class="text-xs font-bold uppercase tracking-widest"><?= $msg ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5 text-gray-500 text-[10px] uppercase font-black tracking-[0.2em]">
                            <th class="p-6 w-20">Rank</th>
                            <th class="p-6">Label / Nama Kategori</th>
                            <th class="p-6 text-right">Aksi Strategis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <tr class="hover:bg-white/5 transition-all group">
                                <td class="p-6">
                                    <span class="text-indigo-400 font-mono font-bold">#<?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td class="p-6 text-white font-black italic uppercase tracking-wider">
                                    <?= htmlspecialchars($row['nama']) ?>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button onclick="openEditModal('<?= $row['id'] ?>','<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>')"
                                            class="p-3 glass-card hover:bg-indigo-500/20 text-gray-400 hover:text-indigo-400 rounded-xl transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <a href="../../controllers/KategoriController.php?delete=<?= $row['id'] ?>"
                                            onclick="return confirm('Hapus kategori ini?')"
                                            class="p-3 glass-card hover:bg-red-500/20 text-gray-400 hover:text-red-400 rounded-xl transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modalBackdrop" class="hidden fixed inset-0 bg-black/90 backdrop-blur-md z-[60] flex items-center justify-center p-4">

        <div id="addModal" class="hidden glass-card w-full max-w-md rounded-[3rem] p-10 relative">
            <h2 class="text-2xl font-black mb-8 text-emerald-400 tracking-tighter uppercase italic">Add Category</h2>
            <form action="../../controllers/KategoriController.php" method="POST" class="space-y-6">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Kategori Baru</label>
                    <input type="text" name="nama" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500 text-white font-bold transition-all" placeholder="Contoh: Fiksi, Sains, Sejarah">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-4 bg-white/5 hover:bg-white/10 text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Abort</button>
                    <button type="submit" name="add" class="flex-2 px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl shadow-xl transition-all text-[10px] font-black uppercase tracking-widest">Execute Save</button>
                </div>
            </form>
        </div>

        <div id="editModal" class="hidden glass-card w-full max-w-md rounded-[3rem] p-10 relative">
            <h2 class="text-2xl font-black mb-8 text-indigo-400 tracking-tighter uppercase italic">Edit Label</h2>
            <form action="../../controllers/KategoriController.php" method="POST" class="space-y-6">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Nama Klasifikasi</label>
                    <input type="text" name="nama" id="editNama" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-indigo-500 text-white font-bold transition-all">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-4 bg-white/5 hover:bg-white/10 text-gray-400 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Cancel</button>
                    <button type="submit" name="edit" class="flex-2 px-10 py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl shadow-xl transition-all text-[10px] font-black uppercase tracking-widest">Update Label</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const backdrop = document.getElementById('modalBackdrop');
        const mAdd = document.getElementById('addModal');
        const mEdit = document.getElementById('editModal');

        function openAddModal() {
            backdrop.classList.remove('hidden');
            mAdd.classList.remove('hidden');
        }

        function openEditModal(id, nama) {
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            backdrop.classList.remove('hidden');
            mEdit.classList.remove('hidden');
        }

        function closeModal() {
            backdrop.classList.add('hidden');
            mAdd.classList.add('hidden');
            mEdit.classList.add('hidden');
        }

        backdrop.onclick = (e) => {
            if (e.target === backdrop) closeModal();
        }
    </script>
</body>

</html>