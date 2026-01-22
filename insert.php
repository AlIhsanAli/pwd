<?php
// Kode koneksi database menggunakan mysqli
$host = 'localhost';
$user = 'root';
$password = 'root';
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
if (empty($data['nim']) || empty($data['nama']) || empty($data['jurusan']) || empty($data['angkatan']) || empty($data['nama_pengguna']) || empty($data['kata_sandi'])) {
    echo json_encode(['status' => 'error', 'message' => 'NIM, nama, jurusan, angkatan, nama_pengguna, and kata_sandi are required']);
    exit();
}

$nim = $data['nim'];
$nama = $data['nama'];
$jurusan = $data['jurusan'];
$angkatan = $data['angkatan'];
$nama_pengguna = $data['nama_pengguna'];
$kata_sandi = password_hash($data['kata_sandi'], PASSWORD_DEFAULT); // Hash password untuk keamanan

// Mulai transaksi
$conn->begin_transaction();

try {
    // Insert ke tabel pengguna
    $stmt1 = $conn->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES (?, ?, 'mahasiswa')");
    if (!$stmt1) {
        throw new Exception('Prepare statement for pengguna failed: ' . $conn->error);
    }
    $stmt1->bind_param("ss", $nama_pengguna, $kata_sandi);
    if (!$stmt1->execute()) {
        throw new Exception('Insert pengguna failed: ' . $stmt1->error);
    }
    $id_pengguna = $stmt1->insert_id;
    $stmt1->close();

    // Insert ke tabel mahasiswa
    $stmt2 = $conn->prepare("INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_pengguna) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt2) {
        throw new Exception('Prepare statement for mahasiswa failed: ' . $conn->error);
    }
    $stmt2->bind_param("sssii", $nim, $nama, $jurusan, $angkatan, $id_pengguna);
    if (!$stmt2->execute()) {
        throw new Exception('Insert mahasiswa failed: ' . $stmt2->error);
    }
    $id_mahasiswa = $stmt2->insert_id;
    $stmt2->close();

    // Commit transaksi
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Mahasiswa inserted successfully', 'mahasiswa_id' => $id_mahasiswa, 'pengguna_id' => $id_pengguna]);

} catch (Exception $e) {
    // Rollback jika ada error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>

<!-- Contoh cara test menggunakan Postman:

1. Buka Postman.
2. Pilih method POST.
3. Masukkan URL: http://localhost/insert.php (sesuaikan dengan path file Anda).
4. Pilih tab Body, pilih raw, dan format JSON.
5. Masukkan contoh request JSON di bawah ini.
6. Klik Send.

Contoh Request JSON:
{
    "nama": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}

Contoh Response JSON Berhasil:
{
    "status": "success",
    "message": "User inserted successfully",
    "user_id": 1
}

Contoh Response JSON Gagal (data kosong):
{
    "status": "error",
    "message": "Nama, email, and password are required"
}

Contoh Response JSON Gagal (invalid JSON):
{
    "status": "error",
    "message": "Invalid JSON data"
}

Contoh Response JSON Gagal (method bukan POST):
{
    "status": "error",
    "message": "Only POST method allowed"
}
-->
