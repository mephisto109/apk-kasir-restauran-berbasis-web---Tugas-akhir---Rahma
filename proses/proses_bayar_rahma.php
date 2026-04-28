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
// UBAH: Ambil nomor meja dari form pembayaran
$id_meja_rahma = $_POST['id_meja_rahma'] ?? '';

// Hitung diskon berdasarkan membership
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT id_user_rahma FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'
");
$data_order_rahma = mysqli_fetch_assoc($query_order_rahma);
$is_member_rahma = !empty($data_order_rahma['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$diskon_nominal_rahma = (int) ($grand_total_rahma * $diskon_persen_rahma / 100);

// Total setelah diskon
$total_setelah_diskon_rahma = $grand_total_rahma - $diskon_nominal_rahma;

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
        ('$id_transaksi_rahma', '$id_order_rahma', '$diskon_persen_rahma', '$total_setelah_diskon_rahma', '$bayar_rahma', '$kembalian_rahma', '$waktu_transaksi_rahma')
");

if ($insert_rahma) {
    // UBAH: Update order dengan id_meja dan status_order = 'diproses' (bayar dulu, baru diproses)
    $id_meja_val_rahma = !empty($id_meja_rahma) ? "'$id_meja_rahma'" : "NULL";
    mysqli_query($koneksiRahma, "
        UPDATE tbl_order_rahma 
        SET id_meja_rahma = $id_meja_val_rahma, status_order_rahma = 'diproses' 
        WHERE id_order_rahma = '$id_order_rahma'
    ");

    // Update status meja jadi kosong — hanya kalau dine in dan meja dipilih
    $query_cek_jenis_rahma = mysqli_query($koneksiRahma, "
    SELECT jenis_pesanan_rahma 
    FROM tbl_order_rahma 
    WHERE id_order_rahma = '$id_order_rahma'
");
    $data_jenis_rahma = mysqli_fetch_assoc($query_cek_jenis_rahma);

    
    if ($data_jenis_rahma['jenis_pesanan_rahma'] == 'dine in' && !empty($id_meja_rahma)) {
        // Biarkan status meja tetap sebagaimana adanya (tidak diupdate)
        // Karena sekarang status meja tidak digunakan lagi
    }
    // Redirect ke cetak struk
    header("Location: ../kasir/cetak_struk_rahma.php?id=$id_transaksi_rahma");
    exit;
} else {
    // Kalau gagal, balik ke halaman pembayaran
    header("Location: ../kasir/pembayaran_rahma.php?id=$id_order_rahma&error=1");
    exit;
}
?>