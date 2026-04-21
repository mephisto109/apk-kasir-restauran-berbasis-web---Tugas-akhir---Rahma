<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

$is_member_rahma = isset($_SESSION['id_user_rahma']);
$id_order_rahma = $_GET['order'] ?? '';
$sukses_rahma = isset($_GET['sukses']) && $_GET['sukses'] == '1';

// Kalau tidak ada id_order, balik ke menu
if (empty($id_order_rahma)) {
    header("Location: menu_rahma.php?meja=" . ($_SESSION['id_meja_rahma'] ?? ''));
    exit;
}

// Ambil data order
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.*,
        COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma,
        MAX(CASE WHEN t.id_transaksi_rahma IS NOT NULL THEN 1 ELSE 0 END) AS sudah_bayar_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    LEFT JOIN tbl_transaksi_rahma t ON o.id_order_rahma = t.id_order_rahma
    WHERE o.id_order_rahma = '$id_order_rahma'
    GROUP BY o.id_order_rahma
");

if (mysqli_num_rows($query_order_rahma) == 0) {
    header("Location: menu_rahma.php?meja=" . ($_SESSION['id_meja_rahma'] ?? ''));
    exit;
}

$order_rahma = mysqli_fetch_assoc($query_order_rahma);

// Ambil detail item yang dipesan
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, mn.nama_menu_rahma, mn.harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '$id_order_rahma'
");

// Hitung diskon kalau member
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = ($order_rahma['grand_total_rahma'] * $diskon_persen_rahma) / 100;
$total_bayar_rahma = $order_rahma['grand_total_rahma'] - $nominal_diskon_rahma;
$nomor_meja_rahma = (int) ltrim($order_rahma['id_meja_rahma'], 'M');

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
    <link rel="stylesheet" href="../assets/css/pelanggan_rahma.css">
    <title>Pesanan Saya</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-receipt me-2"></i>Pesanan Saya
                </h5>
                <small class="text-muted">
                    <i class="bi bi-table me-1"></i>Meja <?= $nomor_meja_rahma ?>
                </small>
            </div>
            <!-- Tombol pesan lagi -->
            <a href="menu_rahma.php?meja=<?= $order_rahma['id_meja_rahma'] ?>"
                class="btn-tambah-rahma px-3 py-2 text-decoration-none">
                <i class="bi bi-plus-circle me-1"></i>Pesan Lagi
            </a>
        </div>

        <!-- Notif sukses -->
        <?php if ($sukses_rahma): ?>
            <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-3"
                style="background: linear-gradient(135deg, rgba(212,44,0,0.08), rgba(162,1,97,0.08));">
                <i class="bi bi-check-circle-fill fs-4" style="color: var(--dark-pink-rahma);"></i>
                <div>
                    <div class="fw-semibold" style="color: var(--dark-orange-rahma);">
                        Pesanan berhasil dibuat! 🎉
                    </div>
                    <small class="text-muted">Silakan tunggu pesananmu ya!</small>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info guest -->
        <?php if (!$is_member_rahma): ?>
            <div class="alert border-0 rounded-3 mb-4"
                style="background-color: rgba(253,152,85,0.1); border-left: 4px solid var(--orange-rahma) !important;">
                <small style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-info-circle me-1"></i>
                    Kamu masuk sebagai <strong>Guest</strong>.
                    <a href="../register_rahma.php" style="color: var(--dark-pink-rahma); font-weight: 600;">
                        Daftar member
                    </a> untuk dapat diskon <?= $diskon_persen_rahma == 0 ? '10' : $diskon_persen_rahma ?>% setiap
                    transaksi!
                </small>
            </div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Daftar item -->
            <div class="col-md-7">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-bag me-2"></i>Item Pesanan
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop item pesanan -->
                                <?php while ($row_detail_rahma = mysqli_fetch_assoc($query_detail_rahma)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold"
                                                style="color: var(--dark-orange-rahma); font-size: 0.88rem;">
                                                <?= htmlspecialchars($row_detail_rahma['nama_menu_rahma']) ?>
                                            </div>
                                            <?php if (!empty($row_detail_rahma['catatan_rahma']) && $row_detail_rahma['catatan_rahma'] != '-'): ?>
                                                <small class="text-muted">
                                                    📝 <?= htmlspecialchars($row_detail_rahma['catatan_rahma']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $row_detail_rahma['qty_rahma'] ?></td>
                                        <td class="text-end pe-3 fw-semibold" style="color: var(--dark-pink-rahma);">
                                            Rp <?= number_format($row_detail_rahma['subtotal_rahma'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 text-muted small">Subtotal</td>
                                    <td class="text-end pe-3">
                                        Rp <?= number_format($order_rahma['grand_total_rahma'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php if ($is_member_rahma): ?>
                                    <tr class="table-light">
                                        <td colspan="2" class="ps-3 text-muted small">
                                            Diskon Member
                                            <span class="badge"
                                                style="background-color: var(--dark-pink-rahma); color:#fff; font-size:0.65rem;">
                                                <?= $diskon_persen_rahma ?>%
                                            </span>
                                        </td>
                                        <td class="text-end pe-3" style="color: var(--dark-pink-rahma);">
                                            - Rp <?= number_format($nominal_diskon_rahma, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="2" class="ps-3 fw-bold">Total Bayar</td>
                                    <td class="text-end pe-3 fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                        Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info order -->
            <div class="col-md-5">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-info-circle me-2"></i>Info Pesanan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">ID Order</small>
                            <span class="text-id-rahma fw-bold">
                                <?= htmlspecialchars($order_rahma['id_order_rahma']) ?>
                            </span>
                        </div>

                        <!-- Tambah nama pelanggan di sini -->
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Nama Pelanggan</small>
                            <span class="fw-semibold">
                                <?= htmlspecialchars($order_rahma['nama_pelanggan_rahma']) ?>
                            </span>
                        </div>

                        <!-- Tambah jenis pesanan di sini -->
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Jenis Pesanan</small>
                            <span class="badge badge-status-rahma"
                                style="background-color: <?= $order_rahma['jenis_pesanan_rahma'] == 'dine in' ? 'var(--dark-pink-rahma)' : 'var(--dark-orange-rahma)' ?>; color:#fff;">
                                <i
                                    class="bi bi-<?= $order_rahma['jenis_pesanan_rahma'] == 'dine in' ? 'shop' : 'bag' ?> me-1"></i>
                                <?= ucfirst($order_rahma['jenis_pesanan_rahma']) ?>
                            </span>
                        </div>

                        <!-- Tambah nomor meja di sini -->
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Meja</small>
                            <span class="badge badge-status-rahma"
                                style="background-color: var(--orange-rahma); color:#fff;">
                                <?= $nomor_meja_rahma ?>
                            </span>
                        </div>

                        <!-- Status order -->
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Status Order</small>
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

                        <!-- Status pembayaran -->
                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">Pembayaran</small>
                            <?php if ($order_rahma['sudah_bayar_rahma']): ?>
                                <span class="badge badge-status-rahma badge-lunas-rahma">
                                    <i class="bi bi-check2 me-1"></i>Lunas
                                </span>
                            <?php else: ?>
                                <span class="badge badge-status-rahma badge-belumbayar-rahma">
                                    <i class="bi bi-x-circle me-1"></i>Belum Bayar
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Tanggal</small>
                            <span><?= $order_rahma['waktu_order_rahma'] ?></span>
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