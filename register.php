<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php
    // Nếu đã đăng nhập, redirect về trang chủ
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
    
    $error = '';
    $success = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include('config/db.php');
        
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate
        if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
            $error = 'Vui lòng điền đầy đủ thông tin!';
        } elseif ($password !== $confirm_password) {
            $error = 'Mật khẩu xác nhận không khớp!';
        } elseif (strlen($password) < 6) {
            $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
        } else {
            // Check username exists
            $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' LIMIT 1");
            if (mysqli_num_rows($check) > 0) {
                $error = 'Tên đăng nhập đã tồn tại!';
            } else {
                // Check email exists
                $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
                if (mysqli_num_rows($check) > 0) {
                    $error = 'Email đã được sử dụng!';
                } else {
                    // Insert new user
                    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=random";
                    
                    $query = "INSERT INTO users (fullname, username, email, password, avatar, role) 
                              VALUES ('$fullname', '$username', '$email', '$password', '$avatar', 0)";
                    
                    if (mysqli_query($conn, $query)) {
                        $success = 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...';
                        header('refresh:2;url=login.php');
                    } else {
                        $error = 'Có lỗi xảy ra. Vui lòng thử lại!';
                    }
                }
            }
        }
    }
    ?>

    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-white to-gray-200 p-4 relative">
        <a href="index.php" class="absolute top-8 left-15 hidden md:block">
            <img src="./assets/images/logo.png" alt="" class="h-14 object-contain">
        </a>
        
        <div class="w-full max-w-md bg-white px-10 py-8 rounded-xl shadow-xl relative">
            <!-- Top Border -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-500 to-orange-400 rounded-t-xl"></div>

            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-semibold text-orange-600 mb-2">Tạo Tài Khoản Mới</h1>
                <p class="text-sm text-gray-600">
                    Đặt phòng tại <span class="font-semibold text-orange-600">Trọ Tốt</span> ngay hôm nay!
                </p>
            </div>

            <!-- Google Register -->
            <button type="button" class="w-full py-2.5 border border-gray-300 text-sm cursor-pointer bg-white rounded-lg hover:bg-gray-50 transition mb-4">
                <div class="flex items-center justify-center">
                    <img src="./assets/images/google-logo.png" alt="google" class="w-5 h-5 mr-2" onerror="this.style.display='none'; this.nextElementSibling.innerHTML='<i class=\'fab fa-google text-red-500 mr-2\'></i>Tạo tài khoản bằng Google'">
                    <span class="text-gray-600 font-medium">Tạo tài khoản bằng Google</span>
                </div>
            </button>

            <!-- Divider -->
            <div class="text-center text-xs text-gray-500 relative mb-5">
                <span class="absolute left-0 top-1/2 w-1/3 h-px bg-gray-300"></span>
                <span class="absolute right-0 top-1/2 w-1/3 h-px bg-gray-300"></span>
                Hoặc tạo tài khoản với
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center">
                <i class="fas fa-check-circle mr-2"></i><?= $success ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <!-- Full Name -->
                <div class="mb-5">
                    <label for="fullname" class="block mb-2 text-orange-600 font-medium text-sm">
                        Họ và tên
                    </label>
                    <input id="fullname" name="fullname" type="text" placeholder="Tên của bạn" required
                           value="<?= $_POST['fullname'] ?? '' ?>"
                           class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition" />
                </div>

                <!-- Username -->
                <div class="mb-5">
                    <label for="username" class="block mb-2 text-orange-600 font-medium text-sm">
                        Tên đăng nhập
                    </label>
                    <input id="username" name="username" type="text" placeholder="Tên đăng nhập" required
                           value="<?= $_POST['username'] ?? '' ?>"
                           class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition" />
                </div>

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="block mb-2 text-orange-600 font-medium text-sm">
                        Email
                    </label>
                    <input id="email" name="email" type="email" placeholder="Nhập email của bạn" required
                           value="<?= $_POST['email'] ?? '' ?>"
                           class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition" />
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block mb-2 text-orange-600 font-medium text-sm">
                        Mật khẩu
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" required
                               class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition" />
                        <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-5">
                    <label for="confirm_password" class="block mb-2 text-orange-600 font-medium text-sm">
                        Xác nhận mật khẩu
                    </label>
                    <div class="relative">
                        <input id="confirm_password" name="confirm_password" type="password" placeholder="Nhập lại mật khẩu" required
                               class="w-full px-4 text-sm py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition" />
                        <button type="button" onclick="togglePassword('confirm_password', 'eyeIconConfirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa fa-eye" id="eyeIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <div class="text-xs text-gray-800 mb-5">
                    Bằng cách nhấp vào Đăng ký, bạn đồng ý với
                    <span class="font-semibold cursor-pointer hover:underline text-orange-600">Điều khoản</span>,
                    <span class="font-semibold cursor-pointer hover:underline text-orange-600">Chính sách quyền riêng tư</span> và
                    <span class="font-semibold cursor-pointer hover:underline text-orange-600">Chính sách cookie</span> của chúng tôi.
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-2.5 cursor-pointer bg-orange-600 text-white rounded-lg font-medium hover:bg-orange-700 transition shadow-lg shadow-orange-500/30">
                    ĐĂNG KÝ
                </button>

                <!-- Login Link -->
                <div class="text-center text-sm mt-5 text-gray-600">
                    Bạn đã có tài khoản?
                    <a href="login.php" class="text-orange-600 font-medium hover:underline">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
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
