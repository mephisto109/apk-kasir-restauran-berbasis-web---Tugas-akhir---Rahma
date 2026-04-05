<?php
session_start();

// Cegah cache supaya ga bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['id_user_rahma'])) {
    header("Location: ../login_rahma.php");
    exit;
}

if ($_SESSION['id_role_rahma'] !== 'R002') {
    header("Location: ../login_rahma.php");
    exit;
}

include '../koneksi/koneksi_rahma.php';
include '../templates/navbar_rahma.php';
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard Kasir</title>
</head>

<body>

</body>

</html>

<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>