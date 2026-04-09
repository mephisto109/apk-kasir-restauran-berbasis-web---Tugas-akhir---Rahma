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

// Ambil keranjang dari session
$keranjang_rahma = $_SESSION['keranjang_rahma'] ?? [];
$id_meja_rahma = $_SESSION['id_meja_rahma'] ?? '';
$nomor_meja_rahma = (int) ltrim($id_meja_rahma, 'M');

// Hitung grand total keranjang
$grand_total_rahma = 0;
foreach ($keranjang_rahma as $item_rahma) {
    $grand_total_rahma += $item_rahma['harga_rahma'] * $item_rahma['qty_rahma'];
}

// Cek apakah member — untuk tampilkan info diskon
$is_member_rahma = isset($_SESSION['id_user_rahma']);
$diskon_persen_rahma = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = ($grand_total_rahma * $diskon_persen_rahma) / 100;
$total_bayar_rahma = $grand_total_rahma - $nominal_diskon_rahma;

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
    <title>Keranjang</title>
</head>

<body>
    <div class="flag-stripe-rahma"></div>
    <div class="container mt-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-semibold mb-0" style="color: var(--dark-orange-rahma);">
                    <i class="bi bi-cart3 me-2"></i>Keranjang
                </h5>
                <small class="text-muted">
                    <i class="bi bi-table me-1"></i>Meja <?= $nomor_meja_rahma ?>
                </small>
            </div>
            <!-- Tombol balik ke menu -->
            <a href="menu_rahma.php?meja=<?= $id_meja_rahma ?>" class="btn btn-sm"
                style="border: 1.5px solid var(--dark-orange-rahma); color: var(--dark-orange-rahma); border-radius: 8px;">
                <i class="bi bi-arrow-left me-1"></i>Tambah Menu
            </a>
        </div>

        <?php if (count($keranjang_rahma) > 0): ?>

            <div class="row g-4">

                <!-- ===== KOLOM KIRI: LIST ITEM ===== -->
                <div class="col-md-7">
                    <div class="card card-table-rahma">
                        <div class="card-header card-header-rahma py-3">
                            <h6 class="mb-0 fw-semibold text-white">
                                <i class="bi bi-list-ul me-2"></i>Item Pesanan
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($keranjang_rahma as $id_menu_rahma => $item_rahma): ?>
                                <div class="item-keranjang-rahma p-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <!-- Nama menu -->
                                            <div class="fw-semibold" style="color: var(--dark-orange-rahma);">
                                                <?= htmlspecialchars($item_rahma['nama_menu_rahma']) ?>
                                            </div>
                                            <!-- Harga satuan -->
                                            <small class="text-muted">
                                                Rp <?= number_format($item_rahma['harga_rahma'], 0, ',', '.') ?> / item
                                            </small>
                                        </div>

                                        <!-- Tombol hapus item -->
                                        <a href="../proses/proses_keranjang_rahma.php?aksi=hapus&id=<?= $id_menu_rahma ?>"
                                            class="btn-hapus-keranjang-rahma ms-2"
                                            onclick="return confirm('Hapus item ini dari keranjang?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </div>

                                    <!-- Qty + subtotal -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <!-- Form update qty -->
                                        <form action="../proses/proses_keranjang_rahma.php" method="POST"
                                            class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="aksi_rahma" value="update">
                                            <input type="hidden" name="id_menu_rahma" value="<?= $id_menu_rahma ?>">
                                            <div class="input-qty-rahma">
                                                <button type="button" class="btn-qty-rahma"
                                                    onclick="kurang_rahma(this)">−</button>
                                                <input type="number" name="qty_rahma" value="<?= $item_rahma['qty_rahma'] ?>"
                                                    min="1" max="99" class="input-angka-rahma" onchange="this.form.submit()">
                                                <button type="button" class="btn-qty-rahma"
                                                    onclick="tambah_rahma(this)">+</button>
                                            </div>
                                        </form>

                                        <!-- Subtotal per item -->
                                        <div class="fw-bold" style="color: var(--dark-pink-rahma);">
                                            Rp
                                            <?= number_format($item_rahma['harga_rahma'] * $item_rahma['qty_rahma'], 0, ',', '.') ?>
                                        </div>
                                    </div>

                                    <!-- Input catatan per item -->
                                    <div class="mt-2">
                                        <form action="../proses/proses_keranjang_rahma.php" method="POST">
                                            <input type="hidden" name="aksi_rahma" value="catatan">
                                            <input type="hidden" name="id_menu_rahma" value="<?= $id_menu_rahma ?>">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="catatan_rahma" class="form-control form-control-sm"
                                                    placeholder="Catatan (opsional)"
                                                    value="<?= htmlspecialchars($item_rahma['catatan_rahma'] ?? '') ?>"
                                                    style="border-color: var(--orange-rahma); font-size: 0.8rem;">
                                                <button type="submit" class="btn btn-sm"
                                                    style="background-color: var(--orange-rahma); color:#fff; border:none;">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ===== KOLOM KANAN: RINGKASAN ===== -->
                <div class="col-md-5">
                    <div class="card card-table-rahma">
                        <div class="card-header card-header-rahma py-3">
                            <h6 class="mb-0 fw-semibold text-white">
                                <i class="bi bi-receipt me-2"></i>Ringkasan
                            </h6>
                        </div>
                        <div class="card-body">

                            <!-- Subtotal -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rp <?= number_format($grand_total_rahma, 0, ',', '.') ?></span>
                            </div>

                            <!-- Diskon member -->
                            <?php if ($is_member_rahma): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">
                                        Diskon Member
                                        <span class="badge"
                                            style="background-color: var(--dark-pink-rahma); color:#fff; font-size:0.7rem;">
                                            <?= $diskon_persen_rahma ?>%
                                        </span>
                                    </span>
                                    <span style="color: var(--dark-pink-rahma);">
                                        - Rp <?= number_format($nominal_diskon_rahma, 0, ',', '.') ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <!-- Info kalau guest — ajak jadi member -->
                                <div class="p-2 rounded-3 mb-2"
                                    style="background-color: rgba(253,152,85,0.1); border: 1px solid var(--orange-rahma);">
                                    <small style="color: var(--dark-orange-rahma);">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Daftar member untuk dapat diskon
                                        <?= $diskon_persen_rahma == 0 ? '10' : $diskon_persen_rahma ?>%!
                                    </small>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <!-- Total bayar -->
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Total Bayar</span>
                                <span class="fw-bold fs-5" style="color: var(--dark-pink-rahma);">
                                    Rp <?= number_format($total_bayar_rahma, 0, ',', '.') ?>
                                </span>
                            </div>

                            <!-- Tombol konfirmasi -->
                            <a href="konfirmasi_rahma.php"
                                class="btn-tambah-rahma w-100 d-block text-center text-decoration-none py-2">
                                <i class="bi bi-check-circle me-2"></i>Konfirmasi Pesanan
                            </a>

                        </div>
                    </div>
                </div>

            </div>

        <?php else: ?>
            <!-- Keranjang kosong -->
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 d-block mb-3" style="color: var(--orange-rahma);"></i>
                <h6 class="fw-semibold" style="color: var(--dark-orange-rahma);">
                    Keranjang masih kosong!
                </h6>
                <p class="text-muted mb-4">Yuk pilih menu dulu 😋</p>
                <a href="menu_rahma.php?meja=<?= $id_meja_rahma ?>" class="btn-tambah-rahma px-4 py-2 text-decoration-none">
                    <i class="bi bi-egg-fried me-2"></i>Lihat Menu
                </a>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tombol kurang qty
        function kurang_rahma(btn) {
            const input_rahma = btn.nextElementSibling;
            if (parseInt(input_rahma.value) > 1) {
                input_rahma.value = parseInt(input_rahma.value) - 1;
                input_rahma.dispatchEvent(new Event('change'));
            }
        }

        // Tombol tambah qty
        function tambah_rahma(btn) {
            const input_rahma = btn.previousElementSibling;
            if (parseInt(input_rahma.value) < 99) {
                input_rahma.value = parseInt(input_rahma.value) + 1;
                input_rahma.dispatchEvent(new Event('change'));
            }
        }
    </script>
</body>

</html>