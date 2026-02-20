<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Ambil ID dari session (sesuaikan dengan cara Anda menyimpan session)
$my_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;

if (!$my_id) {
    die("Error: Session tidak ditemukan.");
}

$room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
$pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

if (!empty($pesan) && !empty($room_id)) {
    // Insert ke tabel pesan
    $sql = "INSERT INTO chat_pesan (chat_room_id, pengirim_id, pesan, created_at) 
            VALUES ('$room_id', '$my_id', '$pesan', NOW())";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}