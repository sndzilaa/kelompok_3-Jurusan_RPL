<?php
include 'koneksi.php';

$query = "SELECT j.id_jadwal, g.nama_guru, g.mata_pelajaran, k.nama_kelas, j.ruang_lab, j.hari, j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_pelajaran";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM-LAB</title>
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
            <a href="guru/index_guru.php">Guru</a>
            <a href="kelas/index_kelas.php">Kelas</a>
        </div>
    </nav>

    <main class="main-container">
        <h1>Dashboard</h1>
        <p>SIM-LAB adalah Sistem Informasi Manajemen Laboratorium yang dirancang untuk mempermudah pengelolaan jadwal laboratorium, data guru, dan data kelas. Dengan SIM-LAB, Anda dapat dengan mudah mengatur jadwal penggunaan laboratorium, melihat informasi guru dan kelas, serta memastikan kelancaran operasional laboratorium.</p>

        <div class="hero-banner">
            <h2>Selamat Datang di SIM-LAB</h2>
            <p class="hero-description">Sistem Informasi Manajemen Laboratorium</p>
            <a href="jadwal lab/jadwal.php" class="btn-hero"><i class="fa-solid fa-calendar-days"></i> Kelola Jadwal Lab</a>
        </div>

        <div class="card-table">
            <div class="header-table">
                <h2>Daftar Jadwal Laboratorium</h2>
                <a href="jadwal lab/tambah_jadwal.php" class="btn btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Data</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Ruang Lab</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($result && mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                                <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                                <td><?= htmlspecialchars($row['ruang_lab']); ?></td>
                                <td><?= htmlspecialchars($row['hari']); ?></td>
                                <td><?= htmlspecialchars($row['jam_pelajaran']); ?></td>
                                <td class="text-center">
                                    <a href="jadwal lab/edit_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="jadwal lab/hapus_jadwal.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-hapus" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data jadwal laboratorium.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>