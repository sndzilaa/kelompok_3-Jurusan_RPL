<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

if (!isset($_GET['id_kelas']) || empty($_GET['id_kelas'])) {
    header("Location: index_kelas.php");
    exit();
}

$id_kelas = intval($_GET['id_kelas']);
$error = '';

if (isset($_POST['submit'])) {
    $nama_kelas = mysqli_real_escape_string($koneksi, trim($_POST['nama_kelas'] ?? ''));
    $tingkatan  = mysqli_real_escape_string($koneksi, trim($_POST['tingkatan'] ?? ''));

    if (!empty($nama_kelas) && !empty($tingkatan)) {
        $query_update = "UPDATE kelas SET nama_kelas = '$nama_kelas', tingkatan = '$tingkatan' WHERE id_kelas = $id_kelas";
        $update = mysqli_query($koneksi, $query_update);

        if ($update) {
            header("Location: index_kelas.php?pesan=berhasil_edit");
            exit();
        } else {
            $error = "Gagal memperbarui data kelas: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}

$query_kelas = mysqli_query($koneksi, "SELECT * FROM kelas WHERE id_kelas = $id_kelas");
$kelas = mysqli_fetch_assoc($query_kelas);

if (!$kelas) {
    header("Location: index_kelas.php?pesan=tidak_ditemukan");
    exit();
}

$daftar_kelas = ["RPL 1", "RPL 2", "RPL 3", "RPL 4"];
if (!in_array($kelas['nama_kelas'], $daftar_kelas) && !empty($kelas['nama_kelas'])) {
    $daftar_kelas[] = $kelas['nama_kelas'];
}

$daftar_tingkatan = ["X", "XI", "XII"];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kelas - SIM-LAB</title>
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
                <h2><i class="fa-solid fa-pen-to-square"></i> Edit Data Kelas</h2>
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
                        <?php foreach ($daftar_kelas as $k): ?>
                            <option value="<?= htmlspecialchars($k); ?>" <?= ($kelas['nama_kelas'] === $k) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label>Tingkatan:</label>
                    <select name="tingkatan" required>
                        <option value="">-- Pilih Tingkatan --</option>
                        <?php foreach ($daftar_tingkatan as $t): ?>
                            <option value="<?= htmlspecialchars($t); ?>" <?= ($kelas['tingkatan'] === $t) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" name="submit" class="btn btn-tambah">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
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
