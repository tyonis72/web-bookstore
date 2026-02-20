<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

// Periksa apakah parameter id_item ada
if (!isset($_GET['id_item'])) {
    header('Location: index.php');
    exit;
}

$id_item = $_GET['id_item'];
$pembeli_id = $_SESSION['user']['id']; // Assuming you have the pembeli_id in session

// Hapus item dari keranjang di database
$query = "DELETE FROM keranjang WHERE id = '$id_item' AND pembeli_id = '$pembeli_id'";
mysqli_query($conn, $query);

// Redirect kembali ke halaman keranjang
header('Location: index.php');
exit;
?>