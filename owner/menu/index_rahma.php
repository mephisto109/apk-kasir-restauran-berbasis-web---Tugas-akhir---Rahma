<?php
session_start();
// Cegah cache supaya ga bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect kalau belum login
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../../login_rahma.php");
    exit;
}

// Cuma role owner (R001) yang boleh masuk
if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../../login_rahma.php");
    exit;
}

include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

// Ambil semua data menu, diurutkan per kategori
$query_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma 
     ORDER BY kategori_rahma ASC, id_menu_rahma DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">
</head>

<body>
    <!-- Stripe dekoratif di atas halaman -->
    <div class="flag-stripe-rahma"></div>

    <div class="container mt-4">

        <!-- Judul halaman -->
        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-menu-button-wide me-2"></i>Data Menu
        </h5>

        <!-- Card tabel utama -->
        <div class="card card-table-rahma">

            <!-- Header card dengan tombol tambah -->
            <div class="card-header card-header-rahma py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-white">
                    <i class="bi bi-list-ul me-2"></i>Daftar Menu
                </h6>
                <a href="tambah_rahma.php" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Menu
                </a>
            </div>

            <!-- Isi tabel -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th>Stok</th>
                                <th>Gambar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kategori_sekarang_rahma = "";
                            $no_rahma = 1;

                            // Loop semua data menu dari database
                            while ($row_rahma = mysqli_fetch_assoc($query_rahma)):

                                // Kalau kategori berubah, tampilkan baris separator kategori
                                if ($kategori_sekarang_rahma != $row_rahma['kategori_rahma']) {
                                    $kategori_sekarang_rahma = $row_rahma['kategori_rahma'];
                                    $no_rahma = 1;
                                    ?>
                                    <tr class="table-secondary">
                                        <td colspan="7" class="fw-bold ps-3">
                                            <i class="bi bi-cup-hot me-2"></i>
                                            <?= htmlspecialchars($kategori_sekarang_rahma) ?>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php
                                // Tentukan warna baris dan label berdasarkan status
                                $warna_rahma = ($row_rahma['status_rahma'] == 'nonaktif') ? 'table-danger' : '';
                                $label_rahma = ($row_rahma['status_rahma'] == 'nonaktif')
                                    ? "<span class='badge badge-nonaktif-rahma ms-1'>NONAKTIF</span>"
                                    : "";

                                // Badge stok tersedia / habis
                                $status_badge_rahma = ($row_rahma['status_menu_rahma'] == 'habis')
                                    ? "<span class='badge badge-status-rahma badge-nonaktif-rahma'>Habis</span>"
                                    : "<span class='badge badge-status-rahma badge-aktif-rahma'>Tersedia</span>";
                                ?>

                                <tr class="<?= $warna_rahma ?>">

                                    <!-- Nomor urut per kategori -->
                                    <td class="ps-3">
                                        <span class="text-id-rahma"><?= $no_rahma++ ?></span>
                                    </td>

                                    <!-- Nama menu + label nonaktif kalau ada -->
                                    <td>
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($row_rahma['nama_menu_rahma']) ?>
                                            <?= $label_rahma ?>
                                        </div>
                                    </td>

                                    <!-- Harga menu -->
                                    <td class="fw-semibold">
                                        Rp <?= number_format($row_rahma['harga_rahma'], 0, ',', '.') ?>
                                    </td>

                                    <!-- Deskripsi singkat menu -->
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($row_rahma['deskripsi_rahma']) ?>
                                    </td>

                                    <!-- Status stok -->
                                    <td><?= $status_badge_rahma ?></td>

                                    <!-- Foto menu atau placeholder -->
                                    <td>
                                        <?php if (!empty($row_rahma['foto_rahma'])): ?>
                                            <img src="../../assets/img/<?= htmlspecialchars($row_rahma['foto_rahma']) ?>"
                                                class="img-menu-rahma">
                                        <?php else: ?>
                                            <span class="text-muted small">Tidak ada foto</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tombol edit + aktif/nonaktif -->
                                    <td class="text-center">
                                        <a href="edit_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma'] ?>"
                                            class="btn btn-sm btn-detail-rahma me-1">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>

                                        <?php if ($row_rahma['status_rahma'] == 'aktif'): ?>
                                            <button type="button" class="btn btn-sm btn-bayar-rahma btn-konfirmasi-rahma"
                                                data-bs-toggle="modal" data-bs-target="#modalKonfirmasiRahma"
                                                data-url="nonaktif_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma'] ?>"
                                                data-title="Non-aktif Menu" data-message="Yakin ingin menonaktifkan menu ini?">
                                                <i class="bi bi-x-circle me-1"></i>Non-aktif
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-bayar-rahma btn-konfirmasi-rahma"
                                                data-bs-toggle="modal" data-bs-target="#modalKonfirmasiRahma"
                                                data-url="aktifkan_rahma.php?id_menu_rahma=<?= $row_rahma['id_menu_rahma'] ?>"
                                                data-title="Aktifkan Menu" data-message="Aktifkan kembali menu ini?">
                                                <i class="bi bi-check-circle me-1"></i>Aktifkan
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- ===================== MODAL KONFIRMASI ===================== -->
    <div class="modal fade" id="modalKonfirmasiRahma" tabindex="-1" aria-labelledby="modalKonfirmasiLabelRahma"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header modal dengan gradient -->
                <div class="modal-header modal-header-rahma border-0">
                    <h5 class="modal-title fw-bold text-white" id="modalKonfirmasiLabelRahma">
                        Konfirmasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>

                <!-- Pesan konfirmasi (diisi oleh JS) -->
                <div class="modal-body pt-3 pb-2 px-4">
                    <p class="mb-0" id="modalKonfirmasiTextRahma">Apakah kamu yakin?</p>
                </div>

                <!-- Tombol aksi modal -->
                <div class="modal-footer border-0 pt-0 px-4 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <a href="#" class="btn btn-bayar-rahma rounded-pill px-3" id="modalKonfirmasiBtnRahma">Ya,
                        lanjutkan</a>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Reload halaman kalau user klik back dari cache browser
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Isi data modal saat tombol konfirmasi diklik
        const modalKonfirmasiRahma = document.getElementById('modalKonfirmasiRahma');
        if (modalKonfirmasiRahma) {
            modalKonfirmasiRahma.addEventListener('show.bs.modal', function (event) {
                // Ambil data dari tombol yang diklik
                const button_rahma = event.relatedTarget;
                const url_rahma = button_rahma.getAttribute('data-url');
                const title_rahma = button_rahma.getAttribute('data-title');
                const message_rahma = button_rahma.getAttribute('data-message');

                // Masukkan ke elemen modal
                document.getElementById('modalKonfirmasiLabelRahma').textContent = title_rahma;
                document.getElementById('modalKonfirmasiTextRahma').textContent = message_rahma;
                document.getElementById('modalKonfirmasiBtnRahma').setAttribute('href', url_rahma);
            });
        }
    </script>
</body>

</html>