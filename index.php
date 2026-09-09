<?php
include 'koneksi.php';

$daftar_hari = [
    "Sunday" => "Minggu",
    "Monday" => "Senin",
    "Tuesday" => "Selasa",
    "Wednesday" => "Rabu",
    "Thursday" => "Kamis",
    "Friday" => "Jumat",
    "Saturday" => "Sabtu"
];
$hari_ini = $daftar_hari[date("l")];

$query = "SELECT 
            j.id_jadwal, 
            g.nama_guru, 
            g.mata_pelajaran, 
            k.nama_kelas, 
            j.ruang_lab, 
            j.hari,    
            j.jam_pelajaran 
          FROM jadwal_lab j
          JOIN guru g ON g.id_guru = j.id_guru
          JOIN kelas k ON k.id_kelas = j.id_kelas
          ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), j.jam_pelajaran";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
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
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                    <td><?= htmlspecialchars($row['mata_pelajaran']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                    <td><?= htmlspecialchars($row['ruang_lab']); ?></td>
                    <td><?= htmlspecialchars($row['hari']); ?></td>
                    <td><?= htmlspecialchars($row['jam_pelajaran']); ?></td>
                    <td>
                        <a href='edit_jadwal.php?id=<?= $row['id_jadwal']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='hapus_jadwal.php?id=<?= $row['id_jadwal']; ?>' class='btn btn-danger btn-sm' onclick='return confirm("Apakah Anda yakin ingin menghapus jadwal ini?")'>Hapus</a>
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