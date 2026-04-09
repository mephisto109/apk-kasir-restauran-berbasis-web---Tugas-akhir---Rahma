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

$id_order_rahma = $_GET['id'] ?? '';

if (empty($id_order_rahma)) {
    header("Location: dashboard_rahma.php");
    exit;
}

// Ambil data order utama
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.*,
        m.status_rahma AS status_meja_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_meja_rahma m ON o.id_meja_rahma = m.id_meja_rahma
    WHERE o.id_order_rahma = '$id_order_rahma'
");

// Cek apakah order ditemukan
if (mysqli_num_rows($query_order_rahma) == 0) {
    header("Location: dashboard_rahma.php");
    exit;
}

$order_rahma = mysqli_fetch_assoc($query_order_rahma);

// Ambil semua item menu yang dipesan
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        d.*,
        mn.nama_menu_rahma,
        mn.harga_rahma,
        mn.kategori_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '$id_order_rahma'
");

// Hitung grand total
$query_total_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_detail_order_rahma
    WHERE id_order_rahma = '$id_order_rahma'
");
$data_total_rahma = mysqli_fetch_assoc($query_total_rahma);
$grand_total_rahma = $data_total_rahma['grand_total_rahma'];

// Cek apakah sudah bayar
$query_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT * FROM tbl_transaksi_rahma
    WHERE id_order_rahma = '$id_order_rahma'
");
$sudah_bayar_rahma = mysqli_num_rows($query_transaksi_rahma) > 0;
$transaksi_rahma = $sudah_bayar_rahma ? mysqli_fetch_assoc($query_transaksi_rahma) : null;

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
    <title>Detail Order</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Tombol kembali ke dashboard -->
        <a href="dashboard_rahma.php" class="btn btn-sm mb-3"
            style="border: 1.5px solid var(--dark-orange-rahma); color: var(--dark-orange-rahma); border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-receipt me-2"></i>Detail Order
        </h5>

        <div class="row g-4">

            <!-- ===== KOLOM KIRI: INFO ORDER ===== -->
            <div class="col-md-5">
                <div class="card card-table-rahma h-100">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-info-circle me-2"></i>Informasi Order
                        </h6>
                    </div>
                    <div class="card-body">

                        <!-- ID Order -->
                        <div class="mb-3">
                            <small class="text-muted">ID Order</small>
                            <div class="fw-bold text-id-rahma fs-5">
                                <?= htmlspecialchars($order_rahma['id_order_rahma']) ?>
                            </div>
                        </div>

                        <!-- Nama Pelanggan -->
                        <div class="mb-3">
                            <small class="text-muted">Nama Pelanggan</small>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($order_rahma['nama_pelanggan_rahma']) ?>
                            </div>
                        </div>

                        <!-- Nomor Meja -->
                        <div class="mb-3">
                            <small class="text-muted">Nomor Meja</small>
                            <div>
                                <span class="badge badge-status-rahma"
                                    style="background-color: var(--orange-rahma); color:#fff;">
                                    <?= (int) ltrim($order_rahma['id_meja_rahma'], 'M') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Tanggal Order -->
                        <div class="mb-3">
                            <small class="text-muted">Tanggal Order</small>
                            <div><?= $order_rahma['waktu_order_rahma'] ?></div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <small class="text-muted">Keterangan</small>
                            <div><?= htmlspecialchars($order_rahma['keterangan_rahma']) ?></div>
                        </div>

                        <!-- Status Order -->
                        <div class="mb-3">
                            <small class="text-muted">Status Order</small>
                            <div>
                                <?php if ($order_rahma['status_order_rahma'] == 'dibuat'): ?>
                                    <span class="badge badge-status-rahma badge-dibuat-rahma">
                                        <i class="bi bi-clock me-1"></i>Dibuat
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-status-rahma badge-selesai-rahma">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status Pembayaran -->
                        <div class="mb-3">
                            <small class="text-muted">Status Pembayaran</small>
                            <div>
                                <?php if ($sudah_bayar_rahma): ?>
                                    <span class="badge badge-status-rahma badge-lunas-rahma">
                                        <i class="bi bi-check2 me-1"></i>Lunas
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                        <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info transaksi kalau sudah bayar -->
                        <?php if ($sudah_bayar_rahma): ?>
                            <hr>
                            <div class="mb-2">
                                <small class="text-muted">Diskon</small>
                                <div>Rp <?= number_format($transaksi_rahma['diskon_rahma'], 0, ',', '.') ?></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Total Bayar</small>
                                <div class="fw-bold" style="color: var(--dark-pink-rahma);">
                                    Rp <?= number_format($transaksi_rahma['total_rahma'], 0, ',', '.') ?>
                                </div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Uang Diterima</small>
                                <div>Rp <?= number_format($transaksi_rahma['bayar_rahma'], 0, ',', '.') ?></div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Kembalian</small>
                                <div>Rp <?= number_format($transaksi_rahma['kembalian_rahma'], 0, ',', '.') ?></div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Tombol aksi di bawah info order -->
                    <div class="card-footer bg-white border-top d-flex gap-2">
                        <?php if (!$sudah_bayar_rahma): ?>
                            <!-- Tombol lanjut bayar — hanya muncul kalau belum bayar -->
                            <a href="pembayaran_rahma.php?id=<?= $order_rahma['id_order_rahma'] ?>"
                                class="btn btn-bayar-rahma flex-grow-1">
                                <i class="bi bi-cash me-1"></i>Proses Pembayaran
                            </a>
                        <?php else: ?>
                            <!-- Tombol cetak struk — hanya muncul kalau sudah bayar -->
                            <a href="cetak_struk_rahma.php?id=<?= $transaksi_rahma['id_transaksi_rahma'] ?>"
                                class="btn flex-grow-1"
                                style="background: linear-gradient(135deg, var(--orange-rahma), var(--dark-orange-rahma)); color:#fff; border:none; border-radius:8px;">
                                <i class="bi bi-printer me-1"></i>Cetak Struk
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- ===== KOLOM KANAN: DAFTAR MENU ===== -->
            <div class="col-md-7">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-bag me-2"></i>Daftar Menu Dipesan
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Menu</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Qty</th>
                                        <th>Catatan</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($query_detail_rahma) > 0): ?>
                                        <?php while ($row_detail_rahma = mysqli_fetch_assoc($query_detail_rahma)): ?>
                                            <tr>
                                                <td class="ps-3">
                                                    <div class="fw-semibold">
                                                        <?= htmlspecialchars($row_detail_rahma['nama_menu_rahma']) ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        Rp <?= number_format($row_detail_rahma['harga_rahma'], 0, ',', '.') ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-status-rahma"
                                                        style="background-color: var(--pink-rahma); color:#fff;">
                                                        <?= htmlspecialchars($row_detail_rahma['kategori_rahma']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?= $row_detail_rahma['qty_rahma'] ?>
                                                </td>
                                                <td>
                                                    <small><?= htmlspecialchars($row_detail_rahma['catatan_rahma']) ?></small>
                                                </td>
                                                <td class="text-end pe-3 fw-semibold">
                                                    Rp <?= number_format($row_detail_rahma['subtotal_rahma'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                                Belum ada menu yang dipesan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                                <!-- Total di bawah tabel -->
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="ps-3 fw-bold">Total</td>
                                        <td class="text-end pe-3 fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                            Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
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