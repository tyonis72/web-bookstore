<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('superadmin');

/* =====================
   TAMBAH PEMBELI
===================== */
if (isset($_POST['tambah'])) {

    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // VALIDASI NIK (16 DIGIT ANGKA)
    if (!preg_match('/^[0-9]{16}$/', $nik)) {
        redirect('/admin/superadmin/pembeli.php?error=nik');
    }

    // CEK DUPLIKAT EMAIL / NIK
    $cek = mysqli_query(
        $conn,
        "SELECT id FROM users WHERE email='$email' OR nik='$nik'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirect('/admin/superadmin/pembeli.php?error=duplikat');
    }

    mysqli_query($conn,
        "INSERT INTO users (nik,username,email,alamat,password,role,status)
         VALUES ('$nik','$username','$email','$alamat','$password','pembeli','offline')"
    );

    redirect('/admin/superadmin/pembeli.php?success=tambah');
}

/* =====================
   EDIT PEMBELI
===================== */
if (isset($_POST['edit'])) {

    $id       = $_POST['id'];
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);

    // VALIDASI NIK
    if (!preg_match('/^[0-9]{16}$/', $nik)) {
        redirect('/admin/superadmin/pembeli.php?error=nik');
    }

    // CEK DUPLIKAT (KECUALI DIRI SENDIRI)
    $cek = mysqli_query($conn,
        "SELECT id FROM users 
         WHERE (email='$email' OR nik='$nik') 
         AND id!='$id'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirect('/admin/superadmin/pembeli.php?error=duplikat');
    }

    mysqli_query($conn,
        "UPDATE users SET
            nik='$nik',
            username='$username',
            email='$email',
            alamat='$alamat'
         WHERE id='$id' AND role='pembeli'"
    );

    redirect('/admin/superadmin/pembeli.php?success=edit');
}

/* =====================
   HAPUS PEMBELI
===================== */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn,
        "DELETE FROM users WHERE id='$id' AND role='pembeli'"
    );

    redirect('/admin/superadmin/pembeli.php?success=hapus');
}
