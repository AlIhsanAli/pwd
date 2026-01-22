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
if (empty($data['nama_pengguna']) || empty($data['kata_sandi']) || empty($data['peran'])) {
    echo json_encode(['status' => 'error', 'message' => 'Nama_pengguna, kata_sandi, and peran are required']);
    exit();
}

$nama_pengguna = $data['nama_pengguna'];
$kata_sandi = password_hash($data['kata_sandi'], PASSWORD_DEFAULT); // Hash password untuk keamanan
$peran = $data['peran'];

// Validasi peran
if (!in_array($peran, ['mahasiswa', 'dosen', 'admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Peran must be mahasiswa, dosen, or admin']);
    exit();
}

// Gunakan prepared statement untuk insert
$stmt = $conn->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sss", $nama_pengguna, $kata_sandi, $peran);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Pengguna inserted successfully', 'pengguna_id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Insert failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

<!-- Contoh cara test menggunakan Postman:

1. Buka Postman.
2. Pilih method POST.
3. Masukkan URL: http://localhost/insert_pengguna.php
4. Pilih tab Body, pilih raw, dan format JSON.
5. Masukkan contoh request JSON di bawah ini.
6. Klik Send.

Contoh Request JSON:
{
    "nama_pengguna": "mahasiswa6",
    "kata_sandi": "password123",
    "peran": "mahasiswa"
}

Contoh Response JSON Berhasil:
{
    "status": "success",
    "message": "Pengguna inserted successfully",
    "pengguna_id": 12
}

Contoh Response JSON Gagal (data kosong):
{
    "status": "error",
    "message": "Nama_pengguna, kata_sandi, and peran are required"
}

Contoh Response JSON Gagal (peran invalid):
{
    "status": "error",
    "message": "Peran must be mahasiswa, dosen, or admin"
}

Contoh Response JSON Gagal (duplicate nama_pengguna):
{
    "status": "error",
    "message": "Insert failed: Duplicate entry 'mahasiswa1' for key 'nama_pengguna'"
}
-->
