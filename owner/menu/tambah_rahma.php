<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

if (isset($_POST['simpan'])) {

    // =====================
    // AMBIL DATA FORM
    // =====================
    $kategori_rahma = $_POST['kategori_rahma'];
    $nama_rahma = $_POST['nama_menu_rahma'];
    $harga_rahma = $_POST['harga_rahma'];
    $status_rahma = $_POST['status_rahma'];

    // =====================
    // GENERATE ID OTOMATIS
    // =====================
    $cek_rahma = mysqli_query(
        $koneksiRahma,
        "SELECT MAX(id_menu_rahma) as max_id FROM tbl_menu_rahma"
    );

    $data_rahma = mysqli_fetch_assoc($cek_rahma);
    $last_id_rahma = $data_rahma['max_id'];

    if ($last_id_rahma) {
        $urutan_rahma = (int) substr($last_id_rahma, 1, 3);
        $urutan_rahma++;
        $id_baru_rahma = "M" . str_pad($urutan_rahma, 3, "0", STR_PAD_LEFT);
    } else {
        $id_baru_rahma = "M001";
    }

    // =====================
    // UPLOAD GAMBAR
    // =====================
    $gambar_rahma = '';

    if (isset($_FILES['foto_rahma']) && $_FILES['foto_rahma']['error'] == 0) {

        $gambar_rahma = time() . "_" . $_FILES['foto_rahma']['name'];
        $tmp_rahma = $_FILES['foto_rahma']['tmp_name'];

        $path_upload = __DIR__ . "/../../../uploads/" . $gambar_rahma;

        if (!move_uploaded_file($tmp_rahma, $path_upload)) {
            echo "Upload gagal!";
            exit;
        }
    }

    // =====================
    // INSERT DATA
    // =====================
    mysqli_query(
        $koneksiRahma,
        "INSERT INTO tbl_menu_rahma
    (id_menu_rahma, kategori_rahma, nama_menu_rahma, foto_rahma, harga_rahma, status_menu_rahma)
    VALUES
    ('$id_baru_rahma','$kategori_rahma','$nama_rahma','$gambar_rahma','$harga_rahma','$status_rahma')"
    );

    header("Location: index_rahma.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow rounded-4">
            <div class="card-header">
                <h5>Tambah Menu</h5>
            </div>
            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label>Kategori</label>
                        <input type="text" name="kategori_rahma" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu_rahma" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga_rahma" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_rahma" class="form-control">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Habis">Habis</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Foto</label>
                        <input type="file" name="foto_rahma" class="form-control">
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        Simpan
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