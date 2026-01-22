<?php
// Kode koneksi database menggunakan mysqli
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'sia_db'; // Database SIA

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Set header untuk JSON response
header('Content-Type: application/json');

// Hanya izinkan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method allowed']);
    exit();
}

// Ambil data JSON dari body request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit();
}

// Validasi sederhana: data tidak boleh kosong
if (empty($data['id_mahasiswa']) || empty($data['id_mata_kuliah']) || empty($data['semester'])) {
    echo json_encode(['status' => 'error', 'message' => 'Id_mahasiswa, id_mata_kuliah, and semester are required']);
    exit();
}

$id_mahasiswa = $data['id_mahasiswa'];
$id_mata_kuliah = $data['id_mata_kuliah'];
$semester = $data['semester'];

// Validasi id_mahasiswa dan id_mata_kuliah
$stmt_check_mahasiswa = $conn->prepare("SELECT id FROM mahasiswa WHERE id = ?");
$stmt_check_mahasiswa->bind_param("i", $id_mahasiswa);
$stmt_check_mahasiswa->execute();
if ($stmt_check_mahasiswa->get_result()->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid id_mahasiswa']);
    exit();
}
$stmt_check_mahasiswa->close();

$stmt_check_matkul = $conn->prepare("SELECT id FROM mata_kuliah WHERE id = ?");
$stmt_check_matkul->bind_param("i", $id_mata_kuliah);
$stmt_check_matkul->execute();
if ($stmt_check_matkul->get_result()->num_rows == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid id_mata_kuliah']);
    exit();
}
$stmt_check_matkul->close();

// Gunakan prepared statement untuk insert
$stmt = $conn->prepare("INSERT INTO pendaftaran (id_mahasiswa, id_mata_kuliah, semester) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("iis", $id_mahasiswa, $id_mata_kuliah, $semester);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Pendaftaran inserted successfully', 'pendaftaran_id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

<!-- Contoh cara test menggunakan Postman:

1. Buka Postman.
2. Pilih method POST.
3. Masukkan URL: http://localhost/insert_pendaftaran.php
4. Pilih tab Body, pilih raw, dan format JSON.
5. Masukkan contoh request JSON di bawah ini.
6. Klik Send.

Contoh Request JSON:
{
    "id_mahasiswa": 6,
    "id_mata_kuliah": 1,
    "semester": "2023/2024-2"
}

Contoh Response JSON Berhasil:
{
    "status": "success",
    "message": "Pendaftaran inserted successfully",
    "pendaftaran_id": 21
}

Contoh Response JSON Gagal (data kosong):
{
    "status": "error",
    "message": "Id_mahasiswa, id_mata_kuliah, and semester are required"
}

Contoh Response JSON Gagal (duplicate pendaftaran):
{
    "status": "error",
    "message": "Insert failed: Duplicate entry '1-1-2023/2024-1' for key 'pendaftaran_unik'"
}
-->
