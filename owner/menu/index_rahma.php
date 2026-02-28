<?php
session_start();
include '../../koneksi/koneksi_rahma.php';
include '../../templates/navbar_rahma.php'; 

$queryRahma= mysqli_query($koneksiRahma, 
"SELECT * FROM tbl_menu_rahma ORDER BY id_menu_rahma DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow rounded-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Data Menu</h5>
            <a href="tambah_rahma.php" class="btn btn-primary btn-sm">+ Tambah Menu</a>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no_rahma=1; while($row_rahma=mysqli_fetch_assoc($queryRahma)){ ?>
                    <tr>
                        <td><?= $no_rahma++; ?></td>
                        <td><?= $row_rahma['nama_menu_rahma']; ?></td>
                        <td>Rp <?= number_format($row_rahma['harga_rahma']); ?></td>
                        <td><?= $row_rahma['deskripsi_rahma']; ?></td>
                        <td>
                            <?php if($row_rahma['foto_rahma']){ ?>
                                <img src="../../../uploads/<?= $row_rahma['gambar_rahma']; ?>" width="70">
                            <?php } ?>
                        </td>
                        <td>
                            <a href="edit_menu_rahma.php?id=<?= $row_rahma['id_menu_rahma']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus_menu_rahma.php?id=<?= $row_rahma['id_menu_rahma']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>