-- Database: konservasienergi_eamcik
CREATE DATABASE IF NOT EXISTS konservasienergi_eamcik CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE konservasienergi_eamcik;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  role ENUM('admin','guru','siswa') NOT NULL DEFAULT 'siswa',
  siswa_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  kelas VARCHAR(30) NOT NULL
);

CREATE TABLE IF NOT EXISTS perangkat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  lokasi VARCHAR(100) NOT NULL,
  daya_watt INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS checklist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  siswa_id INT NOT NULL,
  perangkat_id INT NOT NULL,
  status ENUM('Mati','Hidup') NOT NULL DEFAULT 'Mati',
  catatan TEXT,
  verif_status ENUM('Belum','Disetujui','Perlu Perbaikan') NOT NULL DEFAULT 'Belum',
  verif_oleh INT NULL,
  verif_catatan TEXT NULL,
  verif_at DATETIME NULL,
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (perangkat_id) REFERENCES perangkat(id) ON DELETE CASCADE
);

INSERT INTO siswa (nama,kelas) VALUES ('Contoh Siswa 1','XI RPL 2'),('Contoh Siswa 2','XI RPL 2');
INSERT INTO perangkat (nama,lokasi,daya_watt) VALUES
 ('Lampu LED','Ruang Kelas',12),('AC','Ruang Kelas',750),('Proyektor','Ruang Kelas',250),('Komputer','Lab RPL',200);
-- Akun awal dibuat otomatis saat halaman login pertama dibuka:
--   admin: eamm / eamm123   guru: guru / guru123   siswa: siswa / siswa123
