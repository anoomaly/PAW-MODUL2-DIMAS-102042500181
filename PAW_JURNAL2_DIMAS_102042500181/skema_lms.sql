
CREATE DATABASE IF NOT EXISTS lms_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lms_lab;

DROP TABLE IF EXISTS tugas;
DROP TABLE IF EXISTS materi;
DROP TABLE IF EXISTS mata_kuliah;

CREATE TABLE mata_kuliah (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  dosen VARCHAR(100) NOT NULL,
  semester TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE materi (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul VARCHAR(150) NOT NULL,
  isi TEXT NOT NULL,
  tautan VARCHAR(255) NOT NULL,
  FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE tugas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul VARCHAR(150) NOT NULL,
  instruksi TEXT NOT NULL,
  tenggat DATE NOT NULL,
  status VARCHAR(20) NOT NULL,
  FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Data awal: 2 data per tabel
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
('PAW-01', 'Pemrograman Aplikasi Web', 'Budi Santoso', 3),
('BD-01', 'Basis Data', 'Siti Aminah', 2);

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
(1, 'Dasar PHP', 'Pengenalan sintaks PHP, variabel, dan array.', 'https://www.php.net/manual/id/'),
(2, 'Pengenalan SQL', 'Perintah dasar SELECT, INSERT, UPDATE, dan DELETE.', 'https://dev.mysql.com/doc/');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(1, 'Latihan CRUD', 'Buat CRUD sederhana dengan PHP dan MySQL.', '2026-11-15', 'Aktif'),
(2, 'Rancang Tabel', 'Buat rancangan tabel untuk sebuah toko buku.', '2026-11-20', 'Draf');
