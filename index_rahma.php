<?php
/*
require_once "config/session_rahma.php";

if (isset($_SESSION['tbl_user_rahma'])) {

    switch ($_SESSION['tbl_user_rahma']['tbl_role_rahma']) {
        case 'owner':
            header("Location: owner/dashboard_rahma.php");
            exit;

        case 'kasir':
            header("Location: kasir/dashboard_rahma.php");
            exit;

        case 'chef':
            header("Location: chef/dashboard_rahma.php");
            exit;

        case 'pelanggan':
            header("Location: pelanggan/menu_rahma.php");
            exit;

        default:
            header("Location: login_rahma.php");
            exit;
    }

} else {
    header("Location: login_rahma.php");
    exit;
}
