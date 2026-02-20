<?php
require_once '../../config/app.php';
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

$pembeli_id = $_SESSION['user']['id'];
$produk_id  = (int) $_POST['produk_id'];

// Cek apakah produk sudah ada di keranjang
$cek = mysqli_query($conn,
    "SELECT id, qty FROM keranjang
     WHERE pembeli_id='$pembeli_id'
     AND produk_id='$produk_id'"
);

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah ada → tambah qty
    mysqli_query($conn,
        "UPDATE keranjang
         SET qty = qty + 1
         WHERE pembeli_id='$pembeli_id'
         AND produk_id='$produk_id'"
    );
} else {
    // Jika belum → insert baru
    mysqli_query($conn,
        "INSERT INTO keranjang (pembeli_id, produk_id, qty)
         VALUES ('$pembeli_id', '$produk_id', 1)"
    );
}

redirect('/pembeli/keranjang/index.php');
