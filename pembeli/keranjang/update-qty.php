<?php
// Pastikan tidak ada spasi/enter sebelum baris ini
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../controllers/KeranjangController.php';

check_role('pembeli');

// Set header di awal agar browser tahu ini adalah JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = new KeranjangController($conn);
        
        // Pastikan data POST ada
        if (!isset($_POST['id_item']) || !isset($_POST['qty'])) {
            throw new Exception("Data tidak lengkap");
        }

        $id_item = $_POST['id_item'];
        $qty = (int)$_POST['qty'];
        $user_id = $_SESSION['user']['id'];

        $result = $controller->updateQuantity($id_item, $qty, $user_id);
        
        echo json_encode($result);
    } catch (Exception $e) {
        // Jika ada error, kirim pesan error dalam format JSON
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}