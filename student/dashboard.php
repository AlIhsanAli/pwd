<?php
include '../config.php';
include '../includes/functions.php';
requireStudent();

$student = getStudentByUserId($conn, $_SESSION['user_id']);
$enrollments = getEnrollmentsByStudent($conn, $student['id']);
$gpa = calculateGPA($enrollments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="#">SIA - Student</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Welcome, <?php echo $student['nama']; ?>!</h2>
        <p>NIM: <?php echo $student['nim']; ?> | Jurusan: <?php echo $student['jurusan']; ?> | Angkatan: <?php echo $student['angkatan']; ?></p>
        <p>Current GPA: <?php echo number_format($gpa, 2); ?></p>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kartu Rencana Studi (KRS)</h5>
                        <p class="card-text">Manage your course enrollments.</p>
                        <a href="krs.php" class="btn btn-primary">View KRS</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kartu Hasil Studi (KHS)</h5>
                        <p class="card-text">View your grades and academic record.</p>
                        <a href="khs.php" class="btn btn-primary">View KHS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
