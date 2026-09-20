-- Database: miniprojekdb
-- Skrip Database SIM-LAB Lengkap

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` ENUM('guru', 'siswa') NOT NULL DEFAULT 'siswa',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel guru
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_guru` VARCHAR(80) NOT NULL,
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
  PRIMARY KEY (`id_jadwal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun Default:
-- 1. Guru: username 'guru', password 'guru123'
-- 2. Siswa: username 'siswa', password 'siswa123'
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('guru', '$2y$10$tZ8QGZkEfvU1aP2p2p8dne6pI05kQ32G31c0h62p3hW5f7y8h8yye', 'Bapak Guru RPL', 'guru'),
('siswa', '$2y$10$wT/tK7tL3eA05kQ32G31c0h62p3hW5f7y8h8yye6pI05kQ32G31ce', 'Siswa RPL', 'siswa')
ON DUPLICATE KEY UPDATE `role` = VALUES(`role`);
