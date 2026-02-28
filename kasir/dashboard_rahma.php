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

if($_SESSION['id_role_rahma'] !== 'R002'){
    header("Location: ../login_rahma.php");
    exit;
}

echo "Hallo kasir! " . $_SESSION['username_rahma'];
?>
<?php include '../templates/navbar_rahma.php'; ?>
<form action="../logout_rahma.php" method="POST" style="display:inline;">
    <button type="submit" name="logout">Logout</button>
</form>

<script>
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>


