<?php
session_start();

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if (empty($_SESSION['keranjang_rahma'])) {
    header("Location: ../pelanggan/menu_rahma.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pelanggan/konfirmasi_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Ambil data dari form
$nama_pelanggan_rahma = $_POST['nama_pelanggan_rahma'] ?? '';
$keterangan_rahma = $_POST['keterangan_rahma'] ?? '-';
$id_meja_rahma = $_SESSION['id_meja_rahma'] ?? '';
$keranjang_rahma = $_SESSION['keranjang_rahma'];
$id_user_rahma = $_SESSION['id_user_rahma'] ?? null;
$is_member_rahma = isset($_SESSION['id_user_rahma']);

// Validasi data penting — nama pelanggan dan nomor meja wajib diisi
if (empty($nama_pelanggan_rahma) || empty($id_meja_rahma)) {
    header("Location: ../pelanggan/konfirmasi_rahma.php");
    exit;
}

// Hitung grand total dan diskon
$grand_total_rahma = 0;
foreach ($keranjang_rahma as $item_rahma) {
    $grand_total_rahma += $item_rahma['harga_rahma'] * $item_rahma['qty_rahma'];
}
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = ($grand_total_rahma * $diskon_persen_rahma) / 100;

// Generate ID order baru otomatis
$query_last_order_rahma = mysqli_query($koneksiRahma, "
    SELECT id_order_rahma FROM tbl_order_rahma
    ORDER BY id_order_rahma DESC LIMIT 1
");
$last_order_rahma = mysqli_fetch_assoc($query_last_order_rahma);
$last_id_order_rahma = $last_order_rahma['id_order_rahma'] ?? 'OD000';
$angka_order_rahma = (int) substr($last_id_order_rahma, 2) + 1;
$id_order_rahma = 'OD' . str_pad($angka_order_rahma, 3, '0', STR_PAD_LEFT);

$waktu_order_rahma = date('Y-m-d');

// Simpan order ke tbl_order_rahma
$id_user_val_rahma = $id_user_rahma ? "'$id_user_rahma'" : "NULL";
$insert_order_rahma = mysqli_query($koneksiRahma, "
    INSERT INTO tbl_order_rahma
        (id_order_rahma, id_meja_rahma, id_user_rahma, nama_pelanggan_rahma, keterangan_rahma, waktu_order_rahma, status_order_rahma)
    VALUES
        ('$id_order_rahma', '$id_meja_rahma', $id_user_val_rahma, '$nama_pelanggan_rahma', '$keterangan_rahma', '$waktu_order_rahma', 'dibuat')
");

// Kalau gagal simpan order, balik ke konfirmasi
if (!$insert_order_rahma) {
    header("Location: ../pelanggan/konfirmasi_rahma.php?error=1");
    exit;
}

// Simpan setiap item ke tbl_detail_order_rahma
foreach ($keranjang_rahma as $item_rahma) {
    // Generate ID detail order
    $query_last_dorder_rahma = mysqli_query($koneksiRahma, "
        SELECT id_dorder_rahma FROM tbl_detail_order_rahma
        ORDER BY id_dorder_rahma DESC LIMIT 1
    ");
    $last_dorder_rahma = mysqli_fetch_assoc($query_last_dorder_rahma);
    $last_id_dorder_rahma = $last_dorder_rahma['id_dorder_rahma'] ?? 'DOD000';
    $angka_dorder_rahma = (int) substr($last_id_dorder_rahma, 3) + 1;
    $id_dorder_rahma = 'DOD' . str_pad($angka_dorder_rahma, 3, '0', STR_PAD_LEFT);

    $subtotal_rahma = $item_rahma['harga_rahma'] * $item_rahma['qty_rahma'];
    $catatan_item_rahma = $item_rahma['catatan_rahma'] ?? '-';

    mysqli_query($koneksiRahma, "
        INSERT INTO tbl_detail_order_rahma
            (id_dorder_rahma, id_order_rahma, id_menu_rahma, qty_rahma, catatan_rahma, status_item_rahma, subtotal_rahma)
        VALUES
            ('$id_dorder_rahma', '$id_order_rahma', '{$item_rahma['id_menu_rahma']}', '{$item_rahma['qty_rahma']}', '$catatan_item_rahma', 'tersedia', '$subtotal_rahma')
    ");
}

// Update status meja jadi terpakai
mysqli_query($koneksiRahma, "
    UPDATE tbl_meja_rahma
    SET status_rahma = 'terpakai'
    WHERE id_meja_rahma = '$id_meja_rahma'
");

// Kosongkan keranjang setelah order berhasil
unset($_SESSION['keranjang_rahma']);
unset($_SESSION['id_meja_rahma']);

// Redirect ke halaman sukses atau riwayat
header("Location: ../pelanggan/riwayat_rahma.php?order=$id_order_rahma&sukses=1");
exit;
?>