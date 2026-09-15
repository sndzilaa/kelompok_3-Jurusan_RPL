<?php
include '../koneksi.php';

// Ambil data guru dan kelas untuk opsi dropdown
$query_guru  = mysqli_query($koneksi, "SELECT * FROM guru ORDER BY nama_guru ASC");
$query_kelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY nama_kelas ASC");

$pesan_error = "";

if (isset($_POST['submit'])) {
    $id_guru     = $_POST['id_guru'];
    $id_kelas    = $_POST['id_kelas'];
    $ruang_lab   = trim($_POST['ruang_lab']);
    $hari        = trim($_POST['hari']);
    $jam_mulai   = (int)$_POST['jam_mulai'];
    $jam_selesai = (int)$_POST['jam_selesai'];

    // 1. Validasi: Jam selesai tidak boleh lebih kecil dari jam mulai
    if ($jam_selesai < $jam_mulai) {
        $pesan_error = "Jam selesai tidak boleh lebih kecil dari jam mulai!";
    } else {
        // 2. Query Cek Bentrok di Database (Format LOWER & TRIM agar tidak terkecoh huruf besar/kecil)
        $query_cek = "SELECT j.*, k.nama_kelas 
                      FROM jadwal_lab j
                      JOIN kelas k ON j.id_kelas = k.id_kelas
                      WHERE LOWER(TRIM(j.ruang_lab)) = LOWER('$ruang_lab') 
                      AND LOWER(TRIM(j.hari)) = LOWER('$hari') 
                      AND ('$jam_mulai' <= j.jam_selesai AND '$jam_selesai' >= j.jam_mulai)";

        $cek_bentrok = mysqli_query($koneksi, $query_cek);

        // 3. Jika ditemukan data bentrok
        if (mysqli_num_rows($cek_bentrok) > 0) {
            $data_bentrok = mysqli_fetch_assoc($cek_bentrok);
            $pesan_error = "Jadwal bentrok! Ruang <b>" . htmlspecialchars($ruang_lab) . "</b> pada hari <b>" . htmlspecialchars($hari) . "</b> sudah terisi oleh kelas <b>" . htmlspecialchars($data_bentrok['nama_kelas']) . "</b> (Jam ke-" . $data_bentrok['jam_mulai'] . " s/d " . $data_bentrok['jam_selesai'] . ").";
        } else {
            // 4. Jika aman / tidak bentrok, langsung simpan
            $query_simpan = "INSERT INTO jadwal_lab (id_guru, id_kelas, ruang_lab, hari, jam_mulai, jam_selesai) 
                            VALUES ('$id_guru', '$id_kelas', '$ruang_lab', '$hari', '$jam_mulai', '$jam_selesai')";
            $simpan = mysqli_query($koneksi, $query_simpan);

            if ($simpan) {
                header("Location: ../index.php");
                exit();
            } else {
                $pesan_error = "Gagal menyimpan jadwal: " . mysqli_error($koneksi);
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
    <title>Tambah Jadwal Lab - SIM-LAB</title>
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
    </nav>

    <main class="main-container">
        <div class="card-table">
            <h2>Tambah Jadwal Laboratorium</h2>

            <!-- Pesan Error Muncul Otomatis Jika Bentrok -->
            <?php if (!empty($pesan_error)): ?>
                <div style="background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px; border-radius: 6px; margin-top: 15px; font-size: 14px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $pesan_error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" style="margin-top: 20px;">
                <div style="margin-bottom: 15px;">
                    <label>Pilih Guru & Mapel:</label>
                    <select name="id_guru" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php while ($g = mysqli_fetch_assoc($query_guru)): ?>
                            <option value="<?= $g['id_guru']; ?>">
                                <?= htmlspecialchars($g['nama_guru']); ?> (<?= htmlspecialchars($g['mata_pelajaran']); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Pilih Kelas:</label>
                    <select name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= $k['id_kelas']; ?>">
                                <?= htmlspecialchars($k['tingkatan'] . ' ' . $k['nama_kelas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Menggunakan Dropdown Opsi Lab Agar Penulisan Seragam -->
                <div style="margin-bottom: 15px;">
                    <label>Ruang Lab:</label>
                    <select name="ruang_lab" required>
                        <option value="">-- Pilih Ruang Lab --</option>
                        <option value="Lab 1">Lab 1</option>
                        <option value="Lab 2">Lab 2</option>
                        <option value="Lab 3">Lab 3</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Hari:</label>
                    <select name="hari" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Mulai Jam Ke-:</label>
                        <select name="jam_mulai" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i; ?>">Jam Ke-<?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div style="flex: 1;">
                        <label>Selesai Jam Ke-:</label>
                        <select name="jam_selesai" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i; ?>">Jam Ke-<?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-tambah">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal
                </button>
                <a href="../index.php" class="btn btn-hapus">Batal</a>
            </form>
        </div>
    </main>

</body>

</html>