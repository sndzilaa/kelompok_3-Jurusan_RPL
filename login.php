<?php
include 'koneksi.php';
include 'auth.php';
if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';
$pesan_sukses = '';
$isDaftar = isset($_GET['daftar']);

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'logout') {
        $pesan_sukses = "Anda telah berhasil keluar dari SIM-LAB.";
    } elseif ($_GET['pesan'] === 'daftar_berhasil') {
        $pesan_sukses = "Pendaftaran berhasil. Silakan masuk dengan akun baru Anda.";
    } elseif ($_GET['pesan'] === 'harus_login') {
        $error = "Silakan login terlebih dahulu untuk mengakses sistem SIM-LAB.";
    } elseif ($_GET['pesan'] === 'akses_ditolak') {
        $error = "Akses ditolak. Anda tidak memiliki izin untuk membuka halaman tersebut.";
    }
}

if (isset($_POST['daftar'])) {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';
    $isDaftar = true;

    if ($nama_lengkap === '' || $username === '' || $password === '' || $konfirmasi_password === '') {
        $error = "Semua kolom wajib diisi!";
    } elseif (preg_match('/.{101}/us', $nama_lengkap) === 1 || preg_match('/[0-9]/', $nama_lengkap)) {
        $error = "Nama lengkap maksimal 100 karakter dan tidak boleh mengandung angka.";
    } elseif (preg_match('/.{51}/us', $username) === 1) {
        $error = "Username maksimal 50 karakter.";
    } elseif (strlen($password) < 8) {
        $error = "Password harus terdiri dari minimal 8 karakter.";
    } elseif ($password !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak sesuai.";
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ? LIMIT 1");
        if (!$stmt) {
            error_log("Gagal menyiapkan pemeriksaan username: " . mysqli_error($koneksi));
            $error = "Pendaftaran belum dapat diproses. Silakan coba lagi.";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (!mysqli_stmt_execute($stmt) || !mysqli_stmt_store_result($stmt)) {
                error_log("Gagal memeriksa username: " . mysqli_stmt_error($stmt));
                $error = "Pendaftaran belum dapat diproses. Silakan coba lagi.";
                mysqli_stmt_close($stmt);
            } else {
                $username_tersedia = mysqli_stmt_num_rows($stmt) === 0;
                mysqli_stmt_close($stmt);

                if (!$username_tersedia) {
                    $error = "Username sudah digunakan. Silakan pilih username lain.";
                } else {
                    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'siswa')");
                    if (!$stmt) {
                        error_log("Gagal menyiapkan pendaftaran akun: " . mysqli_error($koneksi));
                        $error = "Pendaftaran belum dapat diproses. Silakan coba lagi.";
                    } else {
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_stmt_bind_param($stmt, "sss", $username, $password_hash, $nama_lengkap);

                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_close($stmt);
                            header("Location: login.php?pesan=daftar_berhasil");
                            exit();
                        }

                        if (mysqli_stmt_errno($stmt) === 1062) {
                            $error = "Username sudah digunakan. Silakan pilih username lain.";
                        } else {
                            error_log("Gagal menyimpan akun baru: " . mysqli_stmt_error($stmt));
                            $error = "Pendaftaran belum dapat diproses. Silakan coba lagi.";
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
            }
        }
    }
} elseif (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id_user, username, password, nama_lengkap, role FROM users WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && $user = mysqli_fetch_assoc($res)) {
            // Verifikasi password (hash atau plain-text fallback untuk pengujian)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user'] = [
                    'id_user'      => $user['id_user'],
                    'username'     => $user['username'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'role'         => $user['role']
                ];

                header("Location: index.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Akun dengan username '$username' tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-LAB</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="loading.css">
    <script src="loading.js" defer></script>
    <style>
        body.login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, #1e40af, #0f172a 75%);
            padding: 20px;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.15);
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 28px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .login-header .brand-logo {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            color: #ffffff;
        }

        .login-header p {
            font-size: 13px;
            color: #bfdbfe;
            margin: 0;
        }

        .login-body {
            padding: 30px 28px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.field-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }

        form .input-wrapper input[type="text"],
        form .input-wrapper input[type="password"] {
            width: 100%;
            padding: 12px 14px 12px 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            background: #f8fafc;
            color: #0f172a;
        }

        form .input-wrapper input[type="password"] {
            padding-right: 42px;
        }

        form .input-wrapper input[type="text"]:focus,
        form .input-wrapper input[type="password"]:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 6px;
            font-size: 14px;
        }

        .toggle-password:hover {
            color: #1e293b;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }

        .register-link {
            margin: 20px 0 0;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .register-link a {
            color: #1d4ed8;
            font-weight: 700;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <i class="fa-solid fa-computer"></i>
            </div>
            <h1><?= $isDaftar ? 'Buat Akun' : 'SIM-LAB'; ?></h1>
            <p><?= $isDaftar ? 'Daftar sebagai siswa untuk menggunakan SIM-LAB' : 'Sistem Informasi Manajemen Laboratorium RPL'; ?></p>
        </div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-bottom: 18px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success" style="margin-bottom: 18px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= htmlspecialchars($pesan_sukses); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($isDaftar): ?>
                <form action="login.php?daftar=1" method="POST" id="registerForm">
                    <div class="input-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card field-icon"></i>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" maxlength="100" required autocomplete="name" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user field-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Buat username" maxlength="50" required autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required minlength="8" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="konfirmasi_password">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password" required minlength="8" autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" name="daftar" class="btn-submit">
                        <i class="fa-solid fa-user-plus"></i> Daftar
                    </button>
                </form>
                <p class="register-link">Sudah punya akun? <a href="login.php">Masuk</a></p>
            <?php else: ?>
            <form action="login.php" method="POST" id="loginForm">
                <div class="input-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user field-icon"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required autocomplete="username">
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="toggle-password" id="btnTogglePassword" title="Tampilkan/Sembunyikan Password">
                            <i class="fa-solid fa-eye" id="toggleEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
                </button>
            </form>
            <p class="register-link">Belum punya akun? <a href="login.php?daftar=1">Daftar sekarang</a></p>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // Toggle lihat password
        const btnTogglePassword = document.getElementById('btnTogglePassword');
        if (btnTogglePassword) {
            const passwordInput = document.getElementById('password');
            const toggleEyeIcon = document.getElementById('toggleEyeIcon');

            btnTogglePassword.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleEyeIcon.classList.remove('fa-eye');
                    toggleEyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleEyeIcon.classList.remove('fa-eye-slash');
                    toggleEyeIcon.classList.add('fa-eye');
                }
            });
        }

    </script>
</body>

</html>
