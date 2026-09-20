<?php
include 'koneksi.php';
include 'auth.php';

// Wajib login untuk mengakses dashboard
require_login();

$user = get_logged_in_user();
$isGuru = is_guru();

$query = "SELECT j.id_jadwal, g.nama_guru, g.mata_pelajaran, k.nama_kelas, k.tingkatan, j.ruang_lab, j.hari, j.jam_mulai, j.jam_selesai 
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_mulai ASC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIM-LAB</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Font Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-computer"></i></div>
            <span class="logo-text">SIM-LAB</span>
        </div>
        <div class="navbar-links">
            <a href="index.php" class="active">Home</a>
            <a href="jadwal lab/jadwal.php">Jadwal Lab</a>
            <?php if ($isGuru): ?>
                <a href="guru/index_guru.php">Guru</a>
                <a href="kelas/index_kelas.php">Kelas</a>
            <?php endif; ?>
        </div>
        <div class="navbar-right">
            <?= render_navbar_user(''); ?>
        </div>
    </nav>

    <main class="main-container">
        <!-- Notifikasi Akses Ditolak -->
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'akses_ditolak'): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-shield-halved"></i>
                <span><b>Akses Ditolak:</b> Akun Siswa hanya memiliki hak akses untuk melihat jadwal laboratorium. Anda tidak diizinkan menambah, mengubah, atau menghapus data.</span>
            </div>
        <?php endif; ?>

        <h1>Dashboard</h1>
        <p>SIM-LAB adalah Sistem Informasi Manajemen Laboratorium yang dirancang untuk mempermudah pengelolaan jadwal laboratorium, data guru, dan data kelas. Dengan SIM-LAB, Anda dapat dengan mudah mengatur jadwal penggunaan laboratorium, melihat informasi guru dan kelas, serta memastikan kelancaran operasional laboratorium.</p>

        <div class="hero-banner">
            <h2>Selamat Datang di SIM-LAB, <?= htmlspecialchars($user['nama_lengkap']); ?>!</h2>
            <p class="hero-description">
                Anda login sebagai <b><?= $isGuru ? 'Guru (Akses Kelola Jadwal)' : 'Siswa (Mode Lihat Jadwal)'; ?></b>.
            </p>
            <a href="jadwal lab/jadwal.php" class="btn-hero">
                <i class="fa-solid fa-calendar-days"></i> <?= $isGuru ? 'Kelola Jadwal Lab' : 'Lihat Jadwal Lab'; ?>
            </a>
        </div>

        <div class="card-table">
            <div class="header-table">
                <h2>Daftar Jadwal Laboratorium</h2>
                <?php if ($isGuru): ?>
                    <a href="jadwal lab/tambah_jadwal.php" class="btn btn-tambah">
                        <i class="fa-solid fa-plus"></i> Tambah Data
                    </a>
                <?php else: ?>
                    <span class="badge-readonly">
                        <i class="fa-solid fa-lock"></i> Mode Lihat Jadwal (Siswa)
                    </span>
                <?php endif; ?>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Nama Kelas</th>
                        <th>Tingkatan</th>
                        <th>Ruang Lab</th>
                        <th>Hari</th>
                        <th>Jam Pelajaran</th>
                        <?php if ($isGuru): ?>
                            <th class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0):
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                                <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                                <td><?= htmlspecialchars($row['tingkatan']); ?></td>
                                <td><?= htmlspecialchars($row['ruang_lab']); ?></td>
                                <td><?= htmlspecialchars($row['hari']); ?></td>
                                <td>Jam ke-<?= htmlspecialchars($row['jam_mulai']); ?> s/d <?= htmlspecialchars($row['jam_selesai']); ?></td>
                                <?php if ($isGuru): ?>
                                    <td class="text-center">
                                        <a href="jadwal lab/edit_jadwal.php?id_jadwal=<?= $row['id_jadwal']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                        <a href="jadwal lab/hapus_jadwal.php?id_jadwal=<?= $row['id_jadwal']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin hapus jadwal ini?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isGuru ? '9' : '8'; ?>" class="text-center">Belum ada data jadwal lab.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>