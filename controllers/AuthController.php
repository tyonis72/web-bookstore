<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/session.php';


/* ======================
   LOGIN
====================== */
if (isset($_POST['login'])) {

    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($query) !== 1) {
        redirect('/auth/login.php?error=1');
    }

    $user = mysqli_fetch_assoc($query);

    if (!password_verify($password, $user['password'])) {
        redirect('/auth/login.php?error=1');
    }

    // SET SESSION
    $_SESSION['user'] = [
        'id'    => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role'  => $user['role']
    ];

    // UPDATE STATUS
    mysqli_query(
        $conn,
        "UPDATE users SET status='online' WHERE id='{$user['id']}'"
    );

    // REDIRECT SESUAI ROLE
    if ($user['role'] === 'superadmin') {
        redirect('/admin/superadmin/index.php');
    }

    if ($user['role'] === 'penjual') {
        redirect('/penjual/dashboard.php');
    }

    if ($user['role'] === 'pembeli') {
        redirect('/pembeli/index.php');
    }

    // JIKA ROLE TIDAK VALID
    redirect('/auth/login.php?error=role');
}



/* ======================
   REGISTER (PENJUAL & PEMBELI SAJA)
====================== */
if (isset($_POST['register'])) {

    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    // VALIDASI ROLE (ANTI MANIPULASI)
    if (!in_array($role, ['penjual', 'pembeli'])) {
        redirect('/auth/register.php?error=role');
    }

    // CEK EMAIL DUPLIKAT
    $check = mysqli_query(
        $conn,
        "SELECT id FROM users WHERE email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {
        redirect('/auth/register.php?error=email');
    }

    // INSERT USER BARU (FIX NIK)
    mysqli_query(
        $conn,
        "INSERT INTO users
        (nik, username, email, alamat, password, role, status)
        VALUES
        ('$nik', '$username', '$email', '$alamat', '$password', '$role', 'offline')"
    );

    redirect('/auth/login.php?register=success');
}


/* ======================
   FORGOT PASSWORD
====================== */
if (isset($_POST['forgot'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = mysqli_query(
        $conn,
        "SELECT id FROM users WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($query) === 1) {

        $token = bin2hex(random_bytes(32));

        mysqli_query(
            $conn,
            "UPDATE users SET reset_token='$token' WHERE email='$email'"
        );

        $link = BASE_URL . "/auth/reset-password.php?token=$token";

        sendMail(
            $email,
            "Reset Password BookStore",
            "
            <h3>Reset Password</h3>
            <p>Klik tombol di bawah untuk reset password Anda:</p>
            <a href='$link'
               style='padding:10px 15px;
               background:#2563eb;
               color:#fff;
               text-decoration:none;
               border-radius:6px'>
               Reset Password
            </a>
            "
        );
    }

    header("Location: ../auth/forgot-password.php?success=1");
    exit;
}

/* ======================
   RESET PASSWORD
====================== */
// Ubah 'reset' menjadi 'update_password' sesuai dengan name di button HTML
if (isset($_POST['update_password'])) {

    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validasi tambahan: Cek apakah password matching
    if ($password !== $confirm) {
        header("Location: ../auth/reset-password.php?token=$token&error=match");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Lakukan update
    $query = "UPDATE users 
              SET password='$hashed_password', reset_token=NULL 
              WHERE reset_token='$token'";
    
    $exec = mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        // Jika berhasil update minimal 1 baris
        header("Location: ../auth/login.php?reset=success");
        exit;
    } else {
        // Jika token tidak ditemukan atau sudah kadaluwarsa
        header("Location: ../auth/login.php?error=invalid_token");
        exit;
    }
}

