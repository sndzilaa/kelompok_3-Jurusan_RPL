-- Database: miniprojekdb
-- Skrip Database SIM-LAB Lengkap

CREATE DATABASE IF NOT EXISTS `miniprojekdb`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `miniprojekdb`;

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` ENUM('guru', 'siswa') NOT NULL DEFAULT 'siswa',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  CONSTRAINT `chk_users_siswa_nama_tanpa_angka`
    CHECK (`role` <> 'siswa' OR `nama_lengkap` NOT REGEXP '[0-9]')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel guru
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_guru` TEXT(80) NOT NULL,
  `mata_pelajaran` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id_guru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` VARCHAR(80) NOT NULL,
  `tingkatan` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel jadwal_lab
CREATE TABLE IF NOT EXISTS `jadwal_lab` (
  `id_jadwal` INT(11) NOT NULL AUTO_INCREMENT,
  `id_kelas` INT(11) NOT NULL,
  `id_guru` INT(11) NOT NULL,
  `ruang_lab` VARCHAR(100) NOT NULL,
  `hari` VARCHAR(80) NOT NULL,
  `jam_mulai` INT(11) NOT NULL DEFAULT 1,
  `jam_selesai` INT(11) NOT NULL DEFAULT 2,
  `jam_pelajaran` VARCHAR(90) DEFAULT '',
  `waktu_mulai` TIME DEFAULT NULL,
  `waktu_selesai` TIME DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan kolom waktu pada database lama tanpa menggandakan kolom yang sudah ada.
SET @has_waktu_mulai = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jadwal_lab'
    AND COLUMN_NAME = 'waktu_mulai'
);
SET @sql = IF(
  @has_waktu_mulai = 0,
  'ALTER TABLE `jadwal_lab` ADD COLUMN `waktu_mulai` TIME DEFAULT NULL',
  'SELECT 1'
);
PREPARE migration FROM @sql;
EXECUTE migration;
DEALLOCATE PREPARE migration;

SET @has_waktu_selesai = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'jadwal_lab'
    AND COLUMN_NAME = 'waktu_selesai'
);
SET @sql = IF(
  @has_waktu_selesai = 0,
  'ALTER TABLE `jadwal_lab` ADD COLUMN `waktu_selesai` TIME DEFAULT NULL',
  'SELECT 1'
);
PREPARE migration FROM @sql;
EXECUTE migration;
DEALLOCATE PREPARE migration;

-- Akun Default:
-- 1. Guru: username 'guru', password 'guru123'
-- 2. Siswa: username 'siswa', password 'siswa123'
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('guru', '$2y$10$tZ8QGZkEfvU1aP2p2p8dne6pI05kQ32G31c0h62p3hW5f7y8h8yye', 'Bapak Guru RPL', 'guru'),
('siswa', '$2y$10$wT/tK7tL3eA05kQ32G31c0h62p3hW5f7y8h8yye6pI05kQ32G31ce', 'Siswa RPL', 'siswa')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);
