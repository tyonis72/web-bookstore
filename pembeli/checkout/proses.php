<?php
require_once '../../config/database.php';
require_once '../../config/session.php';

check_role('pembeli');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$pembeli_id = $_SESSION['user']['id'];

// Menangkap alamat dari form. 
// Jika user mengubah di form, maka $alamat akan berisi alamat baru.
// Jika tidak diubah, otomatis berisi alamat default user dari database.
$nama_penerima = mysqli_real_escape_string($conn, $_POST['nama_penerima']);
$alamat_tujuan = mysqli_real_escape_string($conn, $_POST['alamat_lengkap']);

// 1. Ambil keranjang & Kelompokkan berdasarkan penjual_id
$query_keranjang = mysqli_query($conn, "
    SELECT k.*, p.nama, p.harga, p.penjual_id, p.stok
    FROM keranjang k
    JOIN produk p ON k.produk_id = p.id
    WHERE k.pembeli_id='$pembeli_id'"
);

if (mysqli_num_rows($query_keranjang) == 0) {
    header("Location: ../keranjang/index.php?error=empty");
    exit;
}

$groups = [];
while ($row = mysqli_fetch_assoc($query_keranjang)) {
    $groups[$row['penjual_id']][] = $row;
}

mysqli_begin_transaction($conn);

try {
    foreach ($groups as $id_penjual => $items) {
        $total_per_toko = 0;
        foreach ($items as $item) {
            if ($item['qty'] > $item['stok']) {
                throw new Exception("Stok untuk produk '{$item['nama']}' tidak mencukupi.");
            }
            $total_per_toko += ($item['harga'] * $item['qty']);
        }

        // 2. LOGIKA UPLOAD BUKTI
        $nama_file_bukti = null;
        $input_file_name = "bukti_" . $id_penjual;

        if (isset($_FILES[$input_file_name]) && $_FILES[$input_file_name]['error'] == 0) {
            $ext = pathinfo($_FILES[$input_file_name]['name'], PATHINFO_EXTENSION);
            $nama_file_bukti = "PAY_" . $id_penjual . "_" . time() . "_" . uniqid() . "." . $ext;
            $target_path = "../../public/uploads/bukti_bayar/" . $nama_file_bukti;

            if (!move_uploaded_file($_FILES[$input_file_name]['tmp_name'], $target_path)) {
                throw new Exception("Gagal mengunggah bukti untuk toko ID: $id_penjual");
            }
        }

        // 3. INSERT KE TABEL TRANSAKSI
        // Sekarang menyertakan kolom alamat_pengiriman
        $query_transaksi = "INSERT INTO transaksi (pembeli_id, penjual_id, total, status, bukti_transfer, alamat_pengiriman) 
                            VALUES ('$pembeli_id', '$id_penjual', '$total_per_toko', 'menunggu', '$nama_file_bukti', '$alamat_tujuan')";
        
        if (!mysqli_query($conn, $query_transaksi)) {
            throw new Exception("Gagal menyimpan data transaksi.");
        }

        $transaksi_id = mysqli_insert_id($conn);

        // 4. INSERT DETAIL & UPDATE STOK
        foreach ($items as $item) {
            $produk_id = $item['produk_id'];
            $qty       = $item['qty'];
            $harga     = $item['harga'];

            $query_detail = "INSERT INTO transaksi_detail (transaksi_id, produk_id, qty, harga) 
                             VALUES ('$transaksi_id', '$produk_id', '$qty', '$harga')";
            mysqli_query($conn, $query_detail);

            $query_stok = "UPDATE produk SET stok = stok - $qty WHERE id = '$produk_id'";
            mysqli_query($conn, $query_stok);
        }
    }

    // 5. KOSONGKAN KERANJANG
    mysqli_query($conn, "DELETE FROM keranjang WHERE pembeli_id='$pembeli_id'");

    mysqli_commit($conn);
    header("Location: ../pesanan/index.php?status=success");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    die("Error: " . $e->getMessage());
}