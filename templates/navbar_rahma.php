<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_role_rahma = $_SESSION['id_role_rahma'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container-fluid">

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">

                <?php if ($id_role_rahma == 'R001') {
                    echo "Hallo owner! " . $_SESSION['username_rahma'];
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/owner/dashboard_rahma.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/owner/menu/index_rahma.php">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/owner/user/data_user_rahma.php">Data User</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/owner/laporan/transaksi_rahma.php">Laporan</a>
                    </li>

                <?php } ?>

                <?php if ($id_role_rahma == 'R002') {
                    echo "Hallo kasir! " . $_SESSION['username_rahma'];
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/kasir/dashboard_rahma.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/kasir/transaksi.php">Transaksi</a>
                    </li>

                <?php } ?>

                <?php if ($id_role_rahma == 'R003') {
                    echo "Hallo pelanggan! " . $_SESSION['username_rahma'];
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/pelanggan/menu_rahma.php">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/pelanggan/riwayat_rahma.php">Riwayat</a>
                    </li>

                <?php } ?>

                <?php if ($id_role_rahma == 'R004') {
                    echo "Hallo chef! " . $_SESSION['username_rahma'];
                    ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/chef/dashboard_rahma.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tugas_ama/tugas_akhir/chef/update_status_rahma.php">Update</a>
                    </li>

                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="../../logout_rahma.php">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>