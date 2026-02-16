<?php
session_start();
include '../koneksi/koneksi_rahma.php';

$username_rahma = $_POST['username'];
$password_rahma = $_POST['password'];

// Ambil data user berdasarkan username SAJA
$queryRahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_user_rahma 
     WHERE username_rahma='$username_rahma'"
);

$userRahma = mysqli_fetch_assoc($queryRahma);

// Cek apakah user ditemukan
if ($userRahma) {

    /*
    var_dump($password_rahma);
    var_dump($userRahma['password_rahma']);
    var_dump(password_verify($password_rahma, $userRahma['password_rahma']));
    die();
    */

    //cek password
    if (password_verify($password_rahma, $userRahma['password_rahma'])) {

        $_SESSION['id_user_rahma'] = $userRahma['id_user_rahma'];
        $_SESSION['username_rahma'] = $userRahma['username_rahma'];
        $_SESSION['id_role_rahma'] = $userRahma['id_role_rahma'];

        // Redirect berdasarkan role
        switch ($userRahma['id_role_rahma']) {

            case "R001":
                header("Location: ../owner/dashboard_rahma.php");
                break;

            case "R002":
                header("Location: ../kasir/dashboard_rahma.php");
                break;

            case "R003":
                header("Location: ../pelanggan/menu_rahma.php");
                break;

            case "R004":
                header("Location: ../chef/dashboard_rahma.php");
                break;
        }

        exit;

    } else {
        echo "<script>alert('Password salah!'); window.location='../login_rahma.php';</script>";
    }

} else {
    echo "<script>alert('Username tidak ditemukan!'); window.location='../login_rahma.php';</script>";
}
