<?php
session_start();

// Set session sebagai guest — tanpa diskon
$_SESSION['guest_rahma']        = true;
$_SESSION['nama_guest_rahma']   = 'Guest';

// UBAH: Redirect ke menu langsung (bukan pilih meja lagi)
header("Location: pelanggan/menu_rahma.php");
exit;
?>