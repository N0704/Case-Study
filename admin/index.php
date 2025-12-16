<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get statistics
$stats = [];

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$stats['total_users'] = mysqli_fetch_assoc($result)['count'];

// Total posts
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels");
$stats['total_posts'] = mysqli_fetch_assoc($result)['count'];

// Pending posts
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE approve = 0");
$stats['pending_posts'] = mysqli_fetch_assoc($result)['count'];

// Approved posts
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE approve = 1");
$stats['approved_posts'] = mysqli_fetch_assoc($result)['count'];

// Total views
$result = mysqli_query($conn, "SELECT SUM(count_view) as total FROM motels");
$stats['total_views'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Recent posts
$recent_posts = mysqli_query($conn, "SELECT m.*, u.fullname, d.name as district_name 
                                     FROM motels m 
                                     LEFT JOIN users u ON m.user_id = u.id
                                     LEFT JOIN districts d ON m.district_id = d.id
                                     ORDER BY m.created_at DESC LIMIT 5");

// Recent users
$recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include('components/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 flex flex-col">
            <?php include('components/header.php'); ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 mt-16 overflow-y-auto">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Total Users -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Tổng người dùng</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_users']) ?></h3>
                            </div>
                            <div class="bg-blue-100 rounded-full p-4">
                                <i class="fas fa-users text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Posts -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Tổng tin đăng</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_posts']) ?></h3>
                            </div>
                            <div class="bg-green-100 rounded-full p-4">
                                <i class="fas fa-home text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Posts -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Chờ duyệt</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['pending_posts']) ?></h3>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-4">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                        <?php if ($stats['pending_posts'] > 0): ?>
                        <a href="posts.php?status=0" class="text-sm text-orange-600 hover:text-orange-700 mt-2 inline-block">
                            Xem ngay →
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Total Views -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Tổng lượt xem</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['total_views']) ?></h3>
                            </div>
                            <div class="bg-purple-100 rounded-full p-4">
                                <i class="fas fa-eye text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Posts -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-gray-900">Tin đăng mới nhất</h2>
                            <a href="posts.php" class="text-sm text-orange-600 hover:text-orange-700">Xem tất cả →</a>
                        </div>
                        <div class="space-y-4">
                            <?php while ($post = mysqli_fetch_assoc($recent_posts)): ?>
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100 last:border-0">
                                <img src="../<?= htmlspecialchars($post['image'] ?? 'assets/no-image.jpg') ?>" 
                                     alt="" class="w-16 h-16 rounded object-cover">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate"><?= htmlspecialchars($post['title']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-user mr-1"></i><?= htmlspecialchars($post['fullname']) ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <?php if ($post['approve'] == 0): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Chờ</span>
                                    <?php elseif ($post['approve'] == 1): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Duyệt</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Ẩn</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-gray-900">Người dùng mới</h2>
                            <a href="users.php" class="text-sm text-orange-600 hover:text-orange-700">Xem tất cả →</a>
                        </div>
                        <div class="space-y-4">
                            <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100 last:border-0">
                                <img src="../<?= htmlspecialchars($user['avatar']) ?>" 
                                     alt="" class="w-12 h-12 rounded-full object-cover">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($user['fullname']) ?></p>
                                    <p class="text-sm text-gray-500">@<?= htmlspecialchars($user['username']) ?></p>
                                </div>
                                <?php if ($user['role'] == 1): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">Admin</span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">User</span>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Thao tác nhanh</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="posts.php?status=0" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                            <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Duyệt tin</span>
                        </a>
                        <a href="users.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Quản lý users</span>
                        </a>
                        <a href="districts.php" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <i class="fas fa-map-marked-alt text-green-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Quản lý khu vực</span>
                        </a>
                        <a href="statistics.php" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                            <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700">Thống kê</span>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
