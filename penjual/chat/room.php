<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

// Cek Role Penjual
check_role('penjual');

// Ambil Room ID dari URL
$room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
$my_id = $_SESSION['user']['id']; // Sesuaikan dengan struktur session login Anda

// Ambil info Pembeli (Lawan Bicara) untuk Header
$sql_user = "SELECT u.username FROM chat_room cr 
             JOIN users u ON u.id = cr.pembeli_id 
             WHERE cr.id = '$room_id'";
$res_user = mysqli_query($conn, $sql_user);
$lawan = mysqli_fetch_assoc($res_user);

// Jika room tidak ditemukan, tendang balik
if (!$lawan) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Chat dengan <?= $lawan['username'] ?> | Seller Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top right, #064e3b 0%, #020617 100%);
            min-height: 100vh;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.2);
            border-radius: 10px;
        }
    </style>
</head>

<body class="flex text-emerald-50">

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../../partials/sidebar-penjual.php'; ?>
    </aside>

    <main class="flex-1 ml-64 h-screen flex flex-col p-6 lg:p-10">

        <div class="glass-card rounded-[2rem] p-5 mb-6 flex items-center justify-between px-8 shadow-2xl">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-400 font-black border border-emerald-500/20 shadow-inner uppercase">
                    <?= substr($lawan['username'], 0, 1) ?>
                </div>
                <div>
                    <h2 class="font-black italic uppercase tracking-tight text-white">
                        <?= htmlspecialchars($lawan['username']) ?>
                    </h2>
                    <p class="text-[9px] text-emerald-500/50 font-bold uppercase tracking-widest text-emerald-400">
                        Customer (Pembeli)</p>
                </div>
            </div>
            <a href="index.php"
                class="text-[10px] font-black uppercase text-emerald-500/40 hover:text-emerald-400">Kembali</a>
        </div>

        <div id="chat-box" class="flex-1 overflow-y-auto custom-scrollbar space-y-4 px-4 pb-6 flex flex-col">
        </div>

        <div class="mt-4 relative">
            <form id="chatForm">
                <input type="text" id="msgInput"
                    class="w-full bg-white/5 border border-white/10 p-5 pr-36 rounded-[2rem] text-sm text-white outline-none focus:border-emerald-500/50 transition-all placeholder:text-white/10 shadow-2xl"
                    placeholder="Balas pesan pembeli..." autocomplete="off">

                <button type="submit"
                    class="absolute right-2 top-2 bottom-2 bg-emerald-600 hover:bg-emerald-500 text-white px-10 rounded-full font-black italic uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg shadow-emerald-900/40">
                    Balas
                </button>
            </form>
        </div>

    </main>

    <script>
        const chatBox = document.getElementById('chat-box');
        const roomID = "<?= $room_id ?>";
        const myID = "<?= $my_id ?>";

        // Set untuk menyimpan ID pesan yang sudah muncul di layar (Mencegah Duplikat)
        let displayedMessageIds = new Set();

        // Pastikan path menyesuaikan folder (penjual = ../../ , pembeli = ../)
        // Jika kode ini di penjual/chat/room.php gunakan:
        const apiPath = "../../chat/";

        function scrollToBottom() {
            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
        }

        function appendMessage(text, type, messageId = null) {
            // CEK DUPLIKASI: Jika ID pesan sudah ada, jangan tampilkan lagi
            if (messageId && displayedMessageIds.has(messageId)) return;
            if (messageId) displayedMessageIds.add(messageId);

            const div = document.createElement('div');
            div.className = `flex ${type === 'sent' ? 'justify-end' : 'justify-start'} mb-4 animate-in fade-in`;

            const style = type === 'sent'
                ? 'bg-emerald-600 text-white rounded-2xl rounded-tr-none shadow-lg shadow-emerald-900/20'
                : 'glass-card text-emerald-100 rounded-2xl rounded-tl-none border border-emerald-500/10';

            div.innerHTML = `<div class="p-4 px-6 max-w-[75%] text-sm font-medium ${style}">${text}</div>`;
            chatBox.appendChild(div);
            scrollToBottom();
        }

        // AMBIL SEMUA CHAT SAAT REFRESH
        function loadHistory() {
            fetch(`${apiPath}get_history.php?room_id=${roomID}`)
                .then(res => res.json())
                .then(msgs => {
                    chatBox.innerHTML = '';
                    displayedMessageIds.clear();
                    msgs.forEach(m => {
                        const type = (m.pengirim_id == myID) ? 'sent' : 'received';
                        appendMessage(m.pesan, type, m.id);
                    });
                });
        }

        // KIRIM PESAN
        document.getElementById('chatForm').onsubmit = function (e) {
            e.preventDefault();
            const input = document.getElementById('msgInput');
            const msg = input.value.trim();
            if (!msg) return;

            // Optimistic UI (Langsung tampil tanpa ID dulu tidak apa-apa)
            appendMessage(msg, 'sent');
            input.value = "";

            const params = new URLSearchParams();
            params.append('room_id', roomID);
            params.append('pesan', msg);

            fetch(`${apiPath}send_pesan.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            });
        };

        // CEK PESAN BARU SETIAP 2 DETIK
        function fetchMessages() {
            fetch(`${apiPath}get_pesan.php?room_id=${roomID}`)
                .then(res => res.json())
                .then(msgs => {
                    msgs.forEach(m => {
                        appendMessage(m.pesan, 'received', m.id);
                    });
                });
        }

        window.onload = loadHistory;
        setInterval(fetchMessages, 2000);
    </script>

</body>

</html>