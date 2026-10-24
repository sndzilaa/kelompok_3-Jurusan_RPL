<?php
include '../koneksi.php';
include '../auth.php';

require_login('../');

date_default_timezone_set('Asia/Jakarta');
$timezone = new DateTimeZone('Asia/Jakarta');
$now = new DateTimeImmutable('now', $timezone);
$hariIndonesia = [
    1 => 'Senin',
    2 => 'Selasa',
    3 => 'Rabu',
    4 => 'Kamis',
    5 => 'Jumat',
    6 => 'Sabtu',
    7 => 'Minggu',
];
$hariSekarang = $hariIndonesia[(int) $now->format('N')];
$user = get_logged_in_user();
$isGuru = is_guru();
$jadwalPerLab = [];
$namaLab = ['Lab 1', 'Lab 2', 'Lab 3'];
$queryError = '';

$query = "SELECT j.ruang_lab, j.hari, j.jam_mulai, j.jam_selesai, j.waktu_mulai, j.waktu_selesai,
                 g.nama_guru, g.mata_pelajaran, k.nama_kelas, k.tingkatan
          FROM jadwal_lab j
          JOIN guru g ON j.id_guru = g.id_guru
          JOIN kelas k ON j.id_kelas = k.id_kelas
          ORDER BY j.waktu_mulai ASC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    $queryError = 'Gagal memuat jadwal durasi lab: ' . mysqli_error($koneksi);
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        $lab = $row['ruang_lab'];
        if (!in_array($lab, $namaLab, true)) {
            $namaLab[] = $lab;
        }
        if (strcasecmp(trim($row['hari']), $hariSekarang) === 0) {
            $jadwalPerLab[$lab][] = $row;
        }
    }
}

