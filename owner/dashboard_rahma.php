<?php
session_start();

// Cegah cache supaya ga bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['id_user_rahma'])){
    header("Location: ../login_rahma.php");
    exit;
}

if($_SESSION['id_role_rahma'] !== 'R001'){
    header("Location: ../login_rahma.php");
    exit;
}
?>
<!DOCTYPE html>
<?php include '../templates/navbar_rahma.php'; ?>

<script>
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>

<html>
