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

// =============================================
// AMBIL PARAMETER DARI URL
// =============================================

// Tangkap tanggal filter dari URL
$tanggal_mulai_rahma = $_GET["tanggal_mulai_rahma"] ?? date("Y-m-d");
$tanggal_akhir_rahma = $_GET["tanggal_akhir_rahma"] ?? date("Y-m-d");

// Tangkap jenis laporan: dinein | takeaway | semua
$jenis_rahma = $_GET["jenis"] ?? "semua";

// Validasi — kalau tanggal mulai lebih besar, tukar posisinya
if (strtotime($tanggal_mulai_rahma) > strtotime($tanggal_akhir_rahma)) {
    [$tanggal_mulai_rahma, $tanggal_akhir_rahma] = [$tanggal_akhir_rahma, $tanggal_mulai_rahma];
}

// Format tanggal ke DD-MM-YYYY untuk tampilan
$tgl_mulai_indo_rahma = date("d-m-Y", strtotime($tanggal_mulai_rahma));
$tgl_akhir_indo_rahma = date("d-m-Y", strtotime($tanggal_akhir_rahma));

// Nama kasir dari session
$nama_kasir_rahma = $_SESSION["nama_rahma"] ?? "Kasir";
$waktu_cetak_rahma = date("d-m-Y H:i:s");


// =============================================
// FUNGSI QUERY DATA LAPORAN PER JENIS
// =============================================

