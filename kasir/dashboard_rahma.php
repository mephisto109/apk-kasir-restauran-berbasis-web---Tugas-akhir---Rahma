<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["id_user_rahma"])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION["id_role_rahma"] !== "R002") {
    header("Location: ../login_rahma.php");
    exit;
}

include "../koneksi/koneksi_rahma.php";

// --- LOGIKA FILTER ---
$tgl_mulai = isset($_GET['tanggal_mulai_rahma']) ? $_GET['tanggal_mulai_rahma'] : '';
$tgl_akhir = isset($_GET['tanggal_akhir_rahma']) ? $_GET['tanggal_akhir_rahma'] : '';

$tanggal_hari_ini_rahma = date("Y-m-d");

// Hitung jumlah pesanan hari ini
$query_jumlah_order_rahma = mysqli_query($koneksiRahma, "
    SELECT COUNT(*) AS jumlah_rahma 
    FROM tbl_order_rahma 
    WHERE waktu_order_rahma = '" . $tanggal_hari_ini_rahma . "'
");
$data_jumlah_order_rahma = mysqli_fetch_assoc($query_jumlah_order_rahma);
$jumlah_order_rahma = $data_jumlah_order_rahma["jumlah_rahma"];

// Hitung total transaksi hari ini
$query_total_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(t.total_rahma), 0) AS total_rahma
    FROM tbl_transaksi_rahma t
    WHERE t.waktu_transaksi_rahma = '" . $tanggal_hari_ini_rahma . "'
");
$data_total_transaksi_rahma = mysqli_fetch_assoc($query_total_transaksi_rahma);
$total_transaksi_rahma = $data_total_transaksi_rahma["total_rahma"];

// --- QUERY PESANAN DENGAN FILTER ---
$sql_pesanan = "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.jenis_pesanan_rahma,
        o.waktu_order_rahma,
        o.status_order_rahma,
        o.keterangan_rahma,
        COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma,
        MAX(CASE WHEN t.id_transaksi_rahma IS NOT NULL THEN 1 ELSE 0 END) AS sudah_bayar_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    LEFT JOIN tbl_transaksi_rahma t ON o.id_order_rahma = t.id_order_rahma
    WHERE 1=1
";

if (!empty($tgl_mulai) && !empty($tgl_akhir)) {
    $sql_pesanan .= " AND o.waktu_order_rahma BETWEEN '$tgl_mulai' AND '$tgl_akhir'";
}

$sql_pesanan .= " GROUP BY o.id_order_rahma ORDER BY o.waktu_order_rahma DESC";
$query_pesanan_rahma = mysqli_query($koneksiRahma, $sql_pesanan);

include "../templates/navbar_rahma.php";
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

    <title>Dashboard Kasir</title>
</head>

<body>

    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard Kasir
        </h5>

        <!-- Ringkasan -->
        <div class="row g-3 mb-4">
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

            <!-- Total Transaksi Hari Ini -->
            <div class="col-md-6">
                <div class="card card-summary-rahma h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 icon-circle-pink-rahma">
                            <i class="bi bi-cash-coin fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Transaksi Hari Ini</div>
                            <div class="fs-3 fw-bold text-transaksi-rahma">
                                Rp <?= number_format($total_transaksi_rahma, 0, ",", ".") ?>
                            </div>
                            <div class="text-muted small">sudah terbayar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel pesanan & laporan penjualan -->
        <div class="card card-table-rahma">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Pesanan & Laporan Penjualan
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-4 p-3" style="background-color: #f8f9fa; border-radius: 8px;">
                    <form method="GET" action="dashboard_rahma.php" class="row g-3">

                        <!-- Filter tanggal -->
                        <div class="col-md-3">
                            <label for="tanggal_mulai_rahma" class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai_rahma" id="tanggal_mulai_rahma"
                                class="form-control form-control-sm"
                                value="<?= $tgl_mulai ?: $tanggal_hari_ini_rahma ?>" required>
                        </div>

                        <!-- Filter tanggal akhir -->
                        <div class="col-md-3">
                            <label for="tanggal_akhir_rahma" class="form-label fw-semibold small">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir_rahma" id="tanggal_akhir_rahma"
                                class="form-control form-control-sm"
                                value="<?= $tgl_akhir ?: $tanggal_hari_ini_rahma ?>" required>
                        </div>

                        <!-- Tombol filter & reset -->
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-bayar-rahma">
                                <i class="bi bi-filter me-1"></i> filter
                            </button>
                            <a href="dashboard_rahma.php" class="btn btn-sm btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> reset
                            </a>
                        </div>


                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="cetakPDF()" class="btn btn-bayar-rahma btn-sm w-100">
                                <i class="bi bi-printer me-1"></i> Cetak PDF
                            </button>
                        </div>

                    </form>
                </div>

                <hr class="my-4">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Order</th>
                                <th>Pelanggan</th>
                                <th>Jenis</th>
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
                                <?php while ($row_pesanan_rahma = mysqli_fetch_assoc($query_pesanan_rahma)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="text-id-rahma">
                                                <?= htmlspecialchars($row_pesanan_rahma["id_order_rahma"]) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row_pesanan_rahma["nama_pelanggan_rahma"]) ?></td>
                                        <td><?= htmlspecialchars($row_pesanan_rahma["jenis_pesanan_rahma"]) ?></td>
                                        <td>
                                            <span class="badge badge-status-rahma"
                                                style="background-color: var(--orange-rahma); color:#fff;">
                                                <?= (int) ltrim($row_pesanan_rahma["id_meja_rahma"], "M") ?>
                                            </span>
                                        </td>
                                        <td><?= $row_pesanan_rahma["waktu_order_rahma"] ?></td>
                                        <td class="fw-semibold">
                                            Rp <?= number_format($row_pesanan_rahma["grand_total_rahma"], 0, ",", ".") ?>
                                        </td>
                                        <td>
                                            <?php if ($row_pesanan_rahma["status_order_rahma"] == "dibuat"): ?>
                                                <span class="badge badge-status-rahma badge-dibuat-rahma">
                                                    <i class="bi bi-clock me-1"></i>Dibuat
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-status-rahma badge-selesai-rahma">
                                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row_pesanan_rahma["sudah_bayar_rahma"]): ?>
                                                <span class="badge badge-status-rahma badge-lunas-rahma">
                                                    <i class="bi bi-check2 me-1"></i>Lunas
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                                    <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="detail_order_rahma.php?id=<?= $row_pesanan_rahma["id_order_rahma"] ?>"
                                                class="btn btn-sm btn-detail-rahma me-1">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <?php if (!$row_pesanan_rahma["sudah_bayar_rahma"]): ?>
                                                <a href="pembayaran_rahma.php?id=<?= $row_pesanan_rahma["id_order_rahma"] ?>"
                                                    class="btn btn-sm btn-bayar-rahma">
                                                    <i class="bi bi-cash"></i> Bayar
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Data tidak ditemukan
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
        function cetakPDF() {
            const tglMulai = document.getElementById('tanggal_mulai_rahma').value;
            const tglAkhir = document.getElementById('tanggal_akhir_rahma').value;

            // Redirect ke file cetak di halaman yang sama dengan membawa parameter tanggal
            window.location.href = `cetak_laporan_rahma.php?tanggal_mulai_rahma=${tglMulai}&tanggal_akhir_rahma=${tglAkhir}`;
        }

        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>