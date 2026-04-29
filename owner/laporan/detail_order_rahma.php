<?php
//laporan/detail_order_rahma.php
session_start();

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

// Ambil ID order dari URL
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
// DATA TRANSAKSI + JOIN nama kasir
// =====================
$data_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT t.*, u.nama_rahma AS nama_kasir_rahma
     FROM tbl_transaksi_rahma t
     LEFT JOIN tbl_user_rahma u ON t.id_kasir_rahma = u.id_user_rahma
     WHERE t.id_order_rahma='$id_order_rahma'"
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">

        <!-- Header Halaman -->
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-receipt-cutoff fs-3" style="color: var(--dark-orange-rahma); margin-right: 12px;"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--dark-orange-rahma);">Detail Order</h4>
                <small class="text-muted">Informasi lengkap pesanan dan transaksi</small>
            </div>
        </div>

        <!-- INFO ORDER CARD -->
        <div class="card card-summary-rahma mb-4">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-info-circle me-2"></i>Informasi Order
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-hash me-1"></i>ID Order</label>
                            <div class="text-id-rahma fw-semibold">
                                <?= htmlspecialchars($data_order_rahma['id_order_rahma']) ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-calendar me-1"></i>Tanggal & Waktu</label>
                            <div><?= date('d-m-Y H:i', strtotime($data_order_rahma['waktu_order_rahma'])) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-person me-1"></i>Nama Pemesan</label>
                            <div class="fw-semibold">
                                <?= htmlspecialchars($data_order_rahma['nama_pelanggan_rahma']) ?>
                                <?php if (!empty($data_order_rahma['id_user_rahma'])) { ?>
                                    <span class="badge badge-aktif-rahma ms-2">
                                        <i class="bi bi-star-fill me-1"></i>Member
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-nonaktif-rahma ms-2">Tamu</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-table me-1"></i>Meja</label>
                            <div class="fw-semibold">
                                <span class="badge badge-status-rahma"
                                    style="background-color: var(--orange-rahma); color: #fff; font-size: 0.95rem;">
                                    Meja <?= (int) ltrim($data_order_rahma['id_meja_rahma'], 'M') ?>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-cup-straw me-1"></i>Jenis Pesanan</label>
                            <div class="fw-semibold">
                                <?php if ($data_order_rahma['jenis_pesanan_rahma'] === 'dine in') { ?>
                                    <span class="badge badge-diproses-rahma">
                                        <i class="bi bi-cup-straw me-1"></i>Dine In
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-diproses-rahma"
                                        style="background-color: var(--dark-pink-rahma);">
                                        <i class="bi bi-bag me-1"></i>Take Away
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Nama kasir yang menangani transaksi ini -->
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-person-badge me-1"></i>Kasir Bertugas</label>
                            <div class="fw-semibold">
                                <?php if (!empty($data_transaksi_rahma['nama_kasir_rahma'])): ?>
                                    <span style="color: var(--dark-orange-rahma);">
                                        <?= htmlspecialchars($data_transaksi_rahma['nama_kasir_rahma']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada transaksi</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="label-rahma"><i class="bi bi-printer me-1"></i>Aksi</label>
                            <a href="print_detail_order_rahma.php?id=<?= htmlspecialchars($data_order_rahma['id_order_rahma']) ?>"
                                target="_blank" class="btn btn-bayar-rahma btn-sm">
                                <i class="bi bi-printer me-1"></i>Cetak Detail Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFO TRANSAKSI CARD (jika sudah bayar) -->
        <?php if ($data_transaksi_rahma) { ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-summary-rahma h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-cash-stack me-1"></i>Total Pesanan
                            </div>
                            <div class="fs-3 fw-bold text-order-rahma">
                                Rp <?= number_format($data_transaksi_rahma['total_rahma'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-summary-rahma h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-credit-card me-1"></i>Jumlah Bayar
                            </div>
                            <div class="fs-3 fw-bold text-transaksi-rahma">
                                Rp <?= number_format($data_transaksi_rahma['bayar_rahma'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-summary-rahma h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-coin me-1"></i>Kembalian
                            </div>
                            <div class="fs-3 fw-bold" style="color: var(--dark-orange-rahma);">
                                Rp <?= number_format($data_transaksi_rahma['kembalian_rahma'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- DETAIL MENU TABEL -->
        <div class="card card-table-rahma mb-4">
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Detail Menu yang Dipesan
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-container_rahma">
                    <table class="table-fixed_rahma">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 5%;">No</th>
                                <th style="width: 45%;">Nama Menu</th>
                                <th class="text-center" style="width: 12%;">Jumlah</th>
                                <th class="text-end" style="width: 20%;">Harga Satuan</th>
                                <th class="text-end pe-3" style="width: 18%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no_rahma = 1;
                            $row_count_rahma = 0;
                            $subtotal_sebelum_diskon_rahma = 0;

                            while ($d_rahma = mysqli_fetch_assoc($detail_rahma)) {
                                // Hitung subtotal per item
                                $subtotal_rahma = $d_rahma['qty_rahma'] * $d_rahma['harga_rahma'];
                                // Akumulasi semua subtotal
                                $subtotal_sebelum_diskon_rahma += $subtotal_rahma;
                                $bg_color_rahma = ($row_count_rahma % 2 === 0) ? '#ffffff' : '#f9f9f9';
                            ?>
                                <tr style="background-color: <?= $bg_color_rahma ?>; transition: all 0.2s ease;"
                                    onmouseover="this.style.backgroundColor='#f0e8e0';"
                                    onmouseout="this.style.backgroundColor='<?= $bg_color_rahma ?>';">
                                    <td class="ps-3" style="font-weight: 600; color: var(--dark-orange-rahma);"><?= $no_rahma++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($d_rahma['nama_menu_rahma']) ?></td>
                                    <td class="text-center fw-semibold"><?= $d_rahma['qty_rahma'] ?></td>
                                    <td class="text-end">Rp <?= number_format($d_rahma['harga_rahma'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold pe-3">Rp <?= number_format($subtotal_rahma, 0, ',', '.') ?></td>
                                </tr>
                            <?php $row_count_rahma++; } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Total Footer -->
                <div style="padding: 16px 20px; border-top: 2px solid #e8d0c8; background: linear-gradient(135deg, #fdf4f0, #f9f9f9);">
                    <?php
                    // Ambil persen diskon dari data transaksi
                    $diskon_persen_rahma  = $data_transaksi_rahma['diskon_rahma'] ?? 0;
                    // Hitung nominal diskon
                    $diskon_nominal_rahma = ($subtotal_sebelum_diskon_rahma * $diskon_persen_rahma) / 100;
                    // Total setelah diskon
                    $total_setelah_diskon_rahma = $subtotal_sebelum_diskon_rahma - $diskon_nominal_rahma;
                    // Pajak 11% dari total setelah diskon
                    $pajak_nominal_rahma  = $total_setelah_diskon_rahma * 0.11;
                    // Total akhir
                    $total_akhir_rahma    = $total_setelah_diskon_rahma + $pajak_nominal_rahma;
                    ?>

                    <!-- Subtotal -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #888;">Subtotal</span>
                        <span>Rp <?= number_format($subtotal_sebelum_diskon_rahma, 0, ',', '.') ?></span>
                    </div>

                    <!-- Diskon -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #e05c00;">
                            <i class="bi bi-tag me-1"></i>Diskon (<?= $diskon_persen_rahma ?>%)
                        </span>
                        <span style="color: #e05c00;">- Rp <?= number_format($diskon_nominal_rahma, 0, ',', '.') ?></span>
                    </div>

                    <!-- Pajak -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #888;">
                            <i class="bi bi-receipt me-1"></i>Pajak (11%)
                        </span>
                        <span style="color: #888;">+ Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?></span>
                    </div>

                    <hr style="border-color: #e8d0c8; margin: 8px 0;">

                    <!-- Total akhir -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1rem; font-weight: 600; color: var(--dark-orange-rahma);">Total Akhir :</span>
                        <span style="font-size: 1.3rem; font-weight: bold; color: var(--dark-orange-rahma);">
                            Rp <?= number_format($total_akhir_rahma, 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMBOL AKSI -->
        <div class="d-flex gap-2 mb-4">
            <a href="transaksi_rahma.php" class="btn btn-kembali-rahma">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Laporan
            </a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>