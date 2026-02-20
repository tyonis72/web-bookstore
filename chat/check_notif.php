<?php
require_once '../config/database.php';
require_once '../config/session.php';

$my_id = $_SESSION['user_id'];

// Menghitung total pesan yang belum dibaca (is_read = 0) 
// dan pengirimnya bukan saya (my_id)
$sql = "SELECT COUNT(*) as unread FROM chat_pesan cp
        JOIN chat_room cr ON cp.chat_room_id = cr.id
        WHERE (cr.pembeli_id = '$my_id' OR cr.penjual_id = '$my_id')
        AND cp.pengirim_id != '$my_id'
        AND cp.is_read = 0";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode(['unread' => (int)$data['unread']]);