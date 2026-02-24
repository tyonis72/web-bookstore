<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('superadmin');

/* =====================
   TAMBAH PENJUAL
===================== */
if (isset($_POST['tambah_penjual'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. CEK DUPLIKAT EMAIL ATAU NIK
    $cek = mysqli_query($conn, "SELECT email, nik FROM users WHERE email='$email' OR nik='$nik' LIMIT 1");
    
    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $error = ($row['email'] == $email) ? 'email_exists' : 'nik_exists';
        header("Location: ../admin/superadmin/penjual.php?error=$error");
        exit;
    }

    // 2. INSERT DATA
    mysqli_query($conn, "INSERT INTO users (username, nik, email, alamat, password, role, status) 
                         VALUES ('$username', '$nik', '$email', '$alamat', '$password', 'penjual', 'offline')");

    header("Location: ../admin/superadmin/penjual.php?success=tambah");
    exit;
}

/* =====================
   EDIT PENJUAL
===================== */
if (isset($_POST['update_penjual'])) {
    $id       = (int) $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // 1. CEK DUPLIKAT EMAIL/NIK (KECUALI ID SENDIRI)
    $cek = mysqli_query($conn, "SELECT email, nik FROM users WHERE (email='$email' OR nik='$nik') AND id != '$id' LIMIT 1");

    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $error = ($row['email'] == $email) ? 'email_exists' : 'nik_exists';
        header("Location: ../admin/superadmin/penjual.php?error=$error");
        exit;
    }

    // 2. BANGUN QUERY UPDATE
    $query = "UPDATE users SET username='$username', nik='$nik', email='$email', alamat='$alamat'";
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password='$password'";
    }

    $query .= " WHERE id='$id' AND role='penjual'";

    mysqli_query($conn, $query);
    header("Location: ../admin/superadmin/penjual.php?success=edit");
    exit;
}

/* =====================
   HAPUS PENJUAL
===================== */
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    // 1. CEK STATUS USER (TIDAK BOLEH HAPUS JIKA ONLINE)
    $cekStatus = mysqli_query($conn, "SELECT status FROM users WHERE id='$id' AND role='penjual' LIMIT 1");
    $user = mysqli_fetch_assoc($cekStatus);

    if ($user && $user['status'] === 'online') {
        header("Location: ../admin/superadmin/penjual.php?error=active_user");
        exit;
    }

    // 2. EKSEKUSI HAPUS
    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND role='penjual'");
    header("Location: ../admin/superadmin/penjual.php?success=hapus");
    exit;
}