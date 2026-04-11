<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah cache supaya ga bisa back setelah logout
$id_role_rahma = $_SESSION['id_role_rahma'] ?? '';
$username_rahma = htmlspecialchars($_SESSION['username_rahma'] ?? '');

// bagian untuk menghitung path ke root (misal: ../../) berdasarkan lokasi file yang memanggil navbar ini
$current_script = $_SERVER['SCRIPT_NAME'];
$depth_rahma = substr_count(str_replace('/tugas_ama/tugas_akhir/', '', $current_script), '/');
$base_rahma = str_repeat('../', $depth_rahma);

// Tentukan sapaan berdasarkan role
$sapaan_rahma = '';
if ($id_role_rahma == 'R001')
    $sapaan_rahma = "Selamat datang, Owner <strong>$username_rahma</strong>!";
if ($id_role_rahma == 'R002')
    $sapaan_rahma = "Selamat datang, Kasir <strong>$username_rahma</strong>!";
if ($id_role_rahma == 'R003')
    $sapaan_rahma = "Selamat datang, <strong>$username_rahma</strong>!";
if ($id_role_rahma == 'R004')
    $sapaan_rahma = "Selamat datang, Chef <strong>$username_rahma</strong>!";
if (isset($_SESSION['guest_rahma']))
    $sapaan_rahma = "Selamat datang, <strong>Tamu</strong>!";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap dulu -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Baru CSS kita — path tanpa ../ dobel -->
    <link rel="stylesheet" href="<?= $base_rahma ?>assets/css/global_rahma.css">
    <title>Navbar</title>
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container-fluid">

            <!-- Sapaan di kiri -->
            <span class="navbar-brand mb-0 h6 text-white">
                <?= $sapaan_rahma ?>
            </span>

            <!-- Nav items di kanan -->
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav align-items-center">

                    <?php if ($id_role_rahma == 'R001') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>owner/dashboard_rahma.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>owner/menu/index_rahma.php">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>owner/user/data_user_rahma.php">Data User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>owner/laporan/transaksi_rahma.php">Laporan</a>
                        </li>
                    <?php } ?>

                    <?php if ($id_role_rahma == 'R002') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>kasir/dashboard_rahma.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>kasir/transaksi_rahma.php">Transaksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>kasir/lihat_user_rahma.php">Data User</a>
                        </li>
                    <?php } ?>

                    <?php if ($id_role_rahma == 'R003' || isset($_SESSION['guest_rahma'])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>pelanggan/menu_rahma.php">Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="<?= $base_rahma ?>pelanggan/riwayat_rahma.php?order=<?= $_SESSION['id_order_terakhir_rahma'] ?? '' ?>">Riwayat</a>
                        </li>
                    <?php } ?>

                    <?php if ($id_role_rahma == 'R004') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>chef/dashboard_rahma.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_rahma ?>chef/update_status_rahma.php">Update</a>
                        </li>
                    <?php } ?>

                    <li class="nav-item ms-2">
                        <a href="<?= $base_rahma ?>logout_rahma.php" class="btn-logout-rahma">
                            Logout
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

</body>

</html>