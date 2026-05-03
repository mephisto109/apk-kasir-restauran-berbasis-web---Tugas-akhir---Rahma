<?php
// Halaman yang muncul setelah pembayaran Midtrans berhasil/pending
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$id_transaksi_rahma = $_GET['id'] ?? '';
$status_rahma       = $_GET['status'] ?? 'success';

if (empty($id_transaksi_rahma)) {
    header("Location: menu_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';

// Ambil data transaksi untuk ditampilkan
$query_t_rahma = mysqli_query($koneksiRahma, "
    SELECT t.*, o.nama_pelanggan_rahma, o.jenis_pesanan_rahma
    FROM tbl_transaksi_rahma t
    LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
    WHERE t.id_transaksi_rahma = '$id_transaksi_rahma'
");
$transaksi_rahma = mysqli_fetch_assoc($query_t_rahma);

if (!$transaksi_rahma) {
    header("Location: menu_rahma.php");
    exit;
}

include '../templates/navbar_rahma.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran <?= $status_rahma === 'success' ? 'Berhasil' : 'Pending' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">
    <style>
        body { background: #fafafa; }

        /* ===== HERO STATUS ===== */
        .hero-status-rahma {
            background: <?= $status_rahma === 'success'
                ? 'linear-gradient(135deg, #e0650a, #c2185b)'
                : 'linear-gradient(135deg, #f57c00, #e65100)' ?>;
            padding: 50px 24px 70px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-status-rahma::before {
            content:'';
            position:absolute;
            width:220px; height:220px;
            background:rgba(255,255,255,0.06);
            border-radius:50%;
            top:-60px; right:-50px;
        }
        .hero-status-rahma::after {
            content:'';
            position:absolute;
            width:140px; height:140px;
            background:rgba(255,255,255,0.04);
            border-radius:50%;
            bottom:-30px; left:20px;
        }

        /* Icon status besar di tengah */
        .status-icon-rahma {
            font-size: 4.5rem;
            margin-bottom: 16px;
            animation: popBounce_rahma 0.5s ease both;
            position: relative;
            z-index: 1;
        }
        .hero-status-rahma h3 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .hero-status-rahma p {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        /* ===== CARD DETAIL TRANSAKSI ===== */
        .content-wrap-rahma {
            margin-top: -30px;
            position: relative;
            z-index: 10;
            padding-bottom: 40px;
        }

        .card-detail-rahma {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            overflow: hidden;
            border: none;
        }

        .detail-row-rahma {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 11px 20px;
            font-size: 0.87rem;
            border-bottom: 1px solid #f5f5f5;
        }
        .detail-row-rahma:last-child { border-bottom: none; }
        .detail-row-rahma .label-rahma { color: #888; }
        .detail-row-rahma .value-rahma { font-weight: 600; color: #333; }

        /* ===== TOMBOL AKSI ===== */
        .btn-struk-rahma {
            background: linear-gradient(90deg, #e0650a, #c2185b);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 13px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            cursor: pointer;
            transition: opacity 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .btn-struk-rahma:hover { opacity: 0.9; color: #fff; }

        .btn-selesai-rahma {
            background: transparent;
            border: 2px solid #e0650a;
            color: #e0650a;
            border-radius: 14px;
            padding: 11px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s, color 0.15s;
        }
        .btn-selesai-rahma:hover { background: #e0650a; color: #fff; }

        /* ===== BADGE METODE BAYAR ===== */
        .badge-metode-rahma {
            background: #f3e5f5;
            color: #6a1b9a;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        /* ===== ANIMASI ===== */
        @keyframes popBounce_rahma {
            0%   { transform: scale(0.5); opacity:0; }
            70%  { transform: scale(1.15); opacity:1; }
            100% { transform: scale(1); }
        }
        @keyframes fadeUp_rahma {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .anim-rahma { animation: fadeUp_rahma 0.4s 0.1s ease both; }
    </style>
</head>
<body>

<div class="flag-stripe-rahma"></div>

<!-- ===== HERO STATUS ===== -->
<div class="hero-status-rahma">
    <?php if ($status_rahma === 'success'): ?>
        <div class="status-icon-rahma">✅</div>
        <h3>Pembayaran Berhasil!</h3>
        <p>Terima kasih <?= htmlspecialchars($transaksi_rahma['nama_pelanggan_rahma']) ?>!<br>
           Pesananmu sedang diproses 🍳</p>
    <?php else: ?>
        <div class="status-icon-rahma">⏳</div>
        <h3>Menunggu Konfirmasi</h3>
        <p>Pembayaranmu sedang diverifikasi.<br>
           Kamu akan dikonfirmasi segera 📨</p>
    <?php endif; ?>
</div>

<!-- ===== KONTEN ===== -->
<div class="container content-wrap-rahma">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card-detail-rahma mb-3 anim-rahma">

                <!-- Header card -->
                <div style="padding:16px 20px; background: linear-gradient(90deg, #fff8f0, #fff0f5);
                            border-bottom: 1px solid #f5e0d0;">
                    <div class="fw-bold" style="color: #e0650a; font-size:0.9rem;">
                        <i class="bi bi-receipt me-2"></i>Detail Transaksi
                    </div>
                </div>

                <!-- Baris detail -->
                <div class="detail-row-rahma">
                    <span class="label-rahma">ID Transaksi</span>
                    <span class="value-rahma"><?= htmlspecialchars($id_transaksi_rahma) ?></span>
                </div>
                <div class="detail-row-rahma">
                    <span class="label-rahma">ID Order</span>
                    <span class="value-rahma"><?= htmlspecialchars($transaksi_rahma['id_order_rahma']) ?></span>
                </div>
                <div class="detail-row-rahma">
                    <span class="label-rahma">Nama</span>
                    <span class="value-rahma"><?= htmlspecialchars($transaksi_rahma['nama_pelanggan_rahma']) ?></span>
                </div>
                <div class="detail-row-rahma">
                    <span class="label-rahma">Total Dibayar</span>
                    <span class="value-rahma" style="color:#c2185b; font-size:1rem;">
                        Rp <?= number_format($transaksi_rahma['total_rahma'], 0, ',', '.') ?>
                    </span>
                </div>
                <div class="detail-row-rahma">
                    <span class="label-rahma">Metode Bayar</span>
                    <span class="badge-metode-rahma">
                        <i class="bi bi-phone me-1"></i>
                        <?= htmlspecialchars(strtoupper($transaksi_rahma['metode_bayar_rahma'] ?? 'Online')) ?>
                    </span>
                </div>
                <div class="detail-row-rahma">
                    <span class="label-rahma">Waktu</span>
                    <span class="value-rahma" style="font-size:0.82rem; color:#777;">
                        <?= date('d/m/Y H:i', strtotime($transaksi_rahma['waktu_transaksi_rahma'])) ?>
                    </span>
                </div>

            </div>

            <!-- Tombol aksi -->
            <a href="../proses/generate_struk_pelanggan_rahma.php?id=<?= urlencode($id_transaksi_rahma) ?>"
            target="_blank"
            class="btn-struk-rahma anim-rahma">
                <i class="bi bi-file-earmark-pdf"></i>
                Unduh / Cetak Struk
            </a>

            <a href="../login_rahma.php" class="btn-selesai-rahma anim-rahma">
                <i class="bi bi-check2-circle"></i>
                Selesai
            </a>

        </div>
    </div>
</div>

</body>
</html>