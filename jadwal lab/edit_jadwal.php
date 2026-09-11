<?php
include 'koneksi.php';

$error = '';
$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data jadwal yang akan diedit
$stmt = mysqli_prepare($koneksi, "SELECT * FROM jadwal_lab WHERE id_jadwal = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jadwal = mysqli_fetch_assoc($result);

if (!$jadwal) {
    header("Location: index.php?status=not_found");
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
    $id_guru = $_POST['id_guru'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $ruang_lab = trim($_POST['ruang_lab'] ?? '');
    $hari = trim($_POST['hari'] ?? '');
    $jam_pelajaran = trim($_POST['jam_pelajaran'] ?? '');

    // Jika user menambahkan guru baru saat edit
    if ($id_guru === 'new') {
        $nama_guru_baru = trim($_POST['nama_guru_baru'] ?? '');
        $mapel_baru = trim($_POST['mata_pelajaran_baru'] ?? '');

        if (!empty($nama_guru_baru) && !empty($mapel_baru)) {
            $stmt_guru = mysqli_prepare($koneksi, "INSERT INTO guru (nama_guru, mata_pelajaran) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_guru, "ss", $nama_guru_baru, $mapel_baru);
            if (mysqli_stmt_execute($stmt_guru)) {
                $id_guru = mysqli_insert_id($koneksi);
            } else {
                $error = "Gagal menambahkan guru baru: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Nama guru dan mata pelajaran baru tidak boleh kosong.";
        }
    }

    // Jika user menambahkan kelas baru saat edit
    if (empty($error) && $id_kelas === 'new') {
        $nama_kelas_baru = trim($_POST['nama_kelas_baru'] ?? '');
        $tingkatan_baru = trim($_POST['tingkatan_baru'] ?? '');

        if (!empty($nama_kelas_baru)) {
            $stmt_kelas = mysqli_prepare($koneksi, "INSERT INTO kelas (nama_kelas, tingkatan) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_kelas, "ss", $nama_kelas_baru, $tingkatan_baru);
            if (mysqli_stmt_execute($stmt_kelas)) {
                $id_kelas = mysqli_insert_id($koneksi);
            } else {
                $error = "Gagal menambahkan kelas baru: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Nama kelas baru tidak boleh kosong.";
        }
    }

    // Validasi & Eksekusi Update
    if (empty($error)) {
        if (empty($id_guru) || empty($id_kelas) || empty($ruang_lab) || empty($hari) || empty($jam_pelajaran)) {
            $error = "Semua kolom wajib diisi!";
        } else {
            $id_guru = intval($id_guru);
            $id_kelas = intval($id_kelas);

            $stmt_update = mysqli_prepare($koneksi, "UPDATE jadwal_lab SET id_kelas = ?, id_guru = ?, ruang_lab = ?, hari = ?, jam_pelajaran = ? WHERE id_jadwal = ?");
            mysqli_stmt_bind_param($stmt_update, "iisssi", $id_kelas, $id_guru, $ruang_lab, $hari, $jam_pelajaran, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = "Gagal memperbarui data jadwal: " . mysqli_error($koneksi);
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
    <title>Edit Jadwal Praktikum Lab - SIM-LAB</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Link CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="style.css">

    <style>
        .form-container {
            max-width: 680px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .form-header p {
            font-size: 0.85rem;
            opacity: 0.95;
        }

        .form-body {
            padding: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .form-label span.req {
            color: #ef4444;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 11px 14px;
            font-size: 0.9rem;
            font-family: inherit;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #f59e0b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.18);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .extra-box {
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 14px;
            margin-top: 10px;
            display: none;
        }

        .alert-box {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-update {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        }

        .btn-update:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <!-- Header Form -->
            <div class="form-header">
                <div>
                    <h2>✏️ Edit Jadwal Praktikum Lab</h2>
                    <p>Ubah detail jadwal penggunaan laboratorium #<?= $id; ?></p>
                </div>
                <a href="index.php" class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.2); color: #fff; border: none;">← Kembali</a>
            </div>

            <!-- Body Form -->
            <div class="form-body">
                <?php if (!empty($error)): ?>
                    <div class="alert-box alert-error">
                        <span>⚠️ <?= htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form action="edit_jadwal.php?id=<?= $id; ?>" method="POST">
                    <!-- Guru Pengampu -->
                    <div class="form-group">
                        <label class="form-label" for="id_guru">Guru & Mata Pelajaran <span class="req">*</span></label>
                        <select name="id_guru" id="id_guru" class="form-select" required onchange="toggleGuruBaru(this.value)">
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($guru_list as $g): ?>
                                <option value="<?= $g['id_guru']; ?>" <?= ($jadwal['id_guru'] == $g['id_guru']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($g['nama_guru']); ?> (<?= htmlspecialchars($g['mata_pelajaran']); ?>)
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ Tambah Guru Baru...</option>
                        </select>

                        <div id="boxGuruBaru" class="extra-box">
                            <div class="form-grid">
                                <div>
                                    <label class="form-label">Nama Guru Baru</label>
                                    <input type="text" name="nama_guru_baru" class="form-control" placeholder="Contoh: Budi Santoso, S.Kom">
                                </div>
                                <div>
                                    <label class="form-label">Mata Pelajaran</label>
                                    <input type="text" name="mata_pelajaran_baru" class="form-control" placeholder="Contoh: Pemrograman Web">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kelas -->
                    <div class="form-group">
                        <label class="form-label" for="id_kelas">Kelas <span class="req">*</span></label>
                        <select name="id_kelas" id="id_kelas" class="form-select" required onchange="toggleKelasBaru(this.value)">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                                <option value="<?= $k['id_kelas']; ?>" <?= ($jadwal['id_kelas'] == $k['id_kelas']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($k['nama_kelas']); ?> <?= !empty($k['tingkatan']) ? '('.htmlspecialchars($k['tingkatan']).')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ Tambah Kelas Baru...</option>
                        </select>

                        <div id="boxKelasBaru" class="extra-box">
                            <div class="form-grid">
                                <div>
                                    <label class="form-label">Nama Kelas Baru</label>
                                    <input type="text" name="nama_kelas_baru" class="form-control" placeholder="Contoh: XI RPL 1">
                                </div>
                                <div>
                                    <label class="form-label">Tingkatan</label>
                                    <input type="text" name="tingkatan_baru" class="form-control" placeholder="Contoh: XI">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ruang Lab & Hari -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="ruang_lab">Ruang Laboratorium <span class="req">*</span></label>
                            <select name="ruang_lab" id="ruang_lab" class="form-select" required>
                                <option value="">-- Pilih Ruang Lab --</option>
                                <?php
                                $labs = ["Lab RPL 1", "Lab RPL 2", "Lab Multimedia", "Lab Komputer 1", "Lab Komputer 2", "Lab Jaringan (TKJ)"];
                                if (!in_array($jadwal['ruang_lab'], $labs) && !empty($jadwal['ruang_lab'])) {
                                    $labs[] = $jadwal['ruang_lab'];
                                }
                                foreach ($labs as $lab) {
                                    $sel = ($jadwal['ruang_lab'] === $lab) ? 'selected' : '';
                                    echo "<option value=\"".htmlspecialchars($lab)."\" $sel>".htmlspecialchars($lab)."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="hari">Hari <span class="req">*</span></label>
                            <select name="hari" id="hari" class="form-select" required>
                                <option value="">-- Pilih Hari --</option>
                                <?php
                                $haris = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                                foreach ($haris as $h) {
                                    $sel = ($jadwal['hari'] === $h) ? 'selected' : '';
                                    echo "<option value=\"$h\" $sel>$h</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Jam Pelajaran -->
                    <div class="form-group">
                        <label class="form-label" for="jam_pelajaran">Jam Pelajaran <span class="req">*</span></label>
                        <select name="jam_pelajaran" id="jam_pelajaran" class="form-select" required>
                            <option value="">-- Pilih Jam Pelajaran --</option>
                            <?php
                            $jams = [
                                "Jam 1-2 (07.00 - 08.30)",
                                "Jam 3-4 (08.30 - 10.00)",
                                "Jam 5-6 (10.15 - 11.45)",
                                "Jam 7-8 (12.30 - 14.00)",
                                "Jam 9-10 (14.00 - 15.30)"
                            ];
                            if (!in_array($jadwal['jam_pelajaran'], $jams) && !empty($jadwal['jam_pelajaran'])) {
                                $jams[] = $jadwal['jam_pelajaran'];
                            }
                            foreach ($jams as $j) {
                                $sel = ($jadwal['jam_pelajaran'] === $j) ? 'selected' : '';
                                echo "<option value=\"".htmlspecialchars($j)."\" $sel>".htmlspecialchars($j)."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="form-footer">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-update">💾 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleGuruBaru(val) {
            const box = document.getElementById('boxGuruBaru');
            if (val === 'new') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }

        function toggleKelasBaru(val) {
            const box = document.getElementById('boxKelasBaru');
            if (val === 'new') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }
    </script>
</body>
</html>
