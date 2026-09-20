<?php
include 'koneksi.php';
include 'auth.php';
if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';
$pesan_sukses = '';

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'logout') {
        $pesan_sukses = "Anda telah berhasil keluar dari SIM-LAB.";
    } elseif ($_GET['pesan'] === 'harus_login') {
        $error = "Silakan login terlebih dahulu untuk mengakses sistem SIM-LAB.";
    } elseif ($_GET['pesan'] === 'akses_ditolak') {
        $error = "Akses ditolak. Anda tidak memiliki izin untuk membuka halaman tersebut.";
    }
}

if (isset($_POST['login'])) {
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

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            background: #f8fafc;
            color: #0f172a;
        }

        .input-wrapper input:focus {
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

        .quick-demo-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed #e2e8f0;
            text-align: center;
        }

        .quick-demo-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .quick-demo-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-demo {
            flex: 1;
            padding: 10px 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-demo-guru {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .btn-demo-guru:hover {
            background-color: #dcfce7;
            border-color: #86efac;
        }

        .btn-demo-siswa {
            background-color: #eff6ff;
            color: #1e40af;
            border-color: #bfdbfe;
        }

        .btn-demo-siswa:hover {
            background-color: #dbeafe;
            border-color: #93c5fd;
        }

        .demo-credentials-note {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 12px;
            line-height: 1.4;
        }
    </style>
</head>

<body class="login-page">

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <i class="fa-solid fa-computer"></i>
            </div>
            <h1>SIM-LAB</h1>
            <p>Sistem Informasi Manajemen Laboratorium RPL</p>
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

            <div class="quick-demo-box">
                <div class="quick-demo-title">Akses Cepat Pengujian</div>
                <div class="quick-demo-buttons">
                    <button type="button" class="btn-demo btn-demo-guru" onclick="setDemo('guru', 'guru123')">
                        <i class="fa-solid fa-user-tie"></i> Akun Guru
                    </button>
                    <button type="button" class="btn-demo btn-demo-siswa" onclick="setDemo('siswa', 'siswa123')">
                        <i class="fa-solid fa-graduation-cap"></i> Akun Siswa
                    </button>
                </div>
                <div class="demo-credentials-note">
                    Guru: <b>guru</b> (pass: <i>guru123</i>) &bull; Siswa: <b>siswa</b> (pass: <i>siswa123</i>)
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle lihat password
        const btnTogglePassword = document.getElementById('btnTogglePassword');
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

        // Fitur akses cepat demo akun
        function setDemo(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>

</html>
