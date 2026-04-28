<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — tolak kalau belum login atau bukan chef (R004)
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R004') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// UBAH: Ambil order yang statusnya "diproses" (sudah bayar, sedang dimasak)
$query_diproses_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.waktu_order_rahma,
        o.keterangan_rahma,
        COUNT(d.id_dorder_rahma) AS jumlah_item_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.status_order_rahma = 'diproses'
    GROUP BY o.id_order_rahma
    ORDER BY o.waktu_order_rahma ASC
");

// Ambil order yang statusnya "selesai" — hari ini aja
$tanggal_hari_ini_rahma = date('Y-m-d');
$query_selesai_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.waktu_order_rahma,
        COUNT(d.id_dorder_rahma) AS jumlah_item_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.status_order_rahma = 'selesai'
    AND o.waktu_order_rahma = '$tanggal_hari_ini_rahma'
    GROUP BY o.id_order_rahma
    ORDER BY o.waktu_order_rahma DESC
");

// Hitung jumlah masing-masing
$jumlah_diproses_rahma = mysqli_num_rows($query_diproses_rahma);
$jumlah_selesai_rahma = mysqli_num_rows($query_selesai_rahma);

include '../templates/navbar_rahma.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../assets/css/chef_rahma.css">
    <title>Dashboard Chef</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-fire me-2"></i>Dashboard Chef
        </h5>

        <!-- Card summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-summary-chef-rahma h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 icon-circle-orange-rahma">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Sedang Dimasak</div>
                            <div class="fs-3 fw-bold text-order-rahma"><?= $jumlah_diproses_rahma ?></div>
                            <div class="text-muted small">order masuk</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-summary-chef-rahma h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 icon-circle-pink-rahma">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Selesai Hari Ini</div>
                            <div class="fs-3 fw-bold text-transaksi-rahma"><?= $jumlah_selesai_rahma ?></div>
                            <div class="text-muted small">order selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ORDER DIPROSES ===== -->
        <div class="card card-table-chef-rahma mb-4">
            <div class="card-header card-header-rahma py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-clock me-2"></i>Order Masuk — Sedang Dimasak
                </h6>
                <span class="badge" style="background-color: rgba(255,255,255,0.2); color:#fff;">
                    <?= $jumlah_diproses_rahma ?> order
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Order</th>
                                <th>Pelanggan</th>
                                <th>Meja</th>
                                <th>Waktu</th>
                                <th>Keterangan</th>
                                <th>Item</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Tampilkan data order yang statusnya "diproses" -->
                            <?php if ($jumlah_diproses_rahma > 0): ?>
                                <?php while ($row_diproses_rahma = mysqli_fetch_assoc($query_diproses_rahma)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="text-id-rahma">
                                                <?= htmlspecialchars($row_diproses_rahma['id_order_rahma']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row_diproses_rahma['nama_pelanggan_rahma']) ?></td>
                                        <td>
                                            <span class="badge badge-status-rahma"
                                                style="background-color: var(--orange-rahma); color:#fff;">
                                                <?= (int) ltrim($row_diproses_rahma['id_meja_rahma'], 'M') ?>
                                            </span>
                                        </td>
                                        <td><?= $row_diproses_rahma['waktu_order_rahma'] ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($row_diproses_rahma['keterangan_rahma']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-status-rahma badge-dibuat-rahma">
                                                <?= $row_diproses_rahma['jumlah_item_rahma'] ?> item
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="update_status_rahma.php?id=<?= $row_diproses_rahma['id_order_rahma'] ?>"
                                                class="btn btn-sm btn-detail-chef-rahma">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-check2-circle fs-3 d-block mb-2"
                                            style="color: var(--pink-rahma);"></i>
                                        Tidak ada order yang sedang dimasak
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===== ORDER SELESAI ===== -->
        <div class="card card-table-chef-rahma">
            <div class="card-header card-header-rahma py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-check-circle me-2"></i>Order Selesai — Hari Ini
                </h6>
                <span class="badge" style="background-color: rgba(255,255,255,0.2); color:#fff;">
                    <?= $jumlah_selesai_rahma ?> order
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Order</th>
                                <th>Pelanggan</th>
                                <th>Meja</th>
                                <th>Waktu</th>
                                <th>Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($jumlah_selesai_rahma > 0): ?>
                                <?php while ($row_selesai_rahma = mysqli_fetch_assoc($query_selesai_rahma)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="text-id-rahma">
                                                <?= htmlspecialchars($row_selesai_rahma['id_order_rahma']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row_selesai_rahma['nama_pelanggan_rahma']) ?></td>
                                        <td>
                                            <span class="badge badge-status-rahma"
                                                style="background-color: var(--orange-rahma); color:#fff;">
                                                <?= (int) ltrim($row_selesai_rahma['id_meja_rahma'], 'M') ?>
                                            </span>
                                        </td>
                                        <td><?= $row_selesai_rahma['waktu_order_rahma'] ?></td>
                                        <td>
                                            <span class="badge badge-status-rahma badge-selesai-rahma">
                                                <?= $row_selesai_rahma['jumlah_item_rahma'] ?> item
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada order selesai hari ini
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