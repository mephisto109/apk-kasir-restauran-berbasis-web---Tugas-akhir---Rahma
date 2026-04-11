<?php
$koneksiRahma = mysqli_connect("localhost", "root","","db_restoran_rahma");

if (mysqli_connect_errno()) {
    echo "koneksi database gagal : " . mysqli_connect_errno();
} 