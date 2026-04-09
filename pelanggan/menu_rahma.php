<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek apakah sudah login sebagai member atau guest
if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

// Kalau sudah login tapi bukan member, tolak
if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Ambil id_meja dari URL — wajib ada
$id_meja_rahma = $_GET['meja'] ?? '';
if (empty($id_meja_rahma)) {
    header("Location: pilih_meja_rahma.php");
    exit;
}

// Simpan id_meja ke session biar bisa dipakai di keranjang
$_SESSION['id_meja_rahma'] = $id_meja_rahma;

// Ambil filter kategori dari URL kalau ada
$filter_kategori_rahma = $_GET['kategori'] ?? 'semua';

// Query menu berdasarkan filter kategori
if ($filter_kategori_rahma == 'semua') {
    // Tampil semua menu yang tersedia
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        WHERE status_menu_rahma = 'tersedia'
        ORDER BY kategori_rahma ASC, nama_menu_rahma ASC
    ");
} else {
    // Tampil menu sesuai kategori yang dipilih
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        WHERE status_menu_rahma = 'tersedia'
        AND kategori_rahma = '$filter_kategori_rahma'
        ORDER BY nama_menu_rahma ASC
    ");
}

// Hitung total item di keranjang — dari session
$total_keranjang_rahma = isset($_SESSION['keranjang_rahma']) ? count($_SESSION['keranjang_rahma']) : 0;

// Nomor meja — ambil angkanya aja
$nomor_meja_rahma = (int) ltrim($id_meja_rahma, 'M');

include '../templates/navbar_rahma.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../assets/css/pelanggan_rahma.css">
    <title>Menu</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Info meja + tombol keranjang -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-egg-fried me-2"></i>Menu Kami
                </h5>
                <small class="text-muted">
                    <i class="bi bi-table me-1"></i>Meja <?= $nomor_meja_rahma ?>
                </small>
            </div>

            <!-- Tombol ke keranjang — ada badge jumlah item -->
            <a href="keranjang_rahma.php" class="btn-keranjang-rahma position-relative">
                <i class="bi bi-cart3 fs-5"></i>
                <?php if ($total_keranjang_rahma > 0): ?>
                    <span class="badge-keranjang-rahma">
                        <?= $total_keranjang_rahma ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Filter kategori -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="menu_rahma.php?meja=<?= $id_meja_rahma ?>&kategori=semua"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'semua' ? 'active' : '' ?>">
                Semua
            </a>
            <a href="menu_rahma.php?meja=<?= $id_meja_rahma ?>&kategori=makanan"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'makanan' ? 'active' : '' ?>">
                <i class="bi bi-egg-fried me-1"></i>Makanan
            </a>
            <a href="menu_rahma.php?meja=<?= $id_meja_rahma ?>&kategori=minuman"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'minuman' ? 'active' : '' ?>">
                <i class="bi bi-cup-straw me-1"></i>Minuman
            </a>
        </div>

        <!-- Grid menu -->
        <?php if (mysqli_num_rows($query_menu_rahma) > 0): ?>
            <div class="row g-3">
                <?php while ($row_menu_rahma = mysqli_fetch_assoc($query_menu_rahma)): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card card-menu-rahma h-100">

                            <!-- Foto menu — pakai placeholder kalau foto kosong -->
                            <div class="foto-menu-rahma">
                                <?php if ($row_menu_rahma['foto_rahma'] != '-' && !empty($row_menu_rahma['foto_rahma'])): ?>
                                    <img src="../assets/img/<?= htmlspecialchars($row_menu_rahma['foto_rahma']) ?>"
                                        alt="<?= htmlspecialchars($row_menu_rahma['nama_menu_rahma']) ?>">
                                <?php else: ?>
                                    <div class="foto-placeholder-rahma">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>

                                <!-- Badge kategori -->
                                <span class="badge-kategori-rahma">
                                    <?= htmlspecialchars($row_menu_rahma['kategori_rahma']) ?>
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <!-- Nama menu -->
                                <div class="fw-semibold mb-1 nama-menu-rahma">
                                    <?= htmlspecialchars($row_menu_rahma['nama_menu_rahma']) ?>
                                </div>

                                <!-- Harga -->
                                <div class="harga-menu-rahma mb-3">
                                    Rp <?= number_format($row_menu_rahma['harga_rahma'], 0, ',', '.') ?>
                                </div>

                                <!-- Tombol tambah ke keranjang -->
                                <form action="../proses/proses_keranjang_rahma.php" method="POST" class="mt-auto">
                                    <input type="hidden" name="id_menu_rahma" value="<?= $row_menu_rahma['id_menu_rahma'] ?>">
                                    <input type="hidden" name="id_meja_rahma" value="<?= $id_meja_rahma ?>">
                                    <input type="hidden" name="redirect_rahma"
                                        value="menu_rahma.php?meja=<?= $id_meja_rahma ?>&kategori=<?= $filter_kategori_rahma ?>">

                                    <!-- Input qty -->
                                    <div class="input-qty-rahma mb-2">
                                        <button type="button" class="btn-qty-rahma" onclick="kurang_rahma(this)">−</button>
                                        <input type="number" name="qty_rahma" value="1" min="1" max="99"
                                            class="input-angka-rahma" readonly>
                                        <button type="button" class="btn-qty-rahma" onclick="tambah_rahma(this)">+</button>
                                    </div>

                                    <button type="submit" class="btn-tambah-rahma w-100">
                                        <i class="bi bi-cart-plus me-1"></i>Tambah
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3" style="color: var(--orange-rahma);"></i>
                <h6 class="fw-semibold" style="color: var(--dark-orange-rahma);">
                    Tidak ada menu untuk kategori ini
                </h6>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tombol kurang qty
        function kurang_rahma(btn) {
            const input_rahma = btn.nextElementSibling;
            if (parseInt(input_rahma.value) > 1) {
                input_rahma.value = parseInt(input_rahma.value) - 1;
            }
        }

        // Tombol tambah qty
        function tambah_rahma(btn) {
            const input_rahma = btn.previousElementSibling;
            if (parseInt(input_rahma.value) < 99) {
                input_rahma.value = parseInt(input_rahma.value) + 1;
            }
        }
    </script>
</body>

</html>