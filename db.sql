-- Skema database untuk Sistem Informasi Akademik (SIA)

CREATE DATABASE IF NOT EXISTS sia_db;
USE sia_db;

-- Tabel pengguna untuk autentikasi
CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengguna VARCHAR(50) UNIQUE NOT NULL,
    kata_sandi VARCHAR(255) NOT NULL,
    peran ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel mahasiswa
CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    angkatan INT NOT NULL,
    id_pengguna INT UNIQUE,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
);

-- Tabel dosen
CREATE TABLE dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    keahlian VARCHAR(255),
    id_pengguna INT UNIQUE,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
);

-- Tabel mata kuliah
CREATE TABLE mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    sks INT NOT NULL,
    id_dosen INT,
    FOREIGN KEY (id_dosen) REFERENCES dosen(id) ON DELETE SET NULL
);

-- Tabel pendaftaran (KRS)
CREATE TABLE pendaftaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mahasiswa INT NOT NULL,
    id_mata_kuliah INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    terdaftar_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mahasiswa) REFERENCES mahasiswa(id) ON DELETE CASCADE,
    FOREIGN KEY (id_mata_kuliah) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    UNIQUE KEY pendaftaran_unik (id_mahasiswa, id_mata_kuliah, semester)
);

-- Tabel nilai (KHS)
CREATE TABLE nilai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pendaftaran INT UNIQUE NOT NULL,
    nilai VARCHAR(5) NOT NULL,
    dinilai_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pendaftaran) REFERENCES pendaftaran(id) ON DELETE CASCADE
);

-- Hapus data contoh lama jika ada
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE nilai;
TRUNCATE TABLE pendaftaran;
TRUNCATE TABLE mata_kuliah;
TRUNCATE TABLE dosen;
TRUNCATE TABLE mahasiswa;
TRUNCATE TABLE pengguna;
SET FOREIGN_KEY_CHECKS = 1;

-- Masukkan data contoh
INSERT INTO pengguna (nama_pengguna, kata_sandi, peran) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- kata sandi: password
('mahasiswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('dosen1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen');

INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_pengguna) VALUES
('12345678', 'John Doe', 'Ilmu Komputer', 2020, 2);

INSERT INTO dosen (nidn, nama, keahlian, id_pengguna) VALUES
('98765432', 'Dr. Jane Smith', 'Sistem Basis Data', 3);

INSERT INTO mata_kuliah (kode, nama, sks, id_dosen) VALUES
('CS101', 'Pengantar Pemrograman', 3, 1),
('CS102', 'Struktur Data', 4, 1);

INSERT INTO pendaftaran (id_mahasiswa, id_mata_kuliah, semester) VALUES
(1, 1, '2023/2024-1'),
(1, 2, '2023/2024-1');

INSERT INTO nilai (id_pendaftaran, nilai) VALUES
(1, 'A'),
(2, 'B+');
