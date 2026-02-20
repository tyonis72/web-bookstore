<?php
require_once '../config/database.php';
require_once '../config/session.php';

$room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
$my_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

// Ambil pesan yang dikirim oleh LAWAN (bukan saya) dan BELUM saya baca
$sql = "SELECT * FROM chat_pesan 
        WHERE chat_room_id = '$room_id' 
        AND pengirim_id != '$my_id' 
        AND is_read = 0";

$result = mysqli_query($conn, $sql);
$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
    // Tandai pesan ini sudah dibaca oleh SAYA
    mysqli_query($conn, "UPDATE chat_pesan SET is_read = 1 WHERE id = '{$row['id']}'");
}

header('Content-Type: application/json');
echo json_encode($messages);