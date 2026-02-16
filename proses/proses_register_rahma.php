<?php
session_start();
include "../koneksi/koneksi_rahma.php";

// Ambil data form
$nama_rahma     = mysqli_real_escape_string($koneksiRahma, $_POST['nama']);
$username_rahma = mysqli_real_escape_string($koneksiRahma, $_POST['username']);
$password_rahma = $_POST['password'];

// Cek username sudah ada
$cek_rahma = mysqli_query($koneksiRahma, 
    "SELECT id_user_rahma 
     FROM tbl_user_rahma 
     WHERE username_rahma='$username_rahma'");

if (mysqli_num_rows($cek_rahma) > 0) {
    echo "<script>
            alert('Username sudah digunakan!');
            window.location='../register_rahma.php';
          </script>";
    exit;
}

// Ambil ID terakhir berdasarkan angka
$qRahma = mysqli_query($koneksiRahma, 
    "SELECT id_user_rahma 
     FROM tbl_user_rahma 
     ORDER BY CAST(SUBSTRING(id_user_rahma,4) AS UNSIGNED) DESC 
     LIMIT 1");

$dRahma = mysqli_fetch_assoc($qRahma);

if ($dRahma) {
    $noRahma = (int) substr($dRahma['id_user_rahma'], 3);
    $noRahma++;
} else {
    $noRahma = 1;
}

// Generate ID baru
$id_user_rahma = "USN" . str_pad($noRahma, 3, "0", STR_PAD_LEFT);

// Hash password
$password_hash = password_hash($password_rahma, PASSWORD_DEFAULT);

// Default role pelanggan
$id_role = "R003";

// Insert ke database
mysqli_query($koneksiRahma, "INSERT INTO tbl_user_rahma 
(id_user_rahma, username_rahma, password_rahma, nama_rahma, id_role_rahma)
VALUES 
('$id_user_rahma', '$username_rahma', '$password_hash', '$nama_rahma', '$id_role')");

echo "<script>
        alert('Register berhasil!');
        window.location='../login_rahma.php';
      </script>";
exit;
?>
