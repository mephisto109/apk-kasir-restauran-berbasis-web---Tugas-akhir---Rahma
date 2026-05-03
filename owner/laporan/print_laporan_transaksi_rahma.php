<?php
require('../../assets/fpdf/fpdf.php');
include '../../koneksi/koneksi_rahma.php';

// =====================
// AMBIL FILTER
// =====================
$tgl_awal_rahma = $_GET['tgl_awal'] ?? '';
$tgl_akhir_rahma = $_GET['tgl_akhir'] ?? '';

$where_rahma = "";

if ($tgl_awal_rahma && $tgl_akhir_rahma) {
    $where_rahma = "WHERE DATE(waktu_transaksi_rahma) 
    BETWEEN '$tgl_awal_rahma' AND '$tgl_akhir_rahma'";
}

// =====================
// QUERY
// =====================
$query_rahma = mysqli_query($koneksiRahma, "
    SELECT * FROM tbl_transaksi_rahma
    $where_rahma
    ORDER BY waktu_transaksi_rahma ASC
");

$total_pendapatan_rahma = 0;
$total_transaksi_rahma = mysqli_num_rows($query_rahma);

// =====================
// PDF
// =====================
$pdf = new FPDF('L','mm','A4');
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
$pdf->Line(10, $pdf->GetY()+3, 290, $pdf->GetY()+3);
$pdf->Ln(8);

// =====================
// JUDUL
// =====================
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'LAPORAN TRANSAKSI',0,1,'C');

// =====================
// INFO FILTER
// =====================
$pdf->SetFont('Arial','',12);

if ($tgl_awal_rahma && $tgl_akhir_rahma) {
    $pdf->Cell(0,8,'Periode: '.$tgl_awal_rahma.' s/d '.$tgl_akhir_rahma,0,1,'C');
} else {
    $pdf->Cell(0,8,'Semua Transaksi',0,1,'C');
}

$pdf->Ln(5);

// =====================
// HEADER TABEL
// =====================
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(230,230,230);

$pdf->Cell(40,8,'ID Order',1,0,'C',true);
$pdf->Cell(35,8,'Tanggal',1,0,'C',true);
$pdf->Cell(50,8,'Total',1,0,'C',true);
$pdf->Cell(20,8,'Diskon',1,0,'C',true);
$pdf->Cell(50,8,'Bayar',1,0,'C',true);
$pdf->Cell(30,8,'Metode',1,0,'C',true);
$pdf->Cell(50,8,'Kembali',1,1,'C',true);

// =====================
// DATA
// =====================
$pdf->SetFont('Arial','',11);

while ($row = mysqli_fetch_assoc($query_rahma)) {

    $total_pendapatan_rahma += $row['total_rahma'];

    $pdf->Cell(40,8,$row['id_order_rahma'],1);
    $pdf->Cell(35,8,date('d-m-Y', strtotime($row['waktu_transaksi_rahma'])),1);
    $pdf->Cell(50,8,'Rp '.number_format($row['total_rahma']),1,0,'C');
    $pdf->Cell(20,8,$row['diskon_rahma'].'%',1,0,'C');
    $pdf->Cell(50,8,'Rp '.number_format($row['bayar_rahma']),1,0,'C');
    $pdf->Cell(30,8,$row['metode_bayar_rahma'] ?? 'Online',1,0,'C');
    $pdf->Cell(50,8,'Rp '.number_format($row['kembalian_rahma']),1,1,'C');
}

// =====================
// RINGKASAN
// =====================
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Total Transaksi: '.$total_transaksi_rahma,0,1);
$pdf->Cell(0,8,'Total Pendapatan: Rp '.number_format($total_pendapatan_rahma),0,1);

$rata_rahma = $total_transaksi_rahma > 0 
    ? $total_pendapatan_rahma / $total_transaksi_rahma 
    : 0;

$pdf->Cell(0,8,'Rata-rata: Rp '.number_format($rata_rahma),0,1);


// =====================
// NAMA FILE
// =====================
$nama_file_rahma = "Laporan_Transaksi";

// kalau pakai filter
if ($tgl_awal_rahma && $tgl_akhir_rahma) {
    $nama_file_rahma .= "_" . $tgl_awal_rahma . "_sampai_" . $tgl_akhir_rahma;
} else {
    $nama_file_rahma .= "_semua";
}

// tanggal hari ini
$nama_file_rahma .= "_" . date('Ymd');

// bersihin spasi
$nama_file_rahma = str_replace(' ', '_', $nama_file_rahma);


// =====================
// KOLOM TANDA TANGAN
// =====================

$pdf->Ln(12);

// Tanggal cetak — otomatis ambil tanggal hari ini
$tanggal_cetak_rahma = date('d-m-Y');

// Posisi kolom TTD di kanan bawah (mulai dari x=130)
$pdf->SetFont('Arial','',11);
$pdf->SetX(170);
$pdf->Cell(70,6,'Bandung, '.$tanggal_cetak_rahma,0,1,'C');

// Jarak kosong buat tanda tangan — kayak ruang di bawah surat resmi
$pdf->Ln(18);

// Garis tanda tangan
$x_awal_rahma = 170;
$x_akhir_rahma = 240;
$y_ttd_rahma = $pdf->GetY();
$pdf->Line($x_awal_rahma, $y_ttd_rahma, $x_akhir_rahma, $y_ttd_rahma);


$pdf->Ln(3);

// =====================
// OUTPUT
// =====================
$pdf->Output("I", $nama_file_rahma . ".pdf");