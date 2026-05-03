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

// Ambil nilai filter tanggal dari URL kalau ada
$tgl_mulai_rahma = isset($_GET['tanggal_mulai_rahma']) ? $_GET['tanggal_mulai_rahma'] : '';
$tgl_akhir_rahma = isset($_GET['tanggal_akhir_rahma']) ? $_GET['tanggal_akhir_rahma'] : '';

$tanggal_hari_ini_rahma = date("Y-m-d");

// Hitung jumlah pesanan hari ini
$query_jumlah_order_rahma = mysqli_query($koneksiRahma, "
    SELECT COUNT(*) AS jumlah_rahma 
    FROM tbl_order_rahma 
    WHERE DATE(waktu_order_rahma) = '$tanggal_hari_ini_rahma'
");
$data_jumlah_order_rahma = mysqli_fetch_assoc($query_jumlah_order_rahma);
$jumlah_order_rahma = $data_jumlah_order_rahma["jumlah_rahma"];

// Hitung total transaksi hari ini
$query_total_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(t.total_rahma), 0) AS total_rahma
    FROM tbl_transaksi_rahma t
    WHERE DATE(t.waktu_transaksi_rahma) = '$tanggal_hari_ini_rahma'
");
$data_total_transaksi_rahma = mysqli_fetch_assoc($query_total_transaksi_rahma);
$total_transaksi_rahma = $data_total_transaksi_rahma["total_rahma"];

// Fungsi bantu: bikin query pesanan berdasarkan jenis
function buildQueryPesanan_rahma($jenis_rahma, $tgl_mulai_rahma, $tgl_akhir_rahma)
{
    $sql_rahma = "
        SELECT 
            o.id_order_rahma,
            o.nama_pelanggan_rahma,
            o.id_meja_rahma,
            o.jenis_pesanan_rahma,
            o.waktu_order_rahma,
            o.status_order_rahma,
            t.metode_bayar_rahma,
            COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma,
            MAX(CASE WHEN t.id_transaksi_rahma IS NOT NULL THEN 1 ELSE 0 END) AS sudah_bayar_rahma,
            u.nama_rahma AS nama_kasir_rahma
        FROM tbl_order_rahma o
        LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
        LEFT JOIN tbl_transaksi_rahma t ON o.id_order_rahma = t.id_order_rahma
        LEFT JOIN tbl_user_rahma u ON t.id_kasir_rahma = u.id_user_rahma
        WHERE o.jenis_pesanan_rahma = '$jenis_rahma'
    ";

    // Tambahkan filter tanggal kalau ada
    if (!empty($tgl_mulai_rahma) && !empty($tgl_akhir_rahma)) {
        $sql_rahma .= " AND DATE(o.waktu_order_rahma) BETWEEN '$tgl_mulai_rahma' AND '$tgl_akhir_rahma'";
    }

    $sql_rahma .= " GROUP BY o.id_order_rahma ORDER BY o.id_order_rahma DESC";
    return $sql_rahma;
}

// Jalankan query untuk Dine In dan Take Away
$query_dinein_rahma = mysqli_query($koneksiRahma, buildQueryPesanan_rahma('Dine In', $tgl_mulai_rahma, $tgl_akhir_rahma));
$query_takeaway_rahma = mysqli_query($koneksiRahma, buildQueryPesanan_rahma('Take Away', $tgl_mulai_rahma, $tgl_akhir_rahma));

