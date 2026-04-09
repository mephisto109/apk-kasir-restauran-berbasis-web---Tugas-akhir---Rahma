<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R002') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Ambil id_order dari URL
$id_order_rahma = $_GET['id'] ?? '';

// Kalau id_order kosong, balik ke dashboard
if (empty($id_order_rahma)) {
    header("Location: dashboard_rahma.php");
    exit;
}

// Ambil data order
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT o.*, m.status_rahma AS status_meja_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_meja_rahma m ON o.id_meja_rahma = m.id_meja_rahma
    WHERE o.id_order_rahma = '$id_order_rahma'
");

if (mysqli_num_rows($query_order_rahma) == 0) {
    header("Location: dashboard_rahma.php");
    exit;
}

$order_rahma = mysqli_fetch_assoc($query_order_rahma);

// Cek kalau sudah bayar, redirect ke detail aja
$query_cek_bayar_rahma = mysqli_query($koneksiRahma, "
    SELECT id_transaksi_rahma FROM tbl_transaksi_rahma
    WHERE id_order_rahma = '$id_order_rahma'
");
if (mysqli_num_rows($query_cek_bayar_rahma) > 0) {
    header("Location: detail_order_rahma.php?id=$id_order_rahma");
    exit;
}

// Ambil semua item yang dipesan
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, mn.nama_menu_rahma, mn.harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '$id_order_rahma'
");

// Hitung grand total
$query_total_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_detail_order_rahma
    WHERE id_order_rahma = '$id_order_rahma'
");
$data_total_rahma = mysqli_fetch_assoc($query_total_rahma);
$grand_total_rahma = $data_total_rahma['grand_total_rahma'];

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
    <link rel="stylesheet" href="../assets/css/kasir_rahma.css">
    <title>Pembayaran</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Tombol kembali -->
        <a href="detail_order_rahma.php?id=<?= $id_order_rahma ?>" class="btn btn-sm mb-3"
            style="border: 1.5px solid var(--dark-orange-rahma); color: var(--dark-orange-rahma); border-radius: 8px;">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-cash-coin me-2"></i>Proses Pembayaran
        </h5>

        <div class="row g-4">

            <!-- ===== KOLOM KIRI: RINGKASAN ORDER ===== -->
            <div class="col-md-5">
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
                                <?php while ($row_detail_rahma = mysqli_fetch_assoc($query_detail_rahma)): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($row_detail_rahma['nama_menu_rahma']) ?>
                                            </div>
                                            <small class="text-muted">
                                                Rp <?= number_format($row_detail_rahma['harga_rahma'], 0, ',', '.') ?>
                                            </small>
                                        </td>
                                        <td class="text-center"><?= $row_detail_rahma['qty_rahma'] ?></td>
                                        <td class="text-end pe-3 fw-semibold">
                                            Rp <?= number_format($row_detail_rahma['subtotal_rahma'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 fw-bold">Total</td>
                                    <td class="text-end pe-3 fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                        Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Info order -->
                <div class="card card-table-rahma mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Pelanggan</small>
                            <span class="fw-semibold">
                                <?= htmlspecialchars($order_rahma['nama_pelanggan_rahma']) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Meja</small>
                            <span class="badge badge-status-rahma"
                                style="background-color: var(--orange-rahma); color:#fff;">
                                <?= htmlspecialchars($order_rahma['id_meja_rahma']) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">ID Order</small>
                            <span class="text-id-rahma"><?= htmlspecialchars($id_order_rahma) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== KOLOM KANAN: FORM PEMBAYARAN ===== -->
            <div class="col-md-7">
                <div class="card card-table-rahma">
                    <div class="card-header card-header-rahma py-3">
                        <h6 class="mb-0 fw-semibold text-white">
                            <i class="bi bi-calculator me-2"></i>Input Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">

                        <!-- Total yang harus dibayar -->
                        <div class="p-3 rounded-3 mb-4 text-center"
                            style="background: linear-gradient(135deg, var(--dark-orange-rahma), var(--dark-pink-rahma));">
                            <div class="text-white small mb-1">Total yang harus dibayar</div>
                            <div class="text-white fw-bold fs-3">
                                Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?>
                            </div>
                        </div>

                        <form action="../proses/proses_bayar_rahma.php" method="POST">
                            <!-- Kirim id_order ke proses -->
                            <input type="hidden" name="id_order_rahma" value="<?= $id_order_rahma ?>">
                            <!-- Kirim grand total ke proses -->
                            <input type="hidden" name="grand_total_rahma" value="<?= $grand_total_rahma ?>">

                            <!-- Input nominal bayar -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nominal Bayar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="bayar_rahma" id="input_bayar_rahma" class="form-control"
                                        placeholder="Masukkan nominal bayar" min="<?= $grand_total_rahma ?>"
                                        oninput="hitungKembalian_rahma()" required>
                                </div>
                                <small class="text-muted">Minimal Rp
                                    <?= number_format($grand_total_rahma, 0, ',', '.') ?></small>
                            </div>

                            <!-- Kembalian otomatis -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Kembalian</label>
                                <div class="p-3 rounded-3 text-center"
                                    style="background-color: rgba(209, 97, 162, 0.1); border: 1.5px solid var(--pink-rahma);">
                                    <div id="hasil_kembalian_rahma" class="fw-bold fs-4"
                                        style="color: var(--dark-pink-rahma);">
                                        Rp 0
                                    </div>
                                </div>
                                <!-- Input hidden untuk kirim nilai kembalian ke proses -->
                                <input type="hidden" name="kembalian_rahma" id="input_kembalian_rahma" value="0">
                            </div>

                            <!-- Tombol bayar -->
                            <button type="submit" id="btn_bayar_rahma" class="btn btn-bayar-rahma w-100 py-2" disabled>
                                <i class="bi bi-cash me-2"></i>Konfirmasi Pembayaran
                            </button>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Grand total dari PHP — dipakai untuk hitung kembalian
        const grandTotal_rahma = <?= $grand_total_rahma ?>;

        // Fungsi untuk hitung kembalian dan validasi input bayar
        function hitungKembalian_rahma() {
            const bayar_rahma = parseInt(document.getElementById('input_bayar_rahma').value) || 0;
            const kembalian_rahma = bayar_rahma - grandTotal_rahma;
            const btnBayar_rahma = document.getElementById('btn_bayar_rahma');
            const hasilEl_rahma = document.getElementById('hasil_kembalian_rahma');
            const inputKembalian_rahma = document.getElementById('input_kembalian_rahma');


            // Validasi: kalau input bayar kosong atau kurang dari total, tampilkan peringatan dan nonaktifkan tombol
            if (kembalian_rahma >= 0) {
                // Nominal cukup — tampilkan kembalian dan aktifkan tombol
                hasilEl_rahma.textContent = 'Rp ' + kembalian_rahma.toLocaleString('id-ID');
                hasilEl_rahma.style.color = 'var(--dark-pink-rahma)';
                inputKembalian_rahma.value = kembalian_rahma;
                btnBayar_rahma.disabled = false;
            } else {
                // Nominal kurang — tampilkan peringatan dan nonaktifkan tombol
                hasilEl_rahma.textContent = 'Nominal kurang!';
                hasilEl_rahma.style.color = 'var(--dark-orange-rahma)';
                inputKembalian_rahma.value = 0;
                btnBayar_rahma.disabled = true;
            }
        }

        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>