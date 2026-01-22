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
if (empty($data['nidn']) || empty($data['nama']) || empty($data['nama_pengguna']) || empty($data['kata_sandi'])) {
    echo json_encode(['status' => 'error', 'message' => 'NIDN, nama, nama_pengguna, and kata_sandi are required']);
    exit();
}

$nidn = $data['nidn'];
$nama = $data['nama'];
$keahlian = $data['keahlian'] ?? null; // Optional
$nama_pengguna = $data['nama_pengguna'];
$kata_sandi = password_hash($data['kata_sandi'], PASSWORD_DEFAULT); // Hash password untuk keamanan

// Mulai transaksi
$conn->begin_transaction();

try {
    // Insert ke tabel pengguna
    $stmt1 = $conn->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES (?, ?, 'dosen')");
    if (!$stmt1) {
        throw new Exception('Prepare statement for pengguna failed: ' . $conn->error);
    }
    $stmt1->bind_param("ss", $nama_pengguna, $kata_sandi);
    if (!$stmt1->execute()) {
        throw new Exception('Insert pengguna failed: ' . $stmt1->error);
    }
    $id_pengguna = $stmt1->insert_id;
    $stmt1->close();

    // Insert ke tabel dosen
    $stmt2 = $conn->prepare("INSERT INTO dosen (nidn, nama, keahlian, id_pengguna) VALUES (?, ?, ?, ?)");
    if (!$stmt2) {
        throw new Exception('Prepare statement for dosen failed: ' . $conn->error);
    }
    $stmt2->bind_param("sssi", $nidn, $nama, $keahlian, $id_pengguna);
    if (!$stmt2->execute()) {
        throw new Exception('Insert dosen failed: ' . $stmt2->error);
    }
    $id_dosen = $stmt2->insert_id;
    $stmt2->close();

    // Commit transaksi
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Dosen inserted successfully', 'dosen_id' => $id_dosen, 'pengguna_id' => $id_pengguna]);

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
3. Masukkan URL: http://localhost/insert_dosen.php
4. Pilih tab Body, pilih raw, dan format JSON.
5. Masukkan contoh request JSON di bawah ini.
6. Klik Send.

Contoh Request JSON:
{
    "nidn": "1000006",
    "nama": "Dr. Ahmad Rahman",
    "keahlian": "Data Mining",
    "nama_pengguna": "dosen6",
    "kata_sandi": "password123"
}

Contoh Response JSON Berhasil:
{
    "status": "success",
    "message": "Dosen inserted successfully",
    "dosen_id": 6,
    "pengguna_id": 17
}

Contoh Response JSON Gagal (data kosong):
{
    "status": "error",
    "message": "NIDN, nama, nama_pengguna, and kata_sandi are required"
}

Contoh Response JSON Gagal (duplicate NIDN):
{
    "status": "error",
    "message": "Insert dosen failed: Duplicate entry '1000001' for key 'nidn'"
}
-->
