<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Cegah cache supaya ga bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek apakah sudah login
if (!isset($_SESSION['tbl_user_rahma'])) {
    // Jika belum login, arahkan ke halaman login (bukan index itu sendiri)
    header("Location: login_rahma.php");
    exit;

}
?>