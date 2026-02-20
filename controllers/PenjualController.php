<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('superadmin');

/* =====================
   TAMBAH PENJUAL
===================== */
// Disinkronkan dengan name="tambah_penjual" dari Modal
if (isset($_POST['tambah_penjual'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // CEK EMAIL DUPLIKAT
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        header("Location: ../admin/superadmin/penjual.php?error=email");
        exit;
    }

    mysqli_query(
        $conn,
        "INSERT INTO users
        (username, nik, email, alamat, password, role, status)
        VALUES
        ('$username', '$nik', '$email', '$alamat', '$password', 'penjual', 'offline')"
    );

    header("Location: ../admin/superadmin/penjual.php?success=tambah");
    exit;
}


/* =====================
   EDIT PENJUAL
===================== */
// Disinkronkan dengan name="update_penjual" dari Modal
if (isset($_POST['update_penjual'])) {

    $id       = (int) $_POST['id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);

    // CEK EMAIL DUPLIKAT (KECUALI DIRI SENDIRI)
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != '$id'");

    if (mysqli_num_rows($cek) > 0) {
        header("Location: ../admin/superadmin/penjual.php?error=email");
        exit;
    }

    // Logika Update Password (Hanya jika diisi)
    $query = "UPDATE users SET 
                username='$username', 
                nik='$nik', 
                email='$email', 
                alamat='$alamat'";

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

    // Jika ingin hapus permanen:
    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND role='penjual'");

    header("Location: ../admin/superadmin/penjual.php?success=hapus");
    exit;
}
