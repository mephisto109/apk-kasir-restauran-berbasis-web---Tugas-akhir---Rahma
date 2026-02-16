<?php
session_start();

if (!isset($_SESSION['tbl_user_rahma']) && !isset($_SESSION['guest'])) {
    header("Location: ../login_rahma.php");
    exit;
}

?>
