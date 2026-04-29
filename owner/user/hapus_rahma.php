<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

$id_user_rahma = $_GET['id_user_rahma'];

$query_rahma = mysqli_query($koneksiRahma, 
    "DELETE FROM tbl_user_rahma WHERE id_user_rahma='$id_user_rahma'");

if($query_rahma){
    echo "<script>
            alert('Data berhasil dihapus');
            window.location='data_user_rahma.php';
          </script>";
} else {
    echo "<script>
            alert('Data gagal dihapus...');
            window.location='data_user_rahma.php';
          </script>";
}
?>