// Fungsi ini kayak mesin pencari data — kasih jenis pesanannya,
// dia langsung cariin semua menu yang terjual beserta totalnya
function getDataLaporan_rahma($koneksiRahma, $tgl_mulai, $tgl_akhir, $jenis)
{
    $filter_jenis_rahma = "";

    // Tambahkan filter jenis kalau bukan "semua"
    if ($jenis !== "semua") {
        $label_rahma = ($jenis === "dinein") ? "Dine In" : "Take Away";
        $filter_jenis_rahma = "AND o.jenis_pesanan_rahma = '$label_rahma'";
    }

    $query_rahma = mysqli_query($koneksiRahma, "
        SELECT 
            mn.id_menu_rahma,
            mn.nama_menu_rahma,
            SUM(d.qty_rahma)      AS total_qty_rahma,
            SUM(d.subtotal_rahma) AS total_harga_rahma
        FROM tbl_detail_order_rahma d
        LEFT JOIN tbl_menu_rahma mn  ON d.id_menu_rahma  = mn.id_menu_rahma
        LEFT JOIN tbl_order_rahma o  ON d.id_order_rahma = o.id_order_rahma
        WHERE o.waktu_order_rahma BETWEEN '$tgl_mulai' AND '$tgl_akhir'
        AND o.id_order_rahma IN (SELECT id_order_rahma FROM tbl_transaksi_rahma)
        $filter_jenis_rahma
        GROUP BY mn.id_menu_rahma
        ORDER BY total_harga_rahma DESC
    ");

    return $query_rahma;
}

// Fungsi ambil grand total dan total diskon per jenis
function getSummary_rahma($koneksiRahma, $tgl_mulai, $tgl_akhir, $jenis)
{
    $filter_jenis_rahma = "";

    if ($jenis !== "semua") {
        $label_rahma = ($jenis === "dinein") ? "Dine In" : "Take Away";
        $filter_jenis_rahma = "AND o.jenis_pesanan_rahma = '$label_rahma'";
    }

    // Query grand total
    $q_total_rahma = mysqli_query($koneksiRahma, "
        SELECT COALESCE(SUM(t.total_rahma), 0) AS grand_total_rahma
        FROM tbl_transaksi_rahma t
        LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
        WHERE o.waktu_order_rahma BETWEEN '$tgl_mulai' AND '$tgl_akhir'
        $filter_jenis_rahma
    ");

    // Query total diskon (nominal, dihitung dari persen)
    $q_diskon_rahma = mysqli_query($koneksiRahma, "
        SELECT COALESCE(SUM(t.diskon_rahma * t.total_rahma / (100 - t.diskon_rahma + t.diskon_rahma)), 0) AS total_diskon_nominal_rahma
        FROM tbl_transaksi_rahma t
        LEFT JOIN tbl_order_rahma o ON t.id_order_rahma = o.id_order_rahma
        WHERE o.waktu_order_rahma BETWEEN '$tgl_mulai' AND '$tgl_akhir'
        $filter_jenis_rahma
    ");

    $total_rahma = mysqli_fetch_assoc($q_total_rahma);
    $diskon_rahma = mysqli_fetch_assoc($q_diskon_rahma);

    return [
        "grand_total_rahma" => $total_rahma["grand_total_rahma"],
        "total_diskon_rahma" => (int) $diskon_rahma["total_diskon_nominal_rahma"],
    ];
}


// =============================================
// FUNGSI RENDER TABEL DI PDF (reusable)
// =============================================

// Fungsi ini kayak tukang cetak tabel —
// kasih judul + data, dia langsung gambar tabelnya di PDF
function renderTabelPDF_rahma($pdf, $judul_rahma, $data_rahma, $summary_rahma)
{

    // Judul bagian (contoh: "Laporan Dine In")
    $pdf->SetFont("Helvetica", "B", 12);
    $pdf->SetFillColor(212, 44, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 9, $judul_rahma, 0, 1, "L", true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(2);

    // Header kolom tabel
    $pdf->SetFont("Helvetica", "B", 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(10, 8, "No", 1, 0, "C", true);
    $pdf->Cell(80, 8, "Nama Menu", 1, 0, "L", true);
    $pdf->Cell(25, 8, "Jumlah", 1, 0, "C", true);
    $pdf->Cell(45, 8, "Total Harga", 1, 1, "R", true);

    // Baris data
    $pdf->SetFont("Helvetica", "", 9);
    $no_rahma = 1;
    $total_qty_rahma = 0;

    while ($row_rahma = mysqli_fetch_assoc($data_rahma)) {
        $pdf->Cell(10, 7, $no_rahma, 1, 0, "C");
        $pdf->Cell(80, 7, substr($row_rahma["nama_menu_rahma"], 0, 35), 1, 0, "L");
        $pdf->Cell(25, 7, $row_rahma["total_qty_rahma"], 1, 0, "C");
        $pdf->Cell(45, 7, "Rp " . number_format($row_rahma["total_harga_rahma"], 0, ",", "."), 1, 1, "R");
        $total_qty_rahma += $row_rahma["total_qty_rahma"];
        $no_rahma++;
    }

    // Baris total bawah tabel
    $pdf->SetFont("Helvetica", "B", 10);
    $pdf->Cell(10, 8, "", 0, 0);
    $pdf->Cell(80, 8, "TOTAL", 0, 0, "L");
    $pdf->Cell(25, 8, $total_qty_rahma, 0, 0, "C");
    $pdf->Cell(45, 8, "Rp " . number_format($summary_rahma["grand_total_rahma"], 0, ",", "."), 0, 1, "R");

    // Ringkasan diskon & grand total
    $pdf->Ln(2);
    $pdf->SetFont("Helvetica", "", 9);
    $pdf->Cell(0, 5, "Total Diskon : Rp " . number_format($summary_rahma["total_diskon_rahma"], 0, ",", "."), 0, 1, "R");
    $pdf->Cell(0, 5, "Grand Total  : Rp " . number_format($summary_rahma["grand_total_rahma"], 0, ",", "."), 0, 1, "R");
    $pdf->Ln(6);
}


// =============================================
// FUNGSI FOOTER (nama kasir + waktu cetak)
// =============================================

// Footer ini ditaro di bagian bawah halaman —
// kayak tanda tangan digital si kasir yang cetak laporan
function renderFooter_rahma($pdf, $nama_kasir_rahma, $waktu_cetak_rahma)
{
    $pdf->SetY(-30); // Paksa posisi ke 20mm dari bawah halaman
    $pdf->SetFont("Helvetica", "I", 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 5, "Dicetak oleh: " . $nama_kasir_rahma . "     |     Tanggal/Waktu: " . $waktu_cetak_rahma, 0, 1, "R");
    $pdf->SetTextColor(0, 0, 0);
}


// =============================================
// INISIALISASI PDF
// =============================================

$pdf_rahma = new FPDF("P", "mm", "A4");
$pdf_rahma->SetMargins(15, 15, 15);
$pdf_rahma->SetAutoPageBreak(true, 25); // Sisakan 25mm untuk footer
$pdf_rahma->AddPage();


// =============================================
// HEADER UTAMA PDF
// =============================================

$pdf_rahma->SetFont("Helvetica", "B", 18);
$pdf_rahma->Cell(0, 12, "LAPORAN PENJUALAN", 0, 1, "C");

$pdf_rahma->SetFont("Helvetica", "", 11);
$pdf_rahma->Cell(0, 7, "FAMIRESU IKO - Restoran Keluarga", 0, 1, "C");

$pdf_rahma->SetFont("Helvetica", "", 10);
$pdf_rahma->Cell(0, 6, "Periode: " . $tgl_mulai_indo_rahma . " s/d " . $tgl_akhir_indo_rahma, 0, 1, "C");

$pdf_rahma->Ln(6);

// Garis pemisah header
$pdf_rahma->SetDrawColor(212, 44, 0);
$pdf_rahma->SetLineWidth(0.8);
$pdf_rahma->Line(15, $pdf_rahma->GetY(), 195, $pdf_rahma->GetY());
$pdf_rahma->SetLineWidth(0.2);
$pdf_rahma->SetDrawColor(0, 0, 0);
$pdf_rahma->Ln(5);


// =============================================
// RENDER TABEL SESUAI JENIS
// =============================================

if ($jenis_rahma === "dinein") {

    // Cetak hanya tabel Dine In
    $data_rahma = getDataLaporan_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "dinein");
    $summary_rahma = getSummary_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "dinein");
    renderTabelPDF_rahma($pdf_rahma, "Laporan Dine In", $data_rahma, $summary_rahma);

} elseif ($jenis_rahma === "takeaway") {

    // Cetak hanya tabel Take Away
    $data_rahma = getDataLaporan_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "takeaway");
    $summary_rahma = getSummary_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "takeaway");
    renderTabelPDF_rahma($pdf_rahma, "Laporan Take Away", $data_rahma, $summary_rahma);

} else {

    // Cetak keduanya — Dine In dulu, lalu Take Away
    $data_di_rahma = getDataLaporan_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "dinein");
    $summary_di_rahma = getSummary_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "dinein");
    renderTabelPDF_rahma($pdf_rahma, "Laporan Dine In", $data_di_rahma, $summary_di_rahma);

    $data_ta_rahma = getDataLaporan_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "takeaway");
    $summary_ta_rahma = getSummary_rahma($koneksiRahma, $tanggal_mulai_rahma, $tanggal_akhir_rahma, "takeaway");
    renderTabelPDF_rahma($pdf_rahma, "Laporan Take Away", $data_ta_rahma, $summary_ta_rahma);

    // Tampilkan grand total kombinasi (Dine In + Take Away)
    $pdf_rahma->Ln(8);
    $pdf_rahma->SetFont("Helvetica", "B", 11);
    $pdf_rahma->SetFillColor(212, 44, 0);
    $pdf_rahma->SetTextColor(255, 255, 255);
    $pdf_rahma->Cell(0, 9, "GRAND TOTAL (DINE IN + TAKE AWAY)", 0, 1, "C", true);
    $pdf_rahma->SetTextColor(0, 0, 0);
    $pdf_rahma->Ln(3);

    // Hitung kombinasi
    $total_kombinasi_rahma = $summary_di_rahma["grand_total_rahma"] + $summary_ta_rahma["grand_total_rahma"];
    $diskon_kombinasi_rahma = $summary_di_rahma["total_diskon_rahma"] + $summary_ta_rahma["total_diskon_rahma"];

    $pdf_rahma->SetFont("Helvetica", "", 10);
    $pdf_rahma->Cell(0, 6, "Total Dine In      : Rp " . number_format($summary_di_rahma["grand_total_rahma"], 0, ",", "."), 0, 1, "R");
    $pdf_rahma->Cell(0, 6, "Total Take Away    : Rp " . number_format($summary_ta_rahma["grand_total_rahma"], 0, ",", "."), 0, 1, "R");
    $pdf_rahma->SetLineWidth(0.3);
    $pdf_rahma->SetDrawColor(0, 0, 0);
    $pdf_rahma->Line(15, $pdf_rahma->GetY() + 1, 195, $pdf_rahma->GetY() + 1);
    $pdf_rahma->Ln(2);

    $pdf_rahma->SetFont("Helvetica", "B", 11);
    $pdf_rahma->Cell(0, 7, "Grand Total        : Rp " . number_format($total_kombinasi_rahma, 0, ",", "."), 0, 1, "R");
    $pdf_rahma->SetFont("Helvetica", "", 10);
    $pdf_rahma->Cell(0, 6, "Total Diskon       : Rp " . number_format($diskon_kombinasi_rahma, 0, ",", "."), 0, 1, "R");
}


// =============================================
// FOOTER — nama kasir & waktu cetak
// =============================================

renderFooter_rahma($pdf_rahma, $nama_kasir_rahma, $waktu_cetak_rahma);


// =============================================
// OUTPUT PDF KE BROWSER
// =============================================

$nama_file_rahma = "laporan_" . $jenis_rahma . "_"
    . str_replace("-", "", $tanggal_mulai_rahma) . "_"
    . str_replace("-", "", $tanggal_akhir_rahma) . ".pdf";

$pdf_rahma->Output("I", $nama_file_rahma);
?>