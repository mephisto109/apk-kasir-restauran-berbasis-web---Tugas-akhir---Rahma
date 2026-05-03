<?php
// =====================================================
// File ini dipanggil dari JS (bayar_online_rahma.php)
// setelah Midtrans konfirmasi pembayaran berhasil/pending.
// Tugasnya: simpan transaksi ke tbl_transaksi_rahma,
// update status order, lalu kasih balik id_transaksi.
// =====================================================

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error_rahma' => 'Method not allowed']);
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../config/midtrans_config_rahma.php';

// Ambil data dari JSON body
$input_rahma              = json_decode(file_get_contents('php://input'), true);
$id_order_rahma           = $input_rahma['id_order_rahma'] ?? '';
$total_rahma              = (int) ($input_rahma['total_rahma'] ?? 0);
$pajak_rahma              = (int) ($input_rahma['pajak_rahma'] ?? 0);
$diskon_rahma             = (int) ($input_rahma['diskon_rahma'] ?? 0);
$midtrans_order_id_rahma  = $input_rahma['midtrans_order_id_rahma'] ?? '';
$payment_type_rahma       = $input_rahma['payment_type_rahma'] ?? 'online';
$transaction_id_rahma     = $input_rahma['transaction_id_rahma'] ?? '';
$status_rahma             = $input_rahma['status_rahma'] ?? 'pending';

// Validasi
if (empty($id_order_rahma) || $total_rahma <= 0) {
    echo json_encode(['error_rahma' => 'Data tidak lengkap']);
    exit;
}

// Generate ID transaksi baru (format T001, T002, dst)
$q_last_rahma   = mysqli_query($koneksiRahma, "
    SELECT id_transaksi_rahma FROM tbl_transaksi_rahma 
    ORDER BY id_transaksi_rahma DESC LIMIT 1
");
$last_t_rahma      = mysqli_fetch_assoc($q_last_rahma);
$angka_t_rahma     = (int) substr($last_t_rahma['id_transaksi_rahma'] ?? 'T000', 1) + 1;
$id_transaksi_rahma = 'T' . str_pad($angka_t_rahma, 3, '0', STR_PAD_LEFT);

$waktu_rahma = date('Y-m-d H:i:s');

// "Kasir" untuk transaksi online = sistem (id kosong / id khusus)
// Kita pakai 'SYSTEM' sebagai penanda transaksi online
$id_kasir_rahma = 'SYSTEM';

// Escape string untuk keamanan
$payment_type_esc_rahma      = mysqli_real_escape_string($koneksiRahma, $payment_type_rahma);
$midtrans_order_esc_rahma    = mysqli_real_escape_string($koneksiRahma, $midtrans_order_id_rahma);
$transaction_id_esc_rahma    = mysqli_real_escape_string($koneksiRahma, $transaction_id_rahma);
$status_esc_rahma            = mysqli_real_escape_string($koneksiRahma, $status_rahma);

// Simpan ke tbl_transaksi_rahma
// bayar_rahma = total (karena online sudah pasti sesuai)
// kembalian_rahma = 0 (tidak ada kembalian di online)
// Simpan status sebagai 'lunas' untuk online payment
// (bukan pakai status dari Midtrans yang bisa 'pending')
$status_disimpan_rahma = 'lunas';

// Ganti $status_esc_rahma di INSERT dengan ini:
$insert_rahma = mysqli_query($koneksiRahma, "
    INSERT INTO tbl_transaksi_rahma 
        (id_transaksi_rahma, id_order_rahma, id_kasir_rahma, 
        diskon_rahma, pajak_rahma, total_rahma, bayar_rahma, kembalian_rahma,
        waktu_transaksi_rahma, metode_bayar_rahma, midtrans_order_id_rahma,
        midtrans_transaction_id_rahma, status_midtrans_rahma)
    VALUES 
        ('$id_transaksi_rahma', '$id_order_rahma', '$id_kasir_rahma',
        '$diskon_rahma', '$pajak_rahma', '$total_rahma', '$total_rahma', '0',
        '$waktu_rahma', '$payment_type_esc_rahma', '$midtrans_order_esc_rahma',
        '$transaction_id_esc_rahma', 'lunas')
");

if ($insert_rahma) {
    // Update status order — kalau sukses langsung diproses, kalau pending tetap menunggu
    $status_order_baru_rahma = 'diproses';
    mysqli_query($koneksiRahma, "
        UPDATE tbl_order_rahma 
        SET status_order_rahma = '$status_order_baru_rahma'
        WHERE id_order_rahma = '$id_order_rahma'
    ");

    // Kasih balik id_transaksi ke frontend untuk redirect ke halaman sukses
    echo json_encode([
        'success_rahma'      => true,
        'id_transaksi_rahma' => $id_transaksi_rahma,
    ]);
} else {
    error_log("Gagal insert transaksi midtrans: " . mysqli_error($koneksiRahma));
    echo json_encode(['error_rahma' => 'Gagal menyimpan transaksi']);
}
exit;