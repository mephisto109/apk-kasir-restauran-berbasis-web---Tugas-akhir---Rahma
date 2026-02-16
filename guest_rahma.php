<?php
session_start();
$_SESSION['guest'] = true;

header("Location: pelanggan/pilih_meja_rahma.php");
exit;
?>
