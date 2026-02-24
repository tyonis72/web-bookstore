<?php
// Gunakan path yang benar menuju config
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

// Tambahkan ini untuk melihat error jika masih macet
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// --- LOGIKA PEMBELI: MENGAJUKAN REFUND ---
if (isset($_POST['ajukan_refund'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);

    $sql = "UPDATE transaksi SET status = 'pending_refund', alasan_refund = '$alasan' WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Permintaan refund dikirim!'); window.location.href = '../pembeli/pesanan/detail.php?id=$id';</script>";
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

// --- LOGIKA PENJUAL: MENYETUJUI REFUND ---
if (isset($_POST['approve_refund'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_transaksi']);

    $sql = "UPDATE transaksi SET status = 'refunded' WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Refund berhasil disetujui!'); window.location.href = '../penjual/laporan/index.php';</script>";
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

// --- LOGIKA PENJUAL: MENOLAK REFUND ---
if (isset($_POST['reject_refund'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $alasan_tolak = mysqli_real_escape_string($conn, $_POST['alasan_tolak']);

    // Status dikembalikan ke 'approve' (transaksi dianggap selesai/sukses kembali)
    $sql = "UPDATE transaksi SET status = 'approve', alasan_penolakan_penjual = '$alasan_tolak' WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Refund ditolak.'); window.location.href = '../penjual/laporan/index.php';</script>";
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

