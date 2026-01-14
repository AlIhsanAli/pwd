<?php
// Helper functions for SIA system

// Get student details by user ID
function getStudentByUserId($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id_pengguna = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get lecturer details by user ID
function getLecturerByUserId($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM dosen WHERE id_pengguna = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get all students
function getAllStudents($conn) {
    $result = $conn->query("SELECT * FROM mahasiswa");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all lecturers
function getAllLecturers($conn) {
    $result = $conn->query("SELECT * FROM dosen");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all courses
function getAllCourses($conn) {
    $result = $conn->query("SELECT m.*, d.nama as lecturer_name FROM mata_kuliah m LEFT JOIN dosen d ON m.id_dosen = d.id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get courses by lecturer ID
function getCoursesByLecturer($conn, $lecturer_id) {
    $stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id_dosen = ?");
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get enrollments by student ID
function getEnrollmentsByStudent($conn, $student_id) {
    $stmt = $conn->prepare("SELECT p.*, m.nama as course_name, m.kode, m.sks, n.nilai, d.nama as lecturer_name FROM pendaftaran p LEFT JOIN mata_kuliah m ON p.id_mata_kuliah = m.id LEFT JOIN dosen d ON m.id_dosen = d.id LEFT JOIN nilai n ON p.id = n.id_pendaftaran WHERE p.id_mahasiswa = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get enrollments by course ID (for lecturer)
function getEnrollmentsByCourse($conn, $course_id) {
    $stmt = $conn->prepare("SELECT p.*, m.nama as student_name, m.nim, n.nilai FROM pendaftaran p LEFT JOIN mahasiswa m ON p.id_mahasiswa = m.id LEFT JOIN nilai n ON p.id = n.id_pendaftaran WHERE p.id_mata_kuliah = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add student
function addStudent($conn, $nim, $name, $major, $year, $username, $password) {
    // Insert user first
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES (?, ?, 'mahasiswa')");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $user_id = $conn->insert_id;

    // Insert student
    $stmt = $conn->prepare("INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_pengguna) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $nim, $name, $major, $year, $user_id);
    return $stmt->execute();
}

// Add lecturer
function addLecturer($conn, $nidn, $name, $expertise, $username, $password) {
    // Insert user first
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES (?, ?, 'dosen')");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $user_id = $conn->insert_id;

    // Insert lecturer
    $stmt = $conn->prepare("INSERT INTO dosen (nidn, nama, keahlian, id_pengguna) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nidn, $name, $expertise, $user_id);
    return $stmt->execute();
}

// Add course
function addCourse($conn, $code, $name, $sks, $lecturer_id) {
    $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, sks, id_dosen) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $code, $name, $sks, $lecturer_id);
    return $stmt->execute();
}

// Enroll student in course
function enrollStudent($conn, $student_id, $course_id, $semester) {
    // Check if already enrolled
    $check_stmt = $conn->prepare("SELECT id FROM pendaftaran WHERE id_mahasiswa = ? AND id_mata_kuliah = ? AND semester = ?");
    $check_stmt->bind_param("iis", $student_id, $course_id, $semester);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        return false; // Already enrolled
    }

    $stmt = $conn->prepare("INSERT INTO pendaftaran (id_mahasiswa, id_mata_kuliah, semester) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $course_id, $semester);
    return $stmt->execute();
}

// Assign grade
function assignGrade($conn, $enrollment_id, $grade) {
    $stmt = $conn->prepare("INSERT INTO nilai (id_pendaftaran, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = ?");
    $stmt->bind_param("iss", $enrollment_id, $grade, $grade);
    return $stmt->execute();
}

// Calculate GPA
function calculateGPA($enrollments) {
    $total_points = 0;
    $total_sks = 0;
    $grade_points = ['A' => 4, 'A-' => 3.67, 'B+' => 3.33, 'B' => 3, 'B-' => 2.67, 'C+' => 2.33, 'C' => 2, 'D' => 1, 'E' => 0];

    foreach ($enrollments as $enrollment) {
        if (isset($enrollment['nilai']) && isset($grade_points[$enrollment['nilai']])) {
            $total_points += $grade_points[$enrollment['nilai']] * $enrollment['sks'];
            $total_sks += $enrollment['sks'];
        }
    }

    return $total_sks > 0 ? round($total_points / $total_sks, 2) : 0;
}
?>
