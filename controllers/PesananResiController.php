<?php
check_role('penjual');

$id   = (int) $_POST['id'];
$resi = mysqli_real_escape_string($conn, $_POST['resi']);

mysqli_query($conn,
    "UPDATE pesanan
     SET resi='$resi', status='dikirim'
     WHERE id='$id'"
);

redirect('/penjual/pesanan.php');
