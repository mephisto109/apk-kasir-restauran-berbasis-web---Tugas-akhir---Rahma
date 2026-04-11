<?php
session_start();

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R002') {
    header("Location: ../login_rahma.php");
    exit;
}

// Hanya terima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../kasir/dashboard_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Ambil data dari form
$id_order_rahma = $_POST['id_order_rahma'] ?? '';
$grand_total_rahma = $_POST['grand_total_rahma'] ?? 0;
$bayar_rahma = $_POST['bayar_rahma'] ?? 0;
$kembalian_rahma = $_POST['kembalian_rahma'] ?? 0;
$diskon_rahma = 0;

// Validasi — kalau data kosong, balik ke dashboard
if (empty($id_order_rahma) || $bayar_rahma <= 0) {
    header("Location: ../kasir/dashboard_rahma.php");
    exit;
}

// Buat ID transaksi baru otomatis
$query_last_rahma = mysqli_query($koneksiRahma, "SELECT id_transaksi_rahma FROM tbl_transaksi_rahma ORDER BY id_transaksi_rahma DESC LIMIT 1");
$last_rahma = mysqli_fetch_assoc($query_last_rahma);
$last_id_rahma = $last_rahma['id_transaksi_rahma'] ?? 'T000';
$angka_rahma = (int) substr($last_id_rahma, 1) + 1;
$id_transaksi_rahma = 'T' . str_pad($angka_rahma, 3, '0', STR_PAD_LEFT);

// Simpan transaksi ke database
$waktu_transaksi_rahma = date('Y-m-d');
$insert_rahma = mysqli_query($koneksiRahma, "
    INSERT INTO tbl_transaksi_rahma 
        (id_transaksi_rahma, id_order_rahma, diskon_rahma, total_rahma, bayar_rahma, kembalian_rahma, waktu_transaksi_rahma)
    VALUES 
        ('$id_transaksi_rahma', '$id_order_rahma', '$diskon_rahma', '$grand_total_rahma', '$bayar_rahma', '$kembalian_rahma', '$waktu_transaksi_rahma')
");

if ($insert_rahma) {
    // Update status order jadi selesai
    mysqli_query($koneksiRahma, "
        UPDATE tbl_order_rahma 
        SET status_order_rahma = 'selesai' 
        WHERE id_order_rahma = '$id_order_rahma'
    ");

    // Update status meja jadi kosong setelah bayar
    mysqli_query($koneksiRahma, "
    UPDATE tbl_meja_rahma
    SET status_rahma = 'kosong'
    WHERE id_meja_rahma = (
        SELECT id_meja_rahma FROM tbl_order_rahma
        WHERE id_order_rahma = '$id_order_rahma'
    )
");

    // Redirect ke cetak struk
    header("Location: ../kasir/cetak_struk_rahma.php?id=$id_transaksi_rahma");
    exit;
} else {
    // Kalau gagal, balik ke halaman pembayaran
    header("Location: ../kasir/pembayaran_rahma.php?id=$id_order_rahma&error=1");
    exit;
}
?>