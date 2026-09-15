<?php
include '../koneksi.php';

if (isset($_POST['submit'])) {
    $nama_kelas      = $_POST['nama_kelas'];
    $tingkatan = $_POST['tingkatan'];
    $query  = "INSERT INTO kelas (nama_kelas, tingkatan) VALUES ('$nama_kelas', '$tingkatan')";
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        header("Location: index_kelas.php");
        exit();
    } else {
        echo "Gagal menyimpan data kelas: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Kelas - SIM-LAB</title>
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
            <a href="../guru/index_guru.php">Guru</a>
            <a href="index_kelas.php" class="active">Kelas</a>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <h2>Tambah Data Kelas</h2>

            <form action="" method="POST" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label>Nama Kelas:</label>
                    <select name="nama_kelas" required>
                        <option value="">Pilih Nama Kelas</option>
                        <option value="RPL 1">RPL 1</option>
                        <option value="RPL 2">RPL 2</option>
                        <option value="RPL 3">RPL 3</option>
                        <option value="RPL 4">RPL 4</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Tingkatan:</label>
                    <select name="tingkatan" required>
                        <option value="">Pilih Tingkatan</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-tambah">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
                <a href="index_kelas.php" class="btn btn-hapus">Batal</a>
            </form>
        </div>
    </main>

</body>

</html>