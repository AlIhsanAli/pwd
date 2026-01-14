<?php
include '../config.php';
include '../includes/functions.php';
requireLecturer();

$lecturer = getLecturerByUserId($conn, $_SESSION['user_id']);
$courses = getCoursesByLecturer($conn, $lecturer['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - SIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">SIA - Lecturer</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Selamat datang, <?php echo $lecturer['nama']; ?>!</h2>
        <p>NIDN: <?php echo $lecturer['nidn']; ?> | Keahlian: <?php echo $lecturer['keahlian']; ?></p>

        <h3>Mata Kuliah Anda</h3>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course['nama']; ?> (<?php echo $course['kode']; ?>)</h5>
                            <p class="card-text">SKS: <?php echo $course['sks']; ?></p>
                            <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Kelola Mata Kuliah</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