$formatDurasi = static function (int $targetTimestamp, int $nowTimestamp): string {
    $totalMenit = max(0, (int) ceil(($targetTimestamp - $nowTimestamp) / 60));
    $jam = intdiv($totalMenit, 60);
    $menit = $totalMenit % 60;

    if ($jam === 0) {
        return $menit . ' menit';
    }
    if ($menit === 0) {
        return $jam . ' jam';
    }
    return $jam . ' jam ' . $menit . ' menit';
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Durasi Lab - SIM-LAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <a href="jadwal.php">Jadwal Lab</a>
            <a href="durasi.php" class="active">Durasi Lab</a>
            <?php if ($isGuru): ?>
                <a href="../guru/index_guru.php">Guru</a>
                <a href="../kelas/index_kelas.php">Kelas</a>
            <?php endif; ?>
            <a href="../about.php">About</a>
        </div>
        <div class="navbar-right">
            <?= render_navbar_user('../'); ?>
        </div>
    </nav>

    <main class="main-container">
        <h1>Durasi Penggunaan Lab</h1>
        <p>
            Pantau waktu mulai dan sisa waktu penggunaan setiap laboratorium untuk hari <?= htmlspecialchars($hariSekarang); ?>,
            <?= htmlspecialchars($now->format('d-m-Y')); ?>. Hitung mundur diperbarui otomatis.
        </p>

        <?php if ($queryError !== ''): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= htmlspecialchars($queryError); ?></span>
            </div>
        <?php else: ?>
            <div class="duration-grid">
                <?php foreach ($namaLab as $lab): ?>
                    <?php
                    $jadwalHariIni = $jadwalPerLab[$lab] ?? [];
                    $jadwalAktif = null;
                    $jadwalBerikutnya = null;
                    $jadwalTanpaWaktu = [];

                    foreach ($jadwalHariIni as $jadwal) {
                        if (empty($jadwal['waktu_mulai']) || empty($jadwal['waktu_selesai'])) {
                            $jadwalTanpaWaktu[] = $jadwal;
                            continue;
                        }

                        $mulai = new DateTimeImmutable($now->format('Y-m-d') . ' ' . $jadwal['waktu_mulai'], $timezone);
                        $selesai = new DateTimeImmutable($now->format('Y-m-d') . ' ' . $jadwal['waktu_selesai'], $timezone);
                        $jadwal['mulai_timestamp'] = $mulai->getTimestamp();
                        $jadwal['selesai_timestamp'] = $selesai->getTimestamp();

                        if ($now >= $mulai && $now < $selesai) {
                            $jadwalAktif = $jadwal;
                            break;
                        }
                        if ($mulai > $now && ($jadwalBerikutnya === null || $mulai->getTimestamp() < $jadwalBerikutnya['mulai_timestamp'])) {
                            $jadwalBerikutnya = $jadwal;
                        }
                    }

                    $jadwalPilihan = $jadwalAktif ?? $jadwalBerikutnya;
                    ?>
                    <section class="duration-card">
                        <div class="duration-card-header">
                            <h2><i class="fa-solid fa-computer"></i> <?= htmlspecialchars($lab); ?></h2>
                            <?php if ($jadwalAktif !== null): ?>
                                <span class="duration-badge badge-ongoing">Sedang digunakan</span>
                            <?php elseif ($jadwalBerikutnya !== null): ?>
                                <span class="duration-badge badge-upcoming">Akan digunakan</span>
                            <?php elseif ($jadwalTanpaWaktu !== []): ?>
                                <span class="duration-badge badge-unknown">Waktu belum diatur</span>
                            <?php elseif ($jadwalHariIni !== []): ?>
                                <span class="duration-badge badge-finished">Selesai hari ini</span>
                            <?php else: ?>
                                <span class="duration-badge badge-empty">Tidak ada jadwal</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($jadwalPilihan !== null): ?>
                            <?php
                            $jadwalMulai = new DateTimeImmutable($now->format('Y-m-d') . ' ' . $jadwalPilihan['waktu_mulai'], $timezone);
                            $jadwalSelesai = new DateTimeImmutable($now->format('Y-m-d') . ' ' . $jadwalPilihan['waktu_selesai'], $timezone);
                            $targetTimestamp = $jadwalAktif !== null ? $jadwalPilihan['selesai_timestamp'] : $jadwalPilihan['mulai_timestamp'];
                            ?>
                            <div class="duration-timer"
                                 data-start="<?= $jadwalPilihan['mulai_timestamp']; ?>"
                                 data-end="<?= $jadwalPilihan['selesai_timestamp']; ?>">
                                <span class="duration-label"><?= $jadwalAktif !== null ? 'Selesai dalam' : 'Mulai dalam'; ?></span>
                                <strong class="duration-countdown"><?= htmlspecialchars($formatDurasi($targetTimestamp, $now->getTimestamp())); ?></strong>
                            </div>
                            <div class="duration-details">
                                <p><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($jadwalMulai->format('H:i')); ?> - <?= htmlspecialchars($jadwalSelesai->format('H:i')); ?></p>
                                <p><i class="fa-solid fa-users"></i> <?= htmlspecialchars($jadwalPilihan['tingkatan'] . ' ' . $jadwalPilihan['nama_kelas']); ?></p>
                                <p><i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($jadwalPilihan['nama_guru']); ?> &middot; <?= htmlspecialchars($jadwalPilihan['mata_pelajaran']); ?></p>
                            </div>
                        <?php elseif ($jadwalTanpaWaktu !== []): ?>
                            <p class="duration-message">Atur waktu mulai dan selesai jadwal hari ini agar hitung mundur dapat ditampilkan.</p>
                            <?php if ($isGuru): ?>
                                <a href="jadwal.php" class="duration-edit-link"><i class="fa-solid fa-pen-to-square"></i> Edit jadwal</a>
                            <?php endif; ?>
                        <?php elseif ($jadwalHariIni !== []): ?>
                            <p class="duration-message">Semua jadwal lab hari ini sudah selesai.</p>
                        <?php else: ?>
                            <p class="duration-message">Belum ada jadwal penggunaan lab hari ini.</p>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.querySelectorAll('.duration-timer').forEach(function (timer) {
            const mulai = Number(timer.dataset.start);
            const selesai = Number(timer.dataset.end);
            const label = timer.querySelector('.duration-label');
            const countdown = timer.querySelector('.duration-countdown');
            let reloadScheduled = false;

            function formatDurasi(totalDetik) {
                const totalMenit = Math.max(0, Math.ceil(totalDetik / 60));
                const jam = Math.floor(totalMenit / 60);
                const menit = totalMenit % 60;
                if (jam === 0) return menit + ' menit';
                if (menit === 0) return jam + ' jam';
                return jam + ' jam ' + menit + ' menit';
            }

            function perbaruiHitungMundur() {
                const sekarang = Math.floor(Date.now() / 1000);
                if (sekarang < mulai) {
                    label.textContent = 'Mulai dalam';
                    countdown.textContent = formatDurasi(mulai - sekarang);
                } else if (sekarang < selesai) {
                    label.textContent = 'Selesai dalam';
                    countdown.textContent = formatDurasi(selesai - sekarang);
                } else {
                    label.textContent = 'Sesi selesai';
                    countdown.textContent = 'Selesai';
                    if (!reloadScheduled) {
                        reloadScheduled = true;
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    }
                }
            }

            perbaruiHitungMundur();
            window.setInterval(perbaruiHitungMundur, 1000);
        });
    </script>
</body>

</html>
