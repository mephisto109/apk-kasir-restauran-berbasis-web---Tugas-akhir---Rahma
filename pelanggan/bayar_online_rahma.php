<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — member atau guest
if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if (isset($_SESSION['id_role_rahma']) && $_SESSION['id_role_rahma'] !== 'R003') {
    header("Location: ../login_rahma.php");
    exit;
}

// Ambil id_order dari URL
$id_order_rahma = $_GET['id_order'] ?? '';
if (empty($id_order_rahma)) {
    header("Location: menu_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../config/midtrans_config_rahma.php';

// Ambil data order + total dari DB
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT o.*, 
    COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.id_order_rahma = '$id_order_rahma'
    GROUP BY o.id_order_rahma
");
$order_rahma = mysqli_fetch_assoc($query_order_rahma);

if (!$order_rahma) {
    header("Location: menu_rahma.php");
    exit;
}

// Hitung total akhir dengan diskon & pajak
$subtotal_rahma = $order_rahma['grand_total_rahma'];
$is_member_rahma = !empty($order_rahma['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$diskon_nominal_rahma = ($subtotal_rahma * $diskon_persen_rahma) / 100;
$setelah_diskon_rahma = $subtotal_rahma - $diskon_nominal_rahma;
$pajak_nominal_rahma = $setelah_diskon_rahma * 0.11;
$total_bayar_rahma = (int) round($setelah_diskon_rahma + $pajak_nominal_rahma);

include '../templates/navbar_rahma.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">

    <style>
        body {
            background: #fafafa;
        }

        /* ===== HEADER GRADIEN ===== */
        .header-bayar-rahma {
            background: linear-gradient(135deg, var(--dark-orange-rahma, #e0650a) 0%, var(--dark-pink-rahma, #c2185b) 100%);
            padding: 28px 24px 60px;
            position: relative;
            overflow: hidden;
        }

        .header-bayar-rahma::before {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50%;
            top: -50px;
            right: -30px;
        }

        .header-bayar-rahma h4 {
            color: #fff;
            font-weight: 700;
            position: relative;
            z-index: 1;
            margin-bottom: 4px;
        }

        .header-bayar-rahma p {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.88rem;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }

        /* ===== CARD UTAMA (overlap header) ===== */
        .content-wrap-rahma {
            margin-top: -36px;
            position: relative;
            z-index: 10;
            padding-bottom: 40px;
        }

        .card-bayar-rahma {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);
            overflow: hidden;
        }

        /* ===== TOMBOL BAYAR ===== */
        .btn-pay-rahma {
            background: linear-gradient(90deg, #e0650a, #c2185b);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-pay-rahma:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-pay-rahma:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* ===== TOMBOL KEMBALI ===== */
        .btn-back-rahma {
            background: transparent;
            border: 2px solid #e0650a;
            color: #e0650a;
            border-radius: 14px;
            padding: 11px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-back-rahma:hover {
            background: #e0650a;
            color: #fff;
        }

        /* ===== BARIS RINCIAN HARGA ===== */
        .price-row-rahma {
            display: flex;
            justify-content: space-between;
            padding: 9px 0;
            font-size: 0.87rem;
            color: #555;
            border-bottom: 1px solid #f5f5f5;
        }

        .price-row-rahma:last-child {
            border-bottom: none;
        }

        .price-total-rahma {
            display: flex;
            justify-content: space-between;
            padding: 12px 0 0;
            font-weight: 800;
        }

        .price-total-rahma .label-rahma {
            color: #333;
            font-size: 1rem;
        }

        .price-total-rahma .amount-rahma {
            color: #c2185b;
            font-size: 1.15rem;
        }

        /* ===== BADGE MIDTRANS ===== */
        .midtrans-badge-rahma {
            background: linear-gradient(90deg, #e3f2fd, #fce4ec);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
            color: #555;
            margin-bottom: 20px;
        }

        .midtrans-badge-rahma strong {
            color: #c2185b;
        }

        /* ===== STATUS LOADING ===== */
        .loading-state-rahma {
            display: none;
            text-align: center;
            padding: 20px 0;
        }

        /* ===== ANIMASI ===== */
        @keyframes fadeUp_rahma {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-rahma {
            animation: fadeUp_rahma 0.4s ease both;
        }

        .anim-d1-rahma {
            animation-delay: 0.05s;
        }

        .anim-d2-rahma {
            animation-delay: 0.12s;
        }

        .anim-d3-rahma {
            animation-delay: 0.2s;
        }
    </style>
</head>

<body>

    <div class="flag-stripe-rahma"></div>

    <!-- Header -->
    <div class="header-bayar-rahma">
        <div class="container">
            <h4><i class="bi bi-phone me-2"></i>Bayar di Sini</h4>
            <p>Selesaikan pembayaran sekarang — cepat, mudah, aman ✨</p>
        </div>
    </div>

    <!-- Konten utama -->
    <div class="container content-wrap-rahma">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- ===== CARD RINCIAN & TOMBOL BAYAR ===== -->
                <div class="card-bayar-rahma p-4 mb-3 anim-rahma">

                    <!-- Info order ID -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="background:#fff3e0; border:1.5px solid #ffcc80; border-radius:20px;
                                padding:4px 12px; font-size:0.78rem; font-weight:600; color:#e0650a;">
                            <i class="bi bi-receipt me-1"></i><?= htmlspecialchars($id_order_rahma) ?>
                        </span>
                        <span style="background:#f5f5f5; border-radius:20px;
                                padding:4px 12px; font-size:0.78rem; color:#777;">
                            <?= $order_rahma['jenis_pesanan_rahma'] === 'take away' ? '🛍 Take Away' : '🍽 Dine In' ?>
                        </span>
                    </div>

                    <!-- Rincian harga -->
                    <div class="mb-3">
                        <div class="price-row-rahma">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($subtotal_rahma, 0, ',', '.') ?></span>
                        </div>
                        <?php if ($is_member_rahma): ?>
                            <div class="price-row-rahma" style="color: #c2185b;">
                                <span>Diskon Member (<?= $diskon_persen_rahma ?>%)</span>
                                <span>- Rp <?= number_format($diskon_nominal_rahma, 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="price-row-rahma">
                            <span>PPN (11%)</span>
                            <span>Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?></span>
                        </div>
                        <div class="price-total-rahma">
                            <span class="label-rahma">Total Bayar</span>
                            <span class="amount-rahma">Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <hr style="border-color: #f0f0f0;">

                    <!-- Badge powered by Midtrans -->
                    <div class="midtrans-badge-rahma">
                        <i class="bi bi-shield-check fs-5" style="color: #c2185b;"></i>
                        <div>
                            Pembayaran diproses oleh <strong>Midtrans</strong><br>
                            <span style="font-size:0.75rem; color:#888;">
                                Transfer Bank · QRIS · GoPay · OVO · ShopeePay · dan lainnya
                            </span>
                        </div>
                    </div>

                    <!-- Loading state — muncul saat nunggu Snap Token -->
                    <div class="loading-state-rahma" id="loading-pay-rahma">
                        <div class="spinner-border mb-2" style="color: #e0650a;"></div>
                        <p class="text-muted small">Menyiapkan pembayaran...</p>
                    </div>

                    <!-- Tombol bayar utama -->
                    <button class="btn-pay-rahma mb-3" id="btn-pay-rahma" onclick="mulaiPembayaran_rahma()">
                        <i class="bi bi-lightning-charge-fill"></i>
                        Bayar Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?>
                    </button>

                    <!-- Tombol kembali ke pilih pembayaran -->
                    <a href="pilih_pembayaran_rahma.php?id_order=<?= urlencode($id_order_rahma) ?>"
                        class="btn-back-rahma">
                        <i class="bi bi-arrow-left"></i> Ganti Cara Bayar
                    </a>

                </div>

            </div>
        </div>
    </div>

    <!--
    Midtrans Snap.js — WAJIB ADA
    Untuk production: ganti ke https://app.midtrans.com/snap/snap.js
    Untuk sandbox:    tetap pakai https://app.sandbox.midtrans.com/snap/snap.js
-->
    
    <script>
        // Fungsi utama untuk mulai pembayaran
        function mulaiPembayaran_rahma() {
            document.getElementById('btn-pay-rahma').style.display = 'none';
            document.getElementById('loading-pay-rahma').style.display = 'block';

            // Minta Snap Token ke server (proses_midtrans_rahma.php)
            fetch('../proses/proses_midtrans_rahma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_order_rahma: '<?= htmlspecialchars($id_order_rahma) ?>',
                    total_rahma: <?= $total_bayar_rahma ?>
                })
            })
            // Terima token, lalu panggil Snap.js untuk buka popup pembayaran
                .then(res_rahma => res_rahma.json())
                .then(data_rahma => {
                    if (!data_rahma.snap_token_rahma) {
                        throw new Error(data_rahma.error_rahma || 'Gagal mendapatkan token');
                    }
                    document.getElementById('loading-pay-rahma').style.display = 'none';
                    snap.pay(data_rahma.snap_token_rahma, {
                        onSuccess: function (result_rahma) {
                            catatTransaksi_rahma(result_rahma, 'success');
                        },
                        onPending: function (result_rahma) {
                            catatTransaksi_rahma(result_rahma, 'pending');
                        },
                        onError: function (result_rahma) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                            document.getElementById('btn-pay-rahma').style.display = 'flex';
                            console.error('Midtrans error:', result_rahma);
                        },
                        onClose: function () {
                            document.getElementById('btn-pay-rahma').style.display = 'flex';
                        }
                    });
                })
                // Tangani error saat minta token
                .catch(err_rahma => {
                    document.getElementById('loading-pay-rahma').style.display = 'none';
                    document.getElementById('btn-pay-rahma').style.display = 'flex';
                    alert('Gagal memproses pembayaran: ' + err_rahma.message);
                    console.error(err_rahma);
                });
        }

        // Fungsi untuk catat transaksi ke database setelah pembayaran (proses/catat_transaksi_midtrans_rahma.php)
        function catatTransaksi_rahma(result_rahma, status_rahma) {
            fetch('../proses/catat_transaksi_midtrans_rahma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_order_rahma: '<?= htmlspecialchars($id_order_rahma) ?>',
                    total_rahma: <?= $total_bayar_rahma ?>,
                    pajak_rahma: <?= round($pajak_nominal_rahma) ?>,
                    diskon_rahma: <?= $diskon_persen_rahma ?>,
                    midtrans_order_id_rahma: result_rahma.order_id,
                    payment_type_rahma: result_rahma.payment_type,
                    transaction_id_rahma: result_rahma.transaction_id,
                    status_rahma: status_rahma
                })
            })
            // Setelah server catat transaksi, redirect ke halaman sukses dengan id_transaksi di URL
                .then(res_rahma => res_rahma.json())
                .then(data_rahma => {
                    if (data_rahma.id_transaksi_rahma) {
                        window.location.href = 'sukses_bayar_rahma.php?id=' + data_rahma.id_transaksi_rahma + '&status=' + status_rahma;
                    } else {
                        alert('Pembayaran diterima, tapi gagal mencatat. Hubungi kasir.');
                    }
                })
                // Tangani error saat catat transaksi
                .catch(err_rahma => {
                    alert('Pembayaran diterima, tapi gagal mencatat. Hubungi kasir.');
                    console.error(err_rahma);
                });
        }
    </script>

<!-- Skrip Midtrans Snap.js — token di-generate di proses_midtrans_rahma.php -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?= defined('MIDTRANS_CLIENT_KEY') ? htmlspecialchars(MIDTRANS_CLIENT_KEY) : '' ?>">
        </script>

</body>

</html>