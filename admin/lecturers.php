<?php
include '../config.php';
include '../includes/functions.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nidn = $_POST['nidn'];
        $name = $_POST['name'];
        $expertise = $_POST['expertise'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (addLecturer($conn, $nidn, $name, $expertise, $username, $password)) {
            $message = 'Lecturer added successfully.';
        } else {
            $message = 'Error adding lecturer.';
        }
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        // Delete lecturer and user
        $stmt = $conn->prepare("DELETE FROM dosen WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = 'Lecturer deleted successfully.';
        } else {
            $message = 'Error deleting lecturer.';
        }
    }
}

$lecturers = getAllLecturers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers - SIA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">SIA - Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Kelola Dosen</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addLecturerModal">Tambah Dosen</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>NIDN</th>
                    <th>Nama</th>
                    <th>Keahlian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturers as $lecturer): ?>
                    <tr>
                        <td><?php echo $lecturer['nidn']; ?></td>
                        <td><?php echo $lecturer['nama']; ?></td>
                        <td><?php echo $lecturer['keahlian']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $lecturer['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Lecturer Modal -->
    <div class="modal fade" id="addLecturerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nidn" class="form-label">NIDN</label>
                            <input type="text" class="form-control" id="nidn" name="nidn" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="expertise" class="form-label">Keahlian</label>
                            <input type="text" class="form-control" id="expertise" name="expertise">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" name="add" class="btn btn-primary">Tambah Dosen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
