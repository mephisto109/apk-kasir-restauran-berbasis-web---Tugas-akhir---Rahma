<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — hanya owner (R001)
if (!isset($_SESSION['id_user_rahma']) || $_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Mulai transaksi DB — biar kalau ada yang gagal, semua dibatalkan sekaligus
mysqli_begin_transaction($koneksiRahma);

try {
    $id_order_rahma = $_POST['id_order_rahma'] ?? '';
    $list_id_dorder_rahma = $_POST['id_dorder_rahma'] ?? [];
    $list_id_menu_rahma = $_POST['id_menu_rahma'] ?? [];
    $list_harga_rahma = $_POST['harga_rahma'] ?? [];
    $list_qty_rahma = $_POST['qty_rahma'] ?? [];
    $list_hapus_rahma = $_POST['hapus_rahma'] ?? [];

    if (!$id_order_rahma) {
        throw new Exception("ID order tidak ditemukan");
    }

    // ─── Ambil data transaksi yang ada — untuk hitung ulang total ───
    $data_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
        $koneksiRahma,
        "SELECT * FROM tbl_transaksi_rahma WHERE id_order_rahma = '$id_order_rahma'"
    ));
    $diskon_persen_rahma = (float) ($data_transaksi_rahma['diskon_rahma'] ?? 0);

    // ─── Proses setiap item ───
    $subtotal_baru_rahma = 0; // Akan dipakai untuk hitung ulang total transaksi

    // Loop berdasarkan index yang sama di semua array item
    foreach ($list_id_dorder_rahma as $idx_rahma => $id_dorder_rahma) {
        $id_menu_val_rahma = mysqli_real_escape_string($koneksiRahma, $list_id_menu_rahma[$idx_rahma]);
        $harga_val_rahma = (float) $list_harga_rahma[$idx_rahma];
        $qty_val_rahma = (int) $list_qty_rahma[$idx_rahma];
        $hapus_val_rahma = (int) $list_hapus_rahma[$idx_rahma];
        $subtotal_val_rahma = $harga_val_rahma * $qty_val_rahma;

        if ($id_dorder_rahma === '') {
            // ─── ITEM BARU: id_dorder kosong → insert ke DB ───
            if ($hapus_val_rahma === 1)
                continue; // Kalau langsung dihapus sebelum disimpan, skip

            // Generate ID detail baru
            $q_last_d_rahma = mysqli_query(
                $koneksiRahma,
                "SELECT id_dorder_rahma FROM tbl_detail_order_rahma ORDER BY id_dorder_rahma DESC LIMIT 1"
            );
            $last_d_rahma = mysqli_fetch_assoc($q_last_d_rahma);
            $angka_d_rahma = (int) substr($last_d_rahma['id_dorder_rahma'] ?? 'DOD000', 3) + 1;
            $id_dorder_baru_rahma = 'DOD' . str_pad($angka_d_rahma, 3, '0', STR_PAD_LEFT);

            $sql_insert_rahma = "
                INSERT INTO tbl_detail_order_rahma
                    (id_dorder_rahma, id_order_rahma, id_menu_rahma, qty_rahma, catatan_rahma, status_item_rahma, subtotal_rahma)
                VALUES
                    ('$id_dorder_baru_rahma', '$id_order_rahma', '$id_menu_val_rahma',
                    '$qty_val_rahma', '-', 'tersedia', '$subtotal_val_rahma')
            ";
            if (!mysqli_query($koneksiRahma, $sql_insert_rahma)) {
                throw new Exception("Gagal tambah item baru: " . mysqli_error($koneksiRahma));
            }

            $subtotal_baru_rahma += $subtotal_val_rahma;

        } elseif ($hapus_val_rahma === 1) {
            // ─── ITEM DIHAPUS: flag hapus = 1 → delete dari DB ───
            $id_dorder_esc_rahma = mysqli_real_escape_string($koneksiRahma, $id_dorder_rahma);
            $sql_hapus_rahma = "DELETE FROM tbl_detail_order_rahma WHERE id_dorder_rahma = '$id_dorder_esc_rahma'";
            if (!mysqli_query($koneksiRahma, $sql_hapus_rahma)) {
                throw new Exception("Gagal hapus item: " . mysqli_error($koneksiRahma));
            }
            // Item ini dihapus — tidak ikut dihitung ke subtotal baru

        } else {
            // ─── ITEM LAMA: update qty dan subtotal ───
            $id_dorder_esc_rahma = mysqli_real_escape_string($koneksiRahma, $id_dorder_rahma);
            $sql_update_rahma = "
                UPDATE tbl_detail_order_rahma
                SET qty_rahma = '$qty_val_rahma', subtotal_rahma = '$subtotal_val_rahma'
                WHERE id_dorder_rahma = '$id_dorder_esc_rahma'
            ";
            if (!mysqli_query($koneksiRahma, $sql_update_rahma)) {
                throw new Exception("Gagal update item: " . mysqli_error($koneksiRahma));
            }

            $subtotal_baru_rahma += $subtotal_val_rahma;
        }
    }

    // ─── Hitung ulang total transaksi ───
    // Rumusnya sama kayak di detail_order: subtotal → diskon → pajak 11%
    $diskon_nominal_rahma = $subtotal_baru_rahma * $diskon_persen_rahma / 100;
    $setelah_diskon_rahma = $subtotal_baru_rahma - $diskon_nominal_rahma;
    $pajak_nominal_rahma = $setelah_diskon_rahma * 0.11;
    $total_akhir_rahma = $setelah_diskon_rahma + $pajak_nominal_rahma;

    // Update total di tabel transaksi kalau transaksinya ada
    if ($data_transaksi_rahma) {
        $sql_update_transaksi_rahma = "
            UPDATE tbl_transaksi_rahma
            SET total_rahma = '$total_akhir_rahma'
            WHERE id_order_rahma = '$id_order_rahma'
        ";
        if (!mysqli_query($koneksiRahma, $sql_update_transaksi_rahma)) {
            throw new Exception("Gagal update total transaksi: " . mysqli_error($koneksiRahma));
        }
    }

    // Semua berhasil — commit ke DB
    mysqli_commit($koneksiRahma);

    // Redirect balik ke halaman edit dengan pesan sukses
    header("Location: ../owner/laporan/edit_order_rahma.php?id=$id_order_rahma&sukses=1");
    exit;

} catch (Exception $e_rahma) {
    // Ada yang gagal — batalkan semua perubahan
    mysqli_rollback($koneksiRahma);

    // Redirect balik dengan pesan gagal
    $id_order_rahma = $_POST['id_order_rahma'] ?? '';
    header("Location: ../owner/laporan/edit_order_rahma.php?id=$id_order_rahma&gagal=1");
    exit;
}