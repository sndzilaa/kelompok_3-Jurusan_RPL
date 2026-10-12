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

    </script>
</body>

</html>
