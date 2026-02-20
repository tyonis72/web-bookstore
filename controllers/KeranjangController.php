<?php
// controllers/KeranjangController.php

class KeranjangController {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

public function updateQuantity($id_item, $qty, $user_id) {
    if ($qty < 1) {
        // Jika qty 0 atau minus, jalankan fungsi hapus
        $query = "DELETE FROM keranjang WHERE id = ? AND pembeli_id = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $id_item, $user_id);
    } else {
        // Jika qty 1 atau lebih, jalankan update biasa
        $query = "UPDATE keranjang SET qty = ? WHERE id = ? AND pembeli_id = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "iii", $qty, $id_item, $user_id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    return ['success' => false];
}
}