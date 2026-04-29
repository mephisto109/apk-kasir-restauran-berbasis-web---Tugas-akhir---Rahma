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

// Ambil id_transaksi dari URL
$id_transaksi_rahma = $_GET['id'] ?? '';

if (empty($id_transaksi_rahma)) {
    header("Location: dashboard_rahma.php");
    exit;
}

// Ambil data transaksi
$query_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT t.*, o.nama_pelanggan_rahma, o.id_meja_rahma, o.jenis_pesanan_rahma, o.waktu_order_rahma, o.keterangan_rahma, o.id_order_rahma
    FROM tbl_transaksi_rahma t
    LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
    WHERE t.id_transaksi_rahma = '$id_transaksi_rahma'
");

if (mysqli_num_rows($query_transaksi_rahma) == 0) {
    header("Location: dashboard_rahma.php");
    exit;
}

$transaksi_rahma = mysqli_fetch_assoc($query_transaksi_rahma);

// Ambil detail item yang dipesan
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, mn.nama_menu_rahma, mn.harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '{$transaksi_rahma['id_order_rahma']}'
");

// Hitung grand total order untuk konversi diskon persen ke nominal rupiah
$query_total_order_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_detail_order_rahma
    WHERE id_order_rahma = '{$transaksi_rahma['id_order_rahma']}'
");
$data_total_order_rahma = mysqli_fetch_assoc($query_total_order_rahma);
$grand_total_order_rahma = $data_total_order_rahma['grand_total_rahma'];
$diskon_nominal_rahma = (int) ($grand_total_order_rahma * $transaksi_rahma['diskon_rahma'] / 100);

