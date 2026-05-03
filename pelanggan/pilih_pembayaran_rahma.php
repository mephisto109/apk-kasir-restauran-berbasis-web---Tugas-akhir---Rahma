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

// Hanya role pelanggan yang boleh akses
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

// Ambil data order + detail item
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

// Hitung ulang total dengan diskon & pajak
$subtotal_rahma = $order_rahma['grand_total_rahma'];
$is_member_rahma = !empty($order_rahma['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = ($subtotal_rahma * $diskon_persen_rahma) / 100;
$setelah_diskon_rahma = $subtotal_rahma - $nominal_diskon_rahma;
$pajak_nominal_rahma = $setelah_diskon_rahma * 0.11;
$total_bayar_rahma = $setelah_diskon_rahma + $pajak_nominal_rahma;

include '../templates/navbar_rahma.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Cara Bayar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global_rahma.css">

    <style>
        :root {
            --orange-rahma: #fd9855;
            --dark-orange-rahma: #e0650a;
            --pink-rahma: #f06292;
            --dark-pink-rahma: #c2185b;
        }

        body {
            background: #fafafa;
            min-height: 100vh;
        }

        .page-header-pilih-rahma {
            background: linear-gradient(135deg, var(--dark-orange-rahma) 0%, var(--dark-pink-rahma) 100%);
            padding: 28px 24px 60px;
            position: relative;
            overflow: hidden;
        }

        .page-header-pilih-rahma::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50%;
            top: -60px;
            right: -40px;
        }

        .page-header-pilih-rahma h4 {
            color: #fff;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .page-header-pilih-rahma p {
            color: rgba(255, 255, 255, 0.82);
            font-size: 0.88rem;
            position: relative;
            z-index: 1;
        }

        .total-badge-rahma {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            padding: 10px 18px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            position: relative;
            z-index: 1;
        }

        .total-badge-rahma .amount-rahma {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .content-wrapper-rahma {
            margin-top: -36px;
            position: relative;
            z-index: 10;
            padding-bottom: 40px;
        }

        /* Kartu pilihan bayar */
        .card-pilihan-rahma {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);
            overflow: hidden;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.18s ease;
            text-decoration: none;
            display: block;
        }

        .card-pilihan-rahma:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.14);
            text-decoration: none;
        }

        .card-pilihan-inner-rahma {
            display: flex;
            align-items: center;
            padding: 22px 20px;
            gap: 18px;
        }

        .card-icon-rahma {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .card-icon-kasir-rahma {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: var(--dark-orange-rahma);
        }

        .card-icon-online-rahma {
            background: linear-gradient(135deg, #fce4ec, #f8bbd9);
            color: var(--dark-pink-rahma);
        }

        .card-pilihan-text-rahma h5 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: #222;
        }

        .card-pilihan-text-rahma p {
            font-size: 0.82rem;
            color: #888;
            margin-bottom: 0;
            line-height: 1.45;
        }

        .card-chevron-rahma {
            margin-left: auto;
            color: #ccc;
            font-size: 1.2rem;
            transition: color 0.15s;
        }

        .card-pilihan-rahma:hover .card-chevron-rahma {
            color: var(--dark-orange-rahma);
        }

        .card-pilihan-kasir-rahma {
            border-left: 5px solid var(--dark-orange-rahma) !important;
        }

        .card-pilihan-online-rahma {
            border-left: 5px solid var(--dark-pink-rahma) !important;
        }

        .metode-badges-rahma {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .badge-midtrans-rahma {
            background: linear-gradient(90deg, #e3f2fd, #fce4ec);
            color: var(--dark-pink-rahma);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* Card ringkasan order */
        .card-ringkasan-rahma {
            background: #fff;
            border-radius: 18px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .ringkasan-header-rahma {
            background: linear-gradient(90deg, #fff8f0, #fff0f5);
            padding: 14px 18px;
            border-bottom: 1px solid #f5e0d0;
            font-weight: 600;
            color: var(--dark-orange-rahma);
        }

        .ringkasan-row-rahma {
            display: flex;
            justify-content: space-between;
            padding: 10px 18px;
            font-size: 0.85rem;
            color: #555;
            border-bottom: 1px solid #fafafa;
        }

        .ringkasan-total-rahma {
            background: linear-gradient(90deg, #fff8f0, #fff0f5);
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #f5e0d0;
        }

        .ringkasan-total-rahma .amount-rahma {
            font-weight: 800;
            color: var(--dark-pink-rahma);
            font-size: 1.1rem;
        }

        .order-id-chip-rahma {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3e0;
            border: 1.5px solid #ffcc80;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--dark-orange-rahma);
            margin-bottom: 12px;
        }

        @keyframes fadeSlideUp_rahma {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim-1-rahma {
            animation: fadeSlideUp_rahma 0.4s ease both;
        }

        .anim-2-rahma {
            animation: fadeSlideUp_rahma 0.4s 0.1s ease both;
        }

        .anim-3-rahma {
            animation: fadeSlideUp_rahma 0.4s 0.2s ease both;
        }

        .anim-4-rahma {
            animation: fadeSlideUp_rahma 0.4s 0.3s ease both;
        }
    </style>
</head>

<body>
    <div class="flag-stripe-rahma"></div>

    <!-- Header gradien -->
    <div class="page-header-pilih-rahma">
        <div class="container">
            <h4><i class="bi bi-wallet2 me-2"></i>Pilih Cara Bayar</h4>
            <p>Pesanan kamu sudah masuk! Sekarang pilih metode pembayarannya yaa!</p>
            <div class="total-badge-rahma">
                <i class="bi bi-tag-fill" style="color: rgba(255,255,255,0.7);"></i>
                <div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 0.8rem;">Total Bayar</div>
                    <div class="amount-rahma">Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container content-wrapper-rahma">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">

                <!-- Chip ID Order -->
                <div class="order-id-chip-rahma anim-1-rahma">
                    <i class="bi bi-receipt"></i> Order: <?= htmlspecialchars($id_order_rahma) ?>
                </div>

                <!-- Kartu 1: Bayar di Kasir — langsung cetak struk -->
                <div class="card-pilihan-rahma card-pilihan-kasir-rahma anim-2-rahma" onclick="aksiCetakKasir_rahma()">
                    <div class="card-pilihan-inner-rahma">
                        <div class="card-icon-rahma card-icon-kasir-rahma">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="card-pilihan-text-rahma">
                            <h5>Bayar di Kasir</h5>
                            <p>Ambil struk pesanan & bawa ke kasir untuk pembayaran</p>
                        </div>
                        <i class="bi bi-printer card-chevron-rahma"></i>
                    </div>
                </div>

                <!-- Kartu 2: Bayar Online — langsung ke Midtrans tanpa modal -->
                <a href="bayar_online_rahma.php?id_order=<?= urlencode($id_order_rahma) ?>"
                    class="card-pilihan-rahma card-pilihan-online-rahma anim-3-rahma">
                    <div class="card-pilihan-inner-rahma">
                        <div class="card-icon-rahma card-icon-online-rahma">
                            <i class="bi bi-phone"></i>
                        </div>
                        <div class="card-pilihan-text-rahma">
                            <h5>Bayar di Sini (Online)</h5>
                            <p>Pilih metode bayar langsung: QRIS, Transfer, atau E-Wallet</p>
                            <div class="metode-badges-rahma">
                                <span class="badge-midtrans-rahma">⚡ Powered by Midtrans</span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right card-chevron-rahma"></i>
                    </div>
                </a>

                <!-- Ringkasan order -->
                <div class="card-ringkasan-rahma anim-4-rahma">
                    <div class="ringkasan-header-rahma">
                        <i class="bi bi-list-check me-2"></i>Ringkasan
                    </div>
                    <div class="ringkasan-row-rahma">
                        <span>Subtotal</span>
                        <span>Rp <?= number_format($subtotal_rahma, 0, ',', '.') ?></span>
                    </div>
                    <?php if ($is_member_rahma): ?>
                        <div class="ringkasan-row-rahma">
                            <span style="color: var(--pink-rahma);">Diskon Member</span>
                            <span style="color: var(--pink-rahma);">
                                - Rp <?= number_format($nominal_diskon_rahma, 0, ',', '.') ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="ringkasan-row-rahma">
                        <span>PPN (11%)</span>
                        <span>Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?></span>
                    </div>
                    <div class="ringkasan-total-rahma">
                        <strong>Total Akhir</strong>
                        <span class="amount-rahma">Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?></span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Popup sukses setelah cetak struk kasir -->
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
        <div style="font-size: 5rem; margin-bottom: 20px;">🧾</div>
        <h3 style="color: #fff; font-weight: 700;">Struk Siap!</h3>
        <p style="color: rgba(255,255,255,0.85);">
            Silakan bawa struk ke kasir untuk proses pembayaran.
        </p>
        <div style="width:280px; height:5px; background:rgba(255,255,255,0.3); border-radius:99px; overflow:hidden;">
            <div id="bar-countdown-rahma" style="height:100%; width:100%; background:#fff; transition:width 5s linear;">
            </div>
        </div>
        <small style="color: rgba(255,255,255,0.7); margin-top: 10px; font-size: 0.8rem;">
            Mengalihkan ke halaman login...
        </small>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cetak struk kasir di tab baru, lalu tampilkan popup countdown
        function aksiCetakKasir_rahma() {
            const id_rahma = '<?= htmlspecialchars($id_order_rahma) ?>';

            // Buka struk di tab baru
            window.open('cetak_struk_pesanan_rahma.php?id_order=' + id_rahma, '_blank');

            // Tampilkan popup sukses
            document.getElementById('popup-sukses-rahma').style.display = 'flex';

            // Jalankan bar countdown
            setTimeout(() => {
                document.getElementById('bar-countdown-rahma').style.width = '0%';
            }, 100);

            // Redirect ke login setelah 5 detik
            setTimeout(() => {
                window.location.href = '../login_rahma.php';
            }, 5000);
        }
    </script>
</body>

</html>