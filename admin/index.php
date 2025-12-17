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
            <main class="flex-1 p-8 mt-16 overflow-y-auto bg-gray-50/50">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Xin chào, Admin! 👋</h1>
                    <p class="text-gray-500 mt-1">Dưới đây là tổng quan tình hình hoạt động của hệ thống.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Users -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-blue-50 rounded-xl">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-arrow-up mr-1"></i> +12%
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Tổng người dùng</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_users']) ?></h3>
                        </div>
                    </div>

                    <!-- Total Posts -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-orange-50 rounded-xl">
                                <i class="fas fa-home text-orange-600 text-xl"></i>
                            </div>
                            <span class="flex items-center text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-arrow-up mr-1"></i> +5%
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Tổng tin đăng</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_posts']) ?></h3>
                        </div>
                    </div>

                    <!-- Pending Posts -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-yellow-50 rounded-xl">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <?php if ($stats['pending_posts'] > 0): ?>
                            <span class="flex items-center text-xs font-bold text-white bg-red-500 px-2 py-1 rounded-full animate-pulse">
                                Cần duyệt
                            </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Tin chờ duyệt</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['pending_posts']) ?></h3>
                        </div>
                        <?php if ($stats['pending_posts'] > 0): ?>
                        <a href="posts.php?status=0" class="absolute bottom-0 left-0 w-full bg-yellow-50 text-yellow-700 text-xs font-bold py-2 text-center hover:bg-yellow-100 transition">
                            Xử lý ngay <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Total Views -->
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 bg-purple-50 rounded-xl">
                                <i class="fas fa-eye text-purple-600 text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Tổng lượt xem</p>
                            <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_views']) ?></h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <!-- Recent Posts -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-gray-900">Tin đăng mới nhất</h2>
                            <a href="posts.php" class="text-sm font-medium text-orange-600 hover:text-orange-700 flex items-center">
                                Xem tất cả <i class="fas fa-arrow-right ml-1 text-xs"></i>
                            </a>
                        </div>
                        <div class="p-6 flex-1">
                            <div class="space-y-6">
                                <?php while ($post = mysqli_fetch_assoc($recent_posts)): ?>
                                <div class="flex items-start gap-4 group">
                                    <div class="relative w-20 h-20 rounded-lg overflow-hidden shrink-0">
                                        <img src="../<?= htmlspecialchars($post['image'] ?? 'assets/no-image.jpg') ?>" 
                                             alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-bold text-gray-900 line-clamp-1 group-hover:text-orange-600 transition">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </h3>
                                            <?php if ($post['approve'] == 0): ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-yellow-100 text-yellow-700 shrink-0 ml-2">Chờ duyệt</span>
                                            <?php elseif ($post['approve'] == 1): ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-green-100 text-green-700 shrink-0 ml-2">Đã duyệt</span>
                                            <?php else: ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-red-100 text-red-700 shrink-0 ml-2">Đã ẩn</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <i class="fas fa-map-marker-alt text-xs mr-1.5 w-3 text-center"></i>
                                            <?= htmlspecialchars($post['district_name']) ?>
                                        </p>
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-orange-600 font-bold text-sm"><?= number_format($post['price']) ?> đ</p>
                                            <p class="text-xs text-gray-400">
                                                <i class="far fa-clock mr-1"></i><?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Users & Quick Actions -->
                    <div class="space-y-8">
                        <!-- Recent Users -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                                <h2 class="text-lg font-bold text-gray-900">Thành viên mới</h2>
                                <a href="users.php" class="text-sm font-medium text-orange-600 hover:text-orange-700">Xem tất cả</a>
                            </div>
                            <div class="p-4">
                                <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                                <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl transition">
                                    <img src="../<?= htmlspecialchars($user['avatar']) ?>" 
                                         alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 text-sm truncate"><?= htmlspecialchars($user['fullname']) ?></p>
                                        <p class="text-xs text-gray-500 truncate">@<?= htmlspecialchars($user['username']) ?></p>
                                    </div>
                                    <?php if ($user['role'] == 1): ?>
                                    <i class="fas fa-shield-alt text-purple-500" title="Admin"></i>
                                    <?php endif; ?>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg p-6 text-white">
                            <h2 class="text-lg font-bold mb-4">Thao tác nhanh</h2>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="posts.php?status=0" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm p-4 rounded-xl flex flex-col items-center justify-center transition border border-white/10">
                                    <i class="fas fa-check-double text-2xl mb-2"></i>
                                    <span class="text-xs font-bold">Duyệt tin</span>
                                </a>
                                <a href="users.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm p-4 rounded-xl flex flex-col items-center justify-center transition border border-white/10">
                                    <i class="fas fa-user-plus text-2xl mb-2"></i>
                                    <span class="text-xs font-bold">QL User</span>
                                </a>
                                <a href="districts.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm p-4 rounded-xl flex flex-col items-center justify-center transition border border-white/10">
                                    <i class="fas fa-map text-2xl mb-2"></i>
                                    <span class="text-xs font-bold">Khu vực</span>
                                </a>
                                <a href="statistics.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm p-4 rounded-xl flex flex-col items-center justify-center transition border border-white/10">
                                    <i class="fas fa-chart-pie text-2xl mb-2"></i>
                                    <span class="text-xs font-bold">Báo cáo</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
