<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}
if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../templates/navbar_rahma.php';

// Query untuk statistik
$total_user_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total FROM tbl_user_rahma WHERE id_role_rahma = 'R003'"
))['total'];

$total_pegawai_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total FROM tbl_user_rahma WHERE id_role_rahma IN ('R002','R004')"
))['total'];

$total_menu_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total FROM tbl_menu_rahma WHERE status_rahma = 'aktif'"
))['total'];

$total_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT COUNT(*) as total FROM tbl_transaksi_rahma"
))['total'];

$today_rahma = date('Y-m-d');
$pendapatan_hari_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT SUM(total_rahma) as total FROM tbl_transaksi_rahma WHERE DATE(waktu_transaksi_rahma)='$today_rahma'"
))['total'] ?? 0;

$bulan_rahma = date('m');
$tahun_rahma = date('Y');
$pendapatan_bulan_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT SUM(total_rahma) as total FROM tbl_transaksi_rahma WHERE MONTH(waktu_transaksi_rahma)='$bulan_rahma' AND YEAR(waktu_transaksi_rahma)='$tahun_rahma'"
))['total'] ?? 0;

$transaksi_terbaru_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_transaksi_rahma ORDER BY waktu_transaksi_rahma DESC LIMIT 5");
$user_baru_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_user_rahma ORDER BY id_user_rahma DESC LIMIT 5");
$stok_habis_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma WHERE status_menu_rahma = 'habis' AND status_rahma = 'aktif'"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Global CSS dulu -->
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <!-- Owner specific -->
    <link rel="stylesheet" href="../assets/css/owner_rahma.css">
    <title>Dashboard Owner - Famiresu Iko</title>
</head>

<body>
    <!-- Flag stripe (sama kayak kasir) -->
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-crown fs-3 text-warning me-3"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--dark-orange-rahma);">Dashboard Owner</h4>
                <small class="text-muted">Kelola restoran Famiresu Iko</small>
            </div>
        </div>

        <!-- ===== CARD SUMMARY (6 cards responsive) ===== -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Pendapatan Hari Ini</div>
                            <div class="fs-3 fw-bold text-order-rahma">Rp
                                <?= number_format($pendapatan_hari_rahma, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="rounded-circle p-3 icon-circle-orange-rahma">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Pendapatan Bulan Ini</div>
                            <div class="fs-3 fw-bold text-transaksi-rahma">Rp
                                <?= number_format($pendapatan_bulan_rahma, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="rounded-circle p-3 icon-circle-pink-rahma">
                            <i class="bi bi-graph-up fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small">Member</div>
                        <div class="fs-3 fw-bold text-order-rahma"><?= $total_user_rahma ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small">Pegawai</div>
                        <div class="fs-3 fw-bold text-transaksi-rahma"><?= $total_pegawai_rahma ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small">Menu Aktif</div>
                        <div class="fs-3 fw-bold text-order-rahma"><?= $total_menu_rahma ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body">
                        <div class="text-muted small">Transaksi</div>
                        <div class="fs-3 fw-bold text-transaksi-rahma"><?= $total_transaksi_rahma ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Transaksi Terbaru -->
            <div class="col-lg-6">
                <div class="card card-table-rahma h-100">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-clock-history me-2"></i>Transaksi Terbaru
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($t_rahma = mysqli_fetch_assoc($transaksi_terbaru_rahma)) { ?>
                                        <tr>
                                            <td class="ps-3 text-id-rahma"><?= $t_rahma['id_transaksi_rahma'] ?></td>
                                            <td><?= date('d/m H:i', strtotime($t_rahma['waktu_transaksi_rahma'])) ?></td>
                                            <td class="fw-semibold">Rp
                                                <?= number_format($t_rahma['total_rahma'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Baru -->
            <div class="col-lg-6">
                <div class="card card-table-rahma h-100">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-person-plus me-2"></i>User Terbaru
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($u_rahma = mysqli_fetch_assoc($user_baru_rahma)) { ?>
                                        <tr>
                                            <td class="ps-3"><?= $u_rahma['id_user_rahma'] ?></td>
                                            <td><?= htmlspecialchars($u_rahma['nama_rahma']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi Stok Habis -->
        <?php if (mysqli_num_rows($stok_habis_rahma) > 0): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-table-rahma">
                        <div class="card-header card-header-rahma py-3">
                            <h6 class="mb-0 fw-semibold text-white">
                                <i class="bi bi-exclamation-triangle me-2"></i>Stok Habis
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php mysqli_data_seek($stok_habis_rahma, 0); // Reset pointer ?>
                            <?php while ($s_rahma = mysqli_fetch_assoc($stok_habis_rahma)) { ?>
                                <div class="alert alert-warning d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle fs-5 me-2 text-warning"></i>
                                    <div>
                                        <strong>Stok habis:</strong> <?= htmlspecialchars($s_rahma['nama_menu_rahma']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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