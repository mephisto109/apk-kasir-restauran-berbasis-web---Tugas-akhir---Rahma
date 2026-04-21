<?php
session_start();

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../assets/fpdf/fpdf.php';

// Ambil id transaksi dari URL
$id_transaksi_rahma = $_GET['id'] ?? '';

if (empty($id_transaksi_rahma)) {
    header("Location: ../kasir/dashboard_rahma.php");
    exit;
}

// Ambil data transaksi
$query_transaksi_rahma = mysqli_query($koneksiRahma, "
    SELECT t.*, o.nama_pelanggan_rahma, o.id_meja_rahma, o.jenis_pesanan_rahma, o.waktu_order_rahma, o.keterangan_rahma, o.id_order_rahma
    FROM tbl_transaksi_rahma t
    LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
    WHERE t.id_transaksi_rahma = '$id_transaksi_rahma'
");
$transaksi_rahma = mysqli_fetch_assoc($query_transaksi_rahma);

// Ambil detail item
$query_detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, mn.nama_menu_rahma, mn.harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    WHERE d.id_order_rahma = '{$transaksi_rahma['id_order_rahma']}'
");

// Nomor meja — ambil angkanya aja
$nomor_meja_rahma = (int) ltrim($transaksi_rahma['id_meja_rahma'], 'M');

// ===== GENERATE PDF PAKAI FPDF =====
$pdf_rahma = new FPDF('P', 'mm', array(80, 200));
$pdf_rahma->AddPage();
$pdf_rahma->SetMargins(5, 5, 5);
$pdf_rahma->SetAutoPageBreak(true, 5);

// Header restoran
$pdf_rahma->SetFont('Courier', 'B', 14);
$pdf_rahma->Cell(70, 7, 'FAMIRESU IKO', 0, 1, 'C');
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(70, 5, 'Restoran Keluarga', 0, 1, 'C');
$pdf_rahma->Cell(70, 4, '================================', 0, 1, 'C');
$pdf_rahma->Ln(2);

// Info transaksi
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(35, 5, 'No. Transaksi', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $id_transaksi_rahma, 0, 1);
$pdf_rahma->Cell(35, 5, 'No. Order', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $transaksi_rahma['id_order_rahma'], 0, 1);
$pdf_rahma->Cell(35, 5, 'Pelanggan', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $transaksi_rahma['nama_pelanggan_rahma'], 0, 1);
$pdf_rahma->Cell(35, 5, 'Meja', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $nomor_meja_rahma, 0, 1);
$pdf_rahma->Cell(35, 5, 'Jenis Pesanan', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . ucfirst($transaksi_rahma['jenis_pesanan_rahma']), 0, 1);
$pdf_rahma->Cell(35, 5, 'Tanggal', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $transaksi_rahma['waktu_transaksi_rahma'], 0, 1);

$pdf_rahma->Cell(70, 4, '--------------------------------', 0, 1, 'C');

// Daftar item yang dipesan
while ($row_detail_rahma = mysqli_fetch_assoc($query_detail_rahma)) {
    $nama_menu_rahma = $row_detail_rahma['nama_menu_rahma'];
    $qty_rahma = $row_detail_rahma['qty_rahma'];
    $harga_rahma = $row_detail_rahma['harga_rahma'];
    $subtotal_rahma = $row_detail_rahma['subtotal_rahma'];

    // Nama menu
    $pdf_rahma->SetFont('Courier', '', 9);
    $pdf_rahma->Cell(70, 5, $nama_menu_rahma, 0, 1);

    // Qty x harga = subtotal
    $pdf_rahma->Cell(35, 5, '  ' . $qty_rahma . ' x Rp ' . number_format($harga_rahma, 0, ',', '.'), 0, 0);
    $pdf_rahma->Cell(35, 5, 'Rp ' . number_format($subtotal_rahma, 0, ',', '.'), 0, 1, 'R');
}

$pdf_rahma->Cell(70, 4, '--------------------------------', 0, 1, 'C');

// Total, bayar, kembalian
$pdf_rahma->SetFont('Courier', 'B', 9);
$pdf_rahma->Cell(35, 5, 'TOTAL', 0, 0);
$pdf_rahma->Cell(35, 5, 'Rp ' . number_format($transaksi_rahma['total_rahma'], 0, ',', '.'), 0, 1, 'R');

if ($transaksi_rahma['diskon_rahma'] > 0) {
    $pdf_rahma->SetFont('Courier', '', 9);
    $pdf_rahma->Cell(35, 5, 'Diskon', 0, 0);
    $pdf_rahma->Cell(35, 5, '- Rp ' . number_format($transaksi_rahma['diskon_rahma'], 0, ',', '.'), 0, 1, 'R');
}

$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(35, 5, 'Bayar', 0, 0);
$pdf_rahma->Cell(35, 5, 'Rp ' . number_format($transaksi_rahma['bayar_rahma'], 0, ',', '.'), 0, 1, 'R');
$pdf_rahma->Cell(35, 5, 'Kembalian', 0, 0);
$pdf_rahma->Cell(35, 5, 'Rp ' . number_format($transaksi_rahma['kembalian_rahma'], 0, ',', '.'), 0, 1, 'R');

$pdf_rahma->Cell(70, 4, '================================', 0, 1, 'C');

// Footer
$pdf_rahma->SetFont('Courier', 'I', 8);
$pdf_rahma->Cell(70, 5, 'Terima kasih sudah makan di sini!', 0, 1, 'C');
$pdf_rahma->Cell(70, 5, 'Sampai jumpa lagi :)', 0, 1, 'C');

// Output PDF ke browser — I = inline (preview di browser)
$pdf_rahma->Output('I', 'struk_' . $id_transaksi_rahma . '.pdf');
?>