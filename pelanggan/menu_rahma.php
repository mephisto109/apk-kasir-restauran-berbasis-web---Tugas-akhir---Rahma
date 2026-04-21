<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — tolak kalau belum login atau bukan member (R003) atau guest
if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Cek jenis pesanan
$jenis_pesanan_rahma = $_GET['jenis'] ?? 'dinein';

if ($jenis_pesanan_rahma == 'takeaway') {
    // Take away — set id_meja jadi "TAKEAWAY"
    $id_meja_rahma = 'TAKEAWAY';
    $_SESSION['id_meja_rahma'] = 'TAKEAWAY';
    $_SESSION['jenis_pesanan_rahma'] = 'take away';
} else {
    // Dine in — pastikan id_meja ada di URL, kalau nggak ada balik ke pilih meja
    $id_meja_rahma = $_GET['meja'] ?? '';
    if (empty($id_meja_rahma)) {
        header("Location: pilih_meja_rahma.php");
        exit;
    }
    $_SESSION['id_meja_rahma'] = $id_meja_rahma;
    $_SESSION['jenis_pesanan_rahma'] = 'dine in';
}

// Nomor meja — kalau take away tampil "Take Away"
$nomor_meja_rahma = $id_meja_rahma == 'TAKEAWAY'
    ? 'Take Away'
    : (int) ltrim($id_meja_rahma, 'M');

$_SESSION['id_meja_rahma'] = $id_meja_rahma;
$filter_kategori_rahma = $_GET['kategori'] ?? 'semua';

// Ambil data menu sesuai filter kategori
if ($filter_kategori_rahma == 'semua') {
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        WHERE status_menu_rahma = 'tersedia'
        ORDER BY kategori_rahma ASC, nama_menu_rahma ASC
    ");
} else {
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        WHERE status_menu_rahma = 'tersedia'
        AND kategori_rahma = '$filter_kategori_rahma'
        ORDER BY nama_menu_rahma ASC
    ");
}

$total_keranjang_rahma = isset($_SESSION['keranjang_rahma']) ? count($_SESSION['keranjang_rahma']) : 0;

