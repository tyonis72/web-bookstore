<?php
require_once '../config/database.php';
require_once '../config/session.php';

$room_id = mysqli_real_escape_string($conn, $_GET['room_id']);
$my_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

// Ambil semua pesan dalam room ini, urutkan dari yang terlama
$sql = "SELECT * FROM chat_pesan WHERE chat_room_id = '$room_id' ORDER BY created_at ASC";
$result = mysqli_query($conn, $sql);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);