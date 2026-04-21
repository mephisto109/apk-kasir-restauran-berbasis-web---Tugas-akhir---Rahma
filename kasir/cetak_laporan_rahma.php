<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["id_user_rahma"])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION["id_role_rahma"] !== "R002") {
    header("Location: ../login_rahma.php");
    exit;
}

include "../koneksi/koneksi_rahma.php";
include "../assets/fpdf/fpdf.php";

// Tangkap tanggal dari URL
$tanggal_mulai_rahma = $_GET["tanggal_mulai_rahma"] ?? date("Y-m-d");
$tanggal_akhir_rahma = $_GET["tanggal_akhir_rahma"] ?? date("Y-m-d");

// Validasi jika tanggal mulai lebih besar dari tanggal akhir
if (strtotime($tanggal_mulai_rahma) > strtotime($tanggal_akhir_rahma)) {
    $temp = $tanggal_mulai_rahma;
    $tanggal_mulai_rahma = $tanggal_akhir_rahma;
    $tanggal_akhir_rahma = $temp;
}

// Format tanggal untuk tampilan laporan (DD-MM-YYYY)
$tgl_mulai_indo = date("d-m-Y", strtotime($tanggal_mulai_rahma));
$tgl_akhir_indo = date("d-m-Y", strtotime($tanggal_akhir_rahma));

// Query Data Laporan
$query_laporan_rahma = mysqli_query($koneksiRahma, "
    SELECT 
        mn.id_menu_rahma,
        mn.nama_menu_rahma,
        SUM(d.qty_rahma) AS total_qty_rahma,
        SUM(d.subtotal_rahma) AS total_harga_rahma
    FROM tbl_detail_order_rahma d
    LEFT JOIN tbl_menu_rahma mn ON d.id_menu_rahma = mn.id_menu_rahma
    LEFT JOIN tbl_order_rahma o ON d.id_order_rahma = o.id_order_rahma
    WHERE o.waktu_order_rahma BETWEEN '$tanggal_mulai_rahma' AND '$tanggal_akhir_rahma'
    AND o.id_order_rahma IN (SELECT id_order_rahma FROM tbl_transaksi_rahma)
    GROUP BY mn.id_menu_rahma
    ORDER BY total_harga_rahma DESC
");

// Query Grand Total
$query_total_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(t.total_rahma), 0) AS grand_total_rahma
    FROM tbl_transaksi_rahma t
    LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
    WHERE o.waktu_order_rahma BETWEEN '$tanggal_mulai_rahma' AND '$tanggal_akhir_rahma'
");
$data_total_rahma = mysqli_fetch_assoc($query_total_rahma);
$grand_total_rahma = $data_total_rahma["grand_total_rahma"];

// Query Total Diskon
$query_diskon_rahma = mysqli_query($koneksiRahma, "
    SELECT COALESCE(SUM(t.diskon_rahma), 0) AS total_diskon_rahma
    FROM tbl_transaksi_rahma t
    LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
    WHERE o.waktu_order_rahma BETWEEN '$tanggal_mulai_rahma' AND '$tanggal_akhir_rahma'
");
$data_diskon_rahma = mysqli_fetch_assoc($query_diskon_rahma);
$total_diskon_rahma = $data_diskon_rahma["total_diskon_rahma"];

// Inisialisasi FPDF
$pdf_rahma = new FPDF("P", "mm", "A4");
$pdf_rahma->AddPage();
$pdf_rahma->SetMargins(15, 15, 15);

// Header Laporan
$pdf_rahma->SetFont("Helvetica", "B", 18);
$pdf_rahma->Cell(0, 12, "LAPORAN PENJUALAN", 0, 1, "C");
$pdf_rahma->SetFont("Helvetica", "", 11);
$pdf_rahma->Cell(0, 8, "FAMIRESU IKO - Restoran Keluarga", 0, 1, "C");
$pdf_rahma->SetFont("Helvetica", "", 10);

// Tampilan Tanggal sesuai permintaan
$pdf_rahma->Cell(0, 6, "Dari Tanggal: " . $tgl_mulai_indo . " Sampai Tanggal: " . $tgl_akhir_indo, 0, 1, "C");

$pdf_rahma->Ln(5);

// Tabel Header
$pdf_rahma->SetFont("Helvetica", "B", 10);
$pdf_rahma->SetFillColor(220, 220, 220);
$pdf_rahma->Cell(10, 8, "No", 1, 0, "C", true);
$pdf_rahma->Cell(80, 8, "Nama Menu", 1, 0, "L", true);
$pdf_rahma->Cell(25, 8, "Jumlah", 1, 0, "C", true);
$pdf_rahma->Cell(45, 8, "Total Harga", 1, 1, "R", true);

// Isi Tabel
$pdf_rahma->SetFont("Helvetica", "", 9);
$nomor_rahma = 1;
$total_qty_keseluruhan_rahma = 0;

while ($row_laporan_rahma = mysqli_fetch_assoc($query_laporan_rahma)) {
    $pdf_rahma->Cell(10, 7, $nomor_rahma, 1, 0, "C");
    $pdf_rahma->Cell(80, 7, substr($row_laporan_rahma["nama_menu_rahma"], 0, 30), 1, 0, "L");
    $pdf_rahma->Cell(25, 7, $row_laporan_rahma["total_qty_rahma"], 1, 0, "C");
    $pdf_rahma->Cell(45, 7, "Rp " . number_format($row_laporan_rahma["total_harga_rahma"], 0, ",", "."), 1, 1, "R");
    $total_qty_keseluruhan_rahma += $row_laporan_rahma["total_qty_rahma"];
    $nomor_rahma++;
}

// Footer Tabel
$pdf_rahma->SetFont("Helvetica", "B", 10);
$pdf_rahma->Cell(10, 8, "", 0, 0);
$pdf_rahma->Cell(80, 8, "TOTAL", 0, 0, "L");
$pdf_rahma->Cell(25, 8, $total_qty_keseluruhan_rahma, 0, 0, "C");
$pdf_rahma->Cell(45, 8, "Rp " . number_format($grand_total_rahma, 0, ",", "."), 0, 1, "R");

$pdf_rahma->Ln(5);

// Ringkasan Pembayaran
$pdf_rahma->SetFont("Helvetica", "", 9);
$pdf_rahma->Cell(0, 5, "Total Diskon: Rp " . number_format($total_diskon_rahma, 0, ",", "."), 0, 1);
$pdf_rahma->Cell(0, 5, "Grand Total: Rp " . number_format($grand_total_rahma, 0, ",", "."), 0, 1);

$pdf_rahma->Ln(8);
$pdf_rahma->SetFont("Helvetica", "I", 8);
$pdf_rahma->Cell(0, 5, "Laporan dibuat: " . date("d-m-Y H:i:s"), 0, 1, "C");

// Nama File PDF
$nama_file_rahma = "laporan_" . str_replace("-", "", $tanggal_mulai_rahma) . "_" . str_replace("-", "", $tanggal_akhir_rahma) . ".pdf";
$pdf_rahma->Output("I", $nama_file_rahma);
?>