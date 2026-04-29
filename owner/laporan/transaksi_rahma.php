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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">
    <title>Laporan Transaksi - Owner</title>
</head>

<body>
    <!-- Flag stripe dekoratif -->
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">

        <!-- Header Halaman -->
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-graph-up fs-3" style="color: var(--dark-orange-rahma); margin-right: 12px;"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--dark-orange-rahma);">Laporan Transaksi</h4>
                <small class="text-muted">Analisis pendapatan dan transaksi restoran</small>
            </div>
        </div>

        <!-- =====================
        RINGKASAN CARDS
        ===================== -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            <i class="bi bi-receipt me-1"></i>Total Transaksi
                        </div>
                        <div class="fs-3 fw-bold text-order-rahma"><?= $total_transaksi_rahma ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            <i class="bi bi-cash-stack me-1"></i>Total Pendapatan
                        </div>
                        <div class="fs-3 fw-bold text-transaksi-rahma">
                            Rp <?= number_format($total_pendapatan_rahma, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            <i class="bi bi-calculator me-1"></i>Rata-rata
                        </div>
                        <div class="fs-3 fw-bold text-order-rahma">
                            Rp <?= number_format($rata_rahma, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =====================
        FILTER CARD
        ===================== -->
        <div class="card card-summary-rahma mb-4">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-funnel me-2"></i>Filter Data
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="label-rahma">Tanggal Awal</label>
                        <input type="date" name="tgl_awal" class="form-control input-rahma"
                            value="<?= $tgl_awal_rahma ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="label-rahma">Tanggal Akhir</label>
                        <input type="date" name="tgl_akhir" class="form-control input-rahma"
                            value="<?= $tgl_akhir_rahma ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="label-rahma">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-bayar-rahma flex-grow-1">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="transaksi_rahma.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- =====================
        TOMBOL CETAK
        ===================== -->
        <div class="mb-3">
            <?php if ($tgl_awal_rahma && $tgl_akhir_rahma) { ?>
                <a href="print_laporan_transaksi_rahma.php?tgl_awal=<?= $tgl_awal_rahma ?>&tgl_akhir=<?= $tgl_akhir_rahma ?>"
                    target="_blank" class="btn btn-bayar-rahma">
                    <i class="bi bi-printer me-1"></i>Cetak Laporan (Filtered)
                </a>
            <?php } else { ?>
                <a href="print_laporan_transaksi_rahma.php" target="_blank" class="btn btn-bayar-rahma">
                    <i class="bi bi-printer me-1"></i>Cetak Semua Laporan
                </a>
            <?php } ?>
        </div>

        <!-- =====================
        TABEL LAPORAN
        ===================== -->
        <div class="card card-table-rahma">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-table me-2"></i>Daftar Transaksi
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Diskon</th>
                                <th>Bayar</th>
                                <th>Kembali</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_list_rahma) > 0) { ?>
                                <?php foreach ($data_list_rahma as $row) { ?>
                                    <tr>
                                        <td class="ps-3">
                                            <a href="detail_order_rahma.php?id=<?= $row['id_order_rahma'] ?>"
                                                class="text-decoration-none text-id-rahma fw-semibold">
                                                <?= $row['id_order_rahma'] ?>
                                            </a>
                                        </td>
                                        <td><?= date('d-m-Y H:i', strtotime($row['waktu_transaksi_rahma'])) ?></td>
                                        <td class="fw-semibold">Rp <?= number_format($row['total_rahma'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge badge-status-rahma badge-diproses-rahma">
                                                <?= ($row['diskon_rahma']) ?>%
                                            </span>
                                        </td>
                                        <td class="fw-semibold">Rp <?= number_format($row['bayar_rahma'], 0, ',', '.') ?></td>
                                        <td class="fw-semibold">Rp <?= number_format($row['kembalian_rahma'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="detail_order_rahma.php?id=<?= $row['id_order_rahma'] ?>"
                                                class="btn btn-sm btn-detail-rahma">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Data transaksi tidak ditemukan</span>
                                    </td>
                                </tr>
                            <?php } ?>
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