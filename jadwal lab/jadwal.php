<?php
include 'koneksi.php';

$query = "SELECT j.id_jadwal, g.nama_guru, g.mata_pelajaran, k.nama_kelas, k.tingkatan, j.ruang_lab, j.hari, j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam_pelajaran";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lab - SIM-LAB</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-computer"></i></div>
            <span class="logo-text">SIM-LAB</span>
        </div>

    </nav>
    <main class="main-container">
        <h1>Jadwal Laboratorium</h1>
        <div class="card-table">
            <div class="header-table">
                <h2>Daftar Jadwal Lab</h2>
                <a href="tambah_jadwal.php" class="btn btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Data</a>
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0):
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
                                <td><?= htmlspecialchars($row['jam_pelajaran']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data jadwal lab yang terdata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>