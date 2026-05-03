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

$id_order_rahma = $_GET['id'] ?? '';

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

// Cek kalau sudah bayar, redirect ke detail
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

// Hitung diskon & pajak
$query_order_diskon_rahma = mysqli_query($koneksiRahma, "
    SELECT id_user_rahma FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'
");
$data_order_diskon_rahma = mysqli_fetch_assoc($query_order_diskon_rahma);
$is_member_rahma = !empty($data_order_diskon_rahma['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$diskon_nominal_rahma = (int) ($grand_total_rahma * $diskon_persen_rahma / 100);
$total_setelah_diskon_rahma = $grand_total_rahma - $diskon_nominal_rahma;
$pajak_nominal_rahma = (int) ($total_setelah_diskon_rahma * 0.11);
$grand_total_order_rahma = $total_setelah_diskon_rahma + $pajak_nominal_rahma;

// Daftar meja untuk dropdown dine in
$query_meja_rahma = mysqli_query($koneksiRahma, "
    SELECT id_meja_rahma FROM tbl_meja_rahma ORDER BY id_meja_rahma ASC
");

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

    <style>
        /* ===== KARTU PILIHAN METODE PEMBAYARAN ===== */
        .metode-card-rahma {
            border: 2px solid #e9ecef;
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s, transform 0.12s;
            display: flex;
            align-items: center;
            gap: 14px;
            user-select: none;
        }

        .metode-card-rahma:hover {
            border-color: var(--orange-rahma);
            background: #fff8f0;
            transform: translateY(-2px);
        }

        /* Style saat kartu dipilih — ditambahkan lewat JS */
        .metode-card-rahma.active-rahma {
            border-color: var(--dark-orange-rahma);
            background: #fff3e6;
        }

        /* Icon bulat di kiri kartu metode */
        .metode-icon-rahma {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .metode-label-rahma {
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 2px;
        }

        .metode-desc-rahma {
            font-size: 0.75rem;
            color: #888;
        }

        /* Centang di kanan kartu saat aktif */
        .metode-check-rahma {
            margin-left: auto;
            font-size: 1.3rem;
            color: var(--dark-orange-rahma);
            display: none;
            flex-shrink: 0;
        }

        .metode-card-rahma.active-rahma .metode-check-rahma {
            display: inline-block;
        }

        /* ===== SECTION CASH (muncul/sembunyi tergantung metode) ===== */
        .section-cash-rahma {
            display: none;
            /* default tersembunyi */
            animation: fadeIn_rahma 0.25s ease;
        }

        /* ===== SECTION NONCASH (QRIS/Debit) ===== */
        .section-noncash-rahma {
            display: none;
            animation: fadeIn_rahma 0.25s ease;
        }

        @keyframes fadeIn_rahma {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== INFO BOX NONCASH ===== */
        .info-noncash-rahma {
            background: linear-gradient(135deg, #fff8f0, #fff0f5);
            border: 1.5px solid var(--orange-rahma);
            border-radius: 14px;
            padding: 18px 20px;
            text-align: center;
        }

        .info-noncash-rahma .amount-rahma {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark-pink-rahma);
            display: block;
            margin: 8px 0 4px;
        }

        .info-noncash-rahma .hint-rahma {
            font-size: 0.8rem;
            color: #888;
        }
    </style>
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
                                    <td colspan="2" class="ps-3 fw-bold">Subtotal</td>
                                    <td class="text-end pe-3 fw-bold">
                                        Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 fw-bold">PPN (11%)</td>
                                    <td class="text-end pe-3 fw-bold">
                                        Rp <?= number_format($pajak_nominal_rahma, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php if ($diskon_nominal_rahma > 0): ?>
                                    <tr class="table-light">
                                        <td colspan="2" class="ps-3 fw-bold text-success">
                                            Diskon Member (<?= $diskon_persen_rahma ?>%)
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-success">
                                            - Rp <?= number_format($diskon_nominal_rahma, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="table-light">
                                    <td colspan="2" class="ps-3 fw-bold">Total Bayar</td>
                                    <td class="text-end pe-3 fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                        Rp <?= number_format($grand_total_order_rahma, 0, ',', '.') ?>
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
                            <small class="text-muted">Jenis Pesanan</small>
                            <span class="badge badge-status-rahma"
                                style="background-color: var(--orange-rahma); color:#fff;">
                                <?php
                                $jenis_rahma = $order_rahma['jenis_pesanan_rahma'] ?? '';
                                if ($jenis_rahma === 'dine in') {
                                    echo '<i class="bi bi-egg-fried me-1"></i>Dine In';
                                } elseif ($jenis_rahma === 'take away') {
                                    echo '<i class="bi bi-bag me-1"></i>Take Away';
                                } else {
                                    echo htmlspecialchars($jenis_rahma ?: '-');
                                }
                                ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Meja</small>
                            <span class="badge badge-status-rahma"
                                style="background-color: var(--orange-rahma); color:#fff;">
                                <?= !empty($order_rahma['id_meja_rahma'])
                                    ? htmlspecialchars($order_rahma['id_meja_rahma'])
                                    : 'Belum ditentukan' ?>
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
                                Rp <?= number_format($grand_total_order_rahma, 0, ',', '.') ?>
                            </div>
                        </div>

                        <form action="../proses/proses_bayar_rahma.php" method="POST">
                            <!-- Data hidden — dikirim ke proses_bayar -->
                            <input type="hidden" name="id_order_rahma" value="<?= $id_order_rahma ?>">
                            <input type="hidden" name="grand_total_rahma" value="<?= $grand_total_rahma ?>">
                            <input type="hidden" name="pajak_rahma" value="<?= $pajak_nominal_rahma ?>">
                            <input type="hidden" name="diskon_rahma" value="<?= $diskon_nominal_rahma ?>">

                            <!-- Hidden: nilai metode yang dipilih — diisi lewat JS -->
                            <input type="hidden" name="metode_bayar_rahma" id="input_metode_rahma" value="">

                            <!-- ===== PILIH NOMOR MEJA (hanya dine in) ===== -->
                            <?php if ($order_rahma['jenis_pesanan_rahma'] === 'dine in'): ?>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-table me-1"></i>Nomor Meja
                                    </label>
                                    <select name="id_meja_rahma" class="form-select" required
                                        style="border-color: var(--orange-rahma); border-width: 1.5px;">
                                        <option value="">-- Pilih Nomor Meja --</option>
                                        <?php while ($row_meja_rahma = mysqli_fetch_assoc($query_meja_rahma)): ?>
                                            <option value="<?= $row_meja_rahma['id_meja_rahma'] ?>">
                                                Meja <?= (int) ltrim($row_meja_rahma['id_meja_rahma'], 'M') ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <small class="text-muted">Pilih meja tempat pelanggan duduk</small>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="id_meja_rahma" value="">
                            <?php endif; ?>

                            <!-- ===== PILIH METODE PEMBAYARAN ===== -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-credit-card me-1"></i>Metode Pembayaran
                                </label>

                                <div class="d-flex flex-column gap-2">

                                    <!-- Cash -->
                                    <div class="metode-card-rahma" id="card-cash-rahma"
                                        onclick="pilihMetode_rahma('cash')">
                                        <div class="metode-icon-rahma" style="background:#e8f5e9; color:#2e7d32;">
                                            <i class="bi bi-cash-coin"></i>
                                        </div>
                                        <div>
                                            <div class="metode-label-rahma">Tunai / Cash</div>
                                            <div class="metode-desc-rahma">Input nominal & hitung kembalian</div>
                                        </div>
                                        <i class="bi bi-check-circle-fill metode-check-rahma"></i>
                                    </div>

                                    <!-- QRIS -->
                                    <div class="metode-card-rahma" id="card-qris-rahma"
                                        onclick="pilihMetode_rahma('qris')">
                                        <div class="metode-icon-rahma" style="background:#e3f2fd; color:#1565c0;">
                                            <i class="bi bi-qr-code-scan"></i>
                                        </div>
                                        <div>
                                            <div class="metode-label-rahma">QRIS</div>
                                            <div class="metode-desc-rahma">Pelanggan scan QR di kasir</div>
                                        </div>
                                        <i class="bi bi-check-circle-fill metode-check-rahma"></i>
                                    </div>

                                    <!-- Debit / Kredit -->
                                    <div class="metode-card-rahma" id="card-debit-rahma"
                                        onclick="pilihMetode_rahma('debit')">
                                        <div class="metode-icon-rahma" style="background:#f3e5f5; color:#6a1b9a;">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </div>
                                        <div>
                                            <div class="metode-label-rahma">Kartu Debit / Kredit</div>
                                            <div class="metode-desc-rahma">Tap atau gesek kartu di mesin EDC</div>
                                        </div>
                                        <i class="bi bi-check-circle-fill metode-check-rahma"></i>
                                    </div>

                                </div>
                            </div>

                            <!-- ===== SECTION CASH: input nominal + kembalian ===== -->
                            <!-- Hanya muncul kalau pilih Cash -->
                            <div class="section-cash-rahma" id="section-cash-rahma">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nominal Bayar</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="bayar_rahma" id="input_bayar_rahma"
                                            class="form-control" placeholder="Masukkan nominal bayar"
                                            min="<?= $grand_total_order_rahma ?>" oninput="hitungKembalian_rahma()">
                                    </div>
                                    <small class="text-muted">
                                        Minimal Rp <?= number_format($grand_total_order_rahma, 0, ',', '.') ?>
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Kembalian</label>
                                    <div class="p-3 rounded-3 text-center"
                                        style="background: rgba(209,97,162,0.1); border: 1.5px solid var(--pink-rahma);">
                                        <div id="hasil_kembalian_rahma" class="fw-bold fs-4"
                                            style="color: var(--dark-pink-rahma);">
                                            Rp 0
                                        </div>
                                    </div>
                                    <input type="hidden" name="kembalian_rahma" id="input_kembalian_rahma" value="0">
                                </div>

                            </div>

                            <!-- ===== SECTION NONCASH: konfirmasi langsung ===== -->
                            <!-- Muncul kalau pilih QRIS atau Debit -->
                            <div class="section-noncash-rahma" id="section-noncash-rahma">

                                <div class="info-noncash-rahma mb-4">
                                    <div class="text-muted small">Total yang akan ditagihkan</div>
                                    <span class="amount-rahma">
                                        Rp <?= number_format($grand_total_order_rahma, 0, ',', '.') ?>
                                    </span>
                                    <div class="hint-rahma" id="hint-noncash-rahma">
                                        Pastikan pelanggan sudah melakukan pembayaran sebelum klik konfirmasi
                                    </div>
                                </div>

                                <!-- Untuk noncash: bayar = total, kembalian = 0 — diisi JS -->
                                <input type="hidden" name="kembalian_rahma" id="input_kembalian_noncash_rahma"
                                    value="0">

                            </div>

                            <!-- Tombol konfirmasi — disabled sampai metode dipilih -->
                            <button type="submit" id="btn_bayar_rahma" class="btn btn-bayar-rahma w-100 py-2" disabled>
                                <i class="bi bi-check-circle me-2"></i>Konfirmasi Pembayaran
                            </button>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Total akhir dari PHP — untuk hitung kembalian
        const grandTotal_rahma = <?= $grand_total_order_rahma ?>;

        // Metode yang sedang aktif
        let metodeAktif_rahma = '';

        // =====================================================
        // Fungsi: user klik salah satu kartu metode
        // Kayak kasir milih "laci mana yang dibuka" dulu
        // sebelum proses transaksi
        // =====================================================
        function pilihMetode_rahma(metode_rahma) {
            metodeAktif_rahma = metode_rahma;

            // 1. Reset visual kartu (Tambahkan ini supaya kartu berubah warna saat diklik)
            ['cash', 'qris', 'debit'].forEach(m => {
                document.getElementById('card-' + m + '-rahma').classList.remove('active-rahma');
            });
            document.getElementById('card-' + metode_rahma + '-rahma').classList.add('active-rahma');

            // 2. Simpan nilai metode ke hidden input
            document.getElementById('input_metode_rahma').value = metode_rahma;

            const inputBayar_rahma = document.getElementById('input_bayar_rahma');
            const btnBayar_rahma = document.getElementById('btn_bayar_rahma');

            if (metode_rahma === 'cash') {
                document.getElementById('section-cash-rahma').style.display = 'block';
                document.getElementById('section-noncash-rahma').style.display = 'none';

                // JANGAN isi otomatis, biarkan kosong agar kasir input manual
                inputBayar_rahma.value = '';
                inputBayar_rahma.readOnly = false; // Pastikan bisa diketik
                btnBayar_rahma.disabled = true;

                // Reset tampilan kembalian
                document.getElementById('hasil_kembalian_rahma').textContent = 'Rp 0';
            } else {
                document.getElementById('section-cash-rahma').style.display = 'none';
                document.getElementById('section-noncash-rahma').style.display = 'block';

                // Kalau QRIS/Debit, baru kita isi otomatis seharga total
                inputBayar_rahma.value = grandTotal_rahma;
                inputBayar_rahma.readOnly = true; // Kunci biar nggak diubah-ubah
                btnBayar_rahma.disabled = false;
            }
        }

        // =====================================================
        // Fungsi: hitung kembalian realtime (khusus cash)
        // Kayak kalkulator kasir otomatis
        // =====================================================
        function hitungKembalian_rahma() {
            // Hanya aktif kalau metode cash
            if (metodeAktif_rahma !== 'cash') return;

            const bayar_rahma = parseInt(document.getElementById('input_bayar_rahma').value) || 0;
            const kembalian_rahma = bayar_rahma - grandTotal_rahma;
            const btnBayar_rahma = document.getElementById('btn_bayar_rahma');
            const hasilEl_rahma = document.getElementById('hasil_kembalian_rahma');

            if (kembalian_rahma >= 0) {
                // Nominal cukup — tampilkan kembalian dan aktifkan tombol
                hasilEl_rahma.textContent = 'Rp ' + kembalian_rahma.toLocaleString('id-ID');
                hasilEl_rahma.style.color = 'var(--dark-pink-rahma)';
                document.getElementById('input_kembalian_rahma').value = kembalian_rahma;
                btnBayar_rahma.disabled = false;
            } else {
                // Nominal kurang
                hasilEl_rahma.textContent = 'Nominal kurang!';
                hasilEl_rahma.style.color = 'var(--dark-orange-rahma)';
                document.getElementById('input_kembalian_rahma').value = 0;
                btnBayar_rahma.disabled = true;
            }
        }

        window.addEventListener("pageshow", function (event) {
            if (event.persisted) window.location.reload();
        });
    </script>

</body>

</html>