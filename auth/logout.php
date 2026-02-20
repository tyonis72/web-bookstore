<?php
require_once '../config/app.php';
require_once '../config/database.php';
require_once '../config/session.php';

if (isset($_SESSION['user']['id'])) {
    $id = $_SESSION['user']['id'];
    mysqli_query($conn, "UPDATE users SET status='offline' WHERE id='$id'");
}

session_destroy();
redirect('/auth/login.php');
