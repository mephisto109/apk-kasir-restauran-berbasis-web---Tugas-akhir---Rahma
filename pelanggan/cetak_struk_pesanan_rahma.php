<?php
session_start();

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../assets/fpdf/fpdf.php';
                                
//  Validasi ID Order dari query string
$id_order_rahma = isset($_GET['id_order']) ? mysqli_real_escape_string($koneksiRahma, trim($_GET['id_order'])) : '';

if (empty($id_order_rahma)) {
    header("Location: menu_rahma.php?pesan=id_kosong");
    exit;
}

// Cek apakah order dengan ID tersebut ada
$sql_cek = "SELECT * FROM tbl_order_rahma WHERE id_order_rahma = '$id_order_rahma'";
$query_order_rahma = mysqli_query($koneksiRahma, $sql_cek);
$order_rahma = mysqli_fetch_assoc($query_order_rahma);

if (!$order_rahma) {
    header("Location: menu_rahma.php?pesan=order_tidak_ditemukan");
    exit;
}


// Ambil detail order dan hitung grand total
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, mn.nama_menu_rahma, mn.harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '$id_order_rahma'
");

$grand_total_rahma = 0;
$items_rahma       = [];
// Loop untuk menghitung grand total dan menyimpan item ke array
while ($row_rahma = mysqli_fetch_assoc($query_detail_rahma)) {
    $grand_total_rahma += $row_rahma['subtotal_rahma'];
    $items_rahma[]      = $row_rahma;
}

// Logika Diskon
$is_member_rahma      = isset($_SESSION['id_user_rahma']);
$diskon_persen_rahma  = $is_member_rahma ? 10 : 0;
$nominal_diskon_rahma = (int) (($grand_total_rahma * $diskon_persen_rahma) / 100);
$total_setelah_diskon_rahma    = $grand_total_rahma - $nominal_diskon_rahma;

// Logika Pajak 11% dari total setelah diskon
$pajak_nominal_rahma = (int) ($total_setelah_diskon_rahma * 0.11);
$total_bayar_rahma = $total_setelah_diskon_rahma + $pajak_nominal_rahma;

// =============================================
// PDF GENERATION
// =============================================
ob_end_clean(); // Bersihkan buffer

$pdf_rahma = new FPDF('P', 'mm', [80, 200]);
$pdf_rahma->AddPage();
$pdf_rahma->SetMargins(5, 5, 5);

// Header restoran
$pdf_rahma->SetFont('Courier', 'B', 14);
$pdf_rahma->Cell(60, 7, 'FAMIRESU IKO', 0, 1, 'C');
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(70, 5, 'Restoran Keluarga', 0, 1, 'C');
$pdf_rahma->Cell(70, 4, '================================', 0, 1, 'C');
$pdf_rahma->Ln(2);

// Info Order
$pdf_rahma->SetFont('Helvetica', 'B', 8);
$pdf_rahma->Cell(25, 5, 'ID Order', 0, 0);
$pdf_rahma->SetFont('Helvetica', '', 8);
$pdf_rahma->Cell(0, 5, ': ' . $id_order_rahma, 0, 1);

$pdf_rahma->SetFont('Helvetica', 'B', 8);
$pdf_rahma->Cell(25, 5, 'Nama', 0, 0);
$pdf_rahma->SetFont('Helvetica', '', 8);
$pdf_rahma->Cell(0, 5, ': ' . $order_rahma['nama_pelanggan_rahma'], 0, 1);

$pdf_rahma->SetFont('Helvetica', 'B', 8);
$pdf_rahma->Cell(25, 5, 'Waktu', 0, 0);
$pdf_rahma->SetFont('Helvetica', '', 8);
$pdf_rahma->Cell(0, 5, ': ' . date('d/m/Y H:i', strtotime($order_rahma['waktu_order_rahma'])), 0, 1);

$pdf_rahma->Ln(2);
$pdf_rahma->SetDrawColor(180, 180, 180);
$pdf_rahma->Line(5, $pdf_rahma->GetY(), 75, $pdf_rahma->GetY());
$pdf_rahma->Ln(3);

// Table Item
$pdf_rahma->SetFont('Helvetica', 'B', 8);
$pdf_rahma->Cell(35, 5, 'Menu', 0, 0);
$pdf_rahma->Cell(10, 5, 'Qty', 0, 0, 'C');
$pdf_rahma->Cell(25, 5, 'Total', 0, 1, 'R');
$pdf_rahma->SetFont('Helvetica', '', 8);

foreach ($items_rahma as $it) {
    $pdf_rahma->Cell(35, 5, substr($it['nama_menu_rahma'], 0, 100), 0, 0);
    $pdf_rahma->Cell(10, 5, $it['qty_rahma'], 0, 0, 'C');
    $pdf_rahma->Cell(25, 5, number_format($it['subtotal_rahma'], 0, ',', '.'), 0, 1, 'R');
}

$pdf_rahma->Ln(2);
$pdf_rahma->Line(5, $pdf_rahma->GetY(), 75, $pdf_rahma->GetY());
$pdf_rahma->Ln(2);

// Total
$pdf_rahma->Cell(45, 5, 'Subtotal', 0, 0);
$pdf_rahma->Cell(25, 5, 'Rp ' . number_format($grand_total_rahma, 0, ',', '.'), 0, 1, 'R');

if ($is_member_rahma) {
    $pdf_rahma->Cell(45, 5, 'Diskon Member', 0, 0);
    $pdf_rahma->Cell(25, 5, '- ' . number_format($nominal_diskon_rahma, 0, ',', '.'), 0, 1, 'R');
}

$pdf_rahma->Cell(45, 5, 'PPN (11%)', 0, 0);
$pdf_rahma->Cell(25, 5, 'Rp ' . number_format($pajak_nominal_rahma, 0, ',', '.'), 0, 1, 'R');

$pdf_rahma->SetFont('Helvetica', 'B', 9);
$pdf_rahma->Cell(45, 7, 'TOTAL', 0, 0);
$pdf_rahma->Cell(25, 7, 'Rp ' . number_format($total_bayar_rahma, 0, ',', '.'), 0, 1, 'R');

$pdf_rahma->Ln(4);
$pdf_rahma->SetFont('Helvetica', 'B', 8);
$pdf_rahma->SetTextColor(212, 44, 0);
$pdf_rahma->MultiCell(0, 4, "SILAHKAN BAWA STRUK INI KE KASIR\nUNTUK PEMBAYARAN", 0, 'C');

$pdf_rahma->Output('I', 'Struk_' . $id_order_rahma . '.pdf');

// UBAH: Setelah cetak struk, destroy session dan redirect ke login
// Gunakan JavaScript untuk ensure PDF sudah ditampilkan sebelum logout
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Pesanan</title>
</head>
<body>
    <script>
        // Redirect ke proses logout setelah 2 detik (memastikan PDF sudah ditampilkan)
        setTimeout(function() {
            window.location.href = '../proses/proses_logout_pesanan_rahma.php';
        }, 2000);
    </script>
</body>
</html>