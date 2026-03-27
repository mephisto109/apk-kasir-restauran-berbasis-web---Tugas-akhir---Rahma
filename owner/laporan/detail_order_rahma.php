<?php
session_start();

// CEK LOGIN
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../../login_rahma.php");
    exit;
}

// CEK ROLE OWNER
if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../../login_rahma.php");
    exit;
}

include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

// AMBIL ID ORDER
$id_order_rahma = $_GET['id'] ?? '';

if (!$id_order_rahma) {
    echo "ID Order tidak ditemukan";
    exit;
}

// =====================
// DATA ORDER
// =====================
$data_order_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_order_rahma 
     WHERE id_order_rahma='$id_order_rahma'"
));

if (!$data_order_rahma) {
    echo "Data order tidak ada";
    exit;
}

// =====================
// DATA TRANSAKSI (opsional, karena mungkin belum dibayar)
// =====================
$data_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_transaksi_rahma 
     WHERE id_order_rahma='$id_order_rahma'"
));

// =====================
// DETAIL PESANAN
// =====================
$detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, m.nama_menu_rahma, m.harga_rahma
    FROM tbl_detail_order_rahma d
    JOIN tbl_menu_rahma m 
    ON d.id_menu_rahma = m.id_menu_rahma
    WHERE d.id_order_rahma='$id_order_rahma'
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Detail Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h2>Detail Order</h2>

        <!-- INFO ORDER -->
        <div class="card mb-3">
            <div class="card-body">
                <p>ID Order: <b><?= $data_order_rahma['id_order_rahma'] ?></b></p>
                <p>Tanggal: <b><?= date('d-m-Y H:i', strtotime($data_order_rahma['waktu_order_rahma'])) ?></b></p>
                <p>
                    Nama Pemesan:
                    <b><?= $data_order_rahma['nama_pelanggan_rahma'] ?></b>

                    <?php if (!empty($data_order_rahma['id_user_rahma'])) { ?>
                        <span class="badge bg-success">Member</span>
                    <?php } ?>
                </p>
                <p>Meja: <b><?= $data_order_rahma['id_meja_rahma'] ?></b></p>
                <p><td>
                        <a href="print_detail_order_rahma.php?id=<?= $row['id_order_rahma'] ?>" target="_blank">
                            Cetak order
                        </a>
                    </td></p>
            </div>
        </div>

        <!-- INFO TRANSAKSI -->
        <?php if ($data_transaksi_rahma) { ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p>Total: <b>Rp <?= number_format($data_transaksi_rahma['total_rahma']) ?></b></p>
                    <p>Bayar: <b>Rp <?= number_format($data_transaksi_rahma['bayar_rahma']) ?></b></p>
                    <p>Kembali: <b>Rp <?= number_format($data_transaksi_rahma['kembalian_rahma']) ?></b></p>
                </div>
            </div>
        <?php } ?>

        <!-- DETAIL MENU -->
        <div class="card">
            <div class="card-body">
                <h5>List Menu</h5>

                <table class="table table-bordered">
                    <tr>
                        <th>Nama Menu</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>

                    <?php
                    $total_rahma = 0;
                    while ($d_rahma = mysqli_fetch_assoc($detail_rahma)) {
                        $subtotal_rahma = $d_rahma['qty_rahma'] * $d_rahma['harga_rahma'];
                        $total_rahma += $subtotal_rahma;
                        ?>
                        <tr>
                            <td><?= $d_rahma['nama_menu_rahma'] ?></td>
                            <td><?= $d_rahma['qty_rahma'] ?></td>
                            <td>Rp <?= number_format($d_rahma['harga_rahma']) ?></td>
                            <td>Rp <?= number_format($subtotal_rahma) ?></td>
                        </tr>
                    <?php } ?>

                    <!-- TOTAL -->
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td><b>Rp <?= number_format($total_rahma) ?></b></td>
                    </tr>
                </table>
            </div>
        </div>

        <br>
        <a href="transaksi_rahma.php" class="btn btn-secondary">← Kembali</a>

    </div>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>