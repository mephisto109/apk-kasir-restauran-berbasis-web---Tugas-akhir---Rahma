<?php
session_start();
// Cegah cache supaya ga bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../../login_rahma.php");
    exit;
}

include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

// =====================
// FILTER
// =====================
$tgl_awal_rahma = $_GET['tgl_awal'] ?? '';
$tgl_akhir_rahma = $_GET['tgl_akhir'] ?? '';

$where_rahma = "";

if ($tgl_awal_rahma && $tgl_akhir_rahma) {
    $where_rahma = "WHERE DATE(waktu_transaksi_rahma) 
    BETWEEN '$tgl_awal_rahma' AND '$tgl_akhir_rahma'";
}

// =====================
// QUERY DATA
// =====================
$query_rahma = mysqli_query($koneksiRahma, "
    SELECT * FROM tbl_transaksi_rahma
    $where_rahma
    ORDER BY waktu_transaksi_rahma DESC
");

// =====================
// RINGKASAN
// =====================
$total_transaksi_rahma = mysqli_num_rows($query_rahma);

$total_pendapatan_rahma = 0;
$data_list_rahma = [];

while ($row = mysqli_fetch_assoc($query_rahma)) {
    $total_pendapatan_rahma += $row['total_rahma'];
    $data_list_rahma[] = $row;
}

$rata_rahma = $total_transaksi_rahma > 0
    ? $total_pendapatan_rahma / $total_transaksi_rahma
    : 0;
?>

<!DOCTYPE html>
<html>

<head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Laporan Owner</title>
</head>

<body>

    <h2>Laporan Owner</h2>

    <!-- =====================
FILTER
===================== -->
    <form method="GET">
        Tanggal Awal:
        <input type="date" name="tgl_awal" value="<?= $tgl_awal_rahma ?>">

        Tanggal Akhir:
        <input type="date" name="tgl_akhir" value="<?= $tgl_akhir_rahma ?>">

        <button type="submit">Filter</button>
    </form>

    <br>

    <!-- =====================
    RINGKASAN
    ===================== -->
    <h3>Ringkasan</h3>
    <p>Total Transaksi: <b><?= $total_transaksi_rahma ?></b></p>
    <p>Total Pendapatan: <b>Rp <?= number_format($total_pendapatan_rahma) ?></b></p>
    <p>Rata-rata: <b>Rp <?= number_format($rata_rahma) ?></b></p>

    <br>

    <!-- =====================
    TABEL LAPORAN
    ===================== -->
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Diskon</th>
            <th>Bayar</th>
            <th>Kembali</th>
            <th>print</th>
        </tr>

        <?php if (count($data_list_rahma) > 0) { ?>
            <?php foreach ($data_list_rahma as $row) { ?>
                <tr>
                    <td>
                        <a href="detail_order_rahma.php?id=<?= $row['id_order_rahma'] ?>">
                            <?= $row['id_order_rahma'] ?>
                        </a>
                    </td>
                    <td><?= date('d-m-Y H:i', strtotime($row['waktu_transaksi_rahma'])) ?></td>
                    <td>Rp <?= number_format($row['total_rahma']) ?></td>
                    <td><?=($row['diskon_rahma']) ?>%</td>
                    <td>Rp <?= number_format($row['bayar_rahma']) ?></td>
                    <td>Rp <?= number_format($row['kembalian_rahma']) ?></td>
                    <!--<td>
                        <a href="print_detail_order_rahma.php?id=<?//= $row['id_order_rahma'] ?>" target="_blank">
                            Cetak transkasi
                        </a>
                    </td>-->

                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5" align="center">Data tidak ditemukan</td>
            </tr>
        <?php } ?>
    </table>


    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>