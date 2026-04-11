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

// Kalau id_meja sudah ada di session, langsung ke menu
if (isset($_SESSION['id_meja_rahma']) && !empty($_SESSION['id_meja_rahma'])) {
    header("Location: menu_rahma.php?meja=" . $_SESSION['id_meja_rahma']);
    exit;
}

// Kalau meja sudah ada di session, cek dulu statusnya di database
if (isset($_SESSION['id_meja_rahma']) && !empty($_SESSION['id_meja_rahma'])) {
    
    include '../koneksi/koneksi_rahma.php';
    // Cek status meja di database
    $id_meja_cek_rahma      = $_SESSION['id_meja_rahma'];
    $query_cek_meja_rahma   = mysqli_query($koneksiRahma, "
        SELECT status_rahma FROM tbl_meja_rahma
        WHERE id_meja_rahma = '$id_meja_cek_rahma'
    ");
    $data_meja_cek_rahma    = mysqli_fetch_assoc($query_cek_meja_rahma);

    if ($data_meja_cek_rahma['status_rahma'] == 'terpakai') {
        // Meja masih terpakai — langsung ke menu
        header("Location: menu_rahma.php?meja=" . $_SESSION['id_meja_rahma']);
        exit;
    } else {
        // Meja sudah kosong — berarti sudah bayar, reset session meja
        unset($_SESSION['id_meja_rahma']);
    }
}

include '../koneksi/koneksi_rahma.php';

// Ambil semua meja yang kosong aja
$query_meja_rahma = mysqli_query($koneksiRahma, "
    SELECT * FROM tbl_meja_rahma
    WHERE status_rahma = 'kosong'
    ORDER BY id_meja_rahma ASC
");

// Hitung total meja kosong
$total_meja_kosong_rahma = mysqli_num_rows($query_meja_rahma);

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
            Tersedia <strong><?= $total_meja_kosong_rahma ?></strong> meja kosong
        </p>

        <?php if ($total_meja_kosong_rahma > 0): ?>
            <div class="row g-3">
                <?php while ($row_meja_rahma = mysqli_fetch_assoc($query_meja_rahma)): ?>
                    <div class="col-6 col-md-3">
                        <!-- Setiap meja adalah tombol yang bisa diklik -->
                        <a href="menu_rahma.php?meja=<?= $row_meja_rahma['id_meja_rahma'] ?>"
                            class="card card-meja-rahma text-decoration-none text-center p-4">
                            <div class="icon-meja-rahma">
                                <i class="bi bi-table"></i>
                            </div>
                            <div class="nomor-meja-rahma">
                                <?= (int) ltrim($row_meja_rahma['id_meja_rahma'], 'M') ?>
                            </div>
                            <div class="status-meja-rahma">
                                <span class="badge"
                                    style="background-color: var(--dark-pink-rahma); color:#fff; border-radius: 20px; padding: 4px 12px; font-size: 0.75rem;">
                                    Kosong
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <!-- Tampil kalau semua meja penuh -->
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown fs-1 d-block mb-3" style="color: var(--orange-rahma);"></i>
                <h6 class="fw-semibold" style="color: var(--dark-orange-rahma);">Semua meja sedang terpakai</h6>
                <p class="text-muted">Mohon tunggu sebentar ya!</p>
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