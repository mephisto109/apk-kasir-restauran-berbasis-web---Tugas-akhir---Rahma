<?php
// proses/proses_bayar_rahma.php
session_start();

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R002') {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../kasir/dashboard_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// 1. Ambil data mentah
$id_order_rahma = mysqli_real_escape_string($koneksiRahma, $_POST['id_order_rahma'] ?? '');
$grand_total_rahma = (int) ($_POST['grand_total_rahma'] ?? 0);
$bayar_rahma = (int) ($_POST['bayar_rahma'] ?? 0); // Ini yang krusial!
$id_meja_rahma = $_POST['id_meja_rahma'] ?? '';
$metode_bayar_rahma = $_POST['metode_bayar_rahma'] ?? 'cash';

// 2. Hitung Diskon & Pajak (Lakukan ulang di server agar akurat)
$query_order_rahma = mysqli_query($koneksiRahma, "SELECT id_user_rahma FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'");
$data_order_rahma = mysqli_fetch_assoc($query_order_rahma);
$is_member_rahma = !empty($data_order_rahma['id_user_rahma']);

$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$diskon_nominal_rahma = (int) ($grand_total_rahma * $diskon_persen_rahma / 100);
$total_setelah_diskon_rahma = $grand_total_rahma - $diskon_nominal_rahma;
$pajak_nominal_rahma = (int) ($total_setelah_diskon_rahma * 0.11);
$total_akhir_rahma = $total_setelah_diskon_rahma + $pajak_nominal_rahma;

// 3. LOGIKA VALIDASI UANG (Pencegahan uang kurang)
if ($metode_bayar_rahma === 'cash') {
    if ($bayar_rahma < $total_akhir_rahma) {
        // Balikin ke halaman bayar kalau uangnya kurang
        header("Location: ../kasir/pembayaran_rahma.php?id=$id_order_rahma&error=kurang");
        exit;
    }
    $kembalian_rahma = $bayar_rahma - $total_akhir_rahma;
} else {
    // Non-cash (QRIS/Debit) dianggap uang pas
    $bayar_rahma = $total_akhir_rahma;
    $kembalian_rahma = 0;
}

// 4. Generate ID Transaksi
$query_last_rahma = mysqli_query($koneksiRahma, "SELECT id_transaksi_rahma FROM tbl_transaksi_rahma ORDER BY id_transaksi_rahma DESC LIMIT 1");
$last_rahma = mysqli_fetch_assoc($query_last_rahma);
$last_id_rahma = $last_rahma['id_transaksi_rahma'] ?? 'T000';
$angka_rahma = (int) substr($last_id_rahma, 1) + 1;
$id_transaksi_rahma = 'T' . str_pad($angka_rahma, 3, '0', STR_PAD_LEFT);

$waktu_transaksi_rahma = date('Y-m-d H:i:s');
$metode_bayar_esc_rahma = mysqli_real_escape_string($koneksiRahma, $metode_bayar_rahma);

// 5. SIMPAN KE DATABASE
$insert_rahma = mysqli_query($koneksiRahma, "
    INSERT INTO tbl_transaksi_rahma 
        (id_transaksi_rahma, id_order_rahma, id_kasir_rahma, diskon_rahma, pajak_rahma, total_rahma, bayar_rahma, kembalian_rahma, waktu_transaksi_rahma, metode_bayar_rahma)
    VALUES 
        ('$id_transaksi_rahma', '$id_order_rahma', '{$_SESSION['id_user_rahma']}', $diskon_persen_rahma, $pajak_nominal_rahma, $total_akhir_rahma, $bayar_rahma, $kembalian_rahma, '$waktu_transaksi_rahma', '$metode_bayar_esc_rahma')
");

if ($insert_rahma) {
    $id_meja_val_rahma = !empty($id_meja_rahma) ? "'$id_meja_rahma'" : "NULL";
    mysqli_query($koneksiRahma, "UPDATE tbl_order_rahma SET id_meja_rahma = $id_meja_val_rahma, status_order_rahma = 'diproses' WHERE id_order_rahma = '$id_order_rahma'");
    header("Location: ../kasir/cetak_struk_rahma.php?id=$id_transaksi_rahma");
    exit;
} else {
    header("Location: ../kasir/pembayaran_rahma.php?id=$id_order_rahma&error=1");
    exit;
}
?>