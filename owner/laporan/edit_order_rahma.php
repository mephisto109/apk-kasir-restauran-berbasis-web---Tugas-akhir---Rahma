<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login — hanya owner (R001)
if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../../login_rahma.php");
    exit;
}
if ($_SESSION['id_role_rahma'] !== 'R001') {
    header("Location: ../../login_rahma.php");
    exit;
}

include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php';

// Ambil ID order dari URL
$id_order_rahma = $_GET['id'] ?? '';
if (!$id_order_rahma) {
    echo "ID Order tidak ditemukan";
    exit;
}

// Ambil data order
$data_order_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'"
));
if (!$data_order_rahma) {
    echo "Data order tidak ditemukan";
    exit;
}

// Ambil data transaksi (untuk diskon & pajak)
$data_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_transaksi_rahma WHERE id_order_rahma = '$id_order_rahma'"
));

// Ambil detail order yang sekarang
$detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, m.nama_menu_rahma, m.harga_rahma
    FROM tbl_detail_order_rahma d
    JOIN tbl_menu_rahma m ON d.id_menu_rahma = m.id_menu_rahma
    WHERE d.id_order_rahma = '$id_order_rahma'
");
$detail_list_rahma = [];
while ($row_rahma = mysqli_fetch_assoc($detail_rahma)) {
    $detail_list_rahma[] = $row_rahma;
}

// Ambil semua menu yang tersedia — buat dropdown tambah menu
$semua_menu_rahma = mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_menu_rahma WHERE status_menu_rahma = 'tersedia' ORDER BY kategori_rahma, nama_menu_rahma"
);
$list_menu_rahma = [];
while ($m_rahma = mysqli_fetch_assoc($semua_menu_rahma)) {
    $list_menu_rahma[] = $m_rahma;
}

// Ambil pesan dari redirect (sukses/gagal)
$pesan_sukses_rahma = $_GET['sukses'] ?? '';
$pesan_gagal_rahma = $_GET['gagal'] ?? '';

