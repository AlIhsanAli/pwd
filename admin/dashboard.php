<?php
include '../config.php';
include '../includes/functions.php';
requireAdmin();

$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">SIA - Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard Admin</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kelola Mahasiswa</h5>
                        <p class="card-text">Tambah, edit, atau hapus data mahasiswa.</p>
                        <a href="students.php" class="btn btn-primary">Kelola Mahasiswa</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kelola Dosen</h5>
                        <p class="card-text">Tambah, edit, atau hapus data dosen.</p>
                        <a href="lecturers.php" class="btn btn-primary">Kelola Dosen</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kelola Mata Kuliah</h5>
                        <p class="card-text">Tambah, edit, atau hapus data mata kuliah.</p>
                        <a href="courses.php" class="btn btn-primary">Kelola Mata Kuliah</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
