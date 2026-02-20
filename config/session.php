<?php

// AMAN: session hanya dimulai sekali
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =====================
   CEK LOGIN
===================== */
function check_login()
{
    if (
        !isset($_SESSION['user']) ||
        !is_array($_SESSION['user']) ||
        !isset($_SESSION['user']['id'])
    ) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }
}

/* =====================
   CEK ROLE
===================== */
function check_role($role)
{
    check_login();

    if (
        !isset($_SESSION['user']['role']) ||
        $_SESSION['user']['role'] !== $role
    ) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }
}
