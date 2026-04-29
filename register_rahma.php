<?php
session_start();

// Kalau sudah login, redirect sesuai role
if (isset($_SESSION['id_role_rahma'])) {
    if ($_SESSION['id_role_rahma'] == 'R001')
        header("Location: owner/dashboard_rahma.php");
    if ($_SESSION['id_role_rahma'] == 'R002')
        header("Location: kasir/dashboard_rahma.php");
    if ($_SESSION['id_role_rahma'] == 'R003')
        header("Location: pelanggan/pilih_meja_rahma.php");
    if ($_SESSION['id_role_rahma'] == 'R004')
        header("Location: chef/dashboard_rahma.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global_rahma.css">
    <title>Register - Famiresu Iko</title>
    <style>
        body {
            background-color: var(--bg-rahma);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-register-rahma {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(212, 44, 0, 0.12);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .card-register-header-rahma {
            background: linear-gradient(135deg, var(--dark-orange-rahma), var(--dark-pink-rahma));
            padding: 28px 32px 20px;
            text-align: center;
        }

        .form-control:focus {
            border-color: var(--orange-rahma);
            box-shadow: 0 0 0 3px rgba(253, 152, 85, 0.2);
        }

        .btn-register-rahma {
            background: linear-gradient(135deg, var(--dark-orange-rahma), var(--dark-pink-rahma));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
            transition: opacity 0.2s ease;
            width: 100%;
        }

        .btn-register-rahma:hover {
            opacity: 0.88;
            color: #fff;
        }
    </style>
</head>

<body>


    <div class="card-register-rahma">

        <!-- Header -->
        <div class="card-register-header-rahma">
            <div class="text-white fw-bold fs-4 mb-1">🍽️ Famiresu Iko</div>
            <div class="text-white opacity-75 small">Daftar sebagai member baru</div>
        </div>
        <div class="flag-stripe-rahma"></div>
        <div class="p-4">

            <!-- Error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger py-2 small rounded-3 mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?php
                    if ($_GET['error'] == 'username_exists')
                        echo 'Username sudah dipakai!';
                    elseif ($_GET['error'] == 'password_mismatch')
                        echo 'Password tidak cocok!';
                    else
                        echo 'Registrasi gagal, coba lagi!';
                    ?>
                </div>
            <?php endif; ?>

            <!-- Sukses -->
            <?php if (isset($_GET['sukses'])): ?>
                <div class="alert alert-success py-2 small rounded-3 mb-3">
                    <i class="bi bi-check-circle me-1"></i>
                    Registrasi berhasil! Silakan login.
                </div>
            <?php endif; ?>

            <form action="proses/proses_user_rahma.php" method="POST">
                <input type="hidden" name="aksi_rahma" value="register">

                <!-- Nama lengkap -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="text" name="nama_rahma" class="form-control" placeholder="Masukkan nama lengkap"
                            required>
                    </div>
                </div>

                <!-- No Telepon -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">No Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-telephone" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="text" name="no_telp_rahma" class="form-control" placeholder="Contoh: 081234567890"
                            required>
                    </div>
                </div>

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-at" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="text" name="username_rahma" class="form-control" placeholder="Buat username"
                            required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="password" name="password_rahma" class="form-control" placeholder="Buat password"
                            required>
                    </div>
                </div>

                <!-- Konfirmasi password -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="password" name="konfirmasi_password_rahma" class="form-control"
                            placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="btn-register-rahma">
                    <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                </button>
            </form>

            <!-- Link ke login -->
            <div class="text-center mt-4">
                <small class="text-muted">Sudah punya akun? </small>
                <a href="login_rahma.php"
                    style="color: var(--dark-pink-rahma); font-weight: 600; text-decoration: none;">
                    Login di sini
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>