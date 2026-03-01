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
    $deskripsi_rahma = $_POST['deskripsi_rahma'];

    // =====================
// GENERATE ID OTOMATIS
// =====================
$result_rahma = mysqli_query($koneksiRahma, 
    "SELECT id_menu_rahma 
     FROM tbl_menu_rahma 
     ORDER BY id_menu_rahma DESC 
     LIMIT 1");

$row_rahma = mysqli_fetch_assoc($result_rahma);

if ($row_rahma) {
    $last_id_rahma = $row_rahma['id_menu_rahma'];
    
    // Ambil angka setelah MN
    $number_rahma = (int) substr($last_id_rahma, 2);
    $number_rahma++;
    
    $id_baru_rahma = "MN" . str_pad($number_rahma, 3, "0", STR_PAD_LEFT);
} else {
    $id_baru_rahma = "MN001";
}

    // =====================
    // UPLOAD GAMBAR
    // =====================
    $gambar_rahma = '';

    // Cek apakah ada file yang diupload
    if (isset($_FILES['foto_rahma']) && $_FILES['foto_rahma']['error'] == 0) {

        //bikin nama file baru dengan format: timestamp_namafile
        $gambar_rahma = time() . "_" . $_FILES['foto_rahma']['name'];
        $tmp_rahma = $_FILES['foto_rahma']['tmp_name'];

        // Pindahkan file ke folder upload
        $folder_upload = __DIR__ . "/../../upload/";

        // Buat folder upload jika belum ada
        if (!is_dir($folder_upload)) {
            mkdir($folder_upload, 0777, true);
        }

        // Pindahkan file ke folder upload
        $path_upload_rahma = $folder_upload . $gambar_rahma;

        if (!move_uploaded_file($tmp_rahma, $path_upload_rahma)) {
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
    (id_menu_rahma, kategori_rahma, nama_menu_rahma, deskripsi_rahma, foto_rahma, harga_rahma, status_menu_rahma)
    VALUES
    ('$id_baru_rahma','$kategori_rahma','$nama_rahma','$deskripsi_rahma','$gambar_rahma','$harga_rahma','$status_rahma')"
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
                        <select name="kategori_rahma" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_menu_rahma" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi_rahma" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number" name="harga_rahma" class="form-control" required min="1">
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status_rahma" class="form-control" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Foto</label>
                        <input type="file" name="foto_rahma" class="form-control" required>
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