//ambil pajak dari transaksi
$pajak_nominal_rahma = $transaksi_rahma['pajak_rahma'];

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
    <title>Cetak Struk</title>
    <style>
        /* Styling preview struk — mirip kertas struk beneran */
        .struk-preview-rahma {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            border: 1px dashed #ddd;
            border-radius: 12px;
            padding: 24px;
            font-family: 'Courier New', monospace;
        }

        /* Garis pemisah struk */
        .struk-divider-rahma {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 12px 0;
        }

        /* Nama restoran di atas struk */
        .struk-header-rahma {
            text-align: center;
            margin-bottom: 12px;
        }

        /* Baris item di struk */
        .struk-item-rahma {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        /* Total di struk */
        .struk-total-rahma {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Tombol aksi -->
        <div class="d-flex gap-2 mb-4">
            <a href="dashboard_rahma.php" class="btn btn-sm"
                style="border: 1.5px solid var(--dark-orange-rahma); color: var(--dark-orange-rahma); border-radius: 8px;">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <!-- Tombol cetak — buka generate_struk sebagai PDF -->
            <a href="../proses/generate_struk_rahma.php?id=<?= $id_transaksi_rahma ?>" target="_blank"
                class="btn btn-sm btn-bayar-rahma">
                <i class="bi bi-printer me-1"></i> Cetak / Download PDF
            </a>
        </div>

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-receipt me-2"></i>Preview Struk
        </h5>

        <!-- ===== PREVIEW STRUK ===== -->
        <div class="struk-preview-rahma shadow-sm">

            <!-- Header restoran -->
            <div class="struk-header-rahma">
                <div class="fw-bold fs-5">FAMIRESU IKO</div>
                <div style="font-size: 0.8rem; color: #666;">Restoran Keluarga</div>
                <div style="font-size: 0.82rem; color: #555; line-height: 1.4; margin-top: 8px;">
                    Jl. Kuliner Raya No. 707<br>
                    Bandung<br>
                    WA: 081299887766<br>
                    IG: @famiresu.iko
                </div>
                <div style="font-size: 0.75rem; color: #999; margin-top: 8px;">--------------------------------</div>
            </div>

            <!-- Info transaksi -->
            <div style="font-size: 0.82rem; margin-bottom: 8px;">
                <div class="struk-item-rahma">
                    <span>Tgl: <?= date('d/m/Y', strtotime($transaksi_rahma['waktu_transaksi_rahma'])) ?></span>
                    <span>Jam: <?= date('H:i', strtotime($transaksi_rahma['waktu_transaksi_rahma'])) ?></span>
                </div>
                <div class="struk-item-rahma">
                    <span>No : <?= htmlspecialchars($id_transaksi_rahma) ?></span>
                    <span>Meja: <?= (int) ltrim($transaksi_rahma['id_meja_rahma'], 'M') ?></span>
                </div>
                <div class="struk-item-rahma">
                    <span>Nama:</span>
                    <span><?= htmlspecialchars($transaksi_rahma['nama_pelanggan_rahma']) ?></span>
                </div>
                <div class="struk-item-rahma">
                    <span>Kasir:</span>
                    <span><?= htmlspecialchars($_SESSION['nama_rahma'] ?? $_SESSION['username_rahma'] ?? '-') ?></span>
                </div>
            </div>
            <hr class="struk-divider-rahma">
            <div style="font-size: 0.82rem; margin-bottom: 8px;">
                <div class="struk-item-rahma">
                    <span>No. Order</span>
                    <span><?= htmlspecialchars($transaksi_rahma['id_order_rahma']) ?></span>
                </div>
                <div class="struk-item-rahma">
                    <span>Jenis Pesanan</span>
                    <span><?= ucfirst(htmlspecialchars($transaksi_rahma['jenis_pesanan_rahma'])) ?></span>
                </div>
            </div>

            <hr class="struk-divider-rahma">

            <!-- Daftar item -->
            <?php while ($row_detail_rahma = mysqli_fetch_assoc($query_detail_rahma)): ?>
                <div style="font-size: 0.82rem; margin-bottom: 6px;">
                    <div><?= htmlspecialchars($row_detail_rahma['nama_menu_rahma']) ?></div>
                    <div class="struk-item-rahma">
                        <span>
                            <?= $row_detail_rahma['qty_rahma'] ?> x
                            Rp <?= number_format($row_detail_rahma['harga_rahma'], 0, ',', '.') ?>
                        </span>
                        <span>Rp <?= number_format($row_detail_rahma['subtotal_rahma'], 0, ',', '.') ?></span>
                    </div>
                </div>
            <?php endwhile; ?>

            <hr class="struk-divider-rahma">

            <!-- Total, bayar, kembalian -->
            
            <div class="struk-item-rahma mb-2">
                <span>Subtotal</span>
                <span>Rp <?= number_format($grand_total_order_rahma, 0, ',', '.') ?></span>
            </div>
            <div class="struk-item-rahma mb-2">
                <span>PPN (11%)</span>
                <span>Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?></span>
            </div>
            <?php if ($transaksi_rahma['diskon_rahma'] > 0): ?>
                <div class="struk-item-rahma mb-2" style="color: var(--dark-pink-rahma);">
                    <span>Diskon (<?= $transaksi_rahma['diskon_rahma'] ?>%)</span>
                    <span>- Rp <?= number_format($diskon_nominal_rahma, 0, ',', '.') ?></span>
                </div>
            <?php endif; ?>
            <div class="struk-total-rahma mb-2">
                <span>Total</span>
                <span>Rp <?= number_format($transaksi_rahma['total_rahma'], 0, ',', '.') ?></span>
            </div>
            <div class="struk-item-rahma mb-2">
                <span>Bayar</span>
                <span>Rp <?= number_format($transaksi_rahma['bayar_rahma'], 0, ',', '.') ?></span>
            </div>
            <div class="struk-item-rahma" style="color: var(--dark-orange-rahma);">
                <span>Kembalian</span>
                <span>Rp <?= number_format($transaksi_rahma['kembalian_rahma'], 0, ',', '.') ?></span>
            </div>

            <hr class="struk-divider-rahma">

            <!-- Footer struk -->
            <div class="text-center" style="font-size: 0.78rem; color: #999;">
                <div>Terima kasih sudah makan di sini!</div>
                <div>Sampai jumpa lagi 🍽️</div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>