<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R002') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

$tanggal_hari_ini_rahma = date('Y-m-d');

// Hitung jumlah pesanan hari ini
$query_jumlah_order_rahma = mysqli_query($koneksiRahma, "
    SELECT COUNT(*) AS jumlah_rahma 
    FROM tbl_order_rahma 
    WHERE waktu_order_rahma = '$tanggal_hari_ini_rahma'
");
$data_jumlah_order_rahma = mysqli_fetch_assoc($query_jumlah_order_rahma);
$jumlah_order_rahma = $data_jumlah_order_rahma['jumlah_rahma'];

// Hitung total transaksi hari ini
$query_total_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(t.total_rahma), 0) AS total_rahma
    FROM tbl_transaksi_rahma t
    WHERE t.waktu_transaksi_rahma = '$tanggal_hari_ini_rahma'
");
$data_total_transaksi_rahma = mysqli_fetch_assoc($query_total_transaksi_rahma);
$total_transaksi_rahma = $data_total_transaksi_rahma['total_rahma'];

// Ambil semua pesanan dengan join ke tabel detail dan transaksi
$query_pesanan_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.waktu_order_rahma,
        o.status_order_rahma,
        o.keterangan_rahma,
        COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma,
        MAX(CASE WHEN t.id_transaksi_rahma IS NOT NULL THEN 1 ELSE 0 END) AS sudah_bayar_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    LEFT JOIN tbl_transaksi_rahma t ON o.id_order_rahma = t.id_order_rahma
    GROUP BY o.id_order_rahma
    ORDER BY o.waktu_order_rahma DESC
");

include '../templates/navbar_rahma.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Load global dulu, baru role-specific -->
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../assets/css/kasir_rahma.css">

    <title>Dashboard Kasir</title>
</head>

<body>

<!-- Stripe lesbian flag di paling atas -->
<div class="flag-stripe-rahma"></div>

<div class="container mt-4">

    <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard Kasir
    </h5>

    <!-- ===== CARD SUMMARY ===== -->
    <div class="row g-3 mb-4">

        <!-- Card jumlah pesanan hari ini -->
        <div class="col-md-6">
            <div class="card card-summary-rahma h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 icon-circle-orange-rahma">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pesanan Hari Ini</div>
                        <div class="fs-3 fw-bold text-order-rahma"><?= $jumlah_order_rahma ?></div>
                        <div class="text-muted small">pesanan masuk</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card total transaksi hari ini -->
        <div class="col-md-6">
            <div class="card card-summary-rahma h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 icon-circle-pink-rahma">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Transaksi Hari Ini</div>
                        <div class="fs-3 fw-bold text-transaksi-rahma">
                            Rp <?= number_format($total_transaksi_rahma, 0, ',', '.') ?>
                        </div>
                        <div class="text-muted small">sudah terbayar</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== TABEL DAFTAR PESANAN ===== -->
    <div class="card card-table-rahma">
        <div class="card-header card-header-rahma py-3">
            <h6 class="mb-0 fw-semibold text-white">
                <i class="bi bi-list-ul me-2"></i>Daftar Semua Pesanan
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status Order</th>
                            <th>Pembayaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        
                        <?php if (mysqli_num_rows($query_pesanan_rahma) > 0): ?>
                            <!-- Looping untuk setiap pesanan --> 
                            <?php while ($row_pesanan_rahma = mysqli_fetch_assoc($query_pesanan_rahma)): ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="text-id-rahma">
                                        <?= htmlspecialchars($row_pesanan_rahma['id_order_rahma']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row_pesanan_rahma['nama_pelanggan_rahma']) ?></td>
                                <td>
                                    <span class="badge badge-status-rahma" style="background-color: var(--orange-rahma); color:#fff;">
                                        <?= htmlspecialchars($row_pesanan_rahma['id_meja_rahma']) ?>
                                    </span>
                                </td>
                                <td><?= $row_pesanan_rahma['waktu_order_rahma'] ?></td>
                                <td class="fw-semibold">
                                    Rp <?= number_format($row_pesanan_rahma['grand_total_rahma'], 0, ',', '.') ?>
                                </td>

                                <!-- Badge status order -->
                                <td>
                                    <?php if ($row_pesanan_rahma['status_order_rahma'] == 'dibuat'): ?>
                                        <span class="badge badge-status-rahma badge-dibuat-rahma">
                                            <i class="bi bi-clock me-1"></i>Dibuat
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-status-rahma badge-selesai-rahma">
                                            <i class="bi bi-check-circle me-1"></i>Selesai
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Badge status pembayaran -->
                                <td>
                                    <?php if ($row_pesanan_rahma['sudah_bayar_rahma']): ?>
                                        <span class="badge badge-status-rahma badge-lunas-rahma">
                                            <i class="bi bi-check2 me-1"></i>Lunas
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                            <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Tombol aksi -->
                                <td class="text-center">
                                    <a href="detail_order_rahma.php?id=<?= $row_pesanan_rahma['id_order_rahma'] ?>"class="btn btn-sm btn-detail-rahma me-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <?php if (!$row_pesanan_rahma['sudah_bayar_rahma']): ?>
                                    <a href="pembayaran_rahma.php?id=<?= $row_pesanan_rahma['id_order_rahma'] ?>" class="btn btn-sm btn-bayar-rahma">
                                        <i class="bi bi-cash"></i> Bayar
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada pesanan masuk
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

</body>
</html>