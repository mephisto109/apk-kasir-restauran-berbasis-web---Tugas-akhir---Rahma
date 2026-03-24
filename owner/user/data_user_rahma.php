<?php
session_start();
// Cegah cache supaya ga bisa back setelah logout
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Data User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow rounded-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Data User</h5>
                <a href="tambah_rahma.php" class="btn btn-primary btn-sm">+ Tambah User</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no_rahma = 1;
                        while ($row_rahma = mysqli_fetch_assoc($data_rahma)) {

                            // warna badge role
                            $badge_rahma = "";

                            if ($row_rahma['role_rahma'] == 'owner') {
                                $badge_rahma = "<span class='badge bg-dark'>Owner</span>";
                            } elseif ($row_rahma['role_rahma'] == 'kasir') {
                                $badge_rahma = "<span class='badge bg-primary'>Kasir</span>";
                            } elseif ($row_rahma['role_rahma'] == 'member') {
                                $badge_rahma = "<span class='badge bg-success'>Member</span>";
                            } else {
                                $badge_rahma = "<span class='badge bg-warning text-dark'>Chef</span>";
                            }
                            ?>

                            <tr>
                                <td><?= $no_rahma++; ?></td>
                                <td><?= $row_rahma['username_rahma']; ?></td>
                                <td><?= $row_rahma['nama_rahma']; ?></td>
                                <td><?= $badge_rahma; ?></td>
                                <td>
                                    <a href="edit_rahma.php?id_user_rahma=<?= $row_rahma['id_user_rahma']; ?>"
                                        class="btn btn-warning btn-sm">Edit</a>

                                    <a href="hapus_rahma.php?id_user_rahma=<?= $row_rahma['id_user_rahma']; ?>"
                                        class="btn btn-danger btn-sm" onclick="return confirm('Yakin mau hapus user ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>