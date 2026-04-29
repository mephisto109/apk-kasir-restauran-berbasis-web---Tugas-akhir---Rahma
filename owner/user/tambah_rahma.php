<?php
session_start();
include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

// Proses simpan data
if (isset($_POST['simpan_rahma'])) {
    $id_user_rahma = $_POST['id_user_rahma'];
    $username_rahma = $_POST['username_rahma'];
    $password_rahma = password_hash($_POST['password_rahma'], PASSWORD_DEFAULT);
    $nama_rahma = $_POST['nama_rahma'];
    $id_role_rahma = $_POST['id_role_rahma'];
    $no_telp_rahma = $_POST['no_telp_rahma'];

    $query_rahma = mysqli_query($koneksiRahma, "INSERT INTO tbl_user_rahma 
    VALUES ('$id_user_rahma','$username_rahma','$password_rahma','$nama_rahma','$no_telp_rahma','$id_role_rahma')");

    if ($query_rahma) {
        header("Location: data_user_rahma.php");
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
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
            <i class="bi bi-plus-circle me-2"></i>Tambah User
            <a href="data_user_rahma.php" class="btn btn-kembali-rahma px-4 ">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
        </h5>

        <!-- Card form tambah -->
        <div class="card card-form-rahma">

            <!-- Header card -->
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-person-plus me-2"></i>Form Tambah User
                </h6>
            </div>

            <div class="card-body p-4">
                <form method="POST">

                    <!-- ID User (Readonly) -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-hash me-1"></i>ID User
                        </label>
                        <?php
                        // Generate ID User Otomatis
                        $result_rahma = mysqli_query(
                            $koneksiRahma,
                            "SELECT id_user_rahma 
                            FROM tbl_user_rahma 
                            ORDER BY id_user_rahma DESC 
                            LIMIT 1"
                        );

                        $row_rahma = mysqli_fetch_assoc($result_rahma);

                        if ($row_rahma) {
                            $last_id_rahma = $row_rahma['id_user_rahma'];
                            $number_rahma = (int) substr($last_id_rahma, 3);
                            $number_rahma++;
                            $id_baru_rahma = "USN" . str_pad($number_rahma, 3, "0", STR_PAD_LEFT);
                        } else {
                            $id_baru_rahma = "USN001";
                        }
                        ?>
                        <input type="text" name="id_user_rahma" 
                            class="form-control input-rahma"
                            value="<?= $id_baru_rahma ?>" 
                            readonly>
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-at me-1"></i>Username
                        </label>
                        <input type="text" name="username_rahma" 
                            class="form-control input-rahma"
                            placeholder="Contoh: sadewa_sgr"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-lock me-1"></i>Password
                        </label>
                        <input type="password" name="password_rahma" 
                            class="form-control input-rahma"
                            placeholder="Masukkan password"
                            required>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-person me-1"></i>Nama Lengkap
                        </label>
                        <input type="text" name="nama_rahma" 
                            class="form-control input-rahma"
                            placeholder="Contoh: Sadewa Sagara"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-telephone me-1"></i>No Telepon
                        </label>
                        <input type="text" name="no_telp_rahma" 
                            class="form-control input-rahma"
                            placeholder="Contoh: 081234567890"
                            required>
                    </div>

                    <!-- Role/Jabatan -->
                    <div class="mb-4">
                        <label class="form-label label-rahma">
                            <i class="bi bi-shield-check me-1"></i>Role/Jabatan
                        </label>
                        <select name="id_role_rahma" class="form-select input-rahma" required>
                            <option value="">-- Pilih Role --</option>
                            <?php
                            $data_role_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_role_rahma");
                            while ($role_rahma = mysqli_fetch_assoc($data_role_rahma)) { 
                            ?>
                                <option value="<?= $role_rahma['id_role_rahma']; ?>">
                                    <?= htmlspecialchars($role_rahma['role_rahma']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="simpan_rahma" class="btn btn-simpan-rahma px-4">
                            <i class="bi bi-check-circle me-1"></i>Simpan
                        </button>
                        
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>