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

include '../koneksi/koneksi_rahma.php';

// Matikan autocommit untuk memulai transaksi
mysqli_begin_transaction($koneksiRahma);

try {
    // Ambil data dari form
    $nama_pelanggan_rahma = trim($_POST['nama_pelanggan_rahma'] ?? '');
    $keterangan_rahma = $_POST['keterangan_rahma'] ?? '-';
    $keranjang_rahma = $_SESSION['keranjang_rahma'];
    $id_user_rahma = $_SESSION['id_user_rahma'] ?? null;
    $jenis_pesanan_rahma = $_SESSION['jenis_pesanan_rahma'] ?? 'Dine In';

    if (empty($nama_pelanggan_rahma)) {
        throw new Exception("Nama pelanggan kosong");
    }

    // 1. GENERATE ID ORDER
    $q_last = mysqli_query($koneksiRahma, "SELECT id_order_rahma FROM tbl_order_rahma ORDER BY id_order_rahma DESC LIMIT 1");
    $last = mysqli_fetch_assoc($q_last);
    $angka = (int) substr($last['id_order_rahma'] ?? 'OD000', 2) + 1;
    $id_order_rahma = 'OD' . str_pad($angka, 3, '0', STR_PAD_LEFT);

    // 2. INSERT ORDER (Meja NULL, Status Menunggu Pembayaran)
    $id_user_val = $id_user_rahma ? "'$id_user_rahma'" : "NULL";
    $waktu = date('Y-m-d H:i:s'); // Pakai datetime lebih akurat

    $sql_order = "INSERT INTO tbl_order_rahma 
                (id_order_rahma, id_meja_rahma, id_user_rahma, nama_pelanggan_rahma, keterangan_rahma, waktu_order_rahma, status_order_rahma, jenis_pesanan_rahma) 
                VALUES 
                ('$id_order_rahma', NULL, $id_user_val, '$nama_pelanggan_rahma', '$keterangan_rahma', '$waktu', 'menunggu_pembayaran', '$jenis_pesanan_rahma')";
    
    if (!mysqli_query($koneksiRahma, $sql_order)) {
        throw new Exception("Gagal simpan header order");
    }

    // 3. INSERT DETAIL ITEM
    foreach ($keranjang_rahma as $item) {
        // Generate ID Detail Manual (Karena primary key kamu string)
        $q_last_d = mysqli_query($koneksiRahma, "SELECT id_dorder_rahma FROM tbl_detail_order_rahma ORDER BY id_dorder_rahma DESC LIMIT 1");
        $last_d = mysqli_fetch_assoc($q_last_d);
        $angka_d = (int) substr($last_d['id_dorder_rahma'] ?? 'DOD000', 3) + 1;
        $id_dorder_rahma = 'DOD' . str_pad($angka_d, 3, '0', STR_PAD_LEFT);

        $subtotal = $item['harga_rahma'] * $item['qty_rahma'];
        $catatan = $item['catatan_rahma'] ?? '-';
        $id_menu = $item['id_menu_rahma'];
        $qty = $item['qty_rahma'];

        $sql_detail = "INSERT INTO tbl_detail_order_rahma 
                    (id_dorder_rahma, id_order_rahma, id_menu_rahma, qty_rahma, catatan_rahma, status_item_rahma, subtotal_rahma) 
                    VALUES 
                    ('$id_dorder_rahma', '$id_order_rahma', '$id_menu', '$qty', '$catatan', 'tersedia', '$subtotal')";
        
        if (!mysqli_query($koneksiRahma, $sql_detail)) {
            throw new Exception("Gagal simpan detail item");
        }
    }

    // Jika semua oke, Commit (Simpan Permanen)
    mysqli_commit($koneksiRahma);

    // Bersihkan session
    unset($_SESSION['keranjang_rahma']);
    $_SESSION['id_order_terakhir_rahma'] = $id_order_rahma;

    // Redirect ke struk
    header("Location: ../pelanggan/cetak_struk_pesanan_rahma.php?id_order=" . $id_order_rahma);
    exit;

} catch (Exception $e) {
    // Jika ada yang gagal, batalkan semua (Rollback)
    mysqli_rollback($koneksiRahma);
    header("Location: ../pelanggan/konfirmasi_rahma.php?error=1&msg=" . urlencode($e->getMessage()));
    exit;
}