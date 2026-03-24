<?php
session_start();

// Cegah cache
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// CEK LOGIN
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

// CEK ROLE (OWNER = R001)
if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../templates/navbar_rahma.php';

// =====================
// OVERVIEW
// =====================

// Hitung total user
$total_user_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total 
     FROM tbl_user_rahma 
     WHERE id_role_rahma = 'R003'"
))['total'];


// Hitung total pegawai (kasir + chef)
$total_pegawai_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total 
     FROM tbl_user_rahma 
     WHERE id_role_rahma IN ('R002','R004')"
))['total'];


// Hitung total menu
$total_menu_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total 
     FROM tbl_menu_rahma 
     WHERE status_rahma = 'aktif'"
))['total'];


// Hitung total transaksi
$total_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total FROM tbl_transaksi_rahma"
))['total'];


// Hitung pendapatan hari ini
$today_rahma = date('Y-m-d');
$pendapatan_hari_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT SUM(total_rahma) as total 
     FROM tbl_transaksi_rahma 
     WHERE DATE(waktu_transaksi_rahma)='$today_rahma'"
))['total'] ?? 0;


// Hitung pendapatan bulan ini
$bulan_rahma = date('m');
$tahun_rahma = date('Y');
$pendapatan_bulan_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT SUM(total_rahma) as total 
     FROM tbl_transaksi_rahma 
     WHERE MONTH(waktu_transaksi_rahma)='$bulan_rahma' 
     AND YEAR(waktu_transaksi_rahma)='$tahun_rahma'"
))['total'] ?? 0;

// =====================
// RECENT
// =====================

// Tampilkan 5 transaksi terbaru
$transaksi_terbaru_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_transaksi_rahma ORDER BY waktu_transaksi_rahma DESC LIMIT 5"
);

// Tampilkan 5 user terbaru
$user_baru_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_user_rahma ORDER BY id_user_rahma DESC LIMIT 5"
);


// =====================
// ALERT
// =====================

// Cek menu dengan stok habis
$stok_habis_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma WHERE status_menu_rahma = 'habis'"
);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Data Menu</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }

        .card {
            display: inline-block;
            width: 180px;
            margin: 10px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-top: 40px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
        }

        .alert {
            background: #ffe5e5;
            padding: 10px;
            margin: 5px 0;
            border-left: 5px solid red;
        }
    </style>
</head>

<body>

    <h1>Dashboard Owner</h1>

    <!-- OVERVIEW -->
    <div class="card">User<br><b><?= $total_user_rahma ?></b></div>
    <div class="card">Pegawai<br><b><?= $total_pegawai_rahma ?></b></div>
    <div class="card">Menu<br><b><?= $total_menu_rahma ?></b></div>
    <div class="card">Transaksi<br><b><?= $total_transaksi_rahma ?></b></div>
    <div class="card">Hari Ini<br><b>Rp <?= $pendapatan_hari_rahma ?></b></div>
    <div class="card">Bulan Ini<br><b>Rp <?= $pendapatan_bulan_rahma ?></b></div>

    <!-- RECENT -->
    <h2>Transaksi Terbaru</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Total</th>
        </tr>
        <?php while ($t_rahma = mysqli_fetch_assoc($transaksi_terbaru_rahma)) { ?>
            <tr>
                <td><?= $t_rahma['id_transaksi_rahma'] ?></td>
                <td><?= $t_rahma['waktu_transaksi_rahma'] ?></td>
                <td><?= $t_rahma['total_rahma'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <h2>User Baru</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
        </tr>
        <?php while ($u_rahma = mysqli_fetch_assoc($user_baru_rahma)) { ?>
            <tr>
                <td><?= $u_rahma['id_user_rahma'] ?></td>
                <td><?= $u_rahma['nama_rahma'] ?></td>
            </tr>
        <?php } ?>
    </table>


    <!-- ALERT -->
    <h2>Notifikasi</h2>

    <?php while ($s_rahma = mysqli_fetch_assoc($stok_habis_rahma)) { ?>
        <div class="alert">Stok habis: <?= $s_rahma['nama_menu_rahma'] ?></div>
    <?php } ?>
    
    <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

</body>

</html>



