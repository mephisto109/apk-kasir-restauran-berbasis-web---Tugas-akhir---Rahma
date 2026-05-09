<?php
ob_start(); // Tangkap semua output liar sebelum JSON dikirim
session_start();

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    ob_end_clean();
    echo json_encode(['error_rahma' => 'Session habis, silakan login ulang']);
    exit;
}

if (empty($_SESSION['keranjang_rahma'])) {
    ob_end_clean();
    echo json_encode(['error_rahma' => 'Keranjang kosong']);
    exit;
}

include '../koneksi/koneksi_rahma.php';

mysqli_begin_transaction($koneksiRahma);

try {
    $nama_pelanggan_rahma = trim($_POST['nama_pelanggan_rahma'] ?? '');
    $keterangan_rahma     = $_POST['keterangan_rahma'] ?? '-';
    $keranjang_rahma      = $_SESSION['keranjang_rahma'];
    $id_user_rahma        = $_SESSION['id_user_rahma'] ?? null;
    $jenis_pesanan_rahma  = $_SESSION['jenis_pesanan_rahma'] ?? 'Dine In';

    if (empty($nama_pelanggan_rahma)) {
        throw new Exception("Nama pelanggan kosong");
    }

    // Tentukan id_meja — take away NULL, dine in wajib isi
    $id_meja_rahma_val = "NULL";
    if (strtolower($jenis_pesanan_rahma) !== 'take away') {
        $nomor_meja_input_rahma = (int) ($_POST['nomor_meja_rahma'] ?? 0);
        if ($nomor_meja_input_rahma <= 0) {
            throw new Exception("Nomor meja wajib diisi untuk Dine In");
        }
        $id_meja_rahma_val = "'M" . str_pad($nomor_meja_input_rahma, 3, '0', STR_PAD_LEFT) . "'";
    }

    // Generate ID Order
    $q_last_rahma   = mysqli_query($koneksiRahma, "
        SELECT id_order_rahma FROM tbl_order_rahma 
        ORDER BY id_order_rahma DESC LIMIT 1
    ");
    $last_rahma     = mysqli_fetch_assoc($q_last_rahma);
    $angka_rahma    = (int) substr($last_rahma['id_order_rahma'] ?? 'OD000', 2) + 1;
    $id_order_rahma = 'OD' . str_pad($angka_rahma, 3, '0', STR_PAD_LEFT);

    // Insert header order
    $id_user_val_rahma = $id_user_rahma ? "'$id_user_rahma'" : "NULL";
    $waktu_rahma       = date('Y-m-d H:i:s');

    $sql_order_rahma = "
        INSERT INTO tbl_order_rahma 
            (id_order_rahma, id_meja_rahma, id_user_rahma, nama_pelanggan_rahma, 
            keterangan_rahma, waktu_order_rahma, status_order_rahma, jenis_pesanan_rahma) 
        VALUES 
            ('$id_order_rahma', $id_meja_rahma_val, $id_user_val_rahma, '$nama_pelanggan_rahma', 
            '$keterangan_rahma', '$waktu_rahma', 'menunggu_pembayaran', '$jenis_pesanan_rahma')
    ";

    if (!mysqli_query($koneksiRahma, $sql_order_rahma)) {
        throw new Exception("Gagal simpan order: " . mysqli_error($koneksiRahma));
    }

    // Insert detail item
    foreach ($keranjang_rahma as $item_rahma) {
        $q_last_d_rahma  = mysqli_query($koneksiRahma, "
            SELECT id_dorder_rahma FROM tbl_detail_order_rahma 
            ORDER BY id_dorder_rahma DESC LIMIT 1
        ");
        $last_d_rahma    = mysqli_fetch_assoc($q_last_d_rahma);
        $angka_d_rahma   = (int) substr($last_d_rahma['id_dorder_rahma'] ?? 'DOD000', 3) + 1;
        $id_dorder_rahma = 'DOD' . str_pad($angka_d_rahma, 3, '0', STR_PAD_LEFT);

        $subtotal_rahma = $item_rahma['harga_rahma'] * $item_rahma['qty_rahma'];
        $catatan_rahma  = mysqli_real_escape_string($koneksiRahma, $item_rahma['catatan_rahma'] ?? '-');
        $id_menu_rahma  = $item_rahma['id_menu_rahma'];
        $qty_rahma      = $item_rahma['qty_rahma'];

        $sql_detail_rahma = "
            INSERT INTO tbl_detail_order_rahma 
                (id_dorder_rahma, id_order_rahma, id_menu_rahma, qty_rahma, 
                catatan_rahma, status_item_rahma, subtotal_rahma) 
            VALUES 
                ('$id_dorder_rahma', '$id_order_rahma', '$id_menu_rahma', '$qty_rahma', 
                '$catatan_rahma', 'tersedia', '$subtotal_rahma')
        ";

        if (!mysqli_query($koneksiRahma, $sql_detail_rahma)) {
            throw new Exception("Gagal simpan detail: " . mysqli_error($koneksiRahma));
        }
    }

    mysqli_commit($koneksiRahma);

    unset($_SESSION['keranjang_rahma']);
    $_SESSION['id_order_terakhir_rahma'] = $id_order_rahma;

    // Kirim balik sebagai JSON — bukan plain text
    ob_end_clean();
    echo json_encode(['id_order_rahma' => $id_order_rahma]);
    exit;

} catch (Exception $e_rahma) {
    mysqli_rollback($koneksiRahma);
    // Kirim error sebagai JSON — bukan redirect!
    ob_end_clean();
    echo json_encode(['error_rahma' => $e_rahma->getMessage()]);
    exit;
}