// Simpan semua menu ke array buat modal
$menus_rahma = [];
while ($row_rahma = mysqli_fetch_assoc($query_menu_rahma)) {
    $menus_rahma[] = $row_rahma;
}

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

        <!-- Header + tombol keranjang -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-egg-fried me-2"></i>Menu Kami
                </h5>

                <!-- Tampilkan nomor meja atau "Take Away" di bawah judul -->
                <small class="text-muted">
                    <?php if ($id_meja_rahma == 'TAKEAWAY'): ?>
                        <i class="bi bi-bag me-1"></i>Take Away
                    <?php else: ?>
                        <i class="bi bi-table me-1"></i>Meja <?= $nomor_meja_rahma ?>
                    <?php endif; ?>
                </small>
            </div>
            <a href="keranjang_rahma.php" class="btn-keranjang-rahma position-relative">
                <i class="bi bi-cart3 fs-5"></i>
                <?php if ($total_keranjang_rahma > 0): ?>
                    <span class="badge-keranjang-rahma"><?= $total_keranjang_rahma ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Filter kategori -->
        <?php
        // Tentukan base URL filter sesuai jenis pesanan
        $base_url_filter_rahma = $id_meja_rahma == 'TAKEAWAY'
            ? "menu_rahma.php?jenis=takeaway"
            : "menu_rahma.php?meja=$id_meja_rahma";
        ?>

        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="<?= $base_url_filter_rahma ?>&kategori=semua"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'semua' ? 'active' : '' ?>">
                Semua
            </a>
            <a href="<?= $base_url_filter_rahma ?>&kategori=makanan"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'makanan' ? 'active' : '' ?>">
                <i class="bi bi-egg-fried me-1"></i>Makanan
            </a>
            <a href="<?= $base_url_filter_rahma ?>&kategori=minuman"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'minuman' ? 'active' : '' ?>">
                <i class="bi bi-cup-straw me-1"></i>Minuman
            </a>
        </div>

        <!-- Grid menu foto + nama + harga -->
        <?php if (count($menus_rahma) > 0): ?>
            <div class="row g-3">
                <?php foreach ($menus_rahma as $menu_rahma): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <!-- Klik card → buka modal detail -->
                        <div class="card card-menu-rahma h-100 cursor-pointer"
                            onclick="bukaModal_rahma(<?= htmlspecialchars(json_encode($menu_rahma)) ?>)">

                            <!-- Foto menu -->
                            <div class="foto-menu-rahma">
                                <?php if ($menu_rahma['foto_rahma'] != '-' && !empty($menu_rahma['foto_rahma'])): ?>
                                    <img src="../assets/img/<?= htmlspecialchars($menu_rahma['foto_rahma']) ?>"
                                        alt="<?= htmlspecialchars($menu_rahma['nama_menu_rahma']) ?>">
                                <?php else: ?>
                                    <div class="foto-placeholder-rahma">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- Badge kategori -->
                                <span class="badge-kategori-rahma">
                                    <?= htmlspecialchars($menu_rahma['kategori_rahma']) ?>
                                </span>
                            </div>

                            <!-- Nama + harga aja -->
                            <div class="card-body p-3">
                                <div class="fw-semibold nama-menu-rahma mb-1">
                                    <?= htmlspecialchars($menu_rahma['nama_menu_rahma']) ?>
                                </div>
                                <div class="harga-menu-rahma">
                                    Rp <?= number_format($menu_rahma['harga_rahma'], 0, ',', '.') ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
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

    <!-- ===== MODAL DETAIL MENU ===== -->
    <div class="modal fade" id="modalMenu_rahma" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">

                <!-- Foto di modal -->
                <div id="modalFoto_rahma" class="modal-foto-rahma">
                    <div id="modalFotoPlaceholder_rahma" class="foto-placeholder-rahma">
                        <i class="bi bi-image"></i>
                    </div>
                    <img id="modalFotoImg_rahma" src="" alt="" style="display:none;">
                    <!-- Badge kategori -->
                    <span id="modalKategori_rahma" class="badge-kategori-rahma"></span>
                    <!-- Tombol tutup modal -->
                    <button type="button" class="btn-tutup-modal-rahma" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-4">
                    <!-- Nama menu -->
                    <h5 id="modalNama_rahma" class="fw-bold mb-1" style="color: var(--dark-orange-rahma);"></h5>

                    <!-- Harga -->
                    <div id="modalHarga_rahma" class="fw-bold mb-3"
                        style="color: var(--dark-pink-rahma); font-size: 1.1rem;"></div>

                    <!-- Deskripsi (opsional, bisa ditambahkan di database dan ditampilkan di sini) -->
                    <p id="modalDeskripsi_rahma" class="text-muted small"></p>

                    <!-- Form tambah ke keranjang -->
                    <form action="../proses/proses_keranjang_rahma.php" method="POST">
                        <input type="hidden" id="modalIdMenu_rahma" name="id_menu_rahma">
                        <input type="hidden" name="id_meja_rahma" value="<?= $id_meja_rahma ?>">
                        <input type="hidden" name="redirect_rahma"
                            value="menu_rahma.php?<?= $id_meja_rahma == 'TAKEAWAY' ? 'jenis=takeaway' : 'meja=' . $id_meja_rahma ?>&kategori=<?= $filter_kategori_rahma ?>">
                        
                            <!-- Input qty -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jumlah</label>
                            <div class="input-qty-rahma">
                                <button type="button" class="btn-qty-rahma" onclick="kurang_rahma(this)">−</button>
                                <input type="number" name="qty_rahma" id="modalQty_rahma" value="1" min="1" max="99"
                                    class="input-angka-rahma" readonly>
                                <button type="button" class="btn-qty-rahma" onclick="tambah_rahma(this)">+</button>
                            </div>
                        </div>

                        <!-- Total harga realtime -->
                        <div class="d-flex justify-content-between mb-4 p-3 rounded-3"
                            style="background-color: rgba(253,152,85,0.1);">
                            <span class="text-muted small">Total</span>
                            <span id="modalTotal_rahma" class="fw-bold" style="color: var(--dark-pink-rahma);"></span>
                        </div>

                        <button type="submit" class="btn-tambah-rahma w-100 py-2">
                            <i class="bi bi-cart-plus me-2"></i>Masukkan Keranjang
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variabel harga untuk hitung total realtime
        let hargaMenu_rahma = 0;

        // Buka modal dan isi datanya
        function bukaModal_rahma(menu_rahma) {
            hargaMenu_rahma = menu_rahma.harga_rahma;

            // Isi data ke modal
            document.getElementById('modalNama_rahma').textContent = menu_rahma.nama_menu_rahma;
            document.getElementById('modalHarga_rahma').textContent = 'Rp ' + parseInt(menu_rahma.harga_rahma).toLocaleString('id-ID');
            document.getElementById('modalKategori_rahma').textContent = menu_rahma.kategori_rahma;
            document.getElementById('modalDeskripsi_rahma').textContent = menu_rahma.deskripsi_rahma || 'Tidak ada deskripsi tersedia.';
            document.getElementById('modalIdMenu_rahma').value = menu_rahma.id_menu_rahma;

            // Reset qty ke 1
            document.getElementById('modalQty_rahma').value = 1;
            updateTotal_rahma();

            // Handle foto
            const img_rahma = document.getElementById('modalFotoImg_rahma');
            const placeholder_rahma = document.getElementById('modalFotoPlaceholder_rahma');

            if (menu_rahma.foto_rahma && menu_rahma.foto_rahma !== '-') {
                img_rahma.src = '../assets/img/' + menu_rahma.foto_rahma;
                img_rahma.style.display = 'block';
                placeholder_rahma.style.display = 'none';
            } else {
                img_rahma.style.display = 'none';
                placeholder_rahma.style.display = 'flex';
            }

            // Buka modal Bootstrap
            new bootstrap.Modal(document.getElementById('modalMenu_rahma')).show();
        }

        // Update total harga realtime
        function updateTotal_rahma() {
            const qty_rahma = parseInt(document.getElementById('modalQty_rahma').value) || 1;
            const total_rahma = qty_rahma * hargaMenu_rahma;
            document.getElementById('modalTotal_rahma').textContent = 'Rp ' + total_rahma.toLocaleString('id-ID');
        }

        function kurang_rahma(btn) {
            const input_rahma = btn.nextElementSibling;
            if (parseInt(input_rahma.value) > 1) {
                input_rahma.value = parseInt(input_rahma.value) - 1;
                updateTotal_rahma();
            }
        }

        function tambah_rahma(btn) {
            const input_rahma = btn.previousElementSibling;
            if (parseInt(input_rahma.value) < 99) {
                input_rahma.value = parseInt(input_rahma.value) + 1;
                updateTotal_rahma();
            }
        }
    </script>
</body>

</html>