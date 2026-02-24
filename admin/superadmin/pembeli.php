<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('superadmin');

// --- LOGIKA SEARCH & PAGINATION ---
$limit = 25;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $limit) - $limit : 0;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where_clause = "WHERE role='pembeli'";
if (!empty($search)) {
    $where_clause .= " AND (username LIKE '%$search%' OR email LIKE '%$search%' OR nik LIKE '%$search%')";
}

$total_data_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $where_clause");
$total_res = mysqli_fetch_assoc($total_data_query);
$total_data = $total_res['total'];
$total_halaman = ceil($total_data / $limit);

$pembeli = mysqli_query(
    $conn,
    "SELECT id, username, email, alamat, status, nik 
     FROM users
     $where_clause
     ORDER BY id DESC
     LIMIT $halaman_awal, $limit"
);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Management Pembeli | Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float { 0% { transform: translate(0, 0); } 50% { transform: translate(-20px, 30px); } 100% { transform: translate(0, 0); } }
        body { background: radial-gradient(circle at top right, #1e1b4b 0%, #020617 100%); background-attachment: fixed; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .blob { position: fixed; width: 500px; height: 500px; filter: blur(80px); border-radius: 50%; opacity: 0.1; animation: float 20s infinite alternate; z-index: -1; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    </style>
</head>

<body class="antialiased overflow-x-hidden">
    <div class="blob" style="top: 20%; right: 10%; background: #6366f1;"></div>
    <div class="blob" style="bottom: 5%; left: 15%; background: #0891b2;"></div>

    <div class="flex min-h-screen">
        <?php include __DIR__ . '../../../partials/sidebar-superadmin.php'; ?>

        <main class="flex-1 p-8 lg:p-12">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-6">
                <div>
                    <h1 class="text-4xl font-black tracking-tighter italic uppercase">
                        Customer <span class="text-indigo-400">Database</span>
                    </h1>
                    <p class="text-indigo-300/50 text-xs font-bold uppercase tracking-[0.3em] mt-2">Data Pelanggan Terdaftar</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <form action="" method="GET" class="relative group">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                            placeholder="Search Name, Email..." 
                            class="w-full sm:w-64 bg-white/5 border border-white/10 rounded-2xl py-3 px-5 pl-12 focus:outline-none focus:border-indigo-500 text-xs font-bold transition-all group-hover:bg-white/10 text-white">
                        <svg class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </form>

                    <button onclick="openTambah()"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl shadow-xl shadow-indigo-900/20 transition-all font-black text-[10px] uppercase tracking-widest flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                        Tambah Pembeli
                    </button>
                </div>
            </div>

            <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
            <div class="mb-6">
                <?php 
                $msg = ""; $color = "";
                if (isset($_GET['error'])) {
                    $color = "red";
                    switch ($_GET['error']) {
                        case 'email_exists': $msg = "Email sudah digunakan."; break;
                        case 'nik_exists':   $msg = "NIK sudah terdaftar."; break;
                        case 'active_user':  $msg = "Gagal! Pembeli sedang aktif/online."; break;
                        default: $msg = "Terjadi kesalahan.";
                    }
                } else {
                    $color = "emerald";
                    switch ($_GET['success']) {
                        case 'tambah': $msg = "Pembeli berhasil ditambahkan."; break;
                        case 'edit':   $msg = "Data pembeli diperbarui."; break;
                        case 'hapus':  $msg = "Data telah dihapus."; break;
                    }
                }
                ?>
                <div class="bg-<?= $color ?>-500/10 border border-<?= $color ?>-500/20 text-<?= $color ?>-400 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-<?= $color ?>-500 animate-pulse"></div>
                    <?= $msg ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5 text-gray-500 text-[10px] uppercase font-black tracking-[0.2em]">
                            <th class="p-6">User Profile</th>
                            <th class="p-6">NIK</th>
                            <th class="p-6">Account Email</th>
                            <th class="p-6 text-center">Status</th>
                            <th class="p-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if (mysqli_num_rows($pembeli) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($pembeli)): ?>
                                <tr class="hover:bg-white/5 transition-all group">
                                    <td class="p-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-black">
                                                <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="font-bold text-white uppercase italic tracking-tight"><?= htmlspecialchars($row['username']) ?></p>
                                                <p class="text-[9px] text-gray-500 font-bold tracking-widest uppercase">Member-<?= $row['id'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6 text-gray-400 font-mono text-xs"><?= $row['nik'] ?: '-' ?></td>
                                    <td class="p-6 text-indigo-300/70 font-mono text-sm"><?= htmlspecialchars($row['email']) ?></td>
                                    <td class="p-6 text-center">
                                        <span class="px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full <?= $row['status'] === 'online' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-white/5 text-gray-500' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <div class="flex justify-end gap-3">
                                            <button onclick='openDetail(<?= json_encode($row) ?>)' class="p-2.5 glass-card hover:bg-white/10 text-gray-400 hover:text-white rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </button>
                                            <button onclick='openEdit(<?= json_encode($row) ?>)' class="p-2.5 glass-card hover:bg-indigo-500/20 text-gray-400 hover:text-indigo-400 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                            <a href="<?= BASE_URL ?>/controllers/PembeliController.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus akun pembeli ini?')" class="p-2.5 glass-card hover:bg-red-500/20 text-gray-400 hover:text-red-400 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="p-20 text-center text-gray-500 font-black uppercase tracking-widest text-[10px] italic">Zero customers in records.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_halaman > 1): ?>
            <div class="mt-8 flex justify-center gap-2">
                <?php if ($halaman > 1): ?>
                    <a href="?halaman=<?= $halaman - 1 ?>&search=<?= $search ?>" class="px-4 py-2 glass-card rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 transition-all italic">Prev</a>
                <?php endif; ?>
                <?php for ($x = 1; $x <= $total_halaman; $x++): ?>
                    <a href="?halaman=<?= $x ?>&search=<?= $search ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-xl text-[10px] font-black transition-all <?= ($halaman == $x) ? 'bg-indigo-600 text-white' : 'glass-card text-gray-500 hover:text-white' ?>">
                        <?= $x ?>
                    </a>
                <?php endfor; ?>
                <?php if ($halaman < $total_halaman): ?>
                    <a href="?halaman=<?= $halaman + 1 ?>&search=<?= $search ?>" class="px-4 py-2 glass-card rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 transition-all italic">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <div id="modalUser" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" onclick="closeModal()"></div>
        <div class="glass-card w-full max-w-lg rounded-[2.5rem] p-10 relative z-10 shadow-2xl border border-white/20">
            <h2 id="modalTitle" class="text-3xl font-black italic uppercase mb-8 text-indigo-400 tracking-tighter">Edit Pembeli</h2>
            <form id="formUser" action="<?= BASE_URL ?>/controllers/PembeliController.php" method="POST" class="space-y-6 text-white">
                <input type="hidden" name="id" id="userId">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest ml-1 block mb-2">Username</label>
                        <input type="text" name="username" id="userName" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest ml-1 block mb-2">NIK</label>
                        <input type="text" name="nik" id="userNik" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest ml-1 block mb-2">Email Address</label>
                        <input type="email" name="email" id="userEmail" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest ml-1 block mb-2">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" id="userPassword" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm focus:outline-none focus:border-indigo-500 transition-all" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest ml-1 block mb-2">Alamat</label>
                        <textarea name="alamat" id="userAlamat" rows="3" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm focus:outline-none focus:border-indigo-500 transition-all"></textarea>
                    </div>
                </div>
                <div class="flex gap-4 mt-10">
                    <button type="button" onclick="closeModal()" class="flex-1 py-4 glass-card hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all text-white">Cancel</button>
                    <button type="submit" name="update_pembeli" id="btnSubmit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-500 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-900/40 text-white">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalUser');
        const modalTitle = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');
        const form = document.getElementById('formUser');

        function openEdit(data) {
            modalTitle.innerText = "Edit Customer Profile";
            btnSubmit.name = "update_pembeli";
            btnSubmit.style.display = "block";
            enableInputs(true);
            document.getElementById('userId').value = data.id;
            document.getElementById('userName').value = data.username;
            document.getElementById('userNik').value = data.nik || '';
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userAlamat').value = data.alamat;
            modal.classList.remove('hidden');
        }

        function openDetail(data) {
            modalTitle.innerText = "Customer Intel Detail";
            btnSubmit.style.display = "none";
            document.getElementById('userName').value = data.username;
            document.getElementById('userNik').value = data.nik || 'N/A';
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userAlamat').value = data.alamat;
            enableInputs(false);
            modal.classList.remove('hidden');
        }

        function openTambah() {
            modalTitle.innerText = "Add New Customer";
            form.reset();
            document.getElementById('userId').value = '';
            btnSubmit.name = "tambah_pembeli";
            btnSubmit.style.display = "block";
            enableInputs(true);
            modal.classList.remove('hidden');
        }

        function closeModal() { modal.classList.add('hidden'); }
        function enableInputs(status) {
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (status) { input.removeAttribute('readonly'); input.classList.remove('opacity-50'); }
                else { input.setAttribute('readonly', true); input.classList.add('opacity-50'); }
            });
        }
    </script>
</body>
</html>