<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


// Kalau belum login dan bukan guest, suruh login dulu
if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

// Kalau sudah login tapi bukan member (R003) dan bukan guest, tolak
if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Kalau id_meja sudah ada di session, langsung ke menu
if (isset($_SESSION['id_meja_rahma']) && !empty($_SESSION['id_meja_rahma'])) {
    $jenis_rahma = $_SESSION['jenis_pesanan_rahma'] ?? 'dinein';
    if ($jenis_rahma == 'take away') {
        header("Location: menu_rahma.php?jenis=takeaway");
    } else {
        header("Location: menu_rahma.php?meja=" . $_SESSION['id_meja_rahma']);
    }
    exit;
}

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
    <title>Pilih Meja</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">
        <h5 class="mb-2 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-grid me-2"></i>Pilih Meja
        </h5>
        <p class="text-muted mb-4">
            Pilih dulu jenis pesanan: <strong>Dine In</strong> atau <strong>Take Away</strong>.
        </p>

        <div class="card card-table-rahma border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Jenis Pesanan</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <input type="radio" class="btn-check" name="jenis_pesanan_rahma" id="dinein_rahma"
                            value="dine in">
                        <label class="btn btn-outline-primary rounded-pill px-3" for="dinein_rahma">
                            <i class="bi bi-shop me-1"></i>Dine In
                        </label>

                        <input type="radio" class="btn-check" name="jenis_pesanan_rahma" id="takeaway_rahma"
                            value="take away">
                        <label class="btn btn-outline-primary rounded-pill px-3" for="takeaway_rahma">
                            <i class="bi bi-bag me-1"></i>Take Away
                        </label>
                    </div>
                </div>

                <div id="sectionMejaRahma" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-semibold" style="color: var(--dark-orange-rahma);">
                            <i class="bi bi-table me-2"></i>Pilih Meja
                        </h6>
                        <small class="text-muted">
                            Tersedia
                            <strong><?= mysqli_num_rows(mysqli_query($koneksiRahma, "SELECT * FROM tbl_meja_rahma WHERE status_rahma = 'kosong'")) ?></strong>
                            meja kosong
                        </small>
                    </div>

                    <?php
                    $query_meja_rahma = mysqli_query($koneksiRahma, "
                        SELECT * FROM tbl_meja_rahma
                        ORDER BY id_meja_rahma ASC
                    ");
                    ?>

                    <?php if (mysqli_num_rows($query_meja_rahma) > 0): ?>
                        <div class="row g-3">
                            <?php while ($row_meja_rahma = mysqli_fetch_assoc($query_meja_rahma)): ?>
                                <?php
                                $status_meja_rahma = $row_meja_rahma['status_rahma'];
                                $nomor_meja_rahma = (int) ltrim($row_meja_rahma['id_meja_rahma'], 'M');
                                ?>
                                <div class="col-6 col-md-3">
                                    <?php if ($status_meja_rahma == 'kosong'): ?>
                                        <a href="menu_rahma.php?meja=<?= $row_meja_rahma['id_meja_rahma'] ?>"
                                            class="card card-meja-rahma text-decoration-none text-center p-4 meja-kosong-rahma">
                                            <div class="icon-meja-rahma">
                                                <i class="bi bi-table"></i>
                                            </div>
                                            <div class="nomor-meja-rahma">
                                                <?= $nomor_meja_rahma ?>
                                            </div>
                                            <div class="status-meja-rahma">
                                                <span class="badge bg-success rounded-pill px-3 py-2">Kosong</span>
                                            </div>
                                        </a>
                                    <?php else: ?>
                                        <div class="card card-meja-rahma text-center p-4 meja-terpakai-rahma">
                                            <div class="icon-meja-rahma">
                                                <i class="bi bi-table"></i>
                                            </div>
                                            <div class="nomor-meja-rahma">
                                                <?= $nomor_meja_rahma ?>
                                            </div>
                                            <div class="status-meja-rahma">
                                                <span class="badge bg-secondary rounded-pill px-3 py-2">Terpakai</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-frown fs-1 d-block mb-3" style="color: var(--orange-rahma);"></i>
                            <h6 class="fw-semibold" style="color: var(--dark-orange-rahma);">Belum ada data meja</h6>
                            <p class="text-muted">Mohon tunggu sebentar ya!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const dineinRahma = document.getElementById('dinein_rahma');
            const takeawayRahma = document.getElementById('takeaway_rahma');
            const sectionMejaRahma = document.getElementById('sectionMejaRahma');

            function togglePesananRahma() {
                if (takeawayRahma && takeawayRahma.checked) {
                    // Take away — langsung ke menu dengan parameter jenis
                    window.location.href = 'menu_rahma.php?jenis=takeaway';
                    return;
                }

                if (dineinRahma && dineinRahma.checked) {
                    sectionMejaRahma.style.display = 'block';
                } else {
                    sectionMejaRahma.style.display = 'none';
                }
            }

            dineinRahma.addEventListener('change', togglePesananRahma);
            takeawayRahma.addEventListener('change', togglePesananRahma);
        });
    </script>
</body>

</html>