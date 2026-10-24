<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

$pesan_error = '';
$nama_guru = '';
$mata_pelajaran = '';

if (isset($_POST['submit'])) {
    $nama_guru = trim($_POST['nama_guru'] ?? '');
    $mata_pelajaran = trim($_POST['mata_pelajaran'] ?? '');

    if ($nama_guru === '' || $mata_pelajaran === '') {
        $pesan_error = "Semua kolom wajib diisi!";
    } elseif (preg_match('/^[\p{L}\p{M} ]+$/u', $nama_guru) !== 1) {
        $pesan_error = "Nama guru hanya boleh berisi huruf dan spasi.";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO guru (nama_guru, mata_pelajaran) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $nama_guru, $mata_pelajaran);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index_guru.php?pesan=berhasil_tambah");
            exit();
        }

        $pesan_error = "Gagal menyimpan data guru: " . mysqli_stmt_error($stmt);
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
    <link rel="stylesheet" href="../background.css">
    <link rel="stylesheet" href="../loading.css">
    <script src="../loading.js" defer></script>
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
            <a href="../jadwal lab/durasi.php">Durasi Lab</a>
            <a href="index_guru.php" class="active">Guru</a>
            <a href="../kelas/index_kelas.php">Kelas</a>
            <a href="../about.php">About</a>
        </div>
        <div class="navbar-right">
            <?= render_navbar_user('../'); ?>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <div class="header-table">
                <h2><i class="fa-solid fa-user-plus"></i> Tambah Data Guru</h2>
                <a href="index_guru.php" class="btn btn-edit">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($pesan_error !== ''): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label for="nama_guru">Nama Guru:</label>
                    <input type="text" id="nama_guru" name="nama_guru" value="<?= htmlspecialchars($nama_guru); ?>" placeholder="Masukkan nama guru" title="Nama hanya boleh berisi huruf dan spasi." oninput="this.value = this.value.replace(/[^\p{L}\p{M} ]/gu, '')" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="mata_pelajaran">Mata Pelajaran:</label>
                    <select id="mata_pelajaran" name="mata_pelajaran" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <option value="Konsentrasi Keahlian RPL">Konsentrasi Keahlian RPL</option>
                        <option value="Pemrograman Dasar">Pemrograman Dasar</option>
                        <option value="Pemrograman Web dan Perangkat Bergerak">Pemrograman Web dan Perangkat Bergerak</option>
                        <option value="Pemrograman Berorientasi Objek">Pemrograman Berorientasi Objek</option>
                        <option value="Basis Data">Basis Data</option>
                        <option value="Dasar-dasar PPLG">Dasar-dasar PPLG</option>
                        <option value="Produk Kreatif dan Kewirausahaan">Produk Kreatif dan Kewirausahaan</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" name="submit" class="btn btn-tambah">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                    <a href="index_guru.php" class="btn btn-hapus">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>

</html>