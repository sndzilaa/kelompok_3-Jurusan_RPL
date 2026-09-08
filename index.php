<?php
include 'koneksi.php';

$query = "SELECT j.id_jadwal, g.nama_guru, g.mata_pelajaran, k.nama_kelas, j.ruang_lab, j.hari, j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), j.jam_pelajaran";

$result = mysqli_query($conn, $query);
?>

<h2>Dashboard Penjadwalan Lab</h2>
<br>
<a href="views/jadwal/tambah.php" class="btn btn-primary">+ Tambah Jadwal Lab</a>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Hari</th>
            <th>Jam Pelajaran</th>
            <th>Ruang Lab</th>
            <th>Mata Pelajaran</th>
            <th>Guru Pengampu</th>
            <th>Kelas</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)):
        ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><b><?= $row['hari']; ?></b></td>
                    <td><?= $row['jam_pelajaran']; ?></td>
                    <td><?= $row['ruang_lab']; ?></td>
                    <td><?= $row['mata_pelajaran']; ?></td>
                    <td><?= $row['nama_guru']; ?></td>
                    <td><?= $row['nama_kelas']; ?></td>
                    <td>
                        <a href="views/jadwal/edit.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-warning">Edit</a>
                        <a href="views/jadwal/hapus.php?id=<?= $row['id_jadwal']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</a>
                    </td>
                </tr>
            <?php
            endwhile;
        else:
            ?>
            <tr>
                <td colspan="8" style="text-align: center;">Belum ada data jadwal laboratorium.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
?>