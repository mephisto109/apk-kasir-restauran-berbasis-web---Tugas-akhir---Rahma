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

// Ambil semua order yang BELUM bayar
$query_belum_bayar_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        o.id_order_rahma,
        o.nama_pelanggan_rahma,
        o.id_meja_rahma,
        o.jenis_pesanan_rahma,
        o.waktu_order_rahma,
        o.status_order_rahma,
        o.keterangan_rahma,
        COALESCE(SUM(d.subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_order_rahma o
    LEFT JOIN tbl_detail_order_rahma d ON o.id_order_rahma = d.id_order_rahma
    WHERE o.id_order_rahma NOT IN (
        SELECT id_order_rahma FROM tbl_transaksi_rahma
    )
    GROUP BY o.id_order_rahma
    ORDER BY o.id_order_rahma DESC
");

// Kelompokkan data ke dalam array berdasarkan jenis pesanan
$dine_in_orders = [];
$take_away_orders = [];

while ($row = mysqli_fetch_assoc($query_belum_bayar_rahma)) {
    if (strtolower($row['jenis_pesanan_rahma']) == 'dine in') {
        $dine_in_orders[] = $row;
    } else {
        $take_away_orders[] = $row;
    }
}
include '../templates/navbar_rahma.php';

// Fungsi untuk render tabel order
function renderTableRahma($orders) {
    if (empty($orders)) {
        echo '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada antrean di kategori ini.</td></tr>';
        return;
    }

    foreach ($orders as $row_rahma) {
        $meja_display = (int) ltrim($row_rahma['id_meja_rahma'], 'M');
        $status_badge = '';
        if ($row_rahma['status_order_rahma'] == 'menunggu_pembayaran') {
            $status_badge = '<span class="badge badge-status-rahma badge-dibuat-rahma"><i class="bi bi-clock me-1"></i>Menunggu Pembayaran</span>';
        } elseif ($row_rahma['status_order_rahma'] == 'diproses') {
            $status_badge = '<span class="badge badge-status-rahma badge-diproses-rahma"><i class="bi bi-fire me-1"></i>Sedang Dimasak</span>';
        } elseif ($row_rahma['status_order_rahma'] == 'selesai') {
            $status_badge = '<span class="badge badge-status-rahma badge-selesai-rahma"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
        } else {
            $status_badge = '<span class="badge badge-status-rahma badge-disajikan-rahma"><i class="bi bi-check2-circle me-1"></i>Disajikan</span>';
        }

        echo "
        <tr data-order-id='" . htmlspecialchars($row_rahma['id_order_rahma']) . "' data-order-text='" . htmlspecialchars(strtolower($row_rahma['id_order_rahma'] . ' ' . $row_rahma['nama_pelanggan_rahma'])) . "'>
            <td class='ps-3'><span class='text-id-rahma'>{$row_rahma['id_order_rahma']}</span></td>
            <td>" . htmlspecialchars($row_rahma['nama_pelanggan_rahma']) . "</td>
            <td>" . htmlspecialchars($row_rahma['jenis_pesanan_rahma']) . "</td>
            <td><span class='badge badge-status-rahma' style='background-color: var(--orange-rahma); color:#fff;'>{$meja_display}</span></td>
            <td>{$row_rahma['waktu_order_rahma']}</td>
            <td class='fw-semibold'>Rp " . number_format($row_rahma['grand_total_rahma'], 0, ',', '.') . "</td>
            <td>{$status_badge}</td>
            <td class='text-center'>
                <a href='detail_order_rahma.php?id={$row_rahma['id_order_rahma']}' class='btn btn-sm btn-detail-rahma me-1'><i class='bi bi-eye'></i></a>
                <a href='pembayaran_rahma.php?id={$row_rahma['id_order_rahma']}' class='btn btn-sm btn-bayar-rahma'><i class='bi bi-cash'></i></a>
            </td>
        </tr>";
    }
}
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
    <title>Transaksi</title>
</head>
<body>
    <div class="flag-stripe-rahma"></div>
    
    <!-- =====================
    INFO BAR KASIR
    ===================== -->
    <div style="
        background: linear-gradient(90deg, #2c2c2c, #3a3a3a);
        color: #fff;
        font-size: 0.8rem;
        padding: 6px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 2px solid var(--dark-orange-rahma);
    ">
        <!-- Kiri: badge user logged + nama kasir -->
        <div class="d-flex align-items-center gap-3">
            <div style="
                background: var(--dark-orange-rahma);
                border-radius: 6px;
                padding: 3px 10px;
                font-weight: 700;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
            ">
                USER LOGGED
            </div>
            <span style="color: #ccc;">
                <i class="bi bi-person-fill me-1" style="color: var(--dark-orange-rahma);"></i>
                <?= htmlspecialchars(strtoupper($_SESSION['nama_rahma'] ?? 'KASIR')) ?>
            </span>
        </div>

        <!-- Tengah: info POS dan tanggal bisnis -->
        <div class="d-flex align-items-center gap-4" style="color: #ccc;">
            <span>
                <i class="bi bi-display me-1" style="color: var(--dark-orange-rahma);"></i>
                POS KASIR
            </span>
            <span>
                <i class="bi bi-calendar-check me-1" style="color: var(--dark-orange-rahma);"></i>
                Business Day: <?= date('d/m/Y') ?>
            </span>
        </div>

        <!-- Kanan: jam realtime -->
        <div class="d-flex align-items-center gap-2" style="color: #fff; font-weight: 600;">
            <i class="bi bi-clock me-1" style="color: var(--dark-orange-rahma);"></i>
            <span id="jam-kasir-rahma"></span>
        </div>
    </div>
    <div class="container mt-4">

        <h5 class="mb-4 fw-semibold" style="color: var(--dark-orange-rahma);">
            <i class="bi bi-cash-coin me-2"></i>Transaksi — Antrean Kasir
        </h5>

        <!-- Search Box -->
        <div class="mb-4">
            <input type="text" id="searchOrderRahma" class="form-control form-control-sm" placeholder="Cari ID Order atau Pelanggan..." style="max-width: 400px;">
        </div>

        <!-- Tabel Dine In -->
        <div class="card card-table-rahma mb-5">
            <div class="card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white ps-3">
                    <i class="bi bi-house-door me-2"></i>Antrean Dine In
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableDineInRahma">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Order</th>
                                <th>Pelanggan</th>
                                <th>Jenis</th>
                                <th>Meja</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php renderTableRahma($dine_in_orders); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-table-rahma">
            <div class="card-header-rahma py-3">
                <h6 class="mb-0 fw-semibold text-white ps-3">
                    <i class="bi bi-bag-check me-2"></i>Antrean Take Away
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableTakeAwayRahma">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID Order</th>
                                <th>Pelanggan</th>
                                <th>Jenis</th>
                                <th>Meja</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php renderTableRahma($take_away_orders); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update jam realtime setiap detik di info bar
        function updateJam_rahma() {
            const now_rahma = new Date();
            const jam_rahma = now_rahma.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('jam-kasir-rahma').textContent = jam_rahma;
        }
        setInterval(updateJam_rahma, 1000);
        updateJam_rahma(); // Panggil sekali saat load halaman untuk langsung tampilkan jam tanpa delay 1 detik

        // Setup search untuk kedua tabel
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchOrderRahma');
            const tableBodies = [
                document.querySelector('#tableDineInRahma tbody'),
                document.querySelector('#tableTakeAwayRahma tbody')
            ];
            
            if (!searchInput || tableBodies.some(tb => !tb)) return;
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                let totalVisibleCount = 0;
                
                tableBodies.forEach((tableBody) => {
                    const rows = tableBody.querySelectorAll('tr');
                    let visibleCount = 0;
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (searchTerm === '' || text.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    
                    totalVisibleCount += visibleCount;
                    
                    // Tampilkan pesan jika tidak ada hasil di tabel ini
                    if (visibleCount === 0) {
                        let emptyRow = tableBody.querySelector('.no-results-transaksi-rahma');
                        if (!emptyRow) {
                            emptyRow = document.createElement('tr');
                            emptyRow.className = 'no-results-transaksi-rahma';
                            emptyRow.innerHTML = '<td colspan="8" class="text-center text-muted py-4"><i class="bi bi-search me-2"></i>Tidak ada order di kategori ini</td>';
                            tableBody.appendChild(emptyRow);
                        }
                    } else {
                        const emptyRow = tableBody.querySelector('.no-results-transaksi-rahma');
                        if (emptyRow) emptyRow.remove();
                    }
                });
            });
        });

        // Reload halaman kalau user klik back dari cache browser
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>