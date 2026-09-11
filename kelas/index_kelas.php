<?php
include '../koneksi.php';

$query = "SELECT * FROM kelas ORDER BY nama_kelas ASC";
$result = mysqli_query($koneksi, $query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - SIM-LAB</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-computer"></i></div>
            <span class="logo-text">SIM-LAB</span>
        </div>
        <div class="navbar-links">
            <a href="../index.php">Home</a>
            <a href="../jadwal lab/jadwal.php">Jadwal Lab</a>
            <a href="../guru/index_guru.php">Guru</a>
            <a href="index_kelas.php" class="active">Kelas</a>
        </div>
    </nav>
    <main class="main-container">
        <h1>Data Kelas</h1>
        <div class="card-table">
            <div class="header-table">
                <h2>Daftar Kelas</h2>
                <a href="tambah_kelas.php" class="btn btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Data</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkatan</th>
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
                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td><?= htmlspecialchars($row['tingkatan']) ?></td>
                                <td class="text-center">
                                    <a href="edit_kelas.php?id_kelas=<?= $row['id_kelas'] ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="hapus_kelas.php?id_kelas=<?= $row['id_kelas'] ?>" class="btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data kelas ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data kelas yang terdata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
</body>