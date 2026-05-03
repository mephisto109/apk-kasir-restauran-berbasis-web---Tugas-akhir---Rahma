<?php
// =====================================================
// File ini bertugas: minta Snap Token ke Midtrans
// Bayangkan kayak kamu "pesan tiket antrian" ke Midtrans —
// Midtrans kasih nomor token, lalu kita pakai token itu
// buat buka popup pembayaran di sisi pelanggan.
// =====================================================
ob_start();

session_start();

header('Content-Type: application/json');

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['error_rahma' => 'Method not allowed']);
    exit;
}

// Cek session valid
if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['error_rahma' => 'Unauthorized']);
    exit;
}

// Load konfigurasi Midtrans (Server Key, dll)
include '../config/midtrans_config_rahma.php';
include '../koneksi/koneksi_rahma.php';

// Ambil data dari body JSON yang dikirim bayar_online_rahma.php
$input_rahma     = json_decode(file_get_contents('php://input'), true);
$id_order_rahma  = $input_rahma['id_order_rahma'] ?? '';
$total_rahma     = (int) ($input_rahma['total_rahma'] ?? 0);

// Validasi input dasar
if (empty($id_order_rahma) || $total_rahma <= 0) {
    ob_end_clean();
    echo json_encode(['error_rahma' => 'Data tidak lengkap']);
    exit;
}

// Ambil detail order dari DB untuk dikirim ke Midtrans
$query_order_rahma = mysqli_query($koneksiRahma, "
    SELECT o.nama_pelanggan_rahma, o.id_user_rahma
    FROM tbl_order_rahma o
    WHERE o.id_order_rahma = '$id_order_rahma'
");
$order_data_rahma = mysqli_fetch_assoc($query_order_rahma);

if (!$order_data_rahma) {
    echo json_encode(['error_rahma' => 'Order tidak ditemukan']);
    exit;
}

// Ambil email user kalau member — Midtrans butuh data customer
$email_rahma = 'guest@famiresu.com'; // default untuk guest
if (!empty($order_data_rahma['id_user_rahma'])) {
    $id_u_rahma = $order_data_rahma['id_user_rahma'];
    $q_user_rahma = mysqli_query($koneksiRahma, "
        SELECT email_rahma FROM tbl_user_rahma WHERE id_user_rahma = '$id_u_rahma'
    ");
    $user_data_rahma = mysqli_fetch_assoc($q_user_rahma);
    $email_rahma = $user_data_rahma['email_rahma'] ?? $email_rahma;
}

// =====================================================
// Bangun payload yang dikirim ke Midtrans
// Format: https://docs.midtrans.com/reference/snap-api
// =====================================================
$payload_rahma = [
    'transaction_details' => [
        // order_id HARUS unik setiap transaksi di Midtrans
        // Kita pakai id_order + timestamp supaya unik walau order sama dicoba ulang
        'order_id'     => $id_order_rahma . '-' . time(),
        'gross_amount' => $total_rahma,
    ],
    'customer_details' => [
        'first_name' => $order_data_rahma['nama_pelanggan_rahma'],
        'email'      => $email_rahma,
    ],
    // Aktifkan semua metode pembayaran yang tersedia
    'enabled_payments' => [
        'credit_card', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va',
        'permata_va', 'other_va', 'gopay', 'shopeepay', 'qris',
    ],
    'credit_card' => [
        'secure' => true, // Aktifkan 3DS untuk keamanan kartu kredit
    ],
];

// =====================================================
// Kirim request ke Midtrans pakai cURL
// (cURL = kurir digital yang antar request ke server lain)
// =====================================================
$ch_rahma = curl_init();
curl_setopt_array($ch_rahma, [
    CURLOPT_URL            => MIDTRANS_SNAP_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload_rahma),
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
    ],
    // Matikan SSL verify peer + host untuk sandbox/localhost
    CURLOPT_SSL_VERIFYPEER => false,
    // CURLOPT_SSL_VERIFYHOST harus 0 atau 2, tapi untuk localhost kita set 0
    CURLOPT_SSL_VERIFYHOST => 0, 
]);

$response_raw_rahma = curl_exec($ch_rahma);
$http_code_rahma    = curl_getinfo($ch_rahma, CURLINFO_HTTP_CODE);
$curl_err_rahma     = curl_error($ch_rahma);
curl_close($ch_rahma);

// Kalau cURL sendiri error (bukan HTTP error)
if ($curl_err_rahma) {
    echo json_encode(['error_rahma' => 'Koneksi ke Midtrans gagal: ' . $curl_err_rahma]);
    exit;
}

// Parse response dari Midtrans
$response_rahma = json_decode($response_raw_rahma, true);

ob_end_clean();

// Kalau berhasil, Midtrans kasih token
if ($http_code_rahma === 201 && !empty($response_rahma['token'])) {
    echo json_encode([
        'snap_token_rahma' => $response_rahma['token'],
        'redirect_url_rahma' => $response_rahma['redirect_url'] ?? '',
    ]);
} else {
    // Log error untuk debugging
    error_log("Midtrans error [{$http_code_rahma}]: " . $response_raw_rahma);
    echo json_encode([
        'error_rahma' => 'Gagal mendapatkan token dari Midtrans',
        'detail_rahma' => $response_rahma['error_messages'] ?? [],
    ]);
}
exit;