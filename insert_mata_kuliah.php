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
if (empty($data['kode']) || empty($data['nama']) || empty($data['sks'])) {
    echo json_encode(['status' => 'error', 'message' => 'Kode, nama, and sks are required']);
    exit();
}

$kode = $data['kode'];
$nama = $data['nama'];
$sks = $data['sks'];
$id_dosen = $data['id_dosen'] ?? null; // Optional

// Validasi SKS
if (!is_numeric($sks) || $sks < 1 || $sks > 6) {
    echo json_encode(['status' => 'error', 'message' => 'SKS must be a number between 1 and 6']);
    exit();
}

// Validasi id_dosen jika diberikan
if ($id_dosen !== null) {
    $stmt_check = $conn->prepare("SELECT id FROM dosen WHERE id = ?");
    $stmt_check->bind_param("i", $id_dosen);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid id_dosen']);
        exit();
    }
    $stmt_check->close();
}

// Gunakan prepared statement untuk insert
$stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, sks, id_dosen) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("ssii", $kode, $nama, $sks, $id_dosen);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Mata kuliah inserted successfully', 'mata_kuliah_id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

<!-- Contoh cara test menggunakan Postman:

1. Buka Postman.
2. Pilih method POST.
3. Masukkan URL: http://localhost/insert_mata_kuliah.php
4. Pilih tab Body, pilih raw, dan format JSON.
5. Masukkan contoh request JSON di bawah ini.
6. Klik Send.

Contoh Request JSON:
{
    "kode": "CS501",
    "nama": "Machine Learning",
    "sks": 3,
    "id_dosen": 4
}

Contoh Response JSON Berhasil:
{
    "status": "success",
    "message": "Mata kuliah inserted successfully",
    "mata_kuliah_id": 16
}

Contoh Response JSON Gagal (data kosong):
{
    "status": "error",
    "message": "Kode, nama, and sks are required"
}

Contoh Response JSON Gagal (SKS invalid):
{
    "status": "error",
    "message": "SKS must be a number between 1 and 6"
}

Contoh Response JSON Gagal (duplicate kode):
{
    "status": "error",
    "message": "Insert failed: Duplicate entry 'CS101' for key 'kode'"
}
-->
