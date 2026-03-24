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

$queryRahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma 
     ORDER BY kategori_rahma ASC, id_menu_rahma DESC"
);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow rounded-4">
            <div class="card-header d-flex justify-content-between">
                <h5>Data Menu</h5>
                <a href="tambah_rahma.php" class="btn btn-primary btn-sm">+ Tambah Menu</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th>Status</th> <!-- Tambahan -->
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $kategori_sekarang_rahma = "";
                        $no_rahma = 1;

                        while ($row_rahma = mysqli_fetch_assoc($queryRahma)) {
                            // Tampilkan header kategori jika berubah
                            if ($kategori_sekarang_rahma != $row_rahma['kategori_rahma']) {
                                $kategori_sekarang_rahma = $row_rahma['kategori_rahma'];
                                $no_rahma = 1;
                                echo "
            <tr class='table-secondary'>
                <td colspan='7'><strong>🍽️ $kategori_sekarang_rahma</strong></td>
            </tr>
        ";
                            }

                            $warna_rahma = "";
                            $label_rahma = "";

                            if ($row_rahma['status_rahma'] == 'nonaktif') {
                                $warna_rahma = "table-danger";
                                $label_rahma = "<span class='badge bg-danger'>NONAKTIF</span>";
                            }

                            // STATUS TERSEDIA / HABIS
                            if ($row_rahma['status_menu_rahma'] == 'habis') {
                                $status_badge_rahma = "<span class='badge bg-secondary'>Habis</span>";
                            } else {
                                $status_badge_rahma = "<span class='badge bg-success'>Tersedia</span>";
                            }
                            ?>
                            <tr class="<?= $warna_rahma; ?>">
                                <td><?= $no_rahma++; ?></td>
                                <td>
                                    <?= $row_rahma['nama_menu_rahma']; ?>
                                    <?= $label_rahma; ?>
                                </td>
                                <td>Rp <?= number_format($row_rahma['harga_rahma']); ?></td>
                                <td><?= $row_rahma['deskripsi_rahma']; ?></td>
                                <td><?= $status_badge_rahma; ?></td> <!-- Tambahan -->
                                <td>
                                    <?php if (!empty($row_rahma['foto_rahma'])) { ?>
                                        <img src="/tugas_ama/tugas_akhir/upload/<?= $row_rahma['foto_rahma']; ?>" width="70">
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="edit_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma']; ?>"
                                        class="btn btn-warning btn-sm">Edit</a>

                                    <?php if ($row_rahma['status_rahma'] == 'aktif') { ?>

                                        <a href="nonaktif_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menonaktifkan menu ini?')">
                                            Non-aktif
                                        </a>

                                    <?php } else { ?>

                                        <a href="aktifkan_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma']; ?>"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Aktifkan kembali menu ini?')">
                                            Aktifkan
                                        </a>

                                    <?php } ?>
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