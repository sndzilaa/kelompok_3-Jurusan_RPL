<?php
include '../koneksi.php';
include '../auth.php';

require_guru('../');

// Pastikan ada parameter id_guru di URL
if (!isset($_GET['id_guru']) || empty($_GET['id_guru'])) {
    header("Location: index_guru.php");
    exit();
}

$id_guru = mysqli_real_escape_string($koneksi, $_GET['id_guru']);
$pesan_error = '';

// Proses update saat form disubmit
if (isset($_POST['submit'])) {
    $nama_guru      = mysqli_real_escape_string($koneksi, trim($_POST['nama_guru'] ?? ''));
    $mata_pelajaran = mysqli_real_escape_string($koneksi, trim($_POST['mata_pelajaran'] ?? ''));

    if (empty($nama_guru) || empty($mata_pelajaran)) {
        $pesan_error = "Semua kolom wajib diisi!";
    } else {
        $query_update = "UPDATE guru SET nama_guru = '$nama_guru', mata_pelajaran = '$mata_pelajaran' WHERE id_guru = '$id_guru'";
        $update = mysqli_query($koneksi, $query_update);

        if ($update) {
            header("Location: index_guru.php?pesan=berhasil_edit");
            exit();
        } else {
            $pesan_error = "Gagal memperbarui data guru: " . mysqli_error($koneksi);
        }
    }
}

// Ambil data guru yang akan diedit
$query_guru = "SELECT * FROM guru WHERE id_guru = '$id_guru'";
$result_guru = mysqli_query($koneksi, $query_guru);
$guru = mysqli_fetch_assoc($result_guru);

// Jika ID guru tidak ditemukan di database
if (!$guru) {
    header("Location: index_guru.php?pesan=tidak_ditemukan");
    exit();
}

// Daftar mata pelajaran RPL untuk pilihan dropdown
$daftar_mapel = [
    "Konsentrasi Keahlian RPL",
    "Pemrograman Dasar",
    "Pemrograman Web dan Perangkat Bergerak",
    "Pemrograman Berorientasi Objek",
    "Basis Data",
    "Dasar-dasar PPLG",
    "Produk Kreatif dan Kewirausahaan"
];

// Jika data mata pelajaran guru saat ini belum ada di daftar, sertakan agar tidak hilang
if (!empty($guru['mata_pelajaran']) && !in_array($guru['mata_pelajaran'], $daftar_mapel)) {
    $daftar_mapel[] = $guru['mata_pelajaran'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Guru - SIM-LAB</title>
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
        <div class="navbar-right">
            <?= render_navbar_user('../'); ?>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <div class="header-table">
                <h2><i class="fa-solid fa-user-pen"></i> Edit Data Guru</h2>
                <a href="index_guru.php" class="btn btn-edit">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_guru" value="<?= htmlspecialchars($guru['id_guru']); ?>">

                <div style="margin-bottom: 18px;">
                    <label for="nama_guru">Nama Guru:</label>
                    <input type="text" id="nama_guru" name="nama_guru" value="<?= htmlspecialchars($guru['nama_guru']); ?>" placeholder="Masukkan nama guru" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="mata_pelajaran">Mata Pelajaran:</label>
                    <select id="mata_pelajaran" name="mata_pelajaran" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($daftar_mapel as $mapel): ?>
                            <option value="<?= htmlspecialchars($mapel); ?>" <?= ($guru['mata_pelajaran'] === $mapel) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($mapel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" name="submit" class="btn btn-tambah">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
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
