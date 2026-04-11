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

// Kalau sudah guest, redirect ke pilih meja
if (isset($_SESSION['guest_rahma'])) {
    header("Location: pelanggan/pilih_meja_rahma.php");
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
    <title>Login - Famiresu Iko</title>
    <style>
        body {
            background-color: var(--bg-rahma);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card login utama */
        .card-login-rahma {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(212, 44, 0, 0.12);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        /* Header card — gradient orange ke pink */
        .card-login-header-rahma {
            background: linear-gradient(135deg,
                    var(--dark-orange-rahma),
                    var(--dark-pink-rahma));
            padding: 28px 32px 20px;
            text-align: center;
        }

        /* Input field */
        .form-control:focus {
            border-color: var(--orange-rahma);
            box-shadow: 0 0 0 3px rgba(253, 152, 85, 0.2);
        }

        /* Tombol login member */
        .btn-login-rahma {
            background: linear-gradient(135deg, var(--dark-orange-rahma), var(--dark-pink-rahma));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
            transition: opacity 0.2s ease;
            width: 100%;
        }

        .btn-login-rahma:hover {
            opacity: 0.88;
            color: #fff;
        }

        /* Tombol guest */
        .btn-guest-rahma {
            border: 2px solid var(--dark-orange-rahma);
            color: var(--dark-orange-rahma);
            background: transparent;
            border-radius: 10px;
            padding: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-guest-rahma:hover {
            background-color: var(--dark-orange-rahma);
            color: #fff;
        }

        /* Divider "atau" */
        .divider-rahma {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #aaa;
            font-size: 0.85rem;
            margin: 16px 0;
        }

        .divider-rahma::before,
        .divider-rahma::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="card-login-rahma">

        <!-- Header -->
        <div class="card-login-header-rahma">
            <div class="text-white fw-bold fs-4 mb-1">🍽️ Famiresu Iko</div>
            <div class="text-white opacity-75 small">Selamat datang! Silakan masuk dulu ya</div>
        </div>
        <div class="flag-stripe-rahma "></div>

        <div class="p-4">

            <!-- Pertanyaan member -->
            <div class="text-center mb-4">
                <div class="fw-semibold mb-1" style="color: var(--dark-orange-rahma);">
                    Apakah kamu sudah member?
                </div>
                <small class="text-muted">
                    Member mendapatkan <strong>diskon spesial</strong> setiap transaksi! 🎉
                </small>
            </div>

            <!-- Error login -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger py-2 small rounded-3 mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Username atau password salah!
                </div>
            <?php endif; ?>

            <!-- Sukses register -->
            <?php if (isset($_GET['sukses'])): ?>
                <div class="alert alert-success py-2 small rounded-3 mb-3">
                    <i class="bi bi-check-circle me-1"></i>
                    Registrasi berhasil! Silakan login sekarang 🎉
                </div>
            <?php endif; ?>

            <!-- Label masuk sebagai member — di atas form -->
            <div class="mb-3 fw-semibold" style="color: var(--dark-pink-rahma);">
                <i class="bi bi-person-check me-1"></i>Masuk sebagai Member
            </div>

            <!-- Form login member -->
            <form action="proses/proses_login_rahma.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock" style="color: var(--dark-orange-rahma);"></i>
                        </span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                            required>
                    </div>
                </div>

                <!-- Tombol login — simpel -->
                <button type="submit" name="login" class="btn-login-rahma">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>

            <!-- Divider -->
            <div class="divider-rahma">atau</div>

            <!-- Tombol guest -->
            <a href="guest_rahma.php" class="btn-guest-rahma">
                <i class="bi bi-person-dash me-2"></i>Masuk Tanpa Member
            </a>

            <!-- Link register -->
            <div class="text-center mt-4">
                <small class="text-muted">Belum punya akun member? </small>
                <a href="register_rahma.php"
                    style="color: var(--dark-pink-rahma); font-weight: 600; text-decoration: none;">
                    Daftar di sini
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>