<?php
include 'auth.php';

$anggotaTim = [
    [
        'nama' => 'MUHAMAD ABDUL FATHIR AL GHIFARI',
        'instagram' => '',
        'ketua' => true
    ],
    [
        'nama' => 'NADZILA SAKINA',
        'instagram' => '',
        'ketua' => false
    ],
    [
        'nama' => 'ADELIA OCTAVIANI',
        'instagram' => '',
        'ketua' => false
    ],
    [
        'nama' => 'SILVA SRI AYU DEWI',
        'instagram' => '',
        'ketua' => false
    ]
];

$linkDonasiBisnisKetua = '';
$isGuru = is_guru();
$isLogin = is_logged_in();
$linkDonasiValid = filter_var($linkDonasiBisnisKetua, FILTER_VALIDATE_URL)
    && in_array(strtolower((string) parse_url($linkDonasiBisnisKetua, PHP_URL_SCHEME)), ['http', 'https'], true);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - SIM-LAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="background.css">
    <link rel="stylesheet" href="loading.css">
    <link rel="stylesheet" href="about.css">
    <script src="loading.js" defer></script>
</head>

<body>
    <nav class="navbar">
        <div class="logo-container">
            <div class="logo-icon"><i class="fa-solid fa-computer"></i></div>
            <span class="logo-text">SIM-LAB</span>
        </div>
        <div class="navbar-links">
            <?php if ($isLogin): ?>
                <a href="index.php">Home</a>
                <a href="jadwal lab/jadwal.php">Jadwal Lab</a>
                <a href="jadwal lab/durasi.php">Durasi Lab</a>
                <?php if ($isGuru): ?>
                    <a href="guru/index_guru.php">Guru</a>
                    <a href="kelas/index_kelas.php">Kelas</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php">Masuk</a>
            <?php endif; ?>
            <a href="about.php" class="active">About</a>
        </div>
        <?php if ($isLogin): ?>
            <div class="navbar-right">
                <?= render_navbar_user(''); ?>
            </div>
        <?php endif; ?>
    </nav>

    <main class="main-container about-container">
        <section class="about-hero">
            <span class="about-eyebrow"><i class="fa-solid fa-people-group"></i> Tentang Tim Kami</span>
            <h1>Mengenal pembuat SIM-LAB</h1>
            <p>SIM-LAB dibuat oleh tim yang terdiri dari tiga anggota untuk membantu pengelolaan informasi dan jadwal laboratorium dengan lebih mudah.</p>
        </section>

        <section class="team-section" aria-labelledby="team-heading">
            <div class="team-section-heading">
                <div>
                    <span class="about-eyebrow">Di balik SIM-LAB</span>
                    <h2 id="team-heading">Anggota Tim</h2>
                </div>
                <span class="team-count"><i class="fa-solid fa-user-group"></i> 4 Anggota</span>
            </div>

            <div class="team-grid">
                <?php foreach ($anggotaTim as $anggota): ?>
                    <?php
                    $usernameInstagram = trim($anggota['instagram'], '@');
                    $instagramValid = preg_match('/^[A-Za-z0-9._]{1,30}$/', $usernameInstagram) === 1;
                    ?>
                    <article class="team-card">
                        <div class="member-avatar" aria-hidden="true">
                            <i class="fa-solid <?= $anggota['ketua'] ? 'fa-crown' : 'fa-user'; ?>"></i>
                        </div>
                        <span class="member-role"><?= $anggota['ketua'] ? 'Ketua Tim' : 'Anggota Tim'; ?></span>
                        <h3><?= htmlspecialchars($anggota['nama'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="member-social">
                            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                            <?php if ($instagramValid): ?>
                                <a href="https://www.instagram.com/<?= rawurlencode($usernameInstagram); ?>/" target="_blank" rel="noopener noreferrer">
                                    @<?= htmlspecialchars($usernameInstagram, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php else: ?>
                                <span>Username Instagram belum diisi</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($anggota['ketua']): ?>
                            <div class="leader-link">
                                <?php if ($linkDonasiValid): ?>
                                    <a class="btn btn-leader-link" href="<?= htmlspecialchars($linkDonasiBisnisKetua, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-solid fa-hand-holding-heart"></i> Donasi / Bisnis
                                    </a>
                                <?php else: ?>
                                    <span class="leader-link-pending"><i class="fa-solid fa-link-slash"></i> Link donasi/bisnis belum diisi</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="about-project">
            <div class="project-icon"><i class="fa-solid fa-computer"></i></div>
            <div>
                <h2>Tentang SIM-LAB</h2>
                <p>SIM-LAB adalah Sistem Informasi Manajemen Laboratorium yang membantu pengguna melihat jadwal laboratorium serta mendukung pengelolaan data guru, kelas, dan jadwal.
                    yang dibuat oleh siswa SMK NEGERI 1 MAJA UNTUK memenuhi tugas akhir mata pelajaran Pemrograman Web Lanjut.</p>
            </div>
        </section>
    </main>
</body>

</html>
