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

// Ambil semua user dengan role member (R003)
$query_user_rahma = mysqli_query($koneksiRahma, "
    SELECT u.id_user_rahma, u.username_rahma, u.nama_rahma, r.role_rahma
    FROM tbl_user_rahma u
    LEFT JOIN tbl_role_rahma r ON u.id_role_rahma = r.id_role_rahma
    WHERE u.id_role_rahma = 'R003'
    ORDER BY u.id_user_rahma DESC
");

// Hitung total member
$query_total_member_rahma = mysqli_query($koneksiRahma, "
    SELECT COUNT(*) AS total_rahma 
    FROM tbl_user_rahma 
    WHERE id_role_rahma = 'R003'
");
$data_total_member_rahma = mysqli_fetch_assoc($query_total_member_rahma);
$total_member_rahma = $data_total_member_rahma['total_rahma'];

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
    <title>Data User</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-people me-2"></i>Data Member
        </h5>

        <!-- Card total member -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-summary-rahma">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 icon-circle-pink-rahma">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Member</div>
                            <div class="fs-3 fw-bold text-transaksi-rahma">
                                <?= $total_member_rahma ?>
                            </div>
                            <div class="text-muted small">member terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel data user -->
        <div class="card card-table-rahma">
            <div class="card-header card-header-rahma py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Member Terdaftar
                </h6>
                <!-- Label read-only — kasir tidak bisa edit/hapus -->
                <span class="badge" style="background-color: rgba(255,255,255,0.2); color:#fff; font-size: 0.75rem;">
                    <i class="bi bi-eye me-1"></i>Read Only
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>ID User</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query_user_rahma) > 0): ?>
                                <?php
                                $no_rahma = 1;
                                while ($row_user_rahma = mysqli_fetch_assoc($query_user_rahma)): ?>
                                    <tr>
                                        <td class="ps-3 text-muted"><?= $no_rahma++ ?></td>
                                        <td>
                                            <span class="text-id-rahma">
                                                <?= htmlspecialchars($row_user_rahma['id_user_rahma']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row_user_rahma['username_rahma']) ?></td>
                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($row_user_rahma['nama_rahma']) ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-status-rahma badge-lunas-rahma">
                                                <?= htmlspecialchars($row_user_rahma['role_rahma']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Belum ada member terdaftar
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