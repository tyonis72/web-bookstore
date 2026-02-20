<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

check_role('penjual');

$penjual_id = $_SESSION['user']['id'];

/* ======================
   SIMPAN (INSERT / UPDATE)
====================== */
if (isset($_POST['save'])) {

    $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $modal = (int) $_POST['modal'];
    $margin = (int) $_POST['margin'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori_id = !empty($_POST['kategori_id']) ? (int) $_POST['kategori_id'] : null;

    /* ======================
       CEK NAMA DUPLIKAT
    ====================== */
    $cekQuery = "
        SELECT id FROM produk
        WHERE nama = '$nama'
        AND penjual_id = $penjual_id
    ";

    if ($id) {
        $cekQuery .= " AND id != $id";
    }

    $cek = mysqli_query($conn, $cekQuery);
    if (mysqli_num_rows($cek) > 0) {
        redirect('/penjual/produk.php?error=nama_produk_duplikat');
    }

    /* ======================
       UPLOAD FOTO
    ====================== */
    $foto = null;

    $rootPath = realpath(__DIR__ . '/../');
    if ($rootPath === false) {
        die('Root path tidak ditemukan');
    }

    $uploadDir = $rootPath . '/public/uploads/produk/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['foto']['name'])) {

        if ($_FILES['foto']['error'] !== 0) {
            redirect('/penjual/produk.php?error=system_error_' . $_FILES['foto']['error']);
        }

        if (!is_writable($uploadDir)) {
            redirect('/penjual/produk.php?error=folder_readonly');
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            redirect('/penjual/produk.php?error=format_tidak_didukung');
        }

        $foto = time() . '_' . uniqid() . '.' . $ext;
        $targetFile = $uploadDir . $foto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            redirect('/penjual/produk.php?error=upload_gagal_pindah');
        }
    }

    /* ======================
       QUERY INSERT / UPDATE
    ====================== */
    if ($id) {
        $query = "
            UPDATE produk SET
                nama='$nama',
                harga=$harga,
                stok=$stok,
                modal=$modal,
                margin=$margin,
                deskripsi='$deskripsi',
                kategori_id=" . ($kategori_id ?? 'NULL') . "
                " . ($foto ? ", foto='$foto'" : "") . "
            WHERE id=$id AND penjual_id=$penjual_id
        ";
    } else {
        $query = "
            INSERT INTO produk
            (penjual_id, kategori_id, nama, harga, stok, modal, margin, deskripsi, foto)
            VALUES
            (
                $penjual_id,
                " . ($kategori_id ?? 'NULL') . ",
                '$nama',
                $harga,
                $stok,
                $modal,
                $margin,
                '$deskripsi',
                " . ($foto ? "'$foto'" : "NULL") . "
            )
        ";
    }

    if (mysqli_query($conn, $query)) {
        redirect('/penjual/produk.php?success=simpan');
    } else {
        die('Error Database: ' . mysqli_error($conn));
    }
}

/* ======================
   DELETE PRODUK
====================== */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $q = mysqli_query($conn, "
        SELECT stok, foto FROM produk
        WHERE id=$id AND penjual_id=$penjual_id
    ");

    $produk = mysqli_fetch_assoc($q);
    if (!$produk) {
        redirect('/penjual/produk.php?error=notfound');
    }

    if ($produk['stok'] > 0) {
        redirect('/penjual/produk.php?error=stok_ada');
    }

    if (!empty($produk['foto'])) {
        $path = __DIR__ . '/../public/uploads/produk/' . $produk['foto'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    mysqli_query($conn, "
        DELETE FROM produk
        WHERE id=$id AND penjual_id=$penjual_id
    ");

    redirect('/penjual/produk.php?success=hapus');
}
