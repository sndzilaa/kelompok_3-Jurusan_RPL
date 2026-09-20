<?php
include '../koneksi.php';
include '../auth.php';

// Hanya Guru yang berhak mengedit jadwal
require_guru('../');

$error = '';
$pesan_sukses = '';
$id = intval($_GET['id_jadwal'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: jadwal.php");
    exit;
}

// Ambil data jadwal yang akan diedit
$stmt = mysqli_prepare($koneksi, "SELECT * FROM jadwal_lab WHERE id_jadwal = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jadwal = mysqli_fetch_assoc($result);

if (!$jadwal) {
    header("Location: jadwal.php?status=not_found");
    exit;
}

// Ambil data guru
$guru_res = mysqli_query($koneksi, "SELECT * FROM guru ORDER BY nama_guru ASC");
$guru_list = [];
while ($g = mysqli_fetch_assoc($guru_res)) {
    $guru_list[] = $g;
}

// Ambil data kelas
$kelas_res = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
$kelas_list = [];
while ($k = mysqli_fetch_assoc($kelas_res)) {
    $kelas_list[] = $k;
}

// Proses form update jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_guru     = intval($_POST['id_guru'] ?? 0);
    $id_kelas    = intval($_POST['id_kelas'] ?? 0);
    $ruang_lab   = trim($_POST['ruang_lab'] ?? '');
    $hari        = trim($_POST['hari'] ?? '');
    $jam_mulai   = intval($_POST['jam_mulai'] ?? 0);
    $jam_selesai = intval($_POST['jam_selesai'] ?? 0);

    if (empty($id_guru) || empty($id_kelas) || empty($ruang_lab) || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        $error = "Semua kolom wajib diisi!";
    } elseif ($jam_selesai < $jam_mulai) {
        $error = "Jam selesai tidak boleh lebih awal dari jam mulai!";
    } else {
        // Cek bentrok dengan jadwal lain di ruang dan hari yang sama
        $query_cek = "SELECT j.*, k.nama_kelas 
                      FROM jadwal_lab j
                      JOIN kelas k ON j.id_kelas = k.id_kelas
                      WHERE LOWER(TRIM(j.ruang_lab)) = LOWER('$ruang_lab') 
                      AND LOWER(TRIM(j.hari)) = LOWER('$hari')
                      AND ('$jam_mulai' <= j.jam_selesai AND '$jam_selesai' >= j.jam_mulai)
                      AND j.id_jadwal != $id";

        $cek_bentrok = mysqli_query($koneksi, $query_cek);
        if (mysqli_num_rows($cek_bentrok) > 0) {
            $data_bentrok = mysqli_fetch_assoc($cek_bentrok);
            $error = "Jadwal bentrok! Ruang <b>" . htmlspecialchars($ruang_lab) . "</b> pada hari <b>" . htmlspecialchars($hari) . "</b> sudah terisi oleh kelas <b>" . htmlspecialchars($data_bentrok['nama_kelas']) . "</b> (Jam ke-" . $data_bentrok['jam_mulai'] . " s/d " . $data_bentrok['jam_selesai'] . ").";
        } else {
            $jam_pelajaran = "Jam ke-$jam_mulai s/d $jam_selesai";
            $stmt_update = mysqli_prepare($koneksi, "UPDATE jadwal_lab SET id_kelas = ?, id_guru = ?, ruang_lab = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, jam_pelajaran = ? WHERE id_jadwal = ?");
            mysqli_stmt_bind_param($stmt_update, "iississi", $id_kelas, $id_guru, $ruang_lab, $hari, $jam_mulai, $jam_selesai, $jam_pelajaran, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: jadwal.php?status=sukses_edit");
                exit;
            } else {
                $error = "Gagal memperbarui jadwal: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Lab - SIM-LAB</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <a href="jadwal.php" class="active">Jadwal Lab</a>
            <a href="../guru/index_guru.php">Guru</a>
            <a href="../kelas/index_kelas.php">Kelas</a>
        </div>
        <div class="navbar-right">
            <?= render_navbar_user('../'); ?>
        </div>
    </nav>

    <main class="main-container">
        <div class="card-table">
            <div class="header-table">
                <h2><i class="fa-solid fa-pen-to-square"></i> Edit Jadwal Laboratorium</h2>
                <a href="jadwal.php" class="btn btn-edit">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-top: 15px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span><?= $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="edit_jadwal.php?id_jadwal=<?= $id; ?>" method="POST" style="margin-top: 20px;">
                <!-- Guru -->
                <div style="margin-bottom: 15px;">
                    <label>Pilih Guru & Mata Pelajaran:</label>
                    <select name="id_guru" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($guru_list as $g): ?>
                            <option value="<?= $g['id_guru']; ?>" <?= ($jadwal['id_guru'] == $g['id_guru']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($g['nama_guru']); ?> (<?= htmlspecialchars($g['mata_pelajaran']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kelas -->
                <div style="margin-bottom: 15px;">
                    <label>Pilih Kelas:</label>
                    <select name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id_kelas']; ?>" <?= ($jadwal['id_kelas'] == $k['id_kelas']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['tingkatan'] . ' ' . $k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ruang Lab -->
                <div style="margin-bottom: 15px;">
                    <label>Ruang Lab:</label>
                    <select name="ruang_lab" required>
                        <option value="">-- Pilih Ruang Lab --</option>
                        <?php
                        $labs = ["Lab 1", "Lab 2", "Lab 3"];
                        if (!in_array($jadwal['ruang_lab'], $labs) && !empty($jadwal['ruang_lab'])) {
                            $labs[] = $jadwal['ruang_lab'];
                        }
                        foreach ($labs as $lab):
                        ?>
                            <option value="<?= htmlspecialchars($lab); ?>" <?= ($jadwal['ruang_lab'] === $lab) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($lab); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Hari -->
                <div style="margin-bottom: 15px;">
                    <label>Hari:</label>
                    <select name="hari" required>
                        <option value="">-- Pilih Hari --</option>
                        <?php
                        $haris = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                        foreach ($haris as $h):
                        ?>
                            <option value="<?= $h; ?>" <?= ($jadwal['hari'] === $h) ? 'selected' : ''; ?>>
                                <?= $h; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Rentang Jam Ke- -->
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Mulai Jam Ke-:</label>
                        <select name="jam_mulai" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i; ?>" <?= ($jadwal['jam_mulai'] == $i) ? 'selected' : ''; ?>>Jam Ke-<?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div style="flex: 1;">
                        <label>Selesai Jam Ke-:</label>
                        <select name="jam_selesai" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i; ?>" <?= ($jadwal['jam_selesai'] == $i) ? 'selected' : ''; ?>>Jam Ke-<?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" class="btn btn-tambah">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                    <a href="jadwal.php" class="btn btn-hapus">
                        <i class="fa-solid fa-xmark"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
