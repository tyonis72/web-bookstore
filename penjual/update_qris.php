<?php
require_once '../config/database.php';
require_once '../config/session.php';

if ($_FILES['qris']['name']) {
    $user_id = $_SESSION['user']['id'];
    $nama_file = time() . '_' . $_FILES['qris']['name'];
    $tmp_file = $_FILES['qris']['tmp_name'];
    $path = "../public/uploads/qris/" . $nama_file;

    if (move_uploaded_file($tmp_file, $path)) {
        // Hapus foto lama jika ada
        $old_query = mysqli_query($conn, "SELECT foto_qris FROM users WHERE id = '$user_id'");
        $old_data = mysqli_fetch_assoc($old_query);
        if ($old_data['foto_qris']) unlink("../public/uploads/qris/" . $old_data['foto_qris']);

        mysqli_query($conn, "UPDATE users SET foto_qris = '$nama_file' WHERE id = '$user_id'");
        header("Location: profile/profile.php?success=1");
    } else {
        header("Location: profil.php?error=1");
    }
}