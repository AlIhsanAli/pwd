<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Default XAMPP user
define('DB_PASS', 'root'); // Default XAMPP password
define('DB_NAME', 'sia_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    // Database created or already exists
} else {
    die("Error creating database: " . $conn->error);
}

// Select database
$conn->select_db(DB_NAME);

// Create tables if they don't exist
$tables_sql = "
CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengguna VARCHAR(50) UNIQUE NOT NULL,
    kata_sandi VARCHAR(255) NOT NULL,
    peran ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    angkatan INT NOT NULL,
    id_pengguna INT UNIQUE,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    keahlian VARCHAR(255),
    id_pengguna INT UNIQUE,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    sks INT NOT NULL,
    id_dosen INT,
    FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pendaftaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mahasiswa INT NOT NULL,
    id_mata_kuliah INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    terdaftar_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mahasiswa) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (id_mata_kuliah) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    UNIQUE KEY pendaftaran_unik (id_mahasiswa, id_mata_kuliah, semester)
);

CREATE TABLE IF NOT EXISTS nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftaran INT UNIQUE NOT NULL,
    nilai VARCHAR(5) NOT NULL,
    diberikan_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pendaftaran) REFERENCES pendaftaran(id) ON DELETE CASCADE
);
";

if ($conn->multi_query($tables_sql)) {
    do {
        // Consume all results
    } while ($conn->next_result());
} else {
    die("Error creating tables: " . $conn->error);
}

// Insert sample data if tables are empty
$check_users = $conn->query("SELECT COUNT(*) as count FROM pengguna");
if ($check_users->fetch_assoc()['count'] == 0) {
    $sample_data_sql = "
    INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES
    ('admin', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('mahasiswa1', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
    ('dosen1', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen');

    INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_pengguna) VALUES
    ('12345678', 'John Doe', 'Teknik Informatika', 2020, 2);

    INSERT INTO dosen (nidn, nama, keahlian, id_pengguna) VALUES
    ('98765432', 'Dr. Jane Smith', 'Basis Data', 3);

    INSERT INTO mata_kuliah (kode, nama, sks, id_dosen) VALUES
    ('TI101', 'Pemrograman Dasar', 3, 1),
    ('TI102', 'Struktur Data', 4, 1);

    INSERT INTO pendaftaran (id_mahasiswa, id_mata_kuliah, semester) VALUES
    (1, 1, '2023/2024-1'),
    (1, 2, '2023/2024-1');

    INSERT INTO nilai (id_pendaftaran, nilai) VALUES
    (1, 'A'),
    (2, 'B+');
    ";

    if ($conn->multi_query($sample_data_sql)) {
        do {
            // Consume all results
        } while ($conn->next_result());
    }
}

// Start session
session_start();

// Function to get user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

// Function to redirect if not admin
function requireAdmin() {
    if (getUserRole() !== 'admin') {
        header('Location: index.php');
        exit();
    }
}

// Function to redirect if not lecturer
function requireLecturer() {
    if (getUserRole() !== 'dosen') {
        header('Location: index.php');
        exit();
    }
}

// Function to redirect if not student
function requireStudent() {
    if (getUserRole() !== 'mahasiswa') {
        header('Location: index.php');
        exit();
    }
}
?>
