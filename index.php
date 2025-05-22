<?php 
include "db.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Data Mahasiswa</h2>
    <a href="tambah.php" class="btn btn-primary mb-3">+ Tambah Mahasiswa</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $result = mysqli_query($conn, "SELECT * FROM users");
            
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $foto = !empty($row['foto']) ? $row['foto'] : 'default.png';

                    // Cek apakah file foto ada di folder uploads
                    if (!file_exists('uploads/' . $foto)) {
                        $foto = 'default.png';
                    }

                    $username = htmlspecialchars($row['username'], ENT_QUOTES);

                    echo "<tr>
                        <td>{$no}</td>
                        <td><img src='uploads/{$foto}' width='60' height='60' style='object-fit: cover; border-radius: 50%;' alt='Foto {$username}'></td>
                        <td>{$username}</td>
                        <td>
                            <a href='edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='hapus.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
                        </td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Data tidak ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
