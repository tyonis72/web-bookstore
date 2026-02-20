<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('superadmin');

/* ======================
   TAMBAH KATEGORI
====================== */
if (isset($_POST['add'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    $cek = mysqli_query(
        $conn,
        "SELECT id FROM kategori WHERE nama='$nama'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirect('/admin/superadmin/kategori.php?error=duplicate');
    }

    mysqli_query(
        $conn,
        "INSERT INTO kategori (nama) VALUES ('$nama')"
    );

    redirect('/admin/superadmin/kategori.php?success=add');
}

/* ======================
   EDIT KATEGORI
====================== */
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);

    $cek = mysqli_query(
        $conn,
        "SELECT id FROM kategori WHERE nama='$nama' AND id!='$id'"
    );

    if (mysqli_num_rows($cek) > 0) {
        redirect('/admin/superadmin/kategori.php?error=duplicate');
    }

    mysqli_query(
        $conn,
        "UPDATE kategori SET nama='$nama' WHERE id='$id'"
    );

    redirect('/admin/superadmin/kategori.php?success=edit');
}

/* ======================
   HAPUS KATEGORI
====================== */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    // Cek apakah kategori masih dipakai produk
    $cek = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM produk WHERE kategori_id = $id"
    );

    $data = mysqli_fetch_assoc($cek);

    if ($data['total'] > 0) {
        redirect('/admin/superadmin/kategori.php?error=kategori_dipakai');
    }

    // Jika aman, baru hapus
    mysqli_query(
        $conn,
        "DELETE FROM kategori WHERE id = $id"
    );

    redirect('/admin/superadmin/kategori.php?success=delete');
}