include "../templates/navbar_rahma.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../assets/css/kasir_rahma.css">
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <!-- =====================
    INFO BAR KASIR
    ===================== -->
    <div style="
        background: linear-gradient(90deg, #2c2c2c, #3a3a3a);
        color: #fff;
        font-size: 0.8rem;
        padding: 6px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 2px solid var(--dark-orange-rahma);
    ">
        <!-- Kiri: badge user logged + nama kasir -->
        <div class="d-flex align-items-center gap-3">
            <div style="
                background: var(--dark-orange-rahma);
                border-radius: 6px;
                padding: 3px 10px;
                font-weight: 700;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
            ">
                USER LOGGED
            </div>
            <span style="color: #ccc;">
                <i class="bi bi-person-fill me-1" style="color: var(--dark-orange-rahma);"></i>
                <?= htmlspecialchars(strtoupper($_SESSION['nama_rahma'] ?? 'KASIR')) ?>
            </span>
        </div>

        <!-- Tengah: info POS dan tanggal bisnis -->
        <div class="d-flex align-items-center gap-4" style="color: #ccc;">
            <span>
                <i class="bi bi-display me-1" style="color: var(--dark-orange-rahma);"></i>
                POS KASIR
            </span>
            <span>
                <i class="bi bi-calendar-check me-1" style="color: var(--dark-orange-rahma);"></i>
                Business Day: <?= date('d/m/Y') ?>
            </span>
        </div>

        <!-- Kanan: jam realtime -->
        <div class="d-flex align-items-center gap-2" style="color: #fff; font-weight: 600;">
            <i class="bi bi-clock me-1" style="color: var(--dark-orange-rahma);"></i>
            <span id="jam-kasir-rahma"></span>
        </div>
    </div>

    <div class="container mt-4">

        <!-- Judul halaman -->
        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard Kasir
        </h5>

        <!-- Card ringkasan hari ini -->
        <div class="row g-3 mb-4">

            <!-- Jumlah pesanan hari ini -->
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

            <!-- Total transaksi hari ini -->
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

        <!-- Card tabel pesanan -->
        <div class="card card-table-rahma">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Pesanan & Laporan Penjualan
                </h6>
            </div>

            <div class="card-body">

                <!-- Filter tanggal -->
                <div class="filter-box-rahma mb-4">
                    <form method="GET" action="dashboard_rahma.php" class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai_rahma"
                                class="form-control form-control-sm input-filter-rahma"
                                value="<?= $tgl_mulai_rahma ?: $tanggal_hari_ini_rahma ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir_rahma"
                                class="form-control form-control-sm input-filter-rahma"
                                value="<?= $tgl_akhir_rahma ?: $tanggal_hari_ini_rahma ?>" required>
                        </div>

                        <!-- Tombol filter & reset & cetak -->
                        <div class="col-md-6 d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-sm btn-bayar-rahma">
                                <i class="bi bi-filter me-1"></i>Filter
                            </button>
                            <a href="dashboard_rahma.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                            <button type="button" onclick="cetakPDF()" class="btn btn-sm btn-cetak-rahma">
                                <i class="bi bi-printer me-1"></i>Cetak PDF
                            </button>
                            <button type="button" onclick="cetakSemuaPDF()" class="btn btn-sm btn-cetak-semua-rahma">
                                <i class="bi bi-file-pdf me-1"></i>Cetak Semua Data
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Tab Dine In / Take Away -->
                <ul class="nav nav-tabs-rahma mb-3" id="tabPesananRahma" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-tab-rahma active" id="tab-dinein-rahma" data-bs-toggle="tab"
                            data-bs-target="#konten-dinein-rahma" type="button" role="tab">
                            <i class="bi bi-cup-straw me-2"></i>Dine In
                            <span class="tab-count-rahma">
                                <?= mysqli_num_rows($query_dinein_rahma) ?>
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-tab-rahma" id="tab-takeaway-rahma" data-bs-toggle="tab"
                            data-bs-target="#konten-takeaway-rahma" type="button" role="tab">
                            <i class="bi bi-bag me-2"></i>Take Away
                            <span class="tab-count-rahma">
                                <?= mysqli_num_rows($query_takeaway_rahma) ?>
                            </span>
                        </button>
                    </li>
                </ul>

                <!-- Konten tab -->
                <div class="tab-content" id="tabKontenRahma">

                    <!-- TAB DINE IN -->
                    <div class="tab-pane fade show active" id="konten-dinein-rahma" role="tabpanel">
                        <div class="table-container_rahma">
                            <table class="table-fixed_rahma">
                                <colgroup>
                                    <col style="width: 100px;"> <!-- ID Order -->
                                    <col style="width: 130px;"> <!-- Pelanggan -->
                                    <col style="width: 55px;"> <!-- Meja -->
                                    <col style="width: 90px;"> <!-- Tanggal -->
                                    <col style="width: 190px;"> <!-- Status -->
                                    <col style="width: 120px;"> <!-- Total -->
                                    <col style="width: 90px;"> <!-- Bayar -->
                                    <col style="width: 110px;"> <!-- Kasir -->
                                    <col style="width: 150px;"> <!-- Aksi -->
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="ps-3">ID Order</th>
                                        <th>Pelanggan</th>
                                        <th>Meja</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Bayar</th>
                                        <th>Kasir</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($query_dinein_rahma) > 0): ?>
                                        <?php while ($row_rahma = mysqli_fetch_assoc($query_dinein_rahma)): ?>
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="text-id-rahma">
                                                        <?= htmlspecialchars($row_rahma["id_order_rahma"]) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($row_rahma["nama_pelanggan_rahma"]) ?></td>
                                                <td>
                                                    <span class="badge badge-status-rahma"
                                                        style="background-color: var(--orange-rahma); color:#fff;">
                                                        <?= (int) ltrim($row_rahma["id_meja_rahma"], "M") ?>
                                                    </span>
                                                </td>
                                                <td class="small text-muted">
                                                    <?= $row_rahma["waktu_order_rahma"] ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $row_rahma['status_order_rahma'];
                                                    if ($status == 'menunggu_pembayaran') {
                                                        echo '<span class="badge badge-status-rahma badge-dibuat-rahma"><i class="bi bi-clock me-1"></i>Menunggu Pembayaran</span>';
                                                    } elseif ($status == 'diproses') {
                                                        echo '<span class="badge badge-status-rahma badge-diproses-rahma"><i class="bi bi-fire me-1"></i>Sedang Dimasak</span>';
                                                    } elseif ($status == 'selesai') {
                                                        echo '<span class="badge badge-status-rahma badge-selesai-rahma"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
                                                    } else {
                                                        echo '<span class="badge badge-status-rahma badge-disajikan-rahma"><i class="bi bi-check2-circle me-1"></i>Disajikan</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="fw-semibold">
                                                    Rp <?= number_format($row_rahma["grand_total_rahma"], 0, ",", ".") ?>
                                                </td>
                                                <!-- Status bayar -->
                                                <td>
                                                    <?php if ($row_rahma["sudah_bayar_rahma"]): ?>
                                                        <span class="badge badge-status-rahma badge-lunas-rahma">
                                                            <i class="bi bi-check2 me-1"></i>Lunas
                                                        </span>
                                                        <small><?php echo $row_rahma["metode_bayar_rahma"]; ?></small>
                                                    <?php else: ?>
                                                        <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                                            <i class="bi bi-x-circle me-1"></i>Belum
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- Nama kasir yang proses pembayaran -->
                                                <td>
                                                    <?php if (!empty($row_rahma['nama_kasir_rahma'])): ?>
                                                        <span class="small fw-semibold" style="color: var(--dark-orange-rahma);">
                                                            <i class="bi bi-person-badge me-1"></i>
                                                            <?= htmlspecialchars($row_rahma['nama_kasir_rahma']) ?>
                                                        </span>
                                                    <?php elseif ($row_rahma['sudah_bayar_rahma']): ?>
                                                        <!-- Sudah bayar tapi kasirnya SYSTEM = bayar online -->
                                                        <span class="small fw-semibold" style="color: #c2185b;">
                                                            <i class="bi bi-phone me-1"></i>Online
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="detail_order_rahma.php?id=<?= $row_rahma["id_order_rahma"] ?>"
                                                        class="btn btn-sm btn-detail-rahma me-1">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                    <?php if (!$row_rahma["sudah_bayar_rahma"]): ?>
                                                        <a href="pembayaran_rahma.php?id=<?= $row_rahma["id_order_rahma"] ?>"
                                                            class="btn btn-sm btn-bayar-rahma">
                                                            <i class="bi bi-cash"></i> Bayar
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="bi bi-cup-straw fs-3 d-block mb-2"
                                                    style="color: var(--orange-rahma);"></i>
                                                Tidak ada pesanan Dine In
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB TAKE AWAY -->
                    <div class="tab-pane fade" id="konten-takeaway-rahma" role="tabpanel">
                        <div class="table-container_rahma">
                            <table class="table-fixed_rahma">
                                <colgroup>
                                    <col style="width: 100px;"> <!-- ID Order -->
                                    <col style="width: 120px;"> <!-- Pelanggan -->
                                    <col style="width: 90px;"> <!-- Tanggal -->
                                    <col style="width: 175px;"> <!-- Status -->
                                    <col style="width: 120px;"> <!-- Total -->
                                    <col style="width: 80px;"> <!-- Bayar -->
                                    <col style="width: 120px;"> <!-- Kasir -->
                                    <col style="width: 150px;"> <!-- Aksi -->
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="ps-3">ID Order</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Bayar</th>
                                        <th>Kasir</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($query_takeaway_rahma) > 0): ?>
                                        <?php while ($row_rahma = mysqli_fetch_assoc($query_takeaway_rahma)): ?>
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="text-id-rahma">
                                                        <?= htmlspecialchars($row_rahma["id_order_rahma"]) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($row_rahma["nama_pelanggan_rahma"]) ?></td>
                                                <td class="small text-muted">
                                                    <?= $row_rahma["waktu_order_rahma"] ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $row_rahma['status_order_rahma'];
                                                    if ($status == 'menunggu_pembayaran') {
                                                        echo '<span class="badge badge-status-rahma badge-dibuat-rahma"><i class="bi bi-clock me-1"></i>Menunggu Pembayaran</span>';
                                                    } elseif ($status == 'diproses') {
                                                        echo '<span class="badge badge-status-rahma badge-diproses-rahma"><i class="bi bi-fire me-1"></i>Sedang Dimasak</span>';
                                                    } elseif ($status == 'selesai') {
                                                        echo '<span class="badge badge-status-rahma badge-selesai-rahma"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
                                                    } else {
                                                        echo '<span class="badge badge-status-rahma badge-disajikan-rahma"><i class="bi bi-check2-circle me-1"></i>Disajikan</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="fw-semibold">
                                                    Rp <?= number_format($row_rahma["grand_total_rahma"], 0, ",", ".") ?>
                                                </td>
                                                <!-- Status bayar -->
                                                <td>
                                                    <?php if ($row_rahma["sudah_bayar_rahma"]): ?>
                                                        <span class="badge badge-status-rahma badge-lunas-rahma">
                                                            <i class="bi bi-check2 me-1"></i>Lunas
                                                        </span>
                                                        <small><?php echo $row_rahma['metode_bayar_rahma']; ?></small>
                                                    <?php else: ?>
                                                        <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                                            <i class="bi bi-x-circle me-1"></i>Belum
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- Nama kasir yang proses pembayaran -->
                                                <td>
                                                    <?php if (!empty($row_rahma['nama_kasir_rahma'])): ?>
                                                        <span class="small fw-semibold" style="color: var(--dark-orange-rahma);">
                                                            <i class="bi bi-person-badge me-1"></i>
                                                            <?= htmlspecialchars($row_rahma['nama_kasir_rahma']) ?>
                                                        </span>
                                                    <?php elseif ($row_rahma['sudah_bayar_rahma']): ?>
                                                        <!-- Sudah bayar tapi kasirnya SYSTEM = bayar online -->
                                                        <span class="small fw-semibold" style="color: #c2185b;">
                                                            <i class="bi bi-phone me-1"></i>Online
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <!-- Aksi detail & bayar -->
                                                <td class="text-center">
                                                    <a href="detail_order_rahma.php?id=<?= $row_rahma["id_order_rahma"] ?>"
                                                        class="btn btn-sm btn-detail-rahma me-1">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                    <?php if (!$row_rahma["sudah_bayar_rahma"]): ?>
                                                        <a href="pembayaran_rahma.php?id=<?= $row_rahma["id_order_rahma"] ?>"
                                                            class="btn btn-sm btn-bayar-rahma">
                                                            <i class="bi bi-cash"></i> Bayar
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <i class="bi bi-bag fs-3 d-block mb-2"
                                                    style="color: var(--pink-rahma);"></i>
                                                Tidak ada pesanan Take Away
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /tab-content -->

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update jam realtime setiap detik di info bar
        function updateJam_rahma() {
            const now_rahma = new Date();
            const jam_rahma = now_rahma.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('jam-kasir-rahma').textContent = jam_rahma;
        }
        updateJam_rahma();
        setInterval(updateJam_rahma, 1000);

        // Cetak PDF dengan membawa parameter tanggal filter
        function cetakPDF() {
            const tglMulai_rahma = document.querySelector('[name="tanggal_mulai_rahma"]').value;
            const tglAkhir_rahma = document.querySelector('[name="tanggal_akhir_rahma"]').value;

            const tabAktif_rahma = document.querySelector('.nav-tab-rahma.active');
            const idTab_rahma = tabAktif_rahma ? tabAktif_rahma.id : '';

            let jenis_rahma = "semua";
            if (idTab_rahma === "tab-dinein-rahma") jenis_rahma = "dinein";
            if (idTab_rahma === "tab-takeaway-rahma") jenis_rahma = "takeaway";

            window.location.href = `cetak_laporan_rahma.php?tanggal_mulai_rahma=${tglMulai_rahma}&tanggal_akhir_rahma=${tglAkhir_rahma}&jenis=${jenis_rahma}`;
        }

        // Cetak PDF Semua Data (Dine In + Take Away)
        function cetakSemuaPDF() {
            const tglMulai_rahma = document.querySelector('[name="tanggal_mulai_rahma"]').value;
            const tglAkhir_rahma = document.querySelector('[name="tanggal_akhir_rahma"]').value;
            window.location.href = `cetak_laporan_rahma.php?tanggal_mulai_rahma=${tglMulai_rahma}&tanggal_akhir_rahma=${tglAkhir_rahma}&jenis=semua`;
        }

        // Reload halaman kalau user klik back dari cache browser
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>