<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Join Member</title>
</head>
<body>

<h2>Join Member</h2>

<form action="proses/proses_register_rahma.php" method="POST">
    <label>Nama:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Daftar</button>
</form>

<a href="login_rahma.php">Sudah punya akun?</a>

</body>
</html>
