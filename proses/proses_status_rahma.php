<?php
session_start();

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R004') {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../chef/dashboard_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

$id_order_rahma = $_POST['id_order_rahma'] ?? '';

if (empty($id_order_rahma)) {
    header("Location: ../chef/dashboard_rahma.php");
    exit;
}

// Update status order jadi selesai
$update_rahma = mysqli_query($koneksiRahma, "
    UPDATE tbl_order_rahma
    SET status_order_rahma = 'selesai'
    WHERE id_order_rahma = '$id_order_rahma'
    AND status_order_rahma = 'diproses'
");

if ($update_rahma) {
    $redirect_rahma = $_POST['redirect_rahma'] ?? 'dashboard';
    if ($redirect_rahma == 'update') {
        header("Location: ../chef/update_status_rahma.php?sukses=1");
    } else {
        header("Location: ../chef/update_status_rahma.php?id=$id_order_rahma&sukses=1");
    }
} else {
    header("Location: ../chef/update_status_rahma.php?error=1");
}
exit;
?>