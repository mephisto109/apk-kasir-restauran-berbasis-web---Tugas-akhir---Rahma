<?php
ob_start();
session_start();

include '../koneksi/koneksi_rahma.php';

$aksi_rahma = $_POST['aksi_rahma'] ?? '';

// ===== AKSI REGISTER =====
if ($aksi_rahma == 'register') {

    $nama_rahma = $_POST['nama_rahma'] ?? '';
    $username_rahma = $_POST['username_rahma'] ?? '';
    $password_rahma = $_POST['password_rahma'] ?? '';
    $konfirmasi_password_rahma = $_POST['konfirmasi_password_rahma'] ?? '';

    // Validasi password cocok
    if ($password_rahma !== $konfirmasi_password_rahma) {
        header("Location: ../register_rahma.php?error=password_mismatch");
        exit;
    }

    // Cek username sudah ada atau belum
    $query_cek_rahma = mysqli_query($koneksiRahma, "
        SELECT id_user_rahma FROM tbl_user_rahma
        WHERE username_rahma = '$username_rahma'
    ");

    if (mysqli_num_rows($query_cek_rahma) > 0) {
        header("Location: ../register_rahma.php?error=username_exists");
        exit;
    }

    // Generate ID user baru
    $query_last_user_rahma = mysqli_query($koneksiRahma, "
        SELECT id_user_rahma FROM tbl_user_rahma
        ORDER BY id_user_rahma DESC LIMIT 1
    ");
    $last_user_rahma = mysqli_fetch_assoc($query_last_user_rahma);
    $last_id_user_rahma = $last_user_rahma['id_user_rahma'] ?? 'USN000';
    $angka_user_rahma = (int) substr($last_id_user_rahma, 3) + 1;
    $id_user_rahma = 'USN' . str_pad($angka_user_rahma, 3, '0', STR_PAD_LEFT);

    // Hash password pakai bcrypt
    $hashed_password_rahma = password_hash($password_rahma, PASSWORD_BCRYPT);

    // Insert user baru dengan role member (R003)
    $insert_rahma = mysqli_query($koneksiRahma, "
        INSERT INTO tbl_user_rahma
            (id_user_rahma, username_rahma, password_rahma, nama_rahma, id_role_rahma)
        VALUES
            ('$id_user_rahma', '$username_rahma', '$hashed_password_rahma', '$nama_rahma', 'R003')
    ");

    if ($insert_rahma) {
        header("Location: ../login_rahma.php?sukses=1");
    } else {
        header("Location: ../register_rahma.php?error=gagal");
    }
    exit;
}

// ===== AKSI TAMBAH USER (owner) =====
if ($aksi_rahma == 'tambah') {
    if (!isset($_SESSION['id_user_rahma']) || $_SESSION['id_role_rahma'] !== 'R001') {
        header("Location: ../login_rahma.php");
        exit;
    }

    $nama_rahma = $_POST['nama_rahma'] ?? '';
    $username_rahma = $_POST['username_rahma'] ?? '';
    $password_rahma = $_POST['password_rahma'] ?? '';
    $id_role_rahma = $_POST['id_role_rahma'] ?? '';

    // Cek username sudah ada
    $query_cek_rahma = mysqli_query($koneksiRahma, "
        SELECT id_user_rahma FROM tbl_user_rahma
        WHERE username_rahma = '$username_rahma'
    ");

    if (mysqli_num_rows($query_cek_rahma) > 0) {
        header("Location: ../owner/user/tambah_rahma.php?error=username_exists");
        exit;
    }

    // Generate ID user baru
    $query_last_user_rahma = mysqli_query($koneksiRahma, "
        SELECT id_user_rahma FROM tbl_user_rahma
        ORDER BY id_user_rahma DESC LIMIT 1
    ");
    $last_user_rahma = mysqli_fetch_assoc($query_last_user_rahma);
    $last_id_user_rahma = $last_user_rahma['id_user_rahma'] ?? 'USN000';
    $angka_user_rahma = (int) substr($last_id_user_rahma, 3) + 1;
    $id_user_baru_rahma = 'USN' . str_pad($angka_user_rahma, 3, '0', STR_PAD_LEFT);

    $hashed_password_rahma = password_hash($password_rahma, PASSWORD_BCRYPT);

    $insert_rahma = mysqli_query($koneksiRahma, "
        INSERT INTO tbl_user_rahma
            (id_user_rahma, username_rahma, password_rahma, nama_rahma, id_role_rahma)
        VALUES
            ('$id_user_baru_rahma', '$username_rahma', '$hashed_password_rahma', '$nama_rahma', '$id_role_rahma')
    ");

    if ($insert_rahma) {
        header("Location: ../owner/user/data_user_rahma.php?sukses=tambah");
    } else {
        header("Location: ../owner/user/tambah_rahma.php?error=gagal");
    }
    exit;
}

// ===== AKSI EDIT USER (owner) =====
if ($aksi_rahma == 'edit') {
    if (!isset($_SESSION['id_user_rahma']) || $_SESSION['id_role_rahma'] !== 'R001') {
        header("Location: ../login_rahma.php");
        exit;
    }

    $id_user_rahma = $_POST['id_user_rahma'] ?? '';
    $nama_rahma = $_POST['nama_rahma'] ?? '';
    $username_rahma = $_POST['username_rahma'] ?? '';
    $id_role_rahma = $_POST['id_role_rahma'] ?? '';
    $password_rahma = $_POST['password_rahma'] ?? '';

    // Kalau password diisi, update password juga
    if (!empty($password_rahma)) {
        $hashed_password_rahma = password_hash($password_rahma, PASSWORD_BCRYPT);
        mysqli_query($koneksiRahma, "
            UPDATE tbl_user_rahma
            SET nama_rahma = '$nama_rahma',
                username_rahma = '$username_rahma',
                password_rahma = '$hashed_password_rahma',
                id_role_rahma = '$id_role_rahma'
            WHERE id_user_rahma = '$id_user_rahma'
        ");
    } else {
        // Kalau password kosong, jangan update password
        mysqli_query($koneksiRahma, "
            UPDATE tbl_user_rahma
            SET nama_rahma = '$nama_rahma',
                username_rahma = '$username_rahma',
                id_role_rahma = '$id_role_rahma'
            WHERE id_user_rahma = '$id_user_rahma'
        ");
    }

    header("Location: ../owner/user/data_user_rahma.php?sukses=edit");
    exit;
}

// ===== AKSI HAPUS USER (owner) =====
if ($aksi_rahma == 'hapus') {
    if (!isset($_SESSION['id_user_rahma']) || $_SESSION['id_role_rahma'] !== 'R001') {
        header("Location: ../login_rahma.php");
        exit;
    }

    $id_user_rahma = $_POST['id_user_rahma'] ?? '';

    mysqli_query($koneksiRahma, "
        DELETE FROM tbl_user_rahma
        WHERE id_user_rahma = '$id_user_rahma'
    ");

    header("Location: ../owner/user/data_user_rahma.php?sukses=hapus");
    exit;
}

// Kalau aksi tidak dikenal
header("Location: ../login_rahma.php");
exit;
?>