<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

if (isset($_GET['id_menu_rahma'])) {

    $id_menu_rahma = $_GET['id_menu_rahma'];

    $update = mysqli_query($koneksiRahma,
        "UPDATE tbl_menu_rahma 
         SET status_rahma='aktif' 
         WHERE id_menu_rahma='$id_menu_rahma'");

    if ($update) {
        echo "<script>
                alert('Menu berhasil diaktifkan kembali!');
                window.location='index_rahma.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengaktifkan menu!');
                window.location='index_rahma.php';
              </script>";
    }

} else {
    echo "<script>
            alert('ID tidak ditemukan!');
            window.location='index_rahma.php';
          </script>";
}
?>