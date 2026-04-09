<?php
session_start();

if (!isset($_SESSION['id_user_rahma']) && !isset($_SESSION['guest_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

// Inisialisasi keranjang kalau belum ada
if (!isset($_SESSION['keranjang_rahma'])) {
    $_SESSION['keranjang_rahma'] = [];
}

// ===== AKSI GET: hapus item =====
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_menu_rahma = $_GET['id'] ?? '';
    if (isset($_SESSION['keranjang_rahma'][$id_menu_rahma])) {
        unset($_SESSION['keranjang_rahma'][$id_menu_rahma]);
    }
    header("Location: ../pelanggan/keranjang_rahma.php");
    exit;
}

// ===== AKSI POST =====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi_rahma    = $_POST['aksi_rahma'] ?? 'tambah';
    $id_menu_rahma = $_POST['id_menu_rahma'] ?? '';

    // Aksi: update qty
    if ($aksi_rahma == 'update' && !empty($id_menu_rahma)) {
        $qty_rahma = (int) ($_POST['qty_rahma'] ?? 1);
        if ($qty_rahma < 1) $qty_rahma = 1;
        if (isset($_SESSION['keranjang_rahma'][$id_menu_rahma])) {
            $_SESSION['keranjang_rahma'][$id_menu_rahma]['qty_rahma'] = $qty_rahma;
        }
        header("Location: ../pelanggan/keranjang_rahma.php");
        exit;
    }

    // Aksi: simpan catatan
    if ($aksi_rahma == 'catatan' && !empty($id_menu_rahma)) {
        $catatan_rahma = $_POST['catatan_rahma'] ?? '';
        if (isset($_SESSION['keranjang_rahma'][$id_menu_rahma])) {
            $_SESSION['keranjang_rahma'][$id_menu_rahma]['catatan_rahma'] = $catatan_rahma;
        }
        header("Location: ../pelanggan/keranjang_rahma.php");
        exit;
    }

    // Aksi: tambah item baru dari halaman menu
    if ($aksi_rahma == 'tambah' || !isset($_POST['aksi_rahma'])) {
        $qty_rahma      = (int) ($_POST['qty_rahma'] ?? 1);
        $redirect_rahma = $_POST['redirect_rahma'] ?? 'menu_rahma.php';

        if (empty($id_menu_rahma) || $qty_rahma < 1) {
            header("Location: ../pelanggan/$redirect_rahma");
            exit;
        }

        include '../koneksi/koneksi_rahma.php';

        $query_menu_rahma = mysqli_query($koneksiRahma, "
            SELECT * FROM tbl_menu_rahma
            WHERE id_menu_rahma = '$id_menu_rahma'
            AND status_menu_rahma = 'tersedia'
        ");

        if (mysqli_num_rows($query_menu_rahma) == 0) {
            header("Location: ../pelanggan/$redirect_rahma");
            exit;
        }

        $menu_rahma = mysqli_fetch_assoc($query_menu_rahma);

        // Kalau sudah ada di keranjang, tambah qty
        if (isset($_SESSION['keranjang_rahma'][$id_menu_rahma])) {
            $_SESSION['keranjang_rahma'][$id_menu_rahma]['qty_rahma'] += $qty_rahma;
        } else {
            // Kalau belum ada, tambah sebagai item baru
            $_SESSION['keranjang_rahma'][$id_menu_rahma] = [
                'id_menu_rahma'   => $id_menu_rahma,
                'nama_menu_rahma' => $menu_rahma['nama_menu_rahma'],
                'harga_rahma'     => $menu_rahma['harga_rahma'],
                'qty_rahma'       => $qty_rahma,
                'catatan_rahma'   => '',
            ];
        }

        header("Location: ../pelanggan/$redirect_rahma");
        exit;
    }
}

header("Location: ../pelanggan/keranjang_rahma.php");
exit;
?>