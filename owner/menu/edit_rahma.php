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

// Ambil data lama
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

// Proses update
if (isset($_POST['update'])) {

    $nama_menu_rahma = $_POST['nama_menu_rahma'];
    $harga_rahma = $_POST['harga_rahma'];
    $deskripsi_rahma = $_POST['deskripsi_rahma'];
    $kategori_rahma = $_POST['kategori_rahma'];
    $status_menu_rahma = $_POST['status_menu_rahma'];

    // Cek apakah upload foto baru
    if ($_FILES['foto_rahma']['name'] != "") {

        $foto_rahma = $_FILES['foto_rahma']['name'];
        $tmp_rahma = $_FILES['foto_rahma']['tmp_name'];

        // Pindahkan file ke folder upload
        move_uploaded_file($tmp_rahma, "../../upload/" . $foto_rahma);

        $update_rahma = mysqli_query(
            $koneksiRahma,
            "UPDATE tbl_menu_rahma SET
                    nama_menu_rahma='$nama_menu_rahma',
                    harga_rahma='$harga_rahma',
                    status_menu_rahma='$status_menu_rahma',
                    kategori_rahma='$kategori_rahma',
                    foto_rahma='$foto_rahma'
                    WHERE id_menu_rahma='$id_menu_rahma'"
        );
    } else {
        // Update tanpa ganti foto
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

    // Cek hasil update
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
<html>

<head>
    <title>Edit Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow rounded-4">
            <div class="card-header">
                <h5>Edit Menu</h5>
            </div>

            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu_rahma" class="form-control"
                            value="<?= $data_rahma['nama_menu_rahma']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga_rahma" class="form-control"
                            value="<?= $data_rahma['harga_rahma']; ?>" required>
                    </div>

                    <div>
                        <label>Status</label>
                        <select name="status_menu_rahma" required>
                            <option value="tersedia" <?= ($data_rahma['status_menu_rahma'] == 'tersedia') ? 'selected' : ''; ?>>
                                Tersedia
                            </option>
                            <option value="habis" <?= ($data_rahma['status_menu_rahma'] == 'habis') ? 'selected' : ''; ?>>
                                Habis
                            </option>
                        </select>
                    </div>

                    <div>
                        <label>Kategori</label>
                        <select name="kategori_rahma" required>
                            <option value="makanan" <?= ($data_rahma['kategori_rahma'] == 'makanan') ? 'selected' : ''; ?>>
                                Makanan
                            </option>
                            <option value="minuman" <?= ($data_rahma['kategori_rahma'] == 'minuman') ? 'selected' : ''; ?>>
                                Minuman
                            </option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi_rahma" class="form-control"
                            required><?= $data_rahma['deskripsi_rahma']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Foto Sekarang</label><br>
                        <?php if (!empty($data_rahma['foto_rahma'])) { ?>
                            <img src="/tugas_ama/tugas_akhir/upload/<?= $data_rahma['foto_rahma']; ?>" width="100">
                        <?php } ?>
                    </div>

                    <div class="mb-3">
                        <label>Ganti Foto (Opsional)</label>
                        <input type="file" name="foto_rahma" class="form-control">
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">
                        Update
                    </button>

                    <a href="index_rahma.php" class="btn btn-secondary">
                        Kembali
                    </a>

                </form>
            </div>
        </div>
    </div>

</body>

</html>