// Hitung nilai diskon dan pajak dari transaksi yang ada
$diskon_persen_rahma = $data_transaksi_rahma['diskon_rahma'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global_rahma.css">
    <link rel="stylesheet" href="../../assets/css/owner_rahma.css">

    <style>
        /* Card utama halaman edit */
        .card-edit-order-rahma {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(212, 44, 0, 0.10);
            overflow: hidden;
        }

        /* Baris item yang mau dihapus — dikasih warna merah tipis */
        .baris-hapus-rahma {
            background-color: #fff0f0 !important;
            transition: background-color 0.3s;
        }

        /* Input qty di tabel edit */
        .input-qty-edit-rahma {
            width: 70px;
            text-align: center;
            border: 1.5px solid #e8d0c8;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .input-qty-edit-rahma:focus {
            border-color: var(--dark-orange-rahma);
            outline: none;
            box-shadow: 0 0 0 2px rgba(212, 44, 0, 0.1);
        }

        /* Tombol hapus item */
        .btn-hapus-item-rahma {
            background: #fce4e4;
            color: #c62828;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 0.82rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-hapus-item-rahma:hover {
            background: #c62828;
            color: white;
        }

        /* Dropdown tambah menu baru */
        .select-tambah-menu-rahma {
            border: 1.5px solid #e8d0c8;
            border-radius: 8px;
            font-size: 0.88rem;
            padding: 8px 12px;
            transition: border-color 0.2s;
        }

        .select-tambah-menu-rahma:focus {
            border-color: var(--dark-orange-rahma);
            outline: none;
        }

        /* Tombol tambah menu */
        .btn-tambah-item-rahma {
            background: linear-gradient(135deg, var(--orange-rahma), var(--dark-orange-rahma));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-tambah-item-rahma:hover {
            opacity: 0.88;
        }

        /* Kotak summary total di bawah tabel */
        .box-total-edit-rahma {
            background: linear-gradient(135deg, #fdf4f0, #f9f9f9);
            border-top: 2px solid #e8d0c8;
            border-radius: 0 0 12px 12px;
            padding: 16px 20px;
        }

        .baris-total-rahma {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 0.92rem;
        }

        .total-akhir-edit-rahma {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--dark-orange-rahma);
        }

        /* Toast notifikasi */
        .toast-edit-rahma {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 260px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            animation: slideInEdit_rahma 0.3s ease;
        }

        @keyframes slideInEdit_rahma {
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

    <!-- Toast notifikasi sukses/gagal -->
    <?php if ($pesan_sukses_rahma): ?>
        <div class="toast-edit-rahma alert alert-success d-flex align-items-center gap-2" id="toastSukses_rahma">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>Order berhasil diperbarui! Total sudah dihitung ulang ✅</span>
        </div>
    <?php endif; ?>
    <?php if ($pesan_gagal_rahma): ?>
        <div class="toast-edit-rahma alert alert-danger d-flex align-items-center gap-2" id="toastGagal_rahma">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <span>Gagal update order. Coba lagi ya!</span>
        </div>
    <?php endif; ?>

    <div class="container mt-4 pb-5">

        <!-- Header halaman -->
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-pencil-square fs-3 me-3" style="color: var(--dark-orange-rahma);"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color: var(--dark-orange-rahma);">Edit Order</h4>
                <small class="text-muted">
                    Ubah menu & qty order
                    <span class="text-id-rahma fw-semibold ms-1"><?= htmlspecialchars($id_order_rahma) ?></span>
                </small>
            </div>
        </div>

        <!-- Info order singkat -->
        <div class="card card-summary-rahma mb-4 p-3">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Pelanggan</div>
                    <div class="fw-semibold"><?= htmlspecialchars($data_order_rahma['nama_pelanggan_rahma']) ?></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Jenis Pesanan</div>
                    <div class="fw-semibold text-capitalize">
                        <?= htmlspecialchars($data_order_rahma['jenis_pesanan_rahma']) ?></div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Waktu Order</div>
                    <div class="fw-semibold"><?= date('d-m-Y H:i', strtotime($data_order_rahma['waktu_order_rahma'])) ?>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted small">Diskon</div>
                    <div class="fw-semibold" style="color: var(--dark-orange-rahma);"><?= $diskon_persen_rahma ?>%</div>
                </div>
            </div>
        </div>

        <!-- Form edit order -->
        <form action="../../proses/proses_edit_order_rahma.php" method="POST" id="formEditOrder_rahma">
            <input type="hidden" name="id_order_rahma" value="<?= htmlspecialchars($id_order_rahma) ?>">

            <!-- Tabel item yang dipesan -->
            <div class="card card-edit-order-rahma mb-4">
                <div class="card-header py-3"
                    style="background: linear-gradient(135deg, var(--orange-rahma), var(--dark-orange-rahma));">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-list-ul me-2"></i>Item Pesanan
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="tabelItem_rahma">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:35%;">Menu</th>
                                    <th class="text-end" style="width:18%;">Harga Satuan</th>
                                    <th class="text-center" style="width:15%;">Qty</th>
                                    <th class="text-end" style="width:18%;">Subtotal</th>
                                    <th class="text-center" style="width:14%;">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="bodyItem_rahma">
                                <?php foreach ($detail_list_rahma as $i_rahma => $item_rahma):
                                    $subtotal_item_rahma = $item_rahma['qty_rahma'] * $item_rahma['harga_rahma'];
                                    ?>
                                    <!-- Satu baris = satu item pesanan -->
                                    <tr id="baris_<?= $item_rahma['id_dorder_rahma'] ?>">
                                        <!-- Hidden fields yang dikirim ke proses -->
                                        <input type="hidden" name="id_dorder_rahma[]"
                                            value="<?= $item_rahma['id_dorder_rahma'] ?>">
                                        <input type="hidden" name="id_menu_rahma[]"
                                            value="<?= $item_rahma['id_menu_rahma'] ?>">
                                        <input type="hidden" name="harga_rahma[]" value="<?= $item_rahma['harga_rahma'] ?>"
                                            class="harga-item-rahma">
                                        <input type="hidden" name="hapus_rahma[]" value="0" class="flag-hapus-rahma">

                                        <td class="ps-3 fw-semibold" style="font-size:0.9rem;">
                                            <?= htmlspecialchars($item_rahma['nama_menu_rahma']) ?>
                                        </td>
                                        <td class="text-end text-muted small">
                                            Rp <?= number_format($item_rahma['harga_rahma'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Input qty — kalau diubah, total langsung recalculate -->
                                            <input type="number" name="qty_rahma[]" value="<?= $item_rahma['qty_rahma'] ?>"
                                                min="1" max="99" class="input-qty-edit-rahma qty-input-rahma"
                                                onchange="hitungTotal_rahma()">
                                        </td>
                                        <td class="text-end fw-bold subtotal-cell-rahma">
                                            Rp <?= number_format($subtotal_item_rahma, 0, ',', '.') ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Tombol hapus item dari pesanan -->
                                            <button type="button" class="btn-hapus-item-rahma"
                                                onclick="hapusItem_rahma(this, '<?= $item_rahma['id_dorder_rahma'] ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Box total harga — update realtime saat qty diubah -->
                    <div class="box-total-edit-rahma" id="boxTotal_rahma">
                        <div class="baris-total-rahma">
                            <span class="text-muted">Subtotal</span>
                            <span id="nilaiSubtotal_rahma">Rp 0</span>
                        </div>
                        <div class="baris-total-rahma">
                            <span style="color: var(--dark-orange-rahma);">
                                <i class="bi bi-tag me-1"></i>Diskon (<?= $diskon_persen_rahma ?>%)
                            </span>
                            <span id="nilaiDiskon_rahma" style="color: var(--dark-orange-rahma);">- Rp 0</span>
                        </div>
                        <div class="baris-total-rahma">
                            <span class="text-muted"><i class="bi bi-receipt me-1"></i>Pajak (11%)</span>
                            <span id="nilaiPajak_rahma" class="text-muted">+ Rp 0</span>
                        </div>
                        <hr style="border-color: #e8d0c8; margin: 8px 0;">
                        <div class="baris-total-rahma align-items-center">
                            <span class="fw-semibold" style="color: var(--dark-orange-rahma);">Total Akhir</span>
                            <span class="total-akhir-edit-rahma" id="nilaiTotal_rahma">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section tambah menu baru ke order -->
            <div class="card card-edit-order-rahma mb-4">
                <div class="card-header py-3"
                    style="background: linear-gradient(135deg, var(--pink-rahma), var(--dark-pink-rahma));">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Menu ke Order
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="label-rahma mb-1">Pilih Menu</label>
                            <!-- Dropdown pilih menu yang mau ditambahkan -->
                            <select id="pilihMenu_rahma" class="form-select select-tambah-menu-rahma">
                                <option value="">-- Pilih menu --</option>
                                <?php foreach ($list_menu_rahma as $m_rahma): ?>
                                    <option value="<?= $m_rahma['id_menu_rahma'] ?>"
                                        data-nama="<?= htmlspecialchars($m_rahma['nama_menu_rahma']) ?>"
                                        data-harga="<?= $m_rahma['harga_rahma'] ?>">
                                        <?= htmlspecialchars($m_rahma['nama_menu_rahma']) ?>
                                        — Rp <?= number_format($m_rahma['harga_rahma'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="label-rahma mb-1">Jumlah</label>
                            <input type="number" id="qtyTambah_rahma" value="1" min="1" max="99"
                                class="form-control input-rahma">
                        </div>
                        <div class="col-md-3">
                            <!-- Tombol tambah item baru ke tabel -->
                            <button type="button" class="btn-tambah-item-rahma w-100" onclick="tambahItem_rahma()">
                                <i class="bi bi-plus-lg me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol aksi -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-simpan-rahma px-4 py-2">
                    <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                </button>
                <a href="detail_order_rahma.php?id=<?= htmlspecialchars($id_order_rahma) ?>"
                    class="btn btn-kembali-rahma px-4 py-2">
                    <i class="bi bi-x-lg me-2"></i>Batal
                </a>
            </div>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simpan persen diskon dari PHP ke JS — biar bisa hitung di sisi client
        const diskonPersen_rahma = <?= (float) $diskon_persen_rahma ?>;

        // Counter untuk ID baris baru (menu yang ditambahkan dari dropdown)
        let counterBaris_rahma = 0;

        // ─── Hitung ulang total semua item yang aktif (belum dihapus) ───
        function hitungTotal_rahma() {
            let subtotal_rahma = 0;

            // Loop semua baris di tabel — kecuali yang sudah ditandai hapus
            const barisAll_rahma = document.querySelectorAll('#bodyItem_rahma tr');
            barisAll_rahma.forEach(baris_rahma => {
                // Cek flag hapus — kalau 1 berarti baris ini mau dihapus, skip
                const flagHapus_rahma = baris_rahma.querySelector('.flag-hapus-rahma');
                if (flagHapus_rahma && flagHapus_rahma.value === '1') return;

                const harga_rahma = parseFloat(baris_rahma.querySelector('.harga-item-rahma')?.value || 0);
                const qty_rahma = parseInt(baris_rahma.querySelector('.qty-input-rahma')?.value || 0);
                const sub_rahma = harga_rahma * qty_rahma;

                subtotal_rahma += sub_rahma;

                // Update tampilan subtotal per baris
                const selSubtotal_rahma = baris_rahma.querySelector('.subtotal-cell-rahma');
                if (selSubtotal_rahma) {
                    selSubtotal_rahma.textContent = 'Rp ' + sub_rahma.toLocaleString('id-ID');
                }
            });

            // Hitung diskon, pajak, total akhir
            const diskonNominal_rahma = subtotal_rahma * diskonPersen_rahma / 100;
            const setelahDiskon_rahma = subtotal_rahma - diskonNominal_rahma;
            const pajakNominal_rahma = setelahDiskon_rahma * 0.11;
            const totalAkhir_rahma = setelahDiskon_rahma + pajakNominal_rahma;

            // Tampilkan ke UI
            document.getElementById('nilaiSubtotal_rahma').textContent = 'Rp ' + subtotal_rahma.toLocaleString('id-ID');
            document.getElementById('nilaiDiskon_rahma').textContent = '- Rp ' + diskonNominal_rahma.toLocaleString('id-ID');
            document.getElementById('nilaiPajak_rahma').textContent = '+ Rp ' + pajakNominal_rahma.toLocaleString('id-ID');
            document.getElementById('nilaiTotal_rahma').textContent = 'Rp ' + totalAkhir_rahma.toLocaleString('id-ID');
        }

        // ─── Tandai baris sebagai "mau dihapus" — baris masih ada tapi dikirim dengan flag hapus=1 ───
        function hapusItem_rahma(tombol_rahma, idDorder_rahma) {
            const baris_rahma = tombol_rahma.closest('tr');

            // Tandai flag hapus jadi 1
            const flagHapus_rahma = baris_rahma.querySelector('.flag-hapus-rahma');
            if (flagHapus_rahma) flagHapus_rahma.value = '1';

            // Kasih efek visual — baris jadi merah tipis + disable tombol
            baris_rahma.classList.add('baris-hapus-rahma');
            baris_rahma.style.opacity = '0.4';
            tombol_rahma.disabled = true;
            tombol_rahma.innerHTML = '<i class="bi bi-check"></i> Dihapus';

            // Recalculate total tanpa baris ini
            hitungTotal_rahma();
        }

        // ─── Tambah menu baru ke tabel dari dropdown ───
        function tambahItem_rahma() {
            const select_rahma = document.getElementById('pilihMenu_rahma');
            const qty_rahma = parseInt(document.getElementById('qtyTambah_rahma').value) || 1;
            const opt_rahma = select_rahma.options[select_rahma.selectedIndex];

            if (!select_rahma.value) {
                alert('Pilih menu dulu ya!');
                return;
            }

            const idMenu_rahma = select_rahma.value;
            const namaMenu_rahma = opt_rahma.dataset.nama;
            const harga_rahma = parseFloat(opt_rahma.dataset.harga);
            const subtotal_rahma = harga_rahma * qty_rahma;

            counterBaris_rahma++;
            const idBaris_rahma = 'baru_' + counterBaris_rahma;

            // Buat baris baru — ditandai sebagai "new" biar proses tau ini item tambahan
            const baris_rahma = document.createElement('tr');
            baris_rahma.id = idBaris_rahma;
            baris_rahma.innerHTML = `
            <!-- id_dorder kosong = item baru, proses akan generate ID sendiri -->
            <input type="hidden" name="id_dorder_rahma[]" value="">
            <input type="hidden" name="id_menu_rahma[]" value="${idMenu_rahma}">
            <input type="hidden" name="harga_rahma[]" value="${harga_rahma}" class="harga-item-rahma">
            <input type="hidden" name="hapus_rahma[]" value="0" class="flag-hapus-rahma">

            <td class="ps-3 fw-semibold" style="font-size:0.9rem;">
                ${namaMenu_rahma}
                <span class="badge ms-1" style="background:#e8f5e9; color:#2e7d32; font-size:0.7rem;">Baru</span>
            </td>
            <td class="text-end text-muted small">Rp ${harga_rahma.toLocaleString('id-ID')}</td>
            <td class="text-center">
                <input type="number" name="qty_rahma[]" value="${qty_rahma}"
                    min="1" max="99"
                    class="input-qty-edit-rahma qty-input-rahma"
                    onchange="hitungTotal_rahma()">
            </td>
            <td class="text-end fw-bold subtotal-cell-rahma">
                Rp ${subtotal_rahma.toLocaleString('id-ID')}
            </td>
            <td class="text-center">
                <button type="button" class="btn-hapus-item-rahma"
                    onclick="hapusBaruItem_rahma(this, '${idBaris_rahma}')">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

            document.getElementById('bodyItem_rahma').appendChild(baris_rahma);

            // Reset dropdown dan qty setelah tambah
            select_rahma.value = '';
            document.getElementById('qtyTambah_rahma').value = 1;

            hitungTotal_rahma();
        }

        // ─── Hapus item baru (yang belum ada di DB) — langsung remove baris ───
        function hapusBaruItem_rahma(tombol_rahma, idBaris_rahma) {
            document.getElementById(idBaris_rahma).remove();
            hitungTotal_rahma();
        }

        // ─── Hilangkan toast otomatis setelah 3 detik ───
        setTimeout(() => {
            const s_rahma = document.getElementById('toastSukses_rahma');
            const g_rahma = document.getElementById('toastGagal_rahma');
            if (s_rahma) s_rahma.style.display = 'none';
            if (g_rahma) g_rahma.style.display = 'none';
        }, 3000);

        // Hitung total pertama kali saat halaman dimuat
        hitungTotal_rahma();
    </script>
</body>

</html>