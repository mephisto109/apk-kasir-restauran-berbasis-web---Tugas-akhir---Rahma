<?php
session_start();
include '../../koneksi/koneksi_rahma.php';

$id_user_rahma = $_GET['id_user_rahma'];

$data_rahma = mysqli_query($koneksiRahma, 
    "SELECT * FROM tbl_user_rahma WHERE id_user_rahma='$id_user_rahma'");
$row_rahma = mysqli_fetch_assoc($data_rahma);

// ambil data role
$data_role_rahma = mysqli_query($koneksiRahma, "SELECT * FROM tbl_role_rahma");


// Proses update data
if(isset($_POST['update_rahma'])){
    $username_rahma = $_POST['username_rahma'];
    $nama_rahma = $_POST['nama_rahma'];
    $id_role_rahma = $_POST['id_role_rahma'];

    // Jika password diisi, update password juga
    if(!empty($_POST['password_rahma'])){
        $password_rahma = password_hash($_POST['password_rahma'], PASSWORD_DEFAULT);

        $query_rahma = mysqli_query($koneksiRahma, "UPDATE tbl_user_rahma SET
            username_rahma='$username_rahma',
            password_rahma='$password_rahma',
            nama_rahma='$nama_rahma',
            id_role_rahma='$id_role_rahma'
            WHERE id_user_rahma='$id_user_rahma'");
    } else {
        $query_rahma = mysqli_query($koneksiRahma, "UPDATE tbl_user_rahma SET
            username_rahma='$username_rahma',
            nama_rahma='$nama_rahma',
            id_role_rahma='$id_role_rahma'
            WHERE id_user_rahma='$id_user_rahma'");
    }
    // Redirect ke halaman data user setelah update
    if($query_rahma){
        header("Location: data_user_rahma.php");
    }
}
?>

<div style="width:300px; margin:50px auto; font-family:sans-serif;">
    <!-- Form Edit User -->
    <h3 style="text-align:center;">Edit User</h3>

    <form method="POST" style="display:flex; flex-direction:column; gap:10px;">

        <input type="hidden" name="id_user_rahma"
            value="<?= $row_rahma['id_user_rahma']; ?>" 
            readonly
            style="padding:8px;">

        Username:
        <input type="text" name="username_rahma" 
            value="<?= $row_rahma['username_rahma']; ?>"
            style="padding:8px;">

        password:
        <input type="password" name="password_rahma" 
            placeholder="Kosongkan jika tidak diubah"
            style="padding:8px;">

        nama:
        <input type="text" name="nama_rahma" 
            value="<?= $row_rahma['nama_rahma']; ?>"
            style="padding:8px;">

            <!-- Dropdown Role -->
        role:
        <select name="id_role_rahma" style="padding:8px;">
            <?php while($role_rahma = mysqli_fetch_assoc($data_role_rahma)){ ?>
                <option value="<?= $role_rahma['id_role_rahma']; ?>"
                    <?= ($role_rahma['id_role_rahma'] == $row_rahma['id_role_rahma']) ? 'selected' : ''; ?>>
                    <?= $role_rahma['role_rahma']; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" name="update_rahma"
            style="padding:10px; background:black; color:white; border:none; cursor:pointer;">
            Update
        </button>

    </form>
</div>