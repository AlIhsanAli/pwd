<?php
include '../config.php';
include '../includes/functions.php';
requireStudent();

$student = getStudentByUserId($conn, $_SESSION['user_id']);
$enrollments = getEnrollmentsByStudent($conn, $student['id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    $course_id = $_POST['course_id'];
    $semester = $_POST['semester'];

    if (enrollStudent($conn, $student['id'], $course_id, $semester)) {
        $message = 'Enrolled successfully.';
        $enrollments = getEnrollmentsByStudent($conn, $student['id']); // Refresh
    } else {
        $message = 'Error enrolling in course.';
    }
}

$courses = getAllCourses($conn);
$enrolled_course_ids = array_column($enrollments, 'course_id');
$available_courses = array_filter($courses, function($course) use ($enrolled_course_ids) {
    return !in_array($course['id'], $enrolled_course_ids);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRS - SIA Student</title>
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
        <h2>Kartu Rencana Studi (KRS)</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3>Pendaftaran Anda</h3>
        <a href="../pdf/krs_pdf.php" target="_blank" class="btn btn-danger mb-3">
            Print KRS (PDF)
        </a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode Mata Kuliah</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen</th>
                    <th>Semester</th>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Mata Kuliah Tersedia</h3>
        <div class="row">
            <?php foreach ($available_courses as $course): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course['nama']; ?> (<?php echo $course['kode']; ?>)</h5>
                            <p class="card-text">SKS: <?php echo $course['sks']; ?><br>Dosen: <?php echo $course['lecturer_name'] ?? 'Belum ditugaskan'; ?></p>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <input type="hidden" name="semester" value="2023/2024-1"> <!-- Default semester -->
                                <button type="submit" name="enroll" class="btn btn-success">Daftar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
