<?php
session_start();
require 'config/db.php';

$_SESSION['login_fail'] ??= 0;

// Đã login → redirect
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$needCaptcha = $_SESSION['login_fail'] >= 5;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ===== CAPTCHA ===== */
    if ($needCaptcha) {
        $token = $_POST['cf-turnstile-response'] ?? '';
        if (!$token || !verifyCaptcha($token)) {
            $error = 'CAPTCHA không hợp lệ!';
        }
    }

    /* ===== LOGIN ===== */
    if (!$error) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($res);

        if ($user && $password === $user['password']) {
            $_SESSION['login_fail'] = 0;

            $_SESSION += [
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'role'     => $user['role'],
                'avatar'   => $user['avatar']
            ];

            header('Location: ' . ($user['role'] == 1 ? 'admin/index.php' : 'index.php'));
            exit;
        }

        $_SESSION['login_fail']++;
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        $needCaptcha = $_SESSION['login_fail'] >= 5;
    }
}

/* ===== FUNCTION ===== */
function verifyCaptcha($token): bool
{
    $secret = '1x0000000000000000000000000000000AA'; // test key

    $response = file_get_contents(
        'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        false,
        stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query([
                    'secret'  => $secret,
                    'response'=> $token,
                    'remoteip'=> $_SERVER['REMOTE_ADDR']
                ])
            ]
        ])
    );

    return json_decode($response, true)['success'] ?? false;
};
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen bg-linear-to-br from-white to-gray-200 relative">
        <a href="index.php" class="absolute top-8 left-15">
            <img src="./assets/images/logo.png" alt="" class="h-14 object-contain">
        </a>
        <div class="w-full max-w-md bg-white px-10 py-8 rounded-xl shadow-xl relative">
            <!-- Top Border -->
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-[#FF6600] to-[#FF930F] rounded-t-xl"></div>

            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-semibold text-[#FF6600] mb-2">Đăng Nhập</h1>
                <p class="text-sm text-gray-600">
                    Chào mừng đến với <span class="font-semibold text-[#FF6600]">Trọ Tốt</span>, đăng nhập để tiếp tục
                </p>
            </div>

            <!-- Google Login -->
            <button type="button" class="w-full py-2.5 border border-gray-300 text-sm cursor-pointer bg-white rounded-lg hover:bg-gray-50 transition mb-4">
                <div class="flex items-center justify-center">
                    <img src="./assets/images/google-logo.png" alt="google" class="w-5 h-5 mr-2" onerror="this.style.display='none'; this.nextElementSibling.innerHTML='<i class=\'fab fa-google text-red-500 mr-2\></i>Đăng nhập với Google'">
                    <span class="text-gray-600 font-medium">Đăng nhập với Google</span>
                </div>
            </button>

            <!-- Divider -->
            <div class="text-center text-xs text-gray-500 relative mb-5">
                <span class="absolute left-0 top-1/2 w-1/3 h-px bg-gray-300"></span>
                <span class="absolute right-0 top-1/2 w-1/3 h-px bg-gray-300"></span>
                Hoặc đăng nhập bằng
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
            </div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <!-- Username -->
                <div class="mb-5">
                    <label for="username" class="block mb-2 text-gray-800 font-medium text-sm">
                        Tên đăng nhập
                    </label>
                    <input id="username" name="username" type="text" placeholder="Nhập tên đăng nhập" required
                           class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" />
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block mb-2 text-gray-800 font-medium text-sm">
                        Mật khẩu
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required
                               class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" />
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa fa-eye text-sm" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Turnstile -->
                <?php if ($needCaptcha): ?>
                    <div class="mb-5 flex justify-center">
                        <div class="cf-turnstile" data-sitekey="1x00000000000000000000AA"></div>
                    </div>
                <?php endif; ?>

                <!-- Remember & Forgot -->
                <div class="flex justify-between items-center text-sm mb-6 text-gray-700">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="accent-orange-600 w-4 h-4 rounded border-gray-300" />
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-[#FF6600] hover:underline font-medium">Quên mật khẩu?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-2.5 cursor-pointer bg-[#FF6600] text-white rounded-lg font-medium hover:bg-[#FF930F]/90 transition shadow-lg shadow-orange-500/30">
                    ĐĂNG NHẬP
                </button>

                <!-- Register Link -->
                <div class="text-center text-sm mt-6 text-gray-600">
                    Chưa có tài khoản?
                    <a href="register.php" class="text-[#FF6600] font-semibold hover:underline">Đăng ký ngay</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
