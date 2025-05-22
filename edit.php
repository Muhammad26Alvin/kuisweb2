<?php
include "db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Ambil data user berdasar id
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id LIMIT 1");
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data tidak ditemukan.";
    exit();
}
$data = mysqli_fetch_assoc($result);

// Proses update saat form disubmit
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Ambil nama foto lama
    $old_foto = $data['foto'];

    // Proses upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types) && in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Buat nama unik untuk file baru
            $new_foto = uniqid('foto_', true) . '.' . $ext;
            $upload_path = __DIR__ . '/uploads/' . $new_foto;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Hapus foto lama kalau bukan default.png dan file ada
                if ($old_foto != 'default.png' && file_exists(__DIR__ . '/uploads/' . $old_foto)) {
                    unlink(__DIR__ . '/uploads/' . $old_foto);
                }
                $foto_to_save = $new_foto;
            } else {
                echo "Gagal mengupload foto baru.";
                exit();
            }
        } else {
            echo "Format file foto tidak didukung.";
            exit();
        }
    } else {
        // Tidak upload foto baru, pakai foto lama
        $foto_to_save = $old_foto;
    }

    // Update username dan foto
    $update = mysqli_query($conn, "UPDATE users SET username='$username', foto='$foto_to_save' WHERE id=$id");

    if ($update) {
        echo "<script>
                alert('Data berhasil diupdate');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "Error update: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">
    <h2>Edit Data Mahasiswa</h2>
    <form method="POST" enctype="multipart/form-data" style="max-width: 500px;">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Foto Saat Ini</label><br>
            <?php
            $foto = !empty($data['foto']) && file_exists('uploads/' . $data['foto']) ? $data['foto'] : 'default.png';
            ?>
            <img src="uploads/<?= htmlspecialchars($foto) ?>" width="100" height="100" style="object-fit: cover; border-radius: 50%;">
        </div>

        <div class="mb-3">
            <label>Ganti Foto</label>
            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/gif">
        </div>

        <button type="submit" name="update" class="btn btn-warning">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
