<?php
require_once '../../config/app.php';
require_once '../../config/session.php';
check_role('superadmin');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Support Center | Liquid Glass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0, 1, 0, 1);
        }

        .faq-toggle:checked~.faq-answer {
            max-height: 1000px;
            transition: all 0.5s cubic-bezier(1, 0, 1, 0);
        }

        .faq-toggle:checked~.faq-label {
            background: rgba(99, 102, 241, 0.1);
        }

        .faq-toggle:checked~.faq-label svg {
            transform: rotate(180deg);
            color: #818cf8;
        }
    </style>
</head>

<body class="antialiased">

    <div class="flex min-h-screen">
        <?php include __DIR__ . '../../../partials/sidebar-superadmin.php'; ?>

        <main class="flex-1 p-8 lg:p-12 flex flex-col items-center">
            <div class="mb-12 text-center">
                <h1 class="text-5xl font-black tracking-tighter italic uppercase">
                    PUSAT BANTUAN <span class="text-indigo-400">FAQ</span>
                </h1>
                <p class="text-indigo-300/50 text-[10px] font-bold uppercase tracking-[0.4em] mt-4">Panduan Teknis &
                    Operasional Platform BookStore</p>
            </div>

            <div class="w-full max-w-4xl space-y-6">

                <div class="glass-card rounded-[2rem] overflow-hidden transition-all duration-500">
                    <input type="checkbox" id="faq1" class="hidden faq-toggle">
                    <label for="faq1"
                        class="faq-label flex items-center justify-between p-8 cursor-pointer select-none transition-colors">
                        <div class="flex items-center gap-6">
                            <span class="text-indigo-500 font-black text-xl italic">01</span>
                            <span class="font-black text-sm uppercase tracking-widest text-gray-200">Manajemen Otoritas
                                Akun & Keamanan Data</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                    <div class="faq-answer px-12 pb-8">
                        <div class="pt-4 border-t border-white/5 space-y-4">
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Sebagai **Superadmin**, Anda memegang kendali penuh atas hierarki sistem. Fitur "Tambah
                                Penjual" dan "Tambah Pembeli" bukan sekadar pengisian formulir biasa, melainkan
                                pembuatan entitas digital dalam database Liquid Glass. Setiap akun yang dibuat secara
                                otomatis akan mendapatkan enkripsi pada sisi *password* menggunakan algoritma hash
                                modern.
                            </p>
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Anda bertanggung jawab memastikan bahwa alamat email yang digunakan adalah valid, karena
                                sistem akan menggunakan email tersebut sebagai kunci identifikasi utama jika terjadi
                                masalah autentikasi. Pastikan untuk selalu memeriksa duplikasi data sebelum mengeksekusi
                                perintah "Execute Save".
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2rem] overflow-hidden transition-all duration-500">
                    <input type="checkbox" id="faq2" class="hidden faq-toggle">
                    <label for="faq2"
                        class="faq-label flex items-center justify-between p-8 cursor-pointer select-none transition-colors">
                        <div class="flex items-center gap-6">
                            <span class="text-indigo-500 font-black text-xl italic">02</span>
                            <span class="font-black text-sm uppercase tracking-widest text-gray-200">Prosedur Verifikasi
                                NIK (Identity Validation)</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                    <div class="faq-answer px-12 pb-8">
                        <div class="pt-4 border-t border-white/5 space-y-4">
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Kolom **NIK (Nomor Induk Kependudukan)** pada panel Penjual dan Pembeli merupakan
                                instrumen krusial untuk mencegah akun anonim (*bot accounts*). Sistem telah dilengkapi
                                dengan validasi *Real-Time Filter* yang hanya mengizinkan karakter angka.
                            </p>
                            <div class="bg-indigo-500/5 p-4 rounded-xl border border-indigo-500/10">
                                <p class="text-[10px] text-indigo-300 font-bold uppercase mb-2">Penting untuk Diketahui:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-gray-400 space-y-1">
                                    <li>NIK harus tepat 16 digit sesuai standar KTP Nasional.</li>
                                    <li>Data NIK akan ditampilkan secara tersembunyi (masking) di beberapa bagian untuk
                                        keamanan.</li>
                                    <li>Perubahan NIK hanya dapat dilakukan melalui menu "Modify Member" oleh
                                        Superadmin.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2rem] overflow-hidden transition-all duration-500">
                    <input type="checkbox" id="faq3" class="hidden faq-toggle">
                    <label for="faq3"
                        class="faq-label flex items-center justify-between p-8 cursor-pointer select-none transition-colors">
                        <div class="flex items-center gap-6">
                            <span class="text-indigo-500 font-black text-xl italic">03</span>
                            <span class="font-black text-sm uppercase tracking-widest text-gray-200">Manajemen Kategori
                                & Taksonomi Produk</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                    <div class="faq-answer px-12 pb-8">
                        <div class="pt-4 border-t border-white/5 space-y-4">
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Fitur Klasifikasi Kategori dirancang menggunakan struktur data *A-Z Sorting*. Saat Anda
                                menambah kategori baru, sistem akan melakukan *indexing* ulang pada seluruh katalog buku
                                yang tersedia.
                            </p>
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                **Peringatan Penghapusan:** Jika Anda menghapus sebuah kategori yang masih memiliki
                                keterkaitan dengan buku yang aktif, sistem akan memberikan peringatan atau secara
                                otomatis mengosongkan label kategori pada produk tersebut. Disarankan untuk memindahkan
                                relasi produk terlebih dahulu sebelum menghapus kategori utama.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2rem] overflow-hidden transition-all duration-500">
                    <input type="checkbox" id="faq4" class="hidden faq-toggle">
                    <label for="faq4"
                        class="faq-label flex items-center justify-between p-8 cursor-pointer select-none transition-colors">
                        <div class="flex items-center gap-6">
                            <span class="text-indigo-500 font-black text-xl italic">04</span>
                            <span class="font-black text-sm uppercase tracking-widest text-gray-200">Indikator Aktivitas
                                & Status Sistem</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </label>
                    <div class="faq-answer px-12 pb-8">
                        <div class="pt-4 border-t border-white/5 space-y-4">
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Status **"Active/Online"** didasarkan pada *timestamp* aktivitas terakhir pengguna di
                                database. Jika pengguna tidak melakukan aktivitas selama jangka waktu tertentu (sesuai
                                pengaturan `session.php`), sistem akan secara otomatis mengubah status menjadi
                                **"Inactive/Offline"**.
                            </p>
                            <p class="text-xs text-gray-400 leading-[1.8] text-justify">
                                Sebagai Superadmin, Anda dapat melihat siapa saja yang sedang mengakses platform secara
                                *real-time* melalui kolom status di tabel database pelanggan dan penjual. Ini membantu
                                Anda dalam memonitor beban server dan lalu lintas data harian.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-16 text-center">
                <p class="text-[9px] text-gray-600 font-bold uppercase tracking-[0.3em]">Liquid Glass Framework v3.0 —
                    Documentation</p>
            </div>
        </main>
    </div>

</body>

</html>