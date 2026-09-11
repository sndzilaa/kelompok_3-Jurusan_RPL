<?php
include 'koneksi.php';

$query = "SELECT * FROM guru ORDER BY nama_guru ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru - SIM-LAB</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-computer"></i></div>
            <span class="logo-text">SIM-LAB</span>
        </div>
        <div class="navbar-links">
            <a href="index.php">Home</a>
            <a href="keseluruhan/jadwal lab/jadwal.php">Jadwal Lab</a>
            <a href="index_guru.php" class="active">Guru</a>
            <a href="keseluruhan/kelas/index_kelas.php">Kelas</a>
        </div>
    </nav>
    <main class="main-container">
        <h1>Data Guru</h1>
        <div class="card-table">
            <div class="header-table">
                <h2>Daftar Guru</h2>
                <a href="tambah_guru.php" class="btn btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Data</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Aksi</th>
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
                                <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                                <td><?= htmlspecialchars($row['mata_pelajaran']) ?></td>
                                <td class="text-center">
                                    <a href="edit_guru.php?id_guru=<?= $row['id_guru']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="hapus_guru.php?id_guru=<?= $row['id_guru']; ?>" class="btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data guru ini?');"><i class="fa-solid fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data guru yang terdata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
</body>