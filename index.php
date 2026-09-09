<?php
include 'koneksi.php';

$query = "SELECT j.id_jadwal, g.nama_guru, g.mata_pelajaran, k.nama_kelas, j.ruang_lab, j.hari, j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON id_guru = id_guru
          JOIN kelas k ON jid_kelas = id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), j.jam_pelajaran";
$conn = mysqli_connect($hostname, $username, $password, $database);
$result = mysqli_query($conn, $query);
?>

<h2>SIM-LAB</h2>
<h4>Sistem Informasi Manajemen Laboratorium</h4>
<a href="tambah_jadwal.php" class="btn btn-primary">Tambah Jadwal</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Guru</th>
            <th>Mata Pelajaran</th>
            <th>Nama Kelas</th>
            <th>Ruang Lab</th>
            <th>Hari</th>
            <th>Jam Pelajaran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nama_guru']; ?></td>
                    <td><?php echo $row['mata_pelajaran']; ?></td>
                    <td><?php echo $row['nama_kelas']; ?></td>
                    <td><?php echo $row['ruang_lab']; ?></td>
                    <td><?php echo $row['hari']; ?></td>
                    <td><?php echo $row['jam_pelajaran']; ?></td>
                    <td>
                        <a href='edit_jadwal.php?id=<?php echo $row['id_jadwal']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='hapus_jadwal.php?id=<?php echo $row['id_jadwal']; ?>' class='btn btn-danger btn-sm' onclick='return confirm("Apakah Anda yakin ingin menghapus jadwal ini?")'>Hapus</a>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='8' class='text-center'>Tidak ada jadwal laboratorium yang ditemukan.</td></tr>";
        }
        ?>
    </tbody>
</table>