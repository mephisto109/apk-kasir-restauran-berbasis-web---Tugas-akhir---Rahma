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

// Ambil semua order yang BELUM ada transaksinya (belum bayar)
$query_belum_bayar_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.waktu_order_rahma,
        o.status_order_rahma,
        o.keterangan_rahma,
        COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.id_order_rahma NOT IN (
        SELECT id_order_rahma FROM tbl_transaksi_rahma
    )
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
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../assets/css/kasir_rahma.css">
    <title>Transaksi</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-cash-coin me-2"></i>Transaksi — Order Belum Bayar
        </h5>

        <div class="card card-table-rahma">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Order Belum Bayar
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
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if (mysqli_num_rows($query_belum_bayar_rahma) > 0): ?>
                                <?php while ($row_rahma = mysqli_fetch_assoc($query_belum_bayar_rahma)): ?>
                                    <tr>
                                        <!-- ID order dengan warna khusus -->
                                        <td class="ps-3">
                                            <span class="text-id-rahma">
                                                <?= htmlspecialchars($row_rahma['id_order_rahma']) ?>
                                            </span>
                                        </td>

                                        <!-- Nama pelanggan -->
                                        <td><?= htmlspecialchars($row_rahma['nama_pelanggan_rahma']) ?></td>

                                        <!-- Nomor meja dengan badge -->
                                        <td>
                                            <span class="badge badge-status-rahma"
                                                style="background-color: var(--orange-rahma); color:#fff;">
                                                <?= (int) ltrim($row_rahma['id_meja_rahma'], 'M') ?>
                                            </span>
                                        </td>

                                        <!-- Tanggal order -->
                                        <td><?= $row_rahma['waktu_order_rahma'] ?></td>
                                        <td class="fw-semibold">
                                            Rp <?= number_format($row_rahma['grand_total_rahma'], 0, ',', '.') ?>
                                        </td>

                                        <!-- Badge status order -->
                                        <td>
                                            <?php if ($row_rahma['status_order_rahma'] == 'dibuat'): ?>
                                                <span class="badge badge-status-rahma badge-dibuat-rahma">
                                                    <i class="bi bi-clock me-1"></i>Dibuat
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-status-rahma badge-selesai-rahma">
                                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tombol aksi -->
                                        <td class="text-center">
                                            <a href="detail_order_rahma.php?id=<?= $row_rahma['id_order_rahma'] ?>"
                                                class="btn btn-sm btn-detail-rahma me-1">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="pembayaran_rahma.php?id=<?= $row_rahma['id_order_rahma'] ?>"
                                                class="btn btn-sm btn-bayar-rahma">
                                                <i class="bi bi-cash"></i> Bayar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <!-- Tampil kalau semua order sudah lunas -->
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-check-circle fs-3 d-block mb-2"
                                            style="color: var(--pink-rahma);"></i>
                                        Semua order sudah lunas!
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