<?php
ob_start();
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

$my_id = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;
$penjual_id = mysqli_real_escape_string($conn, $_GET['penjual_id'] ?? '');

if (!$my_id || empty($penjual_id)) {
    die("Error: Session atau ID Penjual tidak valid.");
}

// 1. Cek apakah room sudah ada
$check = mysqli_query($conn, "SELECT id FROM chat_room 
    WHERE (pembeli_id = '$my_id' AND penjual_id = '$penjual_id') 
    OR (pembeli_id = '$penjual_id' AND penjual_id = '$my_id') LIMIT 1");

if (mysqli_num_rows($check) > 0) {
    $room = mysqli_fetch_assoc($check);
    $room_id = $room['id'];
} else {
    // KODE PERBAIKAN: Jangan masukkan ID manual, biarkan AUTO_INCREMENT bekerja
    $query = "INSERT INTO chat_room (pembeli_id, penjual_id) VALUES ('$my_id', '$penjual_id')";
    
    if (mysqli_query($conn, $query)) {
        // Ambil ID angka yang baru saja dibuat oleh database
        $room_id = mysqli_insert_id($conn);
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}

// 2. Redirect
header("Location: room.php?room_id=" . $room_id);
exit;