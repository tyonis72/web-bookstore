<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

// Pastikan hanya pembeli yang bisa akses
check_role('pembeli');

$room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
$my_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

// Ambil info Penjual (Lawan Bicara)
$sql_user = "SELECT u.username FROM chat_room cr 
             JOIN users u ON u.id = cr.penjual_id 
             WHERE cr.id = '$room_id' AND cr.pembeli_id = '$my_id'";
$res_user = mysqli_query($conn, $sql_user);
$lawan = mysqli_fetch_assoc($res_user);

// Jika room tidak ditemukan atau bukan milik pembeli ini, arahkan ke katalog
if (!$lawan) {
    header("Location: ../produk/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Chat: <?= htmlspecialchars($lawan['username']) ?> | Glass Amber Edition</title>
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
            overflow: hidden;
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
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.2);
            border-radius: 10px;
        }

        .msg-sent {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.3);
        }

        .msg-received {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="flex antialiased">

    <div class="liquid-bg">
        <div class="blob" style="top: 15%; right: 15%; background: #f59e0b;"></div>
        <div class="blob" style="bottom: 15%; left: 10%; animation-delay: -5s; background: #10b981;"></div>
    </div>

    <aside class="fixed inset-y-0 left-0 z-50">
        <?php include '../../partials/sidebar-pembeli.php'; ?>
    </aside>

    <main class="flex-1 ml-64 h-screen flex flex-col p-6 lg:p-10 relative">

        <div class="glass-card rounded-[2.5rem] p-6 mb-8 flex items-center justify-between px-10 shadow-2xl border-l-4 border-l-amber-500">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-800 to-gray-950 rounded-2xl flex items-center justify-center text-amber-500 font-black border border-white/10 shadow-inner uppercase italic text-xl">
                        <?= substr($lawan['username'], 0, 1) ?>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-4 border-[#0a1122] rounded-full"></div>
                </div>
                <div>
                    <h2 class="text-xl font-black italic uppercase tracking-tighter text-white">
                        <?= htmlspecialchars($lawan['username']) ?>
                    </h2>
                    <p class="text-[9px] text-amber-500/60 font-black uppercase tracking-[0.2em]">Verified Merchant Hub</p>
                </div>
            </div>
            <a href="../produk/index.php"
                class="px-6 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-amber-400 hover:bg-white/10 transition-all">
                Close Chat
            </a>
        </div>

        <div id="chat-box" class="flex-1 overflow-y-auto custom-scrollbar space-y-6 px-4 pb-8 flex flex-col">
        </div>

        <div class="mt-6 relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-emerald-500 rounded-[2.5rem] blur opacity-10 group-focus-within:opacity-25 transition duration-500"></div>
            <form id="chatForm" class="relative">
                <input type="text" id="msgInput"
                    class="w-full bg-[#0a1122]/80 backdrop-blur-xl border border-white/10 p-6 pr-44 rounded-[2.5rem] text-sm text-white outline-none focus:border-amber-500/50 transition-all placeholder:text-gray-600 font-medium italic shadow-2xl"
                    placeholder="Type your message to the merchant..." autocomplete="off">

                <button type="submit"
                    class="absolute right-3 top-3 bottom-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white px-12 rounded-full font-black italic uppercase text-[11px] tracking-widest transition-all active:scale-95 shadow-xl shadow-amber-900/20">
                    Send
                </button>
            </form>
        </div>

    </main>

    <script>
        const chatBox = document.getElementById('chat-box');
        const roomID = "<?= $room_id ?>";
        const myID = "<?= $my_id ?>";
        let displayedMessageIds = new Set();
        const apiPath = "../../chat/";

        function scrollToBottom() {
            chatBox.scrollTo({
                top: chatBox.scrollHeight,
                behavior: 'smooth'
            });
        }

        function appendMessage(text, type, messageId = null) {
            if (messageId && displayedMessageIds.has(messageId)) return;
            if (messageId) displayedMessageIds.add(messageId);

            const div = document.createElement('div');
            div.className = `flex ${type === 'sent' ? 'justify-end' : 'justify-start'} mb-2 animate-in fade-in slide-in-from-bottom-2 duration-500`;

            const style = type === 'sent' ?
                'msg-sent text-white rounded-[1.5rem] rounded-tr-none' :
                'msg-received text-gray-200 rounded-[1.5rem] rounded-tl-none backdrop-blur-md';

            div.innerHTML = `
                <div class="flex flex-col ${type === 'sent' ? 'items-end' : 'items-start'}">
                    <div class="p-4 px-6 max-w-[85%] text-[13px] font-medium leading-relaxed ${style}">
                        ${text}
                    </div>
                </div>`;

            chatBox.appendChild(div);
            scrollToBottom();
        }

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

        document.getElementById('chatForm').onsubmit = function(e) {
            e.preventDefault();
            const input = document.getElementById('msgInput');
            const msg = input.value.trim();
            if (!msg) return;

            appendMessage(msg, 'sent');
            input.value = "";

            const params = new URLSearchParams();
            params.append('room_id', roomID);
            params.append('pesan', msg);

            fetch(`${apiPath}send_pesan.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });
        };

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

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeIn 0.4s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
    </style>
</body>

</html>