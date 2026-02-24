<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('superadmin');

/* =====================
   TAMBAH PEMBELI
===================== */
// Disinkronkan dengan name="tambah_pembeli" dari Modal
if (isset($_POST['tambah_pembeli'])) {

    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // 1. CEK DUPLIKAT EMAIL ATAU NIK
    $cek = mysqli_query($conn, "SELECT email, nik FROM users WHERE email='$email' OR nik='$nik' LIMIT 1");
    
    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $error = ($row['email'] == $email) ? 'email_exists' : 'nik_exists';
        header("Location: ../admin/superadmin/pembeli.php?error=$error");
        exit;
    }

    // 2. INSERT DATA
    mysqli_query($conn, "INSERT INTO users (nik, username, email, alamat, password, role, status)
                         VALUES ('$nik', '$username', '$email', '$alamat', '$password', 'pembeli', 'offline')");

    header("Location: ../admin/superadmin/pembeli.php?success=tambah");
    exit;
}

/* =====================
   EDIT PEMBELI
===================== */
// Disinkronkan dengan name="update_pembeli" dari Modal
if (isset($_POST['update_pembeli'])) {

    $id       = (int) $_POST['id'];
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);

    // 1. CEK DUPLIKAT EMAIL ATAU NIK (KECUALI MILIK SENDIRI)
    $cek = mysqli_query($conn, "SELECT email, nik FROM users WHERE (email='$email' OR nik='$nik') AND id != '$id' LIMIT 1");

    if (mysqli_num_rows($cek) > 0) {
        $row = mysqli_fetch_assoc($cek);
        $error = ($row['email'] == $email) ? 'email_exists' : 'nik_exists';
        header("Location: ../admin/superadmin/pembeli.php?error=$error");
        exit;
    }

    // 2. QUERY UPDATE
    $query = "UPDATE users SET nik='$nik', username='$username', email='$email', alamat='$alamat'";

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query .= ", password='$password'";
    }

    $query .= " WHERE id='$id' AND role='pembeli'";

    mysqli_query($conn, $query);
    header("Location: ../admin/superadmin/pembeli.php?success=edit");
    exit;
}

/* =====================
   HAPUS PEMBELI
===================== */
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // CEK STATUS USER (TIDAK BOLEH HAPUS JIKA ONLINE)
    $cekStatus = mysqli_query($conn, "SELECT status FROM users WHERE id='$id' AND role='pembeli' LIMIT 1");
    $user = mysqli_fetch_assoc($cekStatus);

    if ($user && $user['status'] === 'online') {
        header("Location: ../admin/superadmin/pembeli.php?error=active_user");
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND role='pembeli'");
    header("Location: ../admin/superadmin/pembeli.php?success=hapus");
    exit;
}