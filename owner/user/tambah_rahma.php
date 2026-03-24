<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

// Proses simpan data
if (isset($_POST['simpan_rahma'])) {
    $id_user_rahma = $_POST['id_user_rahma'];
    $username_rahma = $_POST['username_rahma'];
    $password_rahma = password_hash($_POST['password_rahma'], PASSWORD_DEFAULT);
    $nama_rahma = $_POST['nama_rahma'];
    $id_role_rahma = $_POST['id_role_rahma'];

    $query_rahma = mysqli_query($koneksiRahma, "INSERT INTO tbl_user_rahma 
    VALUES ('$id_user_rahma','$username_rahma','$password_rahma','$nama_rahma','$id_role_rahma')");

    if ($query_rahma) {
        header("Location: data_user_rahma.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tambah data</title>
</head>

<body>
    <!-- Generate ID User Otomatis -->
    <?php
    // ambil ID terakhir
    $result_rahma = mysqli_query(
        $koneksiRahma,
        "SELECT id_user_rahma 
     FROM tbl_user_rahma 
     ORDER BY id_user_rahma DESC 
     LIMIT 1"
    );

    $row_rahma = mysqli_fetch_assoc($result_rahma);

    if ($row_rahma) {
        $last_id_rahma = $row_rahma['id_user_rahma'];

        // ambil angka setelah USN
        $number_rahma = (int) substr($last_id_rahma, 3);
        $number_rahma++;

        $id_baru_rahma = "USN" . str_pad($number_rahma, 3, "0", STR_PAD_LEFT);
    } else {
        $id_baru_rahma = "USN001";
    }

    $data_role_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_role_rahma");
    ?>

    <!-- Form Tambah User -->
    <div style="width:300px; margin:50px auto; font-family:sans-serif;">
        <h3 style="text-align:center;">Tambah User</h3>

        <form method="POST" style="display:flex; flex-direction:column; gap:10px;">

            <input type="text" name="id_user_rahma" value="<?= $id_baru_rahma ?>" readonly style="padding:8px;">

            <input type="text" name="username_rahma" placeholder="Username" style="padding:8px;">

            <input type="password" name="password_rahma" placeholder="Password" style="padding:8px;">

            <input type="text" name="nama_rahma" placeholder="Nama" style="padding:8px;">

            <select name="id_role_rahma" style="padding:8px;">
                <?php while ($role_rahma = mysqli_fetch_assoc($data_role_rahma)) { ?>
                    <option value="<?= $role_rahma['id_role_rahma']; ?>">
                        <?= $role_rahma['role_rahma']; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" name="simpan_rahma"
                style="padding:10px; background:black; color:white; border:none; cursor:pointer;">
                Simpan
            </button>

        </form>
    </div>
</body>

</html>