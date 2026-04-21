<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

if (!isset($_GET['id_menu_rahma'])) {
    echo "<script>
            alert('ID tidak ditemukan!');
            window.location='index_rahma.php';
          </script>";
    exit;
}

$id_menu_rahma = $_GET['id_menu_rahma'];

// Ambil data menu lama berdasarkan ID
$query_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma WHERE id_menu_rahma='$id_menu_rahma'"
);
$data_rahma = mysqli_fetch_assoc($query_rahma);

if (!$data_rahma) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location='index_rahma.php';
          </script>";
    exit;
}

// Proses update kalau form disubmit
if (isset($_POST['update'])) {

    $nama_menu_rahma = $_POST['nama_menu_rahma'];
    $harga_rahma = $_POST['harga_rahma'];
    $deskripsi_rahma = $_POST['deskripsi_rahma'];
    $kategori_rahma = $_POST['kategori_rahma'];
    $status_menu_rahma = $_POST['status_menu_rahma'];

    // Kalau ada foto baru yang diupload, proses fotonya dulu
    if ($_FILES['foto_rahma']['name'] != "") {

        $foto_rahma = $_FILES['foto_rahma']['name'];
        $tmp_rahma = $_FILES['foto_rahma']['tmp_name'];

        // Pindahkan foto baru ke folder upload
        move_uploaded_file($tmp_rahma, "../../upload/" . $foto_rahma);

        // Update data termasuk foto baru
        $update_rahma = mysqli_query(
            $koneksiRahma,
            "UPDATE tbl_menu_rahma SET
                nama_menu_rahma='$nama_menu_rahma',
                harga_rahma='$harga_rahma',
                deskripsi_rahma='$deskripsi_rahma',
                status_menu_rahma='$status_menu_rahma',
                kategori_rahma='$kategori_rahma',
                foto_rahma='$foto_rahma'
            WHERE id_menu_rahma='$id_menu_rahma'"
        );
    } else {
        // Update data tanpa ganti foto
        $update_rahma = mysqli_query(
            $koneksiRahma,
            "UPDATE tbl_menu_rahma SET
                nama_menu_rahma='$nama_menu_rahma',
                harga_rahma='$harga_rahma',
                deskripsi_rahma='$deskripsi_rahma',
                status_menu_rahma='$status_menu_rahma',
                kategori_rahma='$kategori_rahma'
            WHERE id_menu_rahma='$id_menu_rahma'"
        );
    }

    // Redirect balik ke index setelah update
    if ($update_rahma) {
        echo "<script>
                alert('Data berhasil diupdate!');
                window.location='index_rahma.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal update data!');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">
</head>

<body>
    <!-- Stripe dekoratif atas halaman -->
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4" style="max-width: 680px;">

        <!-- Judul halaman -->
        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-pencil-square me-2"></i>Edit Menu
        </h5>

        <!-- Card form edit -->
        <div class="card card-form-rahma">

            <!-- Header card -->
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-pencil me-2"></i>Form Edit Menu
                </h6>
            </div>

            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">

                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-card-text me-1"></i>Nama Menu
                        </label>
                        <input type="text" name="nama_menu_rahma" class="form-control input-rahma"
                            value="<?= htmlspecialchars($data_rahma['nama_menu_rahma']) ?>" required>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-tag me-1"></i>Kategori
                        </label>
                        <select name="kategori_rahma" class="form-select input-rahma" required>
                            <option value="makanan" <?= ($data_rahma['kategori_rahma'] == 'makanan') ? 'selected' : '' ?>>
                                Makanan
                            </option>
                            <option value="minuman" <?= ($data_rahma['kategori_rahma'] == 'minuman') ? 'selected' : '' ?>>
                                Minuman
                            </option>
                        </select>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-chat-left-text me-1"></i>Deskripsi
                        </label>
                        <textarea name="deskripsi_rahma" class="form-control input-rahma" rows="3"
                            required><?= htmlspecialchars($data_rahma['deskripsi_rahma']) ?></textarea>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-cash me-1"></i>Harga
                        </label>
                        <div class="input-group">
                            <span class="input-group-text input-prefix-rahma">Rp</span>
                            <input type="number" name="harga_rahma" class="form-control input-rahma"
                                value="<?= $data_rahma['harga_rahma'] ?>" min="1" required>
                        </div>
                    </div>

                    <!-- Status Stok -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-toggle-on me-1"></i>Status Stok
                        </label>
                        <select name="status_menu_rahma" class="form-select input-rahma" required>
                            <option value="tersedia" <?= ($data_rahma['status_menu_rahma'] == 'tersedia') ? 'selected' : '' ?>>
                                Tersedia
                            </option>
                            <option value="habis" <?= ($data_rahma['status_menu_rahma'] == 'habis') ? 'selected' : '' ?>>
                                Habis
                            </option>
                        </select>
                    </div>

                    <!-- Preview foto yang sekarang -->
                    <?php if (!empty($data_rahma['foto_rahma'])): ?>
                        <div class="mb-3">
                            <label class="form-label label-rahma">
                                <i class="bi bi-image me-1"></i>Foto Sekarang
                            </label>
                            <div>
                                <img src="/tugas_ama/tugas_akhir/upload/<?= htmlspecialchars($data_rahma['foto_rahma']) ?>"
                                    class="img-preview-rahma">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Ganti Foto -->
                    <div class="mb-4">
                        <label class="form-label label-rahma">
                            <i class="bi bi-upload me-1"></i>Ganti Foto
                            <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="file" name="foto_rahma" class="form-control input-rahma" accept="image/*">
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Kosongkan kalau tidak ingin mengganti foto
                        </div>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="update" class="btn btn-simpan-rahma px-4">
                            <i class="bi bi-check-circle me-1"></i>Update
                        </button>
                        <a href="index_rahma.php" class="btn btn-kembali-rahma px-4">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>