<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

// Ambil Room ID dari URL
$room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
$my_id = $_SESSION['user']['id']; // Pastikan sesuai struktur session Anda

// Ambil info lawan bicara untuk Header
$sql_user = "SELECT u.username, u.role FROM chat_room cr 
             JOIN users u ON (u.id = cr.pembeli_id OR u.id = cr.penjual_id) 
             WHERE cr.id = '$room_id' AND u.id != '$my_id'";
$res_user = mysqli_query($conn, $sql_user);
$lawan = mysqli_fetch_assoc($res_user);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Chat with <?= $lawan['username'] ?> | BookStore</title>
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

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.2);
            border-radius: 10px;
        }
    </style>
</head>

<body class="flex text-emerald-50">

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../partials/sidebar-pembeli.php'; ?>
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
                        <?= htmlspecialchars($lawan['username']) ?></h2>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <p class="text-[9px] text-emerald-500/50 font-bold uppercase tracking-widest">
                            <?= $lawan['role'] ?> • Online</p>
                    </div>
                </div>
            </div>
            <a href="index.php" class="bg-white/5 hover:bg-white/10 p-3 rounded-xl transition-all">
                <svg class="w-5 h-5 text-emerald-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" />
                </svg>
            </a>
        </div>

        <div id="chat-box" class="flex-1 overflow-y-auto custom-scrollbar space-y-4 px-4 pb-6 flex flex-col">
        </div>

        <div class="mt-4 relative group">
            <form id="chatForm">
                <input type="text" id="msgInput"
                    class="w-full bg-white/5 border border-white/10 p-5 pr-36 rounded-[2rem] text-sm text-white outline-none focus:border-emerald-500/50 focus:bg-white/10 transition-all placeholder:text-white/10 shadow-2xl"
                    placeholder="Tulis pesan untuk penjual..." autocomplete="off">

                <button type="submit"
                    class="absolute right-2 top-2 bottom-2 bg-emerald-600 hover:bg-emerald-500 text-white px-10 rounded-full font-black italic uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg shadow-emerald-900/40">
                    Kirim
                </button>
            </form>
        </div>

    </main>

    <script>
        const chatBox = document.getElementById('chat-box');
        const roomID = "<?= $room_id ?>";
        const myID = "<?= $my_id ?>";

        // --- A. LOAD HISTORY ---
        function loadHistory() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_history.php?room_id=" + roomID, true);
            xhr.onload = function () {
                if (this.status == 200) {
                    const msgs = JSON.parse(this.responseText);
                    chatBox.innerHTML = '';
                    msgs.forEach(m => {
                        appendMessage(m.pesan, (m.pengirim_id == myID ? 'sent' : 'received'));
                    });
                }
            };
            xhr.send();
        }

        // --- B. KIRIM PESAN ---
        document.getElementById('chatForm').onsubmit = function (e) {
            e.preventDefault();
            const input = document.getElementById('msgInput');
            const msg = input.value.trim();
            if (!msg) return;

            // Optimistic Update (Tampilkan dulu di layar)
            appendMessage(msg, 'sent');
            input.value = "";

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_pesan.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("room_id=" + roomID + "&pesan=" + encodeURIComponent(msg));
        };

        // --- C. POLLING PESAN BARU ---
        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_pesan.php?room_id=" + roomID, true);
            xhr.onload = function () {
                if (this.status == 200) {
                    const msgs = JSON.parse(this.responseText);
                    msgs.forEach(m => {
                        appendMessage(m.pesan, 'received');
                    });
                }
            };
            xhr.send();
        }

        setInterval(fetchMessages, 2000);

        // --- D. RENDER PESAN ---
        function appendMessage(text, type) {
            const div = document.createElement('div');
            div.className = `flex ${type === 'sent' ? 'justify-end' : 'justify-start'} animate-in fade-in slide-in-from-bottom-2 duration-300`;

            const bgColor = (type === 'sent')
                ? 'bg-emerald-600 text-white rounded-tr-none shadow-lg shadow-emerald-900/20'
                : 'glass-card text-emerald-100 rounded-tl-none border-emerald-500/10';

            div.innerHTML = `
                <div class="p-4 px-6 rounded-[2rem] max-w-[75%] text-sm font-medium shadow-sm ${bgColor}">
                    ${escapeHtml(text)}
                </div>
            `;

            chatBox.appendChild(div);
            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        window.onload = loadHistory;
    </script>

</body>

</html>