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

// Ambil semua data dari form pembayaran
$id_order_rahma    = $_POST['id_order_rahma'] ?? '';
$grand_total_rahma = $_POST['grand_total_rahma'] ?? 0;
$bayar_rahma       = $_POST['bayar_rahma'] ?? 0;
$kembalian_rahma   = $_POST['kembalian_rahma'] ?? 0;
$id_meja_rahma     = $_POST['id_meja_rahma'] ?? '';

// Ambil metode bayar yang dipilih kasir (cash / qris / debit)
$metode_bayar_rahma = $_POST['metode_bayar_rahma'] ?? 'cash';

// Validasi metode — hanya terima 3 opsi yang valid
$metode_valid_rahma = ['cash', 'qris', 'debit'];
if (!in_array($metode_bayar_rahma, $metode_valid_rahma)) {
    $metode_bayar_rahma = 'cash'; // fallback ke cash kalau ada yang aneh
}

// Escape untuk keamanan
$metode_bayar_esc_rahma = mysqli_real_escape_string($koneksiRahma, $metode_bayar_rahma);

// Ambil id kasir dari session
$id_kasir_rahma = $_SESSION['id_user_rahma'];

// Validasi data pokok
if (empty($id_order_rahma) || $bayar_rahma <= 0) {
    header("Location: ../kasir/dashboard_rahma.php");
    exit;
}

// Cek member atau bukan — untuk tentukan diskon
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT id_user_rahma FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'
");
$data_order_rahma = mysqli_fetch_assoc($query_order_rahma);
$is_member_rahma  = !empty($data_order_rahma['id_user_rahma']);

$diskon_persen_rahma  = $is_member_rahma ? 10 : 0;
$diskon_nominal_rahma = (int) ($grand_total_rahma * $diskon_persen_rahma / 100);

$total_setelah_diskon_rahma = $grand_total_rahma - $diskon_nominal_rahma;
$pajak_nominal_rahma        = (int) ($total_setelah_diskon_rahma * 0.11);
$total_akhir_rahma          = $total_setelah_diskon_rahma + $pajak_nominal_rahma;

// Generate ID transaksi baru
$query_last_rahma   = mysqli_query($koneksiRahma, "
    SELECT id_transaksi_rahma FROM tbl_transaksi_rahma
    ORDER BY id_transaksi_rahma DESC LIMIT 1
");
$last_rahma         = mysqli_fetch_assoc($query_last_rahma);
$last_id_rahma      = $last_rahma['id_transaksi_rahma'] ?? 'T000';
$angka_rahma        = (int) substr($last_id_rahma, 1) + 1;
$id_transaksi_rahma = 'T' . str_pad($angka_rahma, 3, '0', STR_PAD_LEFT);

$waktu_transaksi_rahma = date('Y-m-d H:i:s');

// Untuk QRIS dan Debit: kembalian selalu 0, bayar = total
// (mesin EDC/QRIS sudah pasti minta jumlah tepat)
if ($metode_bayar_rahma !== 'cash') {
    $bayar_rahma     = $total_akhir_rahma;
    $kembalian_rahma = 0;
}

// Simpan transaksi ke DB — sekarang termasuk kolom metode_bayar_rahma
$insert_rahma = mysqli_query($koneksiRahma, "
    INSERT INTO tbl_transaksi_rahma 
        (id_transaksi_rahma, id_order_rahma, id_kasir_rahma,
         diskon_rahma, pajak_rahma, total_rahma,
         bayar_rahma, kembalian_rahma,
         waktu_transaksi_rahma, metode_bayar_rahma)
    VALUES 
        ('$id_transaksi_rahma', '$id_order_rahma', '$id_kasir_rahma',
         '$diskon_persen_rahma', '$pajak_nominal_rahma', '$total_akhir_rahma',
         '$bayar_rahma', '$kembalian_rahma',
         '$waktu_transaksi_rahma', '$metode_bayar_esc_rahma')
");

if ($insert_rahma) {
    // Update status order dan set nomor meja (kalau dine in)
    $id_meja_val_rahma = !empty($id_meja_rahma) ? "'$id_meja_rahma'" : "NULL";
    mysqli_query($koneksiRahma, "
        UPDATE tbl_order_rahma 
        SET id_meja_rahma = $id_meja_val_rahma, status_order_rahma = 'diproses' 
        WHERE id_order_rahma = '$id_order_rahma'
    ");

    // Redirect ke halaman cetak struk kasir
    header("Location: ../kasir/cetak_struk_rahma.php?id=$id_transaksi_rahma");
    exit;
} else {
    header("Location: ../kasir/pembayaran_rahma.php?id=$id_order_rahma&error=1");
    exit;
}
?>