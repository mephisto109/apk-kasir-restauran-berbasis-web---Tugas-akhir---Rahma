<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Famiresu Iko</title>
</head>
<body>

<h2>Login</h2>

<form action="proses/proses_login_rahma.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button>
</form>

<a href="register_rahma.php">Join Member</a>
<br><br>
<a href="guest_rahma.php">Masuk Tanpa Member</a>


<?php
if(isset($_GET['error'])){
    echo "<p style='color:red;'>Login gagal!</p>";
}
?>

</body>
</html>
