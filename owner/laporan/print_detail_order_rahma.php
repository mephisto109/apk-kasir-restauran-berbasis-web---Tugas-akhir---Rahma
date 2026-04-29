<?php
require('../../assets/fpdf/fpdf.php');
include '../../koneksi/koneksi_rahma.php';

if (!isset($_GET['id'])) {
    die('ID order tidak ditemukan');
}

$id_order_rahma = $_GET['id'];

// =====================
// DATA ORDER
// =====================
$data_order_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_order_rahma 
     WHERE id_order_rahma='$id_order_rahma'"
));

if (!$data_order_rahma) {
    die('Data order tidak ada');
}

// =====================
// DATA TRANSAKSI
// =====================
$data_transaksi_rahma = mysqli_fetch_assoc(mysqli_query(
    $koneksiRahma,
    "SELECT * FROM tbl_transaksi_rahma 
     WHERE id_order_rahma='$id_order_rahma'"
));

// =====================
// DETAIL
// =====================
$detail_rahma = mysqli_query($koneksiRahma, "
    SELECT d.*, m.nama_menu_rahma, m.harga_rahma
    FROM tbl_detail_order_rahma d
    JOIN tbl_menu_rahma m 
    ON d.id_menu_rahma = m.id_menu_rahma
    WHERE d.id_order_rahma='$id_order_rahma'
");

// =====================
// PDF
// =====================
$pdf = new FPDF();
$pdf->AddPage();

// =====================
// KOP SURAT
// =====================

// Nama restoran — besar dan bold, kayak papan nama di depan toko
$pdf->SetFont('Arial','B',18);
$pdf->Cell(0,10,'FAMIRESU IKO',0,1,'C');

// Alamat & kontak — font lebih kecil, rata tengah
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Jl. Kuliner Raya No. 707',0,1,'C');
$pdf->Cell(0,5,'Bandung',0,1,'C');
$pdf->Cell(0,5,'WA: 081299887766  |  IG: @famiresu.iko',0,1,'C');

// Garis pemisah bawah kop — kayak garis bawah di kop surat resmi
$pdf->SetLineWidth(0.5);
$pdf->Line(10, $pdf->GetY()+3, 200, $pdf->GetY()+3);
$pdf->Ln(8);

// Judul halaman
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'DETAIL ORDER',0,1,'C');
$pdf->Ln(3);

// INFO ORDER
$pdf->SetFont('Arial','',12);

$pdf->Cell(40,8,'ID Order',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(60,8,$data_order_rahma['id_order_rahma'],0,1);

$pdf->Cell(40,8,'Tanggal',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(60,8,date('d-m-Y H:i', strtotime($data_order_rahma['waktu_order_rahma'])),0,1);

$pdf->Cell(40,8,'Nama',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(60,8,$data_order_rahma['nama_pelanggan_rahma'],0,1);

if ($data_order_rahma['jenis_pesanan_rahma'] === 'dine in') {

    $pdf->Cell(40,8,'Jenis Pesanan',0,0);
    $pdf->Cell(5,8,':',0,0);
    $pdf->Cell(60,8,$data_order_rahma['jenis_pesanan_rahma'],0,1);

    $pdf->Cell(40,8,'Meja',0,0);
    $pdf->Cell(5,8,':',0,0);
    $pdf->Cell(60,8,$data_order_rahma['id_meja_rahma'],0,1);
} else {
    $pdf->Cell(40,8,'Jenis Pesanan',0,0);
    $pdf->Cell(5,8,':',0,0);
    $pdf->Cell(60,8,$data_order_rahma['jenis_pesanan_rahma'],0,1);
}

$pdf->Ln(5);

// TABLE HEADER
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(230,230,230);

$pdf->Cell(70,8,'Menu',1,0,'C',true);
$pdf->Cell(30,8,'Harga',1,0,'C',true);
$pdf->Cell(25,8,'Qty',1,0,'C',true);
$pdf->Cell(35,8,'Subtotal',1,1,'C',true);

// DATA
$pdf->SetFont('Arial','',11);

$total_rahma = 0;

while ($d_rahma = mysqli_fetch_assoc($detail_rahma)) {

    $subtotal_rahma = $d_rahma['qty_rahma'] * $d_rahma['harga_rahma'];
    $total_rahma += $subtotal_rahma;

    $pdf->Cell(70,8,$d_rahma['nama_menu_rahma'],1);
    $pdf->Cell(30,8,'Rp '.number_format($d_rahma['harga_rahma']),1,0,'C');
    $pdf->Cell(25,8,$d_rahma['qty_rahma'],1,0,'C');
    $pdf->Cell(35,8,'Rp '.number_format($subtotal_rahma),1,1,'C');
}

// TOTAL
$pdf->SetFont('Arial','B',11);
$pdf->Cell(125,8,'Total',1);
$pdf->Cell(35,8,'Rp '.number_format($total_rahma),1,1,'C');

// TRANSAKSI
if ($data_transaksi_rahma) {
    $pdf->Cell(125,8,'Bayar',1);
    $pdf->Cell(35,8,'Rp '.number_format($data_transaksi_rahma['bayar_rahma']),1,1,'C');

    $pdf->Cell(125,8,'Kembali',1);
    $pdf->Cell(35,8,'Rp '.number_format($data_transaksi_rahma['kembalian_rahma']),1,1,'C');
}

// =====================
// KOLOM TANDA TANGAN
// =====================

$pdf->Ln(12);

// Tanggal cetak — otomatis ambil tanggal hari ini
$tanggal_cetak_rahma = date('d-m-Y');

// Posisi kolom TTD di kanan bawah (mulai dari x=130)
$pdf->SetFont('Arial','',11);
$pdf->SetX(130);
$pdf->Cell(70,6,'Bandung, '.$tanggal_cetak_rahma,0,1,'C');

// Jarak kosong buat tanda tangan — kayak ruang di bawah surat resmi
$pdf->Ln(18);

// Garis tanda tangan
$x_awal_rahma = 130;
$x_akhir_rahma = 200;
$y_ttd_rahma = $pdf->GetY();
$pdf->Line($x_awal_rahma, $y_ttd_rahma, $x_akhir_rahma, $y_ttd_rahma);


$pdf->Ln(3);

// OUTPUT
$nama = str_replace(' ', '_', $data_order_rahma['nama_pelanggan_rahma']);

$nama_file = "Laporan_Order_" . $nama . "_" . date('Ymd') . ".pdf";

$pdf->Output("I", $nama_file);

?>