<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

if (isset($_POST['simpan'])) {

    // Ambil semua data dari form
    $kategori_rahma  = $_POST['kategori_rahma'];
    $nama_rahma      = $_POST['nama_menu_rahma'];
    $harga_rahma     = $_POST['harga_rahma'];
    $status_rahma    = $_POST['status_rahma'];
    $deskripsi_rahma = $_POST['deskripsi_rahma'];

    // Generate ID menu otomatis (MN001, MN002, dst)
    $result_rahma = mysqli_query($koneksiRahma,
        "SELECT id_menu_rahma 
        FROM tbl_menu_rahma 
        ORDER BY id_menu_rahma DESC 
        LIMIT 1");

    $row_rahma = mysqli_fetch_assoc($result_rahma);

    if ($row_rahma) {
        $last_id_rahma  = $row_rahma['id_menu_rahma'];
        $number_rahma   = (int) substr($last_id_rahma, 2);
        $number_rahma++;
        $id_baru_rahma  = "MN" . str_pad($number_rahma, 3, "0", STR_PAD_LEFT);
    } else {
        $id_baru_rahma = "MN001";
    }

    // Proses upload foto kalau ada file yang dikirim
    $gambar_rahma = '';

    if (isset($_FILES['foto_rahma']) && $_FILES['foto_rahma']['error'] == 0) {
        $gambar_rahma    = time() . "_" . $_FILES['foto_rahma']['name'];
        $tmp_rahma       = $_FILES['foto_rahma']['tmp_name'];
        $folder_upload   = __DIR__ . "/../../upload/";

        // Buat folder upload kalau belum ada
        if (!is_dir($folder_upload)) {
            mkdir($folder_upload, 0777, true);
        }

        $path_upload_rahma = $folder_upload . $gambar_rahma;

        if (!move_uploaded_file($tmp_rahma, $path_upload_rahma)) {
            echo "Upload gagal!";
            exit;
        }
    }

    // Simpan data menu baru ke database
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
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
            <i class="bi bi-plus-circle me-2"></i>Tambah Menu
        </h5>

        <!-- Card form tambah -->
        <div class="card card-form-rahma">

            <!-- Header card -->
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-pencil-square me-2"></i>Form Tambah Menu
                </h6>
            </div>

            <div class="card-body p-4">
                <form method="POST" enctype="multipart/form-data">

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-tag me-1"></i>Kategori
                        </label>
                        <select name="kategori_rahma" class="form-select input-rahma" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                    </div>

                    <!-- Nama Menu -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-card-text me-1"></i>Nama Menu
                        </label>
                        <input type="text" name="nama_menu_rahma"
                            class="form-control input-rahma"
                            placeholder="Contoh: Nasi Goreng Spesial"
                            required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-chat-left-text me-1"></i>Deskripsi
                        </label>
                        <textarea name="deskripsi_rahma"
                            class="form-control input-rahma"
                            rows="3"
                            placeholder="Tuliskan deskripsi singkat menu..."
                            required></textarea>
                    </div>

                    <!-- Harga -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-cash me-1"></i>Harga
                        </label>
                        <div class="input-group">
                            <span class="input-group-text input-prefix-rahma">Rp</span>
                            <input type="number" name="harga_rahma"
                                class="form-control input-rahma"
                                placeholder="Contoh: 25000"
                                min="1" required>
                        </div>
                    </div>

                    <!-- Status Stok -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-toggle-on me-1"></i>Status Stok
                        </label>
                        <select name="status_rahma" class="form-select input-rahma" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>

                    <!-- Upload Foto -->
                    <div class="mb-4">
                        <label class="form-label label-rahma">
                            <i class="bi bi-image me-1"></i>Foto Menu
                        </label>
                        <input type="file" name="foto_rahma"
                            class="form-control input-rahma"
                            accept="image/*" required>
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Format yang didukung: JPG, PNG, JPEG
                        </div>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="simpan" class="btn btn-simpan-rahma px-4">
                            <i class="bi bi-check-circle me-1"></i>Simpan
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