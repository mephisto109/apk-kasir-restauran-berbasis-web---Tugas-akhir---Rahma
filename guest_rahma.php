<?php
session_start();

// Set session sebagai guest — tanpa diskon
$_SESSION['guest_rahma']        = true;
$_SESSION['nama_guest_rahma']   = 'Guest';

// Redirect ke pilih meja
header("Location: pelanggan/pilih_meja_rahma.php");
exit;
?>