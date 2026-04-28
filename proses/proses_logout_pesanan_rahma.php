<?php
session_start();

/**
 * File: proses_logout_pesanan_rahma.php
 * Fungsi: Destroy session setelah cetak struk pesanan
 * Flow: Cetak struk → Logout otomatis → Redirect ke login
 */

// Hapus semua session data
session_destroy();

// Redirect ke login
header("Location: ../login_rahma.php");
exit;
?>
