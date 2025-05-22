<?php
include 'db.php';

$error = '';
$success = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $foto = 'default.png';
        $foto_asli = '';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_type = mime_content_type($file_tmp);
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $foto_asli = $_FILES['foto']['name']; // Simpan nama asli

            if (in_array($file_type, $allowed_types) && in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $foto = uniqid('foto_', true) . '.' . $ext;
                $upload_path = __DIR__ . '/uploads/' . $foto;

                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $error = "Gagal mengupload foto.";
                }
            } else {
                $error = "Format atau ekstensi file tidak didukung.";
            }
        }

        if (!$error) {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, foto, original_filename) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hash, $foto, $foto_asli);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Data berhasil disimpan.";
                $username = ''; // reset input
            } else {
                $error = "Gagal tambah data: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tambah Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Tambah Mahasiswa</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" style="max-width: 400px;">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username) ?>">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Foto (jpg, png, gif)</label>
            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/gif">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</body>
</html>
