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
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('mahasiswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('mahasiswa2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('mahasiswa3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('mahasiswa4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('mahasiswa5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa'),
('dosen1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen'),
('dosen2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen'),
('dosen3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen'),
('dosen4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen'),
('dosen5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen');

INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, id_pengguna) VALUES
('2020001', 'Ahmad Surya', 'Ilmu Komputer', 2020, 2),
('2020002', 'Budi Santoso', 'Ilmu Komputer', 2020, 3),
('2020003', 'Citra Dewi', 'Sistem Informasi', 2020, 4),
('2020004', 'Dedi Rahman', 'Teknik Elektro', 2020, 5),
('2020005', 'Eka Putri', 'Ilmu Komputer', 2020, 6);

INSERT INTO dosen (nidn, nama, keahlian, id_pengguna) VALUES
('1000001', 'Dr. Ahmad Fauzi', 'Sistem Basis Data', 7),
('1000002', 'Prof. Budi Santosa', 'Pemrograman Berorientasi Objek', 8),
('1000003', 'Dr. Citra Lestari', 'Jaringan Komputer', 9),
('1000004', 'Prof. Dedi Kusnadi', 'Kecerdasan Buatan', 10),
('1000005', 'Dr. Eka Sari', 'Sistem Operasi', 11);

INSERT INTO mata_kuliah (kode, nama, sks, id_dosen) VALUES
('CS101', 'Pengantar Pemrograman', 3, 1),
('CS102', 'Struktur Data dan Algoritma', 4, 2),
('CS201', 'Pemrograman Berorientasi Objek', 3, 2),
('CS202', 'Basis Data', 3, 1),
('CS301', 'Rekayasa Perangkat Lunak', 3, 5),
('CS302', 'Jaringan Komputer', 3, 3),
('CS401', 'Kecerdasan Buatan', 3, 4),
('CS402', 'Sistem Operasi', 3, 5),
('MATH101', 'Matematika Diskrit', 3, 1),
('MATH201', 'Kalkulus', 4, 1),
('SI101', 'Sistem Informasi', 3, 4),
('SI201', 'Analisis dan Perancangan Sistem', 3, 5),
('EL101', 'Dasar Teknik Elektro', 3, 3),
('EL201', 'Sistem Digital', 3, 3),
('WEB101', 'Pemrograman Web', 3, 1);

INSERT INTO pendaftaran (id_mahasiswa, id_mata_kuliah, semester) VALUES
(1, 1, '2023/2024-1'), (1, 2, '2023/2024-1'), (1, 9, '2023/2024-1'), (1, 10, '2023/2024-1'),
(2, 1, '2023/2024-1'), (2, 3, '2023/2024-1'), (2, 4, '2023/2024-1'), (2, 9, '2023/2024-1'),
(3, 1, '2023/2024-1'), (3, 11, '2023/2024-1'), (3, 12, '2023/2024-1'), (3, 15, '2023/2024-1'),
(4, 13, '2023/2024-1'), (4, 14, '2023/2024-1'), (4, 6, '2023/2024-1'), (4, 15, '2023/2024-1'),
(5, 1, '2023/2024-1'), (5, 2, '2023/2024-1'), (5, 3, '2023/2024-1'), (5, 4, '2023/2024-1');

INSERT INTO nilai (id_pendaftaran, nilai) VALUES
(1, 'A'), (2, 'A-'), (3, 'B+'), (4, 'B'),
(5, 'B+'), (6, 'A'), (7, 'A-'), (8, 'B'),
(9, 'A-'), (10, 'B+'), (11, 'A'), (12, 'B'),
(13, 'B+'), (14, 'A'), (15, 'B'), (16, 'A-'),
(17, 'A'), (18, 'A-'), (19, 'B+'), (20, 'B');
