<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

if (isset($_POST['submit'])) {
    $nama_kelas = mysqli_real_escape_string($koneksi, trim($_POST['nama_kelas'] ?? ''));
    $tingkatan  = mysqli_real_escape_string($koneksi, trim($_POST['tingkatan'] ?? ''));

    if (!empty($nama_kelas) && !empty($tingkatan)) {
        $query  = "INSERT INTO kelas (nama_kelas, tingkatan) VALUES ('$nama_kelas', '$tingkatan')";
        $simpan = mysqli_query($koneksi, $query);

        if ($simpan) {
            header("Location: index_kelas.php?pesan=berhasil_tambah");
            exit();
        } else {
            $error = "Gagal menyimpan data kelas: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Semua kolom wajib diisi!";
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
        <div class="navbar-right">
            <?= render_navbar_user('../'); ?>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <div class="header-table">
                <h2><i class="fa-solid fa-plus"></i> Tambah Data Kelas</h2>
                <a href="index_kelas.php" class="btn btn-edit">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-top: 15px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label>Nama Kelas:</label>
                    <select name="nama_kelas" required>
                        <option value="">-- Pilih Nama Kelas --</option>
                        <option value="RPL 1">RPL 1</option>
                        <option value="RPL 2">RPL 2</option>
                        <option value="RPL 3">RPL 3</option>
                        <option value="RPL 4">RPL 4</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Tingkatan:</label>
                    <select name="tingkatan" required>
                        <option value="">-- Pilih Tingkatan --</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" name="submit" class="btn btn-tambah">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                    <a href="index_kelas.php" class="btn btn-hapus">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>

</html>