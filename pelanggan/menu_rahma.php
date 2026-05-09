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

// UBAH: Ambil jenis pesanan dari session atau URL — tanpa harus pilih nomor meja
$jenis_pesanan_rahma = $_GET['jenis'] ?? $_SESSION['jenis_pesanan_rahma'] ?? '';

if ($jenis_pesanan_rahma == 'takeaway') {
    $_SESSION['jenis_pesanan_rahma'] = 'take away';
} elseif ($jenis_pesanan_rahma == 'dinein') {
    $_SESSION['jenis_pesanan_rahma'] = 'dine in';
} else {
    // Kalau belum pilih jenis pesanan, tampilkan dialog di halaman
    $show_pilih_jenis_rahma = true;
    $jenis_pesanan_rahma = '';
}

$filter_kategori_rahma = $_GET['kategori'] ?? 'semua';

$total_keranjang_rahma = isset($_SESSION['keranjang_rahma']) ? count($_SESSION['keranjang_rahma']) : 0;

// Ambil data menu berdasarkan jenis pesanan dan filter kategori
if ($filter_kategori_rahma == 'semua') {
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        ORDER BY kategori_rahma ASC, nama_menu_rahma ASC
    ");
} else {
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma
        WHERE kategori_rahma = '$filter_kategori_rahma'
        ORDER BY nama_menu_rahma ASC
    ");
}

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

    <style>
        /* 1. Paksa modal lebar ke samping */
        #modalPilihJenis_rahma .modal-dialog {
            max-width: 90% !important;
            width: 90% !important;
            margin: 10px auto;
        }

        /* 2. Posisi di bawah navbar */
        #modalPilihJenis_rahma {
            top: 71px !important;
            height: calc(100% - 70px) !important;
        }

        /* 3. Styling tombol agar sejajar dan besar */
        .pilihan-container-rahma {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .card-pilihan-rahma {
            flex: 1;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            /* Animasi lebih smooth */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        /* Hover General */
        .card-pilihan-rahma:hover {
            transform: translateY(-10px) scale(1.02);
            /* Naik ke atas sedikit */
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            filter: brightness(1);
            /* Tetap terang */
        }

        /* Hover khusus Dine In (Pink) */
        .card-pilihan-rahma.dine-in-hover:hover {
            background-color: var(--pink-rahma) !important;
            color: white !important;
            border-color: var(--dark-pink-rahma);
        }

        /* Hover khusus Take Away (Orange) */
        .card-pilihan-rahma.take-away-hover:hover {
            background-color: var(--orange-rahma) !important;
            color: white !important;
            border-color: var(--dark-orange-rahma);
        }

        /* Animasi Ikon saat di-hover */
        .card-pilihan-rahma i {
            transition: all 0.3s ease;
        }

        .card-pilihan-rahma:hover i {
            transform: scale(1.2) rotate(5deg);
            color: white !important;
        }

        /* Responsive: Kalau di HP */
        @media (max-width: 768px) {
            .pilihan-container-rahma {
                flex-direction: column;
            }

            .card-pilihan-rahma {
                padding: 40px 20px;
            }
        }

        /* Card menu yang habis */
        .card-menu-rahma.habis-rahma {
            opacity: 0.55;
            cursor: not-allowed;
            position: relative;
        }

        .badge-habis-rahma {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #e53935, #c62828);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            z-index: 5;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(229, 57, 53, 0.4);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <?php if (!empty($show_pilih_jenis_rahma) && $show_pilih_jenis_rahma): ?>

        <div class="modal fade show" id="modalPilihJenis_rahma" tabindex="-1"
            style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content"
                    style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <div class="modal-body p-0">
                        <div class="row g-0">
                            <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center"
                                style="background: linear-gradient(135deg, var(--orange-rahma), var(--dark-orange-rahma)); color: white;">
                                <div class="text-center p-4">
                                    <i class="bi bi-egg-fried" style="font-size: 5rem;"></i>
                                    <h3 class="fw-bold mt-3">Selamat Datang!</h3>
                                    <p>Silakan pilih cara Anda menikmati hidangan kami hari ini.</p>
                                </div>
                            </div>

                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center bg-white">
                                <div class="text-center mb-4">
                                    <h4 class="fw-bold" style="color: var(--dark-orange-rahma);">Pilih Jenis Pesanan</h4>
                                    <p class="text-muted">Mau makan di sini atau bawa pulang?</p>
                                </div>

                                <div class="d-grid gap-3">
                                    <a href="menu_rahma.php?jenis=dinein"
                                        class="card-pilihan-rahma dine-in-hover btn btn-lg p-4 d-flex align-items-center"
                                        style="background: #fff0f5; border: 2px solid var(--pink-rahma); color: var(--dark-pink-rahma); border-radius: 15px; transition: 0.3s;">
                                        <i class="bi bi-shop fs-1 me-3"></i>
                                        <div class="text-start">
                                            <div class="fw-bold">Makan di Tempat</div>
                                            <small class="text-muted">Nikmati suasana resto kami</small>
                                        </div>
                                    </a>

                                    <a href="menu_rahma.php?jenis=takeaway"
                                        class="card-pilihan-rahma take-away-hover btn btn-lg p-4 d-flex align-items-center"
                                        style="background: #fff8f0; border: 2px solid var(--orange-rahma); color: var(--dark-orange-rahma); border-radius: 15px; transition: 0.3s;">
                                        <i class="bi bi-bag-check fs-1 me-3"></i>
                                        <div class="text-start">
                                            <div class="fw-bold">Bawa Pulang</div>
                                            <small class="text-muted">Bungkus untuk dinikmati di rumah</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-4">

        <!-- Header + tombol keranjang -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-egg-fried me-2"></i>Menu Kami
                </h5>

                <!-- Tampilkan jenis pesanan yang dipilih -->
                <small class="text-muted">
                    <?php if ($_SESSION['jenis_pesanan_rahma'] === 'take away'): ?>
                        <i class="bi bi-bag me-1"></i>Bawa Pulang (Take Away)
                    <?php else: ?>
                        <i class="bi bi-shop me-1"></i>Makan di Tempat (Dine In)
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
        <div class="d-flex gap-2 mb-4 flex-wrap">
            <a href="menu_rahma.php?jenis=<?= $_SESSION['jenis_pesanan_rahma'] === 'take away' ? 'takeaway' : 'dinein' ?>&kategori=semua"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'semua' ? 'active' : '' ?>">
                Semua
            </a>
            <a href="menu_rahma.php?jenis=<?= $_SESSION['jenis_pesanan_rahma'] === 'take away' ? 'takeaway' : 'dinein' ?>&kategori=makanan"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'makanan' ? 'active' : '' ?>">
                <i class="bi bi-egg-fried me-1"></i>Makanan
            </a>
            <a href="menu_rahma.php?jenis=<?= $_SESSION['jenis_pesanan_rahma'] === 'take away' ? 'takeaway' : 'dinein' ?>&kategori=minuman"
                class="btn-filter-rahma <?= $filter_kategori_rahma == 'minuman' ? 'active' : '' ?>">
                <i class="bi bi-cup-straw me-1"></i>Minuman
            </a>
        </div>

        <!-- Grid menu foto + nama + harga -->
        <?php if (count($menus_rahma) > 0): ?>
            <div class="row g-3">
                <?php foreach ($menus_rahma as $menu_rahma): ?>
                    <?php $is_habis_rahma = $menu_rahma['status_menu_rahma'] !== 'tersedia'; ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card card-menu-rahma h-100 <?= $is_habis_rahma ? 'habis-rahma' : '' ?>"
                            style="<?= !$is_habis_rahma ? 'cursor: pointer;' : '' ?>" <?= !$is_habis_rahma ? 'onclick="bukaModal_rahma(' . htmlspecialchars(json_encode($menu_rahma)) . ')"' : 'onclick="bukaModalHabis_rahma(' . htmlspecialchars(json_encode($menu_rahma)) . ')"' ?>>

                            <div class="foto-menu-rahma" style="position: relative;">
                                <?php if ($menu_rahma['foto_rahma'] != '-' && !empty($menu_rahma['foto_rahma'])): ?>
                                    <img src="../assets/img/<?= htmlspecialchars($menu_rahma['foto_rahma']) ?>"
                                        alt="<?= htmlspecialchars($menu_rahma['nama_menu_rahma']) ?>">
                                <?php else: ?>
                                    <div class="foto-placeholder-rahma">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge-kategori-rahma"><?= htmlspecialchars($menu_rahma['kategori_rahma']) ?></span>

                                <!-- Badge HABIS — muncul kalau stok kosong -->
                                <?php if ($is_habis_rahma): ?>
                                    <span class="badge-habis-rahma">
                                        <i class="bi bi-x-circle me-1"></i>Habis
                                    </span>
                                <?php endif; ?>
                            </div>

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
                        <input type="hidden" name="redirect_rahma"
                            value="menu_rahma.php?jenis=<?= $_SESSION['jenis_pesanan_rahma'] === 'take away' ? 'takeaway' : 'dinein' ?>&kategori=<?= $filter_kategori_rahma ?>">

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
        // Reset tombol keranjang kalau menu tersedia
        const btn_reset_rahma = document.querySelector('#modalMenu_rahma .btn-tambah-rahma');
        btn_reset_rahma.disabled = false;
        btn_reset_rahma.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Masukkan Keranjang';
        btn_reset_rahma.style.background = '';
        btn_reset_rahma.style.cursor = '';

        // Variabel harga untuk hitung total realtime
        let hargaMenu_rahma = 0;

        function bukaModal_rahma(menu_rahma) {
            // Reset tombol keranjang kalau menu tersedia
            const btn_reset_rahma = document.querySelector('#modalMenu_rahma .btn-tambah-rahma');
            btn_reset_rahma.disabled = false;
            btn_reset_rahma.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Masukkan Keranjang';
            btn_reset_rahma.style.background = '';
            btn_reset_rahma.style.cursor = '';

            hargaMenu_rahma = menu_rahma.harga_rahma;

            document.getElementById('modalNama_rahma').textContent = menu_rahma.nama_menu_rahma;
            document.getElementById('modalHarga_rahma').textContent = 'Rp ' + parseInt(menu_rahma.harga_rahma).toLocaleString('id-ID');
            document.getElementById('modalKategori_rahma').textContent = menu_rahma.kategori_rahma;
            document.getElementById('modalDeskripsi_rahma').textContent = menu_rahma.deskripsi_rahma || 'Tidak ada deskripsi tersedia.';
            document.getElementById('modalIdMenu_rahma').value = menu_rahma.id_menu_rahma;

            document.getElementById('modalQty_rahma').value = 1;
            updateTotal_rahma();

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