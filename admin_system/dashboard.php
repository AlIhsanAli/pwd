<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM mahasiswa")->fetch_assoc()['count'];
$total_lecturers = $conn->query("SELECT COUNT(*) as count FROM dosen")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM mata_kuliah")->fetch_assoc()['count'];
$total_enrollments = $conn->query("SELECT COUNT(*) as count FROM pendaftaran")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Welcome to Admin Dashboard</h2>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text"><?php echo $total_students; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Lecturers</h5>
                        <p class="card-text"><?php echo $total_lecturers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Courses</h5>
                        <p class="card-text"><?php echo $total_courses; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Enrollments</h5>
                        <p class="card-text"><?php echo $total_enrollments; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Students</h5>
                        <p class="card-text">Add, edit, or delete students.</p>
                        <a href="manage_students.php" class="btn btn-primary">Manage Students</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Manage Lecturers</h5>
                        <p class="card-text">Add, edit, or delete lecturers.</p>
                        <a href="manage_lecturers.php" class="btn btn-primary">Manage Lecturers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
