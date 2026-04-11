<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R004') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Notif sukses/error dari proses
$sukses_rahma = isset($_GET['sukses']) && $_GET['sukses'] == '1';
$error_rahma = isset($_GET['error']) && $_GET['error'] == '1';

// Ambil semua order yang belum selesai + detail itemnya
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.waktu_order_rahma,
        o.keterangan_rahma,
        COUNT(d.id_dorder_rahma) AS jumlah_item_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.status_order_rahma = 'dibuat'
    GROUP BY o.id_order_rahma
    ORDER BY o.waktu_order_rahma ASC
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
    <link rel="stylesheet" href="../assets/css/chef_rahma.css">
    <title>Update Status Order</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-fire me-2"></i>Order Perlu Dimasak
        </h5>

        <!-- Notif sukses -->
        <?php if ($sukses_rahma): ?>
            <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-3"
                style="background: linear-gradient(135deg, rgba(212,44,0,0.08), rgba(162,1,97,0.08));">
                <i class="bi bi-check-circle-fill fs-4" style="color: var(--dark-pink-rahma);"></i>
                <div class="fw-semibold" style="color: var(--dark-orange-rahma);">
                    Order berhasil ditandai selesai! 🎉
                </div>
            </div>
        <?php endif; ?>

        <!-- Notif error -->
        <?php if ($error_rahma): ?>
            <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-3"
                style="background-color: rgba(212,44,0,0.08);">
                <i class="bi bi-exclamation-circle-fill fs-4" style="color: var(--dark-orange-rahma);"></i>
                <div class="fw-semibold" style="color: var(--dark-orange-rahma);">
                    Gagal update status — coba lagi!
                </div>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($query_order_rahma) > 0): ?>
            <div class="row g-3">
                <?php while ($row_order_rahma = mysqli_fetch_assoc($query_order_rahma)):
                    // Ambil detail item per order
                    $id_order_item_rahma = $row_order_rahma['id_order_rahma'];
                    $query_item_rahma = mysqli_query($koneksiRahma, "
                    SELECT d.qty_rahma, d.catatan_rahma, mn.nama_menu_rahma
                    FROM tbl_detail_order_rahma d
                    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
                    WHERE d.id_order_rahma = '$id_order_item_rahma'
                ");
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-order-chef-rahma h-100">

                            <!-- Header card per order -->
                            <div class="card-order-header-rahma p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-id-rahma">
                                        <?= htmlspecialchars($row_order_rahma['id_order_rahma']) ?>
                                    </span>
                                    <span class="badge badge-status-rahma"
                                        style="background-color: var(--orange-rahma); color:#fff;">
                                        Meja <?= (int) ltrim($row_order_rahma['id_meja_rahma'], 'M') ?>
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    👤 <?= htmlspecialchars($row_order_rahma['nama_pelanggan_rahma']) ?>
                                </div>
                                <?php if (!empty($row_order_rahma['keterangan_rahma']) && $row_order_rahma['keterangan_rahma'] != '-'): ?>
                                    <div class="mt-1 p-2 rounded-2"
                                        style="background-color: rgba(253,152,85,0.1); font-size: 0.8rem; color: var(--dark-orange-rahma);">
                                        📝 <?= htmlspecialchars($row_order_rahma['keterangan_rahma']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- List item pesanan -->
                            <div class="card-body p-3 flex-grow-1">
                                <div class="fw-semibold small mb-2" style="color: var(--dark-pink-rahma);">
                                    <i class="bi bi-bag me-1"></i>Item Pesanan:
                                </div>
                                <?php while ($row_item_rahma = mysqli_fetch_assoc($query_item_rahma)): ?>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.88rem; color: var(--dark-orange-rahma);">
                                                <?= htmlspecialchars($row_item_rahma['nama_menu_rahma']) ?>
                                            </div>
                                            <?php if (!empty($row_item_rahma['catatan_rahma']) && $row_item_rahma['catatan_rahma'] != '-'): ?>
                                                <small class="text-muted">
                                                    📝 <?= htmlspecialchars($row_item_rahma['catatan_rahma']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Qty per item -->
                                        <span class="badge ms-2 flex-shrink-0"
                                            style="background-color: var(--dark-orange-rahma); color:#fff; border-radius: 20px; padding: 4px 10px;">
                                            x<?= $row_item_rahma['qty_rahma'] ?>
                                        </span>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <!-- Tombol tandai selesai -->
                            <div class="p-3 pt-0">
                                <form action="../proses/proses_status_rahma.php" method="POST">
                                    <input type="hidden" name="id_order_rahma"
                                        value="<?= $row_order_rahma['id_order_rahma'] ?>">
                                    <input type="hidden" name="redirect_rahma" value="update">
                                    <button type="submit" class="btn-update-chef-rahma w-100"
                                        onclick="return confirm('Tandai order <?= $row_order_rahma['id_order_rahma'] ?> sebagai selesai?')">
                                        <i class="bi bi-check-circle me-2"></i>Tandai Selesai
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-check2-circle fs-1 d-block mb-3" style="color: var(--pink-rahma); opacity: 0.6;"></i>
                <h6 class="fw-semibold" style="color: var(--dark-orange-rahma);">
                    Semua order sudah selesai dimasak!
                </h6>
                <p class="text-muted">Tidak ada order yang perlu dimasak saat ini</p>
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