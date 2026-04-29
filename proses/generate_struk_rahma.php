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

// Hitung grand total order untuk konversi diskon persen ke nominal rupiah
$query_total_order_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(subtotal_rahma), 0) AS grand_total_rahma
    FROM tbl_detail_order_rahma
    WHERE id_order_rahma = '{$transaksi_rahma['id_order_rahma']}'
");
$data_total_order_rahma = mysqli_fetch_assoc($query_total_order_rahma);
$grand_total_order_rahma = $data_total_order_rahma['grand_total_rahma'];
$diskon_nominal_rahma = (int) ($grand_total_order_rahma * $transaksi_rahma['diskon_rahma'] / 100);
$pajak_nominal_rahma = $transaksi_rahma['pajak_rahma'];

// ===== GENERATE PDF PAKAI FPDF =====
$pdf_rahma = new FPDF('P', 'mm', array(80, 200));
$pdf_rahma->AddPage();
$pdf_rahma->SetMargins(5, 5, 5);
$pdf_rahma->SetAutoPageBreak(true, 5);

// Header restoran
$pdf_rahma->SetFont('Courier', 'B', 14);
$pdf_rahma->Cell(60, 7, 'FAMIRESU IKO', 0, 1, 'C');
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(70, 5, 'Restoran Keluarga', 0, 1, 'C');
$pdf_rahma->SetFont('Courier', '', 8);
$pdf_rahma->Cell(70, 5, 'Jl. Kuliner Raya No. 707', 0, 1, 'C');
$pdf_rahma->Cell(70, 5, 'Bandung', 0, 1, 'C');
$pdf_rahma->Cell(70, 5, 'WA: 081299887766', 0, 1, 'C');
$pdf_rahma->Cell(70, 5, 'IG: @famiresu.iko', 0, 1, 'C');
$pdf_rahma->Cell(70, 4, '--------------------------------', 0, 1, 'C');
$pdf_rahma->Ln(1);

// Info transaksi
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(35, 5, 'Tgl', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . date('d/m/Y', strtotime($transaksi_rahma['waktu_transaksi_rahma'])), 0, 1);
$pdf_rahma->Cell(35, 5, 'Jam', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . date('H:i', strtotime($transaksi_rahma['waktu_transaksi_rahma'])), 0, 1);
$pdf_rahma->Cell(35, 5, 'No', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $id_transaksi_rahma, 0, 1);
$pdf_rahma->Cell(35, 5, 'Meja', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $nomor_meja_rahma, 0, 1);
$pdf_rahma->Cell(35, 5, 'Nama', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . $transaksi_rahma['nama_pelanggan_rahma'], 0, 1);
$pdf_rahma->Cell(35, 5, 'Kasir', 0, 0);
$pdf_rahma->Cell(35, 5, ': ' . ($_SESSION['nama_rahma'] ?? $_SESSION['username_rahma'] ?? 'Kasir'), 0, 1);

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

// Baris subtotal sebelum pajak & diskon
$pdf_rahma->SetFont('Courier', '', 9);
$pdf_rahma->Cell(35, 5, 'Subtotal', 0, 0);
$pdf_rahma->Cell(35, 5, 'Rp ' . number_format($grand_total_order_rahma, 0, ',', '.'), 0, 1, 'R');

// Baris pajak — selalu tampil
$pdf_rahma->Cell(35, 5, 'PPN (11%)', 0, 0);
$pdf_rahma->Cell(35, 5, '+ Rp ' . number_format($pajak_nominal_rahma, 0, ',', '.'), 0, 1, 'R');

// Baris diskon — hanya tampil kalau member
if ($transaksi_rahma['diskon_rahma'] > 0) {
    $pdf_rahma->Cell(35, 5, 'Diskon (' . $transaksi_rahma['diskon_rahma'] . '%)', 0, 0);
    $pdf_rahma->Cell(35, 5, '- Rp ' . number_format($diskon_nominal_rahma, 0, ',', '.'), 0, 1, 'R');
}

$pdf_rahma->SetFont('Courier', 'B', 9);
$pdf_rahma->Cell(35, 5, 'TOTAL', 0, 0);
$pdf_rahma->Cell(35, 5, 'Rp ' . number_format($transaksi_rahma['total_rahma'], 0, ',', '.'), 0, 1, 'R');

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