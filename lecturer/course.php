<?php
include '../config.php';
include '../includes/functions.php';
requireLecturer();

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header('Location: dashboard.php');
    exit();
}

// Verify the course belongs to this lecturer
$lecturer = getLecturerByUserId($conn, $_SESSION['user_id']);
$courses = getCoursesByLecturer($conn, $lecturer['id']);
$course = null;
foreach ($courses as $c) {
    if ($c['id'] == $course_id) {
        $course = $c;
        break;
    }
}
if (!$course) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade'])) {
    $enrollment_id = $_POST['enrollment_id'];
    $grade = $_POST['grade'];

    if (assignGrade($conn, $enrollment_id, $grade)) {
        $message = 'Grade assigned successfully.';
    } else {
        $message = 'Error assigning grade.';
    }
}

$enrollments = getEnrollmentsByCourse($conn, $course_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course - SIA Lecturer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">SIA - Lecturer</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2><?php echo $course['nama']; ?> (<?php echo $course['kode']; ?>)</h2>
        <p>SKS: <?php echo $course['sks']; ?></p>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3>Mahasiswa Terdaftar</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Semester</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?php echo $enrollment['nim']; ?></td>
                        <td><?php echo $enrollment['student_name']; ?></td>
                        <td><?php echo $enrollment['semester']; ?></td>
                        <td><?php echo $enrollment['nilai'] ?? 'Belum dinilai'; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gradeModal<?php echo $enrollment['id']; ?>">Berikan Nilai</button>
                        </td>
                    </tr>

                    <!-- Grade Modal -->
                    <div class="modal fade" id="gradeModal<?php echo $enrollment['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Berikan Nilai untuk <?php echo $enrollment['student_name']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                        <div class="mb-3">
                                            <label for="grade" class="form-label">Nilai</label>
                                            <select class="form-control" id="grade" name="grade" required>
                                                <option value="">Pilih Nilai</option>
                                                <option value="A">A</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B">B</option>
                                                <option value="B-">B-</option>
                                                <option value="C+">C+</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                                <option value="E">E</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Berikan Nilai</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
