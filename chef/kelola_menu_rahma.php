<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — tolak kalau belum login atau bukan chef (R004)
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R004') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Tangkap pesan sukses/gagal dari proses update
$pesan_sukses_rahma = $_GET['sukses'] ?? '';
$pesan_gagal_rahma = $_GET['gagal'] ?? '';

// Filter kategori — default semua
$filter_kategori_rahma = $_GET['kategori'] ?? 'semua';

// Ambil semua menu sesuai filter
if ($filter_kategori_rahma === 'semua') {
    $query_menu_rahma = mysqli_query($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma 
        ORDER BY status_rahma ASC, kategori_rahma ASC, nama_menu_rahma ASC
    ");
} else {
    $stmt_rahma = mysqli_prepare($koneksiRahma, "
        SELECT * FROM tbl_menu_rahma 
        WHERE kategori_rahma = ? 
        ORDER BY status_rahma ASC, nama_menu_rahma ASC
    ");
    mysqli_stmt_bind_param($stmt_rahma, 's', $filter_kategori_rahma);
    mysqli_stmt_execute($stmt_rahma);
    $query_menu_rahma = mysqli_stmt_get_result($stmt_rahma);
}

// Hitung jumlah menu tersedia dan habis untuk summary card
$count_tersedia_rahma = mysqli_fetch_assoc(mysqli_query($koneksiRahma, "SELECT COUNT(*) as total FROM tbl_menu_rahma WHERE status_menu_rahma = 'tersedia' AND status_rahma = 'aktif'"))['total'];
$count_habis_rahma = mysqli_fetch_assoc(mysqli_query($koneksiRahma, "SELECT COUNT(*) as total FROM tbl_menu_rahma WHERE status_menu_rahma = 'habis' AND status_rahma = 'aktif'"))['total'];

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
    <link rel="stylesheet" href="../assets/css/chef_rahma.css">
    <title>Kelola Status Menu</title>

    <style>
        /* Kartu summary atas */
        .card-summary-menu-rahma {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s;
        }

        .card-summary-menu-rahma:hover {
            transform: translateY(-3px);
        }

        /* Tabel menu */
        .card-tabel-menu-rahma {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        /* Foto kecil di tabel */
        .foto-tabel-rahma {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 10px;
        }

        .foto-placeholder-tabel-rahma {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bbb;
            font-size: 1.3rem;
        }

        /* Badge status */
        .badge-tersedia-rahma {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-size: 0.78rem;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-habis-tabel-rahma {
            background-color: #fce4e4;
            color: #c62828;
            font-size: 0.78rem;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Tombol toggle status */
        .btn-toggle-tersedia-rahma {
            background: linear-gradient(135deg, #43a047, #2e7d32);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-toggle-tersedia-rahma:hover {
            opacity: 0.85;
            transform: scale(1.04);
        }

        .btn-toggle-habis-rahma {
            background: linear-gradient(135deg, #e53935, #c62828);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-toggle-habis-rahma:hover {
            opacity: 0.85;
            transform: scale(1.04);
        }

        /* Filter tab kategori */
        .btn-filter-chef-rahma {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 2px solid var(--orange-rahma);
            color: var(--dark-orange-rahma);
            background: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-filter-chef-rahma:hover,
        .btn-filter-chef-rahma.active {
            background: var(--orange-rahma);
            color: white;
        }

        /* Toast notifikasi */
        .toast-rahma {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 260px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            animation: slideIn_rahma 0.3s ease;
        }

        @keyframes slideIn_rahma {
            from {
                transform: translateX(100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <!-- Toast notifikasi sukses -->
    <?php if ($pesan_sukses_rahma): ?>
        <div class="toast-rahma alert alert-success d-flex align-items-center gap-2" id="toastSukses_rahma">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>
                <?php if ($pesan_sukses_rahma === 'tersedia'): ?>
                    Menu berhasil diubah ke <strong>Tersedia</strong> ✅
                <?php else: ?>
                    Menu berhasil diubah ke <strong>Habis</strong> 🚫
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Toast notifikasi gagal -->
    <?php if ($pesan_gagal_rahma): ?>
        <div class="toast-rahma alert alert-danger d-flex align-items-center gap-2" id="toastGagal_rahma">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <span>Gagal update status menu. Coba lagi ya!</span>
        </div>
    <?php endif; ?>

    <div class="container mt-4 pb-5">

        <!-- Judul halaman -->
        <h5 class="mb-1 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-toggles me-2"></i>Kelola Status Menu
        </h5>
        <p class="text-muted small mb-4">Update status menu — tersedia atau habis</p>

        <!-- Summary card atas -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="card card-summary-menu-rahma h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3" style="background:#e8f5e9;">
                            <i class="bi bi-check-circle fs-4" style="color:#2e7d32;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Menu Tersedia</div>
                            <div class="fs-3 fw-bold" style="color:#2e7d32;"><?= $count_tersedia_rahma ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-summary-menu-rahma h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3" style="background:#fce4e4;">
                            <i class="bi bi-x-circle fs-4" style="color:#c62828;"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Menu Habis</div>
                            <div class="fs-3 fw-bold" style="color:#c62828;"><?= $count_habis_rahma ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <!-- Filter Kategori (Tetap di kiri biar Chef gampang pilih kelompok menu) -->
            <div class="d-flex gap-2">
                <a href="kelola_menu_rahma.php?kategori=semua"
                    class="btn-filter-chef-rahma <?= $filter_kategori_rahma === 'semua' ? 'active' : '' ?>">
                    Semua
                </a>
                <a href="kelola_menu_rahma.php?kategori=makanan"
                    class="btn-filter-chef-rahma <?= $filter_kategori_rahma === 'makanan' ? 'active' : '' ?>">
                    Makanan
                </a>
                <a href="kelola_menu_rahma.php?kategori=minuman"
                    class="btn-filter-chef-rahma <?= $filter_kategori_rahma === 'minuman' ? 'active' : '' ?>">
                    Minuman
                </a>
            </div>

            <!-- DROPDOWN FILTER STATUS (Filter Cepat di kanan) -->
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <input type="text" id="inputSearchNama_rahma" class="form-control form-control-sm rounded-pill ps-4"
                        placeholder="Cari nama menu..." style="border: 2px solid var(--orange-rahma); width: 200px;">
                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y pe-3 text-muted"></i>
                </div>

                <label class="small fw-bold text-muted"><i class="bi bi-funnel"></i> Status:</label>
                <select id="filterStatus_rahma" class="form-select form-select-sm rounded-pill border-orange-rahma"
                    style="width: 150px; border: 2px solid var(--orange-rahma);">
                    <option value="semua">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Habis</option>
                    <option value="non-aktif">Non-aktif</option>
                </select>
            </div>
        </div>

        <!-- Tabel daftar menu -->
        <div class="card card-tabel-menu-rahma">
            <div class="card-header py-3 d-flex justify-content-between align-items-center"
                style="background: linear-gradient(135deg, var(--orange-rahma), var(--dark-orange-rahma));">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Menu
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelMenu_rahma">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ada_menu_rahma = false;
                            while ($menu_rahma = mysqli_fetch_assoc($query_menu_rahma)):
                                $ada_menu_rahma = true;
                                $is_tersedia_rahma = $menu_rahma['status_menu_rahma'] === 'tersedia';

                                // CEK STATUS AKTIF/NONAKTIF DARI OWNER
                                $is_nonaktif_owner_rahma = ($menu_rahma['status_rahma'] === 'nonaktif');

                                // Tentukan style baris tabel jika nonaktif
                                $baris_style_rahma = $is_nonaktif_owner_rahma ? 'style="opacity: 0.5; background-color: #f8f9fa;"' : '';
                                ?>
                                <tr <?= $baris_style_rahma ?>>
                                    <!-- Foto + nama menu -->
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($menu_rahma['foto_rahma'] !== '-' && !empty($menu_rahma['foto_rahma'])): ?>
                                                <img src="../assets/img/<?= htmlspecialchars($menu_rahma['foto_rahma']) ?>"
                                                    class="foto-tabel-rahma">
                                            <?php else: ?>
                                                <div class="foto-placeholder-tabel-rahma"><i class="bi bi-image"></i></div>
                                            <?php endif; ?>
                                            <span class="fw-semibold text-search-rahma" style="font-size:0.9rem;">
                                                <?= htmlspecialchars($menu_rahma['nama_menu_rahma']) ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td><span
                                            class="text-muted small text-capitalize text-search-rahma"><?= htmlspecialchars($menu_rahma['kategori_rahma']) ?></span>
                                    </td>
                                    <td><span class="small fw-semibold" style="color: var(--dark-pink-rahma);">Rp
                                            <?= number_format($menu_rahma['harga_rahma'], 0, ',', '.') ?></span></td>

                                    <!-- Badge status sekarang -->
                                    <td class="text-center">
                                        <?php if ($is_nonaktif_owner_rahma): ?>
                                            <span class="badge bg-secondary text-white text-search-rahma"
                                                style="font-size: 0.78rem; padding: 5px 12px; border-radius: 20px;">
                                                <i class="bi bi-slash-circle me-1"></i>Non-aktif
                                            </span>
                                        <?php elseif ($is_tersedia_rahma): ?>
                                            <span class="badge-tersedia-rahma text-search-rahma"><i
                                                    class="bi bi-check-circle me-1"></i>Tersedia</span>
                                        <?php else: ?>
                                            <span class="badge-habis-tabel-rahma text-search-rahma"><i
                                                    class="bi bi-x-circle me-1"></i>Habis</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tombol toggle -->
                                    <td class="text-center">
                                        <?php if ($is_nonaktif_owner_rahma): ?>
                                            <small class="text-muted italic">Akses Terkunci</small>
                                        <?php else: ?>
                                            <form action="../proses/proses_status_menu_rahma.php" method="POST">
                                                <input type="hidden" name="id_menu_rahma"
                                                    value="<?= $menu_rahma['id_menu_rahma'] ?>">
                                                <input type="hidden" name="status_baru_rahma"
                                                    value="<?= $is_tersedia_rahma ? 'habis' : 'tersedia' ?>">
                                                <input type="hidden" name="redirect_kategori_rahma"
                                                    value="<?= $filter_kategori_rahma ?>">

                                                <?php if ($is_tersedia_rahma): ?>
                                                    <button type="submit" class="btn-toggle-habis-rahma">
                                                        <i class="bi bi-x-circle me-1"></i>Tandai Habis
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="btn-toggle-tersedia-rahma">
                                                        <i class="bi bi-check-circle me-1"></i>Tandai Tersedia
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <tr id="rowNoData_rahma" style="display: none;">
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-search fs-2 d-block mb-2"></i>
                                    Menu yang kamu cari nggak ada, Kitten...
                                </td>
                            </tr>

                            <?php if (!$ada_menu_rahma): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada menu di kategori ini
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const inputSearch_rahma = document.getElementById('inputSearchNama_rahma');
        const selectStatus_rahma = document.getElementById('filterStatus_rahma');
        const rows_rahma = document.querySelectorAll('#tabelMenu_rahma tbody tr:not(#rowNoData_rahma)');
        const rowNoData_rahma = document.getElementById('rowNoData_rahma');

        function filterTabel_rahma() {
            const keyword_rahma = inputSearch_rahma.value.toLowerCase().trim();
            const statusPilihan_rahma = selectStatus_rahma.value.toLowerCase();
            let adaData_rahma = false;

            rows_rahma.forEach(row => {
                // Ambil teks dari kolom Nama (kolom 1) dan Status (kolom 4)
                const namaMenu_rahma = row.cells[0].textContent.toLowerCase();
                const statusMenu_rahma = row.cells[3].textContent.toLowerCase();

                // Cek kecocokan Search Nama
                const cocokNama_rahma = namaMenu_rahma.includes(keyword_rahma);

                // Cek kecocokan Dropdown Status
                let cocokStatus_rahma = false;
                if (statusPilihan_rahma === "semua") {
                    cocokStatus_rahma = true;
                } else if (statusMenu_rahma.includes(statusPilihan_rahma)) {
                    cocokStatus_rahma = true;
                }

                // Eksekusi: Tampilkan jika KEDUA-DUANYA cocok
                if (cocokNama_rahma && cocokStatus_rahma) {
                    row.style.display = '';
                    adaData_rahma = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Munculin pesan kalau ga ada yang cocok
            rowNoData_rahma.style.display = adaData_rahma ? 'none' : '';
        }

        // Jalankan fungsi tiap kali input diketik atau dropdown diganti
        inputSearch_rahma.addEventListener('keyup', filterTabel_rahma);
        selectStatus_rahma.addEventListener('change', filterTabel_rahma);

        // Toast notif handling
        setTimeout(() => {
            const ts_rahma = document.getElementById('toastSukses_rahma');
            const tg_rahma = document.getElementById('toastGagal_rahma');
            if (ts_rahma) ts_rahma.style.display = 'none';
            if (tg_rahma) tg_rahma.style.display = 'none';
        }, 3000);
    </script>
</body>

</html>