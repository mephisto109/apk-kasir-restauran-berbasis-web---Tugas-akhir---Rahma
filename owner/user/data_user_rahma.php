<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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

$data_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        tbl_user_rahma.*, 
        tbl_role_rahma.role_rahma 
    FROM tbl_user_rahma
    JOIN tbl_role_rahma 
    ON tbl_user_rahma.id_role_rahma = tbl_role_rahma.id_role_rahma
");

// Pisahkan data tanpa ubah nama variabel field-nya
$owner_rahma = [];
$member_rahma = [];
$kasir_rahma = [];
$chef_rahma = [];

while ($row_rahma = mysqli_fetch_assoc($data_rahma)) {
    if ($row_rahma['role_rahma'] == 'owner') $owner_rahma[] = $row_rahma;
    elseif ($row_rahma['role_rahma'] == 'member') $member_rahma[] = $row_rahma;
    elseif ($row_rahma['role_rahma'] == 'kasir') $kasir_rahma[] = $row_rahma;
    elseif ($row_rahma['role_rahma'] == 'chef') $chef_rahma[] = $row_rahma;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Famiresu Iko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">
</head>

<body>
<div class="flag-stripe-rahma"></div>
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-orange-rahma);">Data User</h4>
            <small class="text-muted">Manajemen hak akses pengguna</small>
        </div>
        <a href="tambah_rahma.php" class="btn-tambah-owner-rahma">
            <i class="bi bi-plus-circle me-1"></i>Tambah User
        </a>
    </div>

    <div class="mb-5">
        <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-briefcase me-2"></i> Karyawan</h6>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-table-rahma h-100 shadow-sm">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white"><i class="bi bi-cash-register me-2"></i>Data Kasir</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nama</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kasir_rahma as $r): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($r['nama_rahma']) ?></div>
                                            <small class="text-muted"><?= $r['username_rahma'] ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                            <a href="hapus_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-table-rahma h-100 shadow-sm">
                    <div class="card-header card-header-rahma py-3" style="background: var(--dark-pink-rahma) !important;">
                        <h6 class="mb-0 fw-semibold text-white"><i class="bi bi-fire me-2"></i>Data Chef</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nama</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chef_rahma as $r): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($r['nama_rahma']) ?></div>
                                            <small class="text-muted"><?= $r['username_rahma'] ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                            <a href="hapus_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header card-header-rahma py-3">
                    <h6 class="mb-0 small fw-bold"><i class="bi bi-person-badge me-2"></i>OWNER / ADMIN</h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($owner_rahma as $r): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold d-block"><?= htmlspecialchars($r['nama_rahma']) ?></span>
                        </div>
                        <a href="edit_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-light border"><i class="bi bi-gear"></i></a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-table-rahma shadow-sm">
                <div class="card-header card-header-rahma py-3" style="background: var(--dark-pink-rahma) !important;">
                    <h6 class="mb-0 fw-semibold text-white"><i class="bi bi-people me-2"></i>Daftar Member Aktif</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-3">MEMBER NAME</th>
                                    <th>USERNAME</th>
                                    <th class="text-center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($member_rahma as $r): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold"><?= htmlspecialchars($r['nama_rahma']) ?></td>
                                    <td class="text-muted"><?= $r['username_rahma'] ?></td>
                                    <td class="text-center">
                                        <a href="edit_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                        <a href="hapus_rahma.php?id_user_rahma=<?= $r['id_user_rahma'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>