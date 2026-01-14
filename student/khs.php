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
    <title>KHS - SIA Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">SIA - Student</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Kartu Hasil Studi (KHS)</h2>
        <p><strong>Mahasiswa:</strong> <?php echo $student['nama']; ?> (<?php echo $student['nim']; ?>)</p>
        <p><strong>Jurusan:</strong> <?php echo $student['jurusan']; ?> | <strong>Angkatan:</strong> <?php echo $student['angkatan']; ?></p>
        <p><strong>IPK Keseluruhan:</strong> <?php echo number_format($gpa, 2); ?></p>
        <a href="../pdf/khs_pdf.php" target="_blank" class="btn btn-danger mb-3">
            Print KHS (PDF)
        </a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode Mata Kuliah</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen</th>
                    <th>Semester</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?php echo $enrollment['kode']; ?></td>
                        <td><?php echo $enrollment['course_name']; ?></td>
                        <td><?php echo $enrollment['sks']; ?></td>
                        <td><?php echo $enrollment['lecturer_name'] ?? 'N/A'; ?></td>
                        <td><?php echo $enrollment['semester']; ?></td>
                        <td><?php echo $enrollment['nilai'] ?? 'Belum dinilai'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
