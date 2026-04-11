<?php
ob_start();
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

$username_rahma = $_POST['username'] ?? '';
$password_rahma = $_POST['password'] ?? '';

// Validasi input kosong
if (empty($username_rahma) || empty($password_rahma)) {
    header("Location: ../login_rahma.php?error=1");
    exit;
}

// Cari user berdasarkan username
$query_user_rahma = mysqli_query($koneksiRahma, "
    SELECT u.*, r.role_rahma
    FROM tbl_user_rahma u
    LEFT JOIN tbl_role_rahma r ON u.id_role_rahma = r.id_role_rahma
    WHERE u.username_rahma = '$username_rahma'
");

if (mysqli_num_rows($query_user_rahma) == 0) {
    // Username tidak ditemukan
    header("Location: ../login_rahma.php?error=1");
    exit;
}

$user_rahma = mysqli_fetch_assoc($query_user_rahma);

// Verifikasi password pakai password_verify karena disimpan pakai bcrypt
if (!password_verify($password_rahma, $user_rahma['password_rahma'])) {
    header("Location: ../login_rahma.php?error=1");
    exit;
}

// Login berhasil — simpan ke session
$_SESSION['id_user_rahma'] = $user_rahma['id_user_rahma'];
$_SESSION['username_rahma'] = $user_rahma['username_rahma'];
$_SESSION['nama_rahma'] = $user_rahma['nama_rahma'];
$_SESSION['id_role_rahma'] = $user_rahma['id_role_rahma'];

// Redirect sesuai role
if ($user_rahma['id_role_rahma'] == 'R001') {
    header("Location: ../owner/dashboard_rahma.php");
} elseif ($user_rahma['id_role_rahma'] == 'R002') {
    header("Location: ../kasir/dashboard_rahma.php");
} elseif ($user_rahma['id_role_rahma'] == 'R003') {
    header("Location: ../pelanggan/pilih_meja_rahma.php");
} elseif ($user_rahma['id_role_rahma'] == 'R004') {
    header("Location: ../chef/dashboard_rahma.php");
} else {
    header("Location: ../login_rahma.php?error=1");
}
exit;
?>