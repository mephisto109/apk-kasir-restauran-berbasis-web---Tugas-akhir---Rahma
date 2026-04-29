<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

// Kalau keranjang kosong, balik ke menu
if (empty($_SESSION['keranjang_rahma'])) {
    header("Location: menu_rahma.php?jenis=" . ($_SESSION['jenis_pesanan_rahma'] === 'take away' ? 'takeaway' : 'dinein'));
    exit;
}

$keranjang_rahma = $_SESSION['keranjang_rahma'];
// UBAH: Tidak perlu ambil id_meja lagi
$jenis_pesanan_rahma = $_SESSION['jenis_pesanan_rahma'] ?? 'dine in';

// Hitung total
$grand_total_rahma = 0;
foreach ($keranjang_rahma as $item_rahma) {
    $grand_total_rahma += $item_rahma['harga_rahma'] * $item_rahma['qty_rahma'];
}

// Cek member atau guest — untuk diskon
$is_member_rahma = isset($_SESSION['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = ($grand_total_rahma * $diskon_persen_rahma) / 100;
$total_setelah_diskon_rahma = $grand_total_rahma - $nominal_diskon_rahma;
$pajak_nominal_rahma = $total_setelah_diskon_rahma * 0.11;
$total_bayar_rahma = $total_setelah_diskon_rahma + $pajak_nominal_rahma;

// Ambil nama user kalau member
$nama_default_rahma = '';
if ($is_member_rahma) {
    include '../koneksi/koneksi_rahma.php';
    $id_user_rahma = $_SESSION['id_user_rahma'];
    $query_user_rahma = mysqli_query($koneksiRahma, "
        SELECT nama_rahma FROM tbl_user_rahma
        WHERE id_user_rahma = '$id_user_rahma'
    ");
    $data_user_rahma = mysqli_fetch_assoc($query_user_rahma);
    $nama_default_rahma = $data_user_rahma['nama_rahma'] ?? '';
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
    <title>Konfirmasi Pesanan</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-check-circle me-2"></i>Konfirmasi Pesanan
                </h5>
                <small class="text-muted">
                    <?php if ($jenis_pesanan_rahma === 'take away'): ?>
                        <i class="bi bi-bag me-1"></i>Bawa Pulang (Take Away)
                    <?php else: ?>
                        <i class="bi bi-shop me-1"></i>Makan di Tempat (Dine In)
                    <?php endif; ?>
                </small>
            </div>
            <!-- Tombol balik ke keranjang -->
            <a href="keranjang_rahma.php" class="btn btn-sm"
                style="border: 1.5px solid var(--dark-orange-rahma); color: var(--dark-orange-rahma); border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i>Keranjang
            </a>
        </div>

        <div class="row g-4">

            <!-- ===== KOLOM KIRI: RINGKASAN PESANAN ===== -->
            <div class="col-md-6">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-bag me-2"></i>Ringkasan Pesanan
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Menu</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keranjang_rahma as $item_rahma): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold"
                                                style="color: var(--dark-orange-rahma); font-size: 0.88rem;">
                                                <?= htmlspecialchars($item_rahma['nama_menu_rahma']) ?>
                                            </div>
                                            <?php if (!empty($item_rahma['catatan_rahma'])): ?>
                                                <small class="text-muted">
                                                    📝 <?= htmlspecialchars($item_rahma['catatan_rahma']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $item_rahma['qty_rahma'] ?></td>
                                        <td class="text-end pe-3 fw-semibold" style="color: var(--dark-pink-rahma);">
                                            Rp
                                            <?= number_format($item_rahma['harga_rahma'] * $item_rahma['qty_rahma'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <!-- Subtotal -->
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 text-muted small">Subtotal</td>
                                    <td class="text-end pe-3">
                                        Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <!-- Diskon kalau member -->
                                <?php if ($is_member_rahma): ?>
                                    <tr class="table-light">
                                        <td colspan="2" class="ps-3 text-muted small">
                                            Diskon Member
                                            <span class="badge"
                                                style="background-color: var(--dark-pink-rahma); color:#fff; font-size:0.65rem;">
                                                <?= $diskon_persen_rahma ?>%
                                            </span>
                                        </td>
                                        <td class="text-end pe-3" style="color: var(--dark-pink-rahma);">
                                            - Rp <?= number_format($nominal_diskon_rahma, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Pajak 11% -->
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 text-muted small">PPN (11%)</td>
                                    <td class="text-end pe-3">
                                        Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>

                                <!-- Total bayar -->
                                <tr>
                                    <td colspan="2" class="ps-3 fw-bold">Total Bayar</td>
                                    <td class="text-end pe-3 fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                        Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== KOLOM KANAN: FORM KONFIRMASI ===== -->
            <div class="col-md-6">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-person me-2"></i>Detail Pemesan
                        </h6>
                    </div>
                    <div class="card-body">

                        <form id="form-order-rahma" action="../proses/proses_order_rahma.php" method="POST">

                            <!-- Nama pelanggan -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan_rahma" class="form-control"
                                    placeholder="Masukkan nama kamu"
                                    value="<?= htmlspecialchars($nama_default_rahma) ?>" required
                                    style="border-color: var(--orange-rahma);">
                                <small class="text-muted">Nama ini akan tertera di struk</small>
                            </div>

                            <!-- Keterangan tambahan -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Keterangan (opsional)</label>
                                <textarea name="keterangan_rahma" class="form-control" rows="3"
                                    placeholder="Contoh: tidak pedas, alergi kacang, dll"
                                    style="border-color: var(--orange-rahma); resize: none;"></textarea>
                            </div>

                            <!-- Info meja -->
                            <div class="p-3 rounded-3 mb-4"
                                style="background-color: rgba(253,152,85,0.1); border: 1.5px solid var(--orange-rahma);">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Jenis Pesanan</small>
                                    <span class="fw-semibold" style="color: var(--dark-orange-rahma);">
                                        <?= $jenis_pesanan_rahma === 'take away' ? 'Bawa Pulang' : 'Makan di Tempat' ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Status</small>
                                    <span class="badge"
                                        style="background-color: var(--dark-pink-rahma); color:#fff; border-radius:20px; padding: 3px 10px; font-size:0.75rem;">
                                        <?= $is_member_rahma ? 'Member' : 'Guest' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol submit -->
                            <div class="text-center mb-3">
                                <p class="mb-1 fw-bold" style="color: var(--dark-orange-rahma);">Apakah pesanan sudah
                                    sesuai?</p>
                                <small class="text-muted">Klik "Pesan Sekarang" untuk mendapatkan nomor antrean dan
                                    struk.</small>
                            </div>

                            <!-- Tombol submit -->
                            <button type="button" onclick="submitPesanan_rahma()"
                                class="btn-tambah-rahma w-100 py-2 mb-2">
                                <i class="bi bi-send me-2"></i>Pesan Sekarang!
                            </button>

                            <a href="keranjang_rahma.php" class="btn w-100 py-2 btn-sekunder-konfirmasi-rahma">
                                Nanti dulu, mau tambah menu
                            </a>

                        </form>
                    </div>
                </div>
            </div>

        </div>
        <!-- Pop up sukses — muncul sebentar lalu hilang sendiri -->
        <div id="popup-sukses-rahma" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, var(--dark-orange-rahma), var(--dark-pink-rahma));
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    text-align: center;
    padding: 40px;
">
            <!-- Icon centang -->
            <div style="font-size: 5rem; margin-bottom: 20px; animation: popIn_rahma 0.4s ease;">✅</div>

            <!-- Teks utama -->
            <h3 style="color: #fff; font-weight: 700; margin-bottom: 10px;">
                Pesanan Berhasil Dibuat!
            </h3>
            <p style="color: rgba(255,255,255,0.85); font-size: 1rem; margin-bottom: 30px;">
                Bawa struk kamu ke kasir untuk membayar 🧾
            </p>

            <!-- Bar countdown -->
            <div
                style="width: 280px; height: 5px; background: rgba(255,255,255,0.3); border-radius: 99px; overflow: hidden;">
                <div id="bar-countdown-rahma" style="
            height: 100%;
            width: 100%;
            background: #fff;
            border-radius: 99px;
            transition: width 10s linear;
        "></div>
            </div>
            <small style="color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-top: 10px;">
                Mengalihkan ke halaman login...
            </small>
        </div>

        <style>
            @keyframes popIn_rahma {
                from {
                    transform: scale(0.8);
                    opacity: 0;
                }

                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        </style>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function submitPesanan_rahma() {
            // Ambil data form, kirim ke proses_order via fetch
            const form_rahma = document.getElementById('form-order-rahma');
            const formData_rahma = new FormData(form_rahma);

            fetch('../proses/proses_order_rahma.php', {
                method: 'POST',
                body: formData_rahma
            })
                .then(res => res.text())
                .then(response_rahma => {
                    // Ambil id_order dari response — proses_order harus echo id_order-nya
                    const id_order_rahma = response_rahma.trim();

                    // Buka struk di tab baru
                    window.open(
                        'cetak_struk_pesanan_rahma.php?id_order=' + id_order_rahma,
                        '_blank'
                    );

                    // Tampilkan pop up sukses
                    const popup_rahma = document.getElementById('popup-sukses-rahma');
                    popup_rahma.style.display = 'flex';

                    // Jalankan animasi bar countdown
                    setTimeout(() => {
                        document.getElementById('bar-countdown-rahma').style.width = '0%';
                    }, 100);

                    // Redirect ke login setelah 10 detik
                    setTimeout(() => {
                        window.location.href = '../login_rahma.php';
                    }, 10000);
                });
        }
    </script>
</body>

</html>