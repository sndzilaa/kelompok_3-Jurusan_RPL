<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Mengecek apakah user sudah login
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id_user']);
}

/**
 * Mengambil data user yang sedang login
 * @return array|null
 */
function get_logged_in_user() {
    return is_logged_in() ? $_SESSION['user'] : null;
}

/**
 * Mengecek apakah user adalah Guru
 * @return bool
 */
function is_guru() {
    return is_logged_in() && ($_SESSION['user']['role'] === 'guru');
}

/**
 * Mengecek apakah user adalah Siswa
 * @return bool
 */
function is_siswa() {
    return is_logged_in() && ($_SESSION['user']['role'] === 'siswa');
}

/**
 * Memastikan user sudah login. Jika belum, redirect ke login.php
 * @param string $root_prefix Prefix path ke root ('', '../', '../../')
 */
function require_login($root_prefix = '') {
    if (!is_logged_in()) {
        header("Location: " . $root_prefix . "login.php");
        exit();
    }
}

/**
 * Memastikan user adalah Guru. Jika siswa/bukan guru, redirect ke index dengan pesan error.
 * @param string $root_prefix Prefix path ke root ('', '../', '../../')
 */
function require_guru($root_prefix = '') {
    require_login($root_prefix);
    if (!is_guru()) {
        header("Location: " . $root_prefix . "index.php?pesan=akses_ditolak");
        exit();
    }
}

/**
 * Render elemen informasi user & tombol logout untuk navbar
 * @param string $root_prefix Prefix path ke root ('', '../')
 */
function render_navbar_user($root_prefix = '') {
    $user = get_logged_in_user();
    if (!$user) return '';

    $isGuru = ($user['role'] === 'guru');
    $roleLabel = $isGuru ? 'Guru' : 'Siswa';
    $roleClass = $isGuru ? 'badge-role-guru' : 'badge-role-siswa';
    $roleIcon = $isGuru ? 'fa-solid fa-user-tie' : 'fa-solid fa-graduation-cap';
    $nama = htmlspecialchars($user['nama_lengkap']);

    ob_start();
    ?>
    <div class="user-profile-widget">
        <span class="user-role-badge <?= $roleClass; ?>">
            <i class="<?= $roleIcon; ?>"></i> <?= $roleLabel; ?>
        </span>
        <span class="user-display-name" title="<?= $nama; ?>">
            <?= $nama; ?>
        </span>
        <a href="<?= $root_prefix; ?>logout.php" class="btn-nav-logout" title="Keluar dari sistem" onclick="return confirm('Apakah Anda yakin ingin logout?');">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
        </a>
    </div>
    <?php
    return ob_get_clean();
}
