<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('penjual');

$user_id = $_SESSION['user']['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

$message = "";
if (isset($_GET['success'])) $message = "✨ Data berhasil diperbarui!";
if (isset($_GET['error'])) $message = "❌ Terjadi kesalahan, coba lagi.";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Penjual | <?= APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .liquid-bg {
            position: fixed;
            z-index: -1;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #0f172a 0%, #020617 100%);
            overflow: hidden;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.15;
            animation: float 15s infinite alternate;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>

<body class="text-gray-100 antialiased overflow-x-hidden">

    <div class="liquid-bg">
        <div class="blob" style="top: -10%; right: -5%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: -10%; left: -5%; background: #10b981; animation-delay: -5s;"></div>
    </div>

    <div class="flex min-h-screen">
        <?php include '../../partials/sidebar-penjual.php'; ?>

        <main class="flex-1 p-6 md:p-10">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-amber-400 to-emerald-400 tracking-tight">
                        MY PROFILE
                    </h1>
                    <p class="text-gray-500 text-sm font-medium mt-1 uppercase tracking-widest">Kelola identitas & keamanan toko</p>
                </div>

                <?php if ($message): ?>
                    <div class="px-6 py-3 glass-card rounded-2xl text-emerald-400 text-xs font-black uppercase tracking-widest animate-bounce">
                        <?= $message ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <section class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 rounded-full blur-3xl group-hover:bg-amber-500/10 transition-all"></div>

                        <h2 class="text-sm font-black text-amber-500 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-amber-500/30"></span>
                            Informasi Dasar
                        </h2>

                        <form action="update_profil.php" method="POST" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block ml-1">Username</label>
                                    <input type="text" name="username" value="<?= $user['username'] ?>"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all shadow-inner" required>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block ml-1">Email Toko</label>
                                    <input type="email" name="email" value="<?= $user['email'] ?>"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all shadow-inner" required>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block ml-1">Alamat Domisili / Pengiriman</label>
                                <textarea name="alamat" rows="4"
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-amber-500/50 text-white transition-all shadow-inner resize-none"><?= $user['alamat'] ?></textarea>
                            </div>

                            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-amber-900/20 transition-all active:scale-95">
                                Simpan Perubahan
                            </button>
                        </form>
                    </section>

                    <section class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                        <h2 class="text-sm font-black text-emerald-500 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-emerald-500/30"></span>
                            Update Keamanan
                        </h2>

                        <form action="update_password.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                            <div class="md:col-span-1">
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block ml-1">Password Saat Ini</label>
                                <input type="password" name="old_password" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500/50 text-white transition-all shadow-inner" required>
                            </div>
                            <div class="md:col-span-1">
                                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block ml-1">Password Baru</label>
                                <input type="password" name="new_password" class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 focus:outline-none focus:border-emerald-500/50 text-white transition-all shadow-inner" required>
                            </div>
                            <button type="submit" class="py-4 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] transition-all">
                                Ganti Password
                            </button>
                        </form>
                    </section>
                </div>

                <div class="lg:col-span-1">
                    <section class="glass-card p-8 rounded-[2.5rem] flex flex-col items-center text-center sticky top-10">
                        <h2 class="text-sm font-black text-emerald-400 uppercase tracking-[0.3em] mb-8 self-start">Metode Bayar</h2>

                        <div class="relative group w-full aspect-square max-w-[250px] mb-8">
                            <div class="absolute inset-0 bg-gradient-to-tr from-amber-500 to-emerald-500 rounded-3xl blur opacity-20 group-hover:opacity-40 transition-duration-500"></div>
                            <div class="relative h-full w-full glass-card rounded-3xl overflow-hidden border-2 border-dashed border-white/10 flex items-center justify-center p-4">
                                <?php if ($user['foto_qris']): ?>
                                    <img src="<?= BASE_URL ?>/public/uploads/qris/<?= $user['foto_qris'] ?>" class="w-full h-full object-contain rounded-xl">
                                <?php else: ?>
                                    <div class="text-gray-600">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        <p class="text-[10px] font-black uppercase tracking-tighter opacity-40">No QRIS Detected</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <form action="../update_qris.php" method="POST" enctype="multipart/form-data" class="w-full">
                            <input type="file" name="qris" class="hidden" id="qrisInput" onchange="this.form.submit()">
                            <button type="button" onclick="document.getElementById('qrisInput').click()"
                                class="w-full py-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-emerald-500 hover:text-white transition-all duration-500 shadow-lg shadow-emerald-900/20">
                                Upload QRIS Baru
                            </button>
                            <p class="text-[9px] text-gray-500 mt-4 uppercase font-bold tracking-widest italic opacity-50">Format: JPG, PNG • Max: 2MB</p>
                        </form>
                    </section>
                </div>

            </div>
        </main>
    </div>

</body>

</html>