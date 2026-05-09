<?php
session_start();
include '../koneksi/koneksi_rahma.php';

// Cek apakah ada kiriman POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Tangkap data dari form chef
    $id_menu_rahma = $_POST['id_menu_rahma'];
    $status_baru_rahma = $_POST['status_baru_rahma'];
    $kategori_rahma = $_POST['redirect_kategori_rahma'];

    // Validasi data (jangan sampai kosong)
    if (!empty($id_menu_rahma) && !empty($status_baru_rahma)) {
        
        // Query update status menu
        $query_update_rahma = "UPDATE tbl_menu_rahma 
                                SET status_menu_rahma = '$status_baru_rahma' 
                                WHERE id_menu_rahma = '$id_menu_rahma'";
        
        $eksekusi_rahma = mysqli_query($koneksiRahma, $query_update_rahma);

        if ($eksekusi_rahma) {
            // Kalau update berhasil, redirect kembali ke halaman kelola menu
            header("Location: ../chef/kelola_menu_rahma.php?kategori=$kategori_rahma&sukses=$status_baru_rahma");
            exit;
        } else {
            header("Location: ../chef/kelola_menu_rahma.php?kategori=$kategori_rahma&gagal=1");
            exit;
        }

    } else {
        // Kalau data POST gak lengkap
        header("Location: ../chef/kelola_menu_rahma.php?gagal=1");
        exit;
    }

} else {
    // Kalau ada yang coba akses file ini tanpa POST (iseng nembak URL)
    header("Location: ../chef/kelola_menu_rahma.php");
    exit;
}
?>