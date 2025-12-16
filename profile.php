<?php
    session_start();
    include('config/db.php');
    
    // Check login
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get user data
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Get user stats
    $stats_query = "SELECT 
                    COUNT(*) as total_posts,
                    SUM(CASE WHEN approve = 1 THEN 1 ELSE 0 END) as approved_posts,
                    SUM(count_view) as total_views
                    FROM motels WHERE user_id = $user_id";
    $stats_result = mysqli_query($conn, $stats_query);
    $stats = mysqli_fetch_assoc($stats_result);
    
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-100"></body>
    <?php include('components/header.php'); ?>  
    <main class="pt-24 pb-8">
        <div class="max-w-5xl mx-auto px-4">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i><?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <div class="grid grid-cols-3 gap-6">
                <!-- Left Sidebar - User Info -->
                <div class="col-span-1">
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24">
                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="relative inline-block">
                                <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/images/default-avatar.png') ?>" 
                                     alt="Avatar" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-orange-100 mx-auto">
                                <button onclick="document.getElementById('avatar-upload').click()" 
                                        class="absolute bottom-0 right-0 bg-orange-500 text-white w-10 h-10 rounded-full hover:bg-orange-600 transition">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mt-4"><?= htmlspecialchars($user['fullname']) ?></h2>
                            <p class="text-gray-500">@<?= htmlspecialchars($user['username']) ?></p>
                        </div>

                        <!-- Stats -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600">Tổng tin đăng:</span>
                                <span class="font-medium text-gray-900"><?= $stats['total_posts'] ?></span>
                            </div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600">Tin đã duyệt:</span>
                                <span class="font-medium text-gray-900"><?= $stats['approved_posts'] ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tổng lượt xem:</span>
                                <span class="font-medium text-gray-900"><?= number_format($stats['total_views']) ?></span>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <a href="my-posts.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg mb-2">
                                <i class="fas fa-list mr-2"></i>Quản lý tin đăng
                            </a>
                            <a href="post.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Đăng tin mới
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Edit Profile -->
                <div class="col-span-2 space-y-6">
                    <!-- Personal Info Card -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h1 class="text-2xl font-bold text-gray-900 mb-6">Thông tin cá nhân</h1>
                        
                        <!-- Avatar Upload Form (Hidden) -->
                        <form action="actions/update-avatar.php" method="POST" enctype="multipart/form-data" id="avatar-form">
                            <input type="file" name="avatar" id="avatar-upload" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </form>

                        <!-- Profile Update Form -->
                        <form action="actions/update-profile.php" method="POST">
                            <!-- Họ tên -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Họ và tên <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="fullname" required
                                    value="<?= htmlspecialchars($user['fullname']) ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" required
                                    value="<?= htmlspecialchars($user['email']) ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <!-- Username (Read-only) -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tên đăng nhập
                                </label>
                                <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-500">
                                <p class="text-sm text-gray-500 mt-1">Tên đăng nhập không thể thay đổi</p>
                            </div>

                            <button type="submit" 
                                class="w-full bg-orange-500 text-white font-semibold py-3 rounded-lg hover:bg-orange-600 transition">
                                <i class="fas fa-save mr-2"></i>Lưu thay đổi
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Card -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Đổi mật khẩu</h2>
                        
                        <form action="actions/change-password.php" method="POST">
                            <!-- Current Password -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Mật khẩu hiện tại <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <!-- New Password -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Mật khẩu mới <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="new_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <!-- Confirm New Password -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="confirm_password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <button type="submit" 
                                class="w-full bg-orange-500 text-white font-semibold py-3 rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('components/footer.php'); ?>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
</html>
