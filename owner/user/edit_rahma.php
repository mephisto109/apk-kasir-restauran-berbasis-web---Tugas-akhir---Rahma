<?php
session_start();
include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

$id_user_rahma = $_GET['id_user_rahma'];

$data_rahma = mysqli_query($koneksiRahma, 
    "SELECT * FROM tbl_user_rahma WHERE id_user_rahma='$id_user_rahma'");
$row_rahma = mysqli_fetch_assoc($data_rahma);

// ambil data role
$data_role_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_role_rahma");


// Proses update data
if(isset($_POST['update_rahma'])){
    $username_rahma = $_POST['username_rahma'];
    $nama_rahma = $_POST['nama_rahma'];
    $id_role_rahma = $_POST['id_role_rahma'];
    $no_telp_rahma = $_POST['no_telp_rahma'];

    // Jika password diisi, update password juga
    if(!empty($_POST['password_rahma'])){
        $password_rahma = password_hash($_POST['password_rahma'], PASSWORD_DEFAULT);

        $query_rahma = mysqli_query($koneksiRahma, "UPDATE tbl_user_rahma SET
            username_rahma='$username_rahma',
            password_rahma='$password_rahma',
            nama_rahma='$nama_rahma',
            no_telp_rahma='$no_telp_rahma',
            id_role_rahma='$id_role_rahma'
            WHERE id_user_rahma='$id_user_rahma'");
    } else {
        $query_rahma = mysqli_query($koneksiRahma, "UPDATE tbl_user_rahma SET
            username_rahma='$username_rahma',
            nama_rahma='$nama_rahma',
            no_telp_rahma='$no_telp_rahma',
            id_role_rahma='$id_role_rahma'
            WHERE id_user_rahma='$id_user_rahma'");
    }
    // Redirect ke halaman data user setelah update
    if($query_rahma){
        header("Location: data_user_rahma.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            <i class="bi bi-pencil-square me-2"></i>Edit User
        </h5>

        <!-- Card form edit -->
        <div class="card card-form-rahma">

            <!-- Header card -->
            <div class="card-header card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-person-check me-2"></i>Form Edit User
                </h6>
            </div>

            <div class="card-body p-4">
                <form method="POST">

                    <!-- ID User (Readonly) -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-hash me-1"></i>ID User
                        </label>
                        <input type="text" name="id_user_rahma"
                            class="form-control input-rahma"
                            value="<?= htmlspecialchars($row_rahma['id_user_rahma']); ?>" 
                            readonly>
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-at me-1"></i>Username
                        </label>
                        <input type="text" name="username_rahma" 
                            class="form-control input-rahma"
                            value="<?= htmlspecialchars($row_rahma['username_rahma']); ?>"
                            required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-lock me-1"></i>Password
                        </label>
                        <input type="password" name="password_rahma" 
                            class="form-control input-rahma"
                            placeholder="Kosongkan jika tidak diubah">
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Biarkan kosong jika tidak ingin mengubah password
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-person me-1"></i>Nama Lengkap
                        </label>
                        <input type="text" name="nama_rahma" 
                            class="form-control input-rahma"
                            value="<?= htmlspecialchars($row_rahma['nama_rahma']); ?>"
                            required>
                    </div>

                    <!-- No Telepon -->
                    <div class="mb-3">
                        <label class="form-label label-rahma">
                            <i class="bi bi-telephone me-1"></i>No Telepon
                        </label>
                        <input type="text" name="no_telp_rahma" 
                            class="form-control input-rahma"
                            value="<?= htmlspecialchars($row_rahma['no_telp_rahma']); ?>"
                            required>
                    </div>

                    <!-- Role/Jabatan -->
                    <div class="mb-4">
                        <label class="form-label label-rahma">
                            <i class="bi bi-shield-check me-1"></i>Role/Jabatan
                        </label>
                        <select name="id_role_rahma" class="form-select input-rahma" required>
                            <?php while($role_rahma = mysqli_fetch_assoc($data_role_rahma)){ ?>
                                <option value="<?= $role_rahma['id_role_rahma']; ?>"
                                    <?= ($role_rahma['id_role_rahma'] == $row_rahma['id_role_rahma']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($role_rahma['role_rahma']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="update_rahma" class="btn btn-simpan-rahma px-4">
                            <i class="bi bi-arrow-repeat me-1"></i>Update
                        </button>
                        <a href="data_user_rahma.php" class="btn btn-kembali-rahma px-4">
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