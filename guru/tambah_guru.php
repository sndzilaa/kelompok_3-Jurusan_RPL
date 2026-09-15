<?php
include '../koneksi.php';

if (isset($_POST['submit'])) {
    $nama_guru      = $_POST['nama_guru'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $query  = "INSERT INTO guru (nama_guru, mata_pelajaran) VALUES ('$nama_guru', '$mata_pelajaran')";
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        header("Location: index_guru.php");
        exit();
    } else {
        echo "Gagal menyimpan data guru: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Guru - SIM-LAB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="index_guru.php" class="active">Guru</a>
            <a href="../kelas/index_kelas.php">Kelas</a>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <h2>Tambah Data Guru</h2>

            <form action="" method="POST" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label>Nama Guru:</label>
                    <input type="text" name="nama_guru" placeholder="Masukkan nama guru" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Mata Pelajaran:</label>
                    <select name="mata_pelajaran" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        <option value="Pemrograman Dasar">Konsentrasi Keahlian RPL</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-tambah">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
                <a href="../index.php" class="btn btn-hapus">Batal</a>
            </form>
        </div>
    </main>

</body>

</html>