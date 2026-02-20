<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

check_role('penjual');
$penjual_id = $_SESSION['user']['id'];

if (isset($_POST['transaksi_id'], $_POST['status'])) {

    $id = (int) $_POST['transaksi_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $resi = isset($_POST['resi']) ? mysqli_real_escape_string($conn, trim($_POST['resi'])) : '';
    $jasa = !empty($_POST['ekspedisi']) ? mysqli_real_escape_string($conn, trim($_POST['ekspedisi'])) : null;
    $link = !empty($_POST['url_tracking']) ? mysqli_real_escape_string($conn, trim($_POST['url_tracking'])) : null;

    // 1. Ambil data pesanan lama
    $check_old = mysqli_query($conn, "SELECT status FROM transaksi WHERE id='$id' AND penjual_id='$penjual_id'");
    $old_data = mysqli_fetch_assoc($check_old);

    if (!$old_data) {
        redirect('/penjual/pesanan/index.php?error=pesanan_invalid');
    }

    $old_status = $old_data['status'];

    // 2. LOGIKA JIKA STATUS DIUBAH MENJADI 'APPROVE'
    if ($status === 'approve') {

        // Hanya kurangi stok jika status SEBELUMNYA bukan 'approve'
        // Ini mencegah stok berkurang dua kali jika Anda hanya ingin mengedit nomor resi
        if ($old_status !== 'approve') {
            $detail = mysqli_query($conn, "SELECT d.*, p.stok FROM transaksi_detail d JOIN produk p ON d.produk_id = p.id WHERE d.transaksi_id='$id'");

            // Cek ketersediaan stok
            while ($row = mysqli_fetch_assoc($detail)) {
                if ($row['stok'] < $row['qty']) {
                    redirect('/penjual/pesanan/index.php?error=stok_habis');
                }
            }

            mysqli_begin_transaction($conn);
            try {
                mysqli_data_seek($detail, 0);
                while ($row = mysqli_fetch_assoc($detail)) {
                    mysqli_query($conn, "UPDATE produk SET stok = stok - {$row['qty']} WHERE id='{$row['produk_id']}'");
                }

                // Update status dan simpan resi jika ada
                mysqli_query($conn, "UPDATE transaksi SET status='$status', resi=" . ($resi !== '' ? "'$resi'" : "NULL") . " WHERE id='$id'");

                mysqli_commit($conn);
                redirect('/penjual/pesanan/index.php?success=approved');
            } catch (Exception $e) {
                mysqli_rollback($conn);
                redirect('/penjual/pesanan/index.php?error=db');
            }
        } else {
            // Jika status memang sudah 'approve', cukup update nomor resi saja
            $sql = "UPDATE transaksi SET resi = " . ($resi !== '' ? "'$resi'" : "NULL") . ", jasa_pengiriman = " . ($jasa !== null ? "'$jasa'" : "NULL") . ", link_lacak = " . ($link !== null ? "'$link'" : "NULL") . " WHERE id = '$id' AND penjual_id = '$penjual_id'";
            mysqli_query($conn, $sql);
            redirect('/penjual/pesanan/index.php?success=updated');
        }
    }

    // 3. LOGIKA JIKA STATUS 'TOLAK'
    else if ($status === 'tolak') {
        // Jika sebelumnya sudah 'approve' tapi dibatalkan/ditolak, Anda mungkin perlu mengembalikan stok di sini.
        // Namun jika alurnya dari 'menunggu' ke 'tolak', cukup update status & kosongkan resi.
        $sql = "UPDATE transaksi SET status='tolak', resi=NULL WHERE id='$id' AND penjual_id='$penjual_id'";
        mysqli_query($conn, $sql);
        redirect('/penjual/pesanan/index.php?success=rejected');
    }

    // 4. UPDATE STATUS LAIN (Refund, dll)
    else {
        $sql = "UPDATE transaksi SET status = '$status', resi = " . ($resi !== '' ? "'$resi'" : "NULL") . " 
                WHERE id = '$id' AND penjual_id = '$penjual_id'";

        if (mysqli_query($conn, $sql)) {
            redirect('/penjual/pesanan/index.php?success=updated');
        } else {
            redirect('/penjual/pesanan/index.php?error=db');
        }
    }
}