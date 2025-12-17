<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Posts by district
$posts_by_district = mysqli_query($conn, "SELECT d.name, COUNT(m.id) as count 
                                          FROM districts d 
                                          LEFT JOIN motels m ON d.id = m.district_id 
                                          GROUP BY d.id 
                                          ORDER BY count DESC");

// Posts by category
$posts_by_category = mysqli_query($conn, "SELECT c.name, COUNT(m.id) as count 
                                          FROM categories c 
                                          LEFT JOIN motels m ON c.id = m.category_id 
                                          GROUP BY c.id 
                                          ORDER BY count DESC");

// Top posts by views
$top_posts = mysqli_query($conn, "SELECT m.*, u.fullname, d.name as district_name 
                                  FROM motels m 
                                  LEFT JOIN users u ON m.user_id = u.id
                                  LEFT JOIN districts d ON m.district_id = d.id
                                  WHERE m.approve = 1
                                  ORDER BY m.count_view DESC LIMIT 10");

// Top users by post count
$top_users = mysqli_query($conn, "SELECT u.*, COUNT(m.id) as post_count 
                                  FROM users u 
                                  LEFT JOIN motels m ON u.id = m.user_id 
                                  GROUP BY u.id 
                                  ORDER BY post_count DESC LIMIT 10");

// Monthly stats (last 6 months)
$monthly_stats = mysqli_query($conn, "SELECT 
                                      DATE_FORMAT(created_at, '%Y-%m') as month,
                                      COUNT(*) as count
                                      FROM motels
                                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                      GROUP BY month
                                      ORDER BY month ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/output.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include('components/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 flex flex-col">
            <?php include('components/header.php'); ?>

            <!-- Main Content -->
            <main class="flex-1 p-8 mt-16 overflow-y-auto bg-gray-50/50">
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Thống kê & Báo cáo 📊</h1>
                        <p class="text-gray-500 mt-1">Phân tích chi tiết hoạt động của hệ thống.</p>
                    </div>
                    <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm flex items-center text-sm font-medium">
                        <i class="fas fa-print mr-2"></i> In báo cáo
                    </button>
                </div>

                <!-- Charts Row 1 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Posts by District -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-map-marked-alt"></i>
                            </span>
                            Phân bố theo khu vực
                        </h2>
                        <div class="relative h-64">
                            <canvas id="districtChart"></canvas>
                        </div>
                    </div>

                    <!-- Posts by Category -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            Phân bố theo loại hình
                        </h2>
                        <div class="relative h-64 flex items-center justify-center">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mr-3 text-sm">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        Xu hướng đăng tin (6 tháng gần nhất)
                    </h2>
                    <div class="relative h-72">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Top Posts -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center mr-3 text-sm">
                                    <i class="fas fa-trophy"></i>
                                </span>
                                Top 10 tin xem nhiều nhất
                            </h2>
                        </div>
                        <div class="p-4 flex-1">
                            <div class="space-y-2">
                                <?php $rank = 1; while ($post = mysqli_fetch_assoc($top_posts)): ?>
                                <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition group">
                                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center font-bold text-lg
                                        <?= $rank == 1 ? 'text-yellow-500' : ($rank == 2 ? 'text-gray-400' : ($rank == 3 ? 'text-orange-700' : 'text-gray-400 text-sm')) ?>">
                                        <?php if ($rank <= 3): ?>
                                            <i class="fas fa-crown"></i>
                                        <?php else: ?>
                                            #<?= $rank ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="relative w-12 h-12 rounded-lg overflow-hidden shrink-0">
                                        <img src="../<?= htmlspecialchars($post['image'] ?? 'assets/no-image.jpg') ?>" 
                                             alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 truncate text-sm group-hover:text-orange-600 transition">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($post['district_name']) ?>
                                        </p>
                                    </div>
                                    <div class="text-right bg-gray-100 px-3 py-1 rounded-lg">
                                        <p class="text-sm font-bold text-gray-900"><?= number_format($post['count_view']) ?></p>
                                        <p class="text-[10px] text-gray-500 uppercase">Views</p>
                                    </div>
                                </div>
                                <?php $rank++; endwhile; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Top Users -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm">
                                    <i class="fas fa-medal"></i>
                                </span>
                                Top người dùng tích cực
                            </h2>
                        </div>
                        <div class="p-4 flex-1">
                            <div class="space-y-2">
                                <?php $rank = 1; while ($user = mysqli_fetch_assoc($top_users)): ?>
                                <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                                    <div class="flex-shrink-0 w-6 text-center font-bold text-gray-400 text-sm">
                                        <?= $rank ?>
                                    </div>
                                    <div class="relative">
                                        <img src="../<?= htmlspecialchars($user['avatar']) ?>" 
                                             alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        <?php if ($rank <= 3): ?>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full border-2 border-white flex items-center justify-center text-[8px] text-white
                                            <?= $rank == 1 ? 'bg-yellow-500' : ($rank == 2 ? 'bg-gray-400' : 'bg-orange-700') ?>">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($user['fullname']) ?></p>
                                        <p class="text-xs text-gray-500">@<?= htmlspecialchars($user['username']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= $user['post_count'] ?> tin
                                        </span>
                                    </div>
                                </div>
                                <?php $rank++; endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // District Chart
        const districtCtx = document.getElementById('districtChart').getContext('2d');
        new Chart(districtCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    mysqli_data_seek($posts_by_district, 0);
                    $labels = [];
                    while ($row = mysqli_fetch_assoc($posts_by_district)) {
                        $labels[] = "'" . addslashes($row['name']) . "'";
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    label: 'Số tin đăng',
                    data: [<?php 
                        mysqli_data_seek($posts_by_district, 0);
                        $data = [];
                        while ($row = mysqli_fetch_assoc($posts_by_district)) {
                            $data[] = $row['count'];
                        }
                        echo implode(',', $data);
                    ?>],
                    backgroundColor: 'rgba(249, 115, 22, 0.8)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    mysqli_data_seek($posts_by_category, 0);
                    $labels = [];
                    while ($row = mysqli_fetch_assoc($posts_by_category)) {
                        $labels[] = "'" . addslashes($row['name']) . "'";
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    data: [<?php 
                        mysqli_data_seek($posts_by_category, 0);
                        $data = [];
                        while ($row = mysqli_fetch_assoc($posts_by_category)) {
                            $data[] = $row['count'];
                        }
                        echo implode(',', $data);
                    ?>],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $labels = [];
                    while ($row = mysqli_fetch_assoc($monthly_stats)) {
                        $labels[] = "'" . $row['month'] . "'";
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    label: 'Tin đăng mới',
                    data: [<?php 
                        mysqli_data_seek($monthly_stats, 0);
                        $data = [];
                        while ($row = mysqli_fetch_assoc($monthly_stats)) {
                            $data[] = $row['count'];
                        }
                        echo implode(',', $data);
                    ?>],
                    borderColor: 'rgba(249, 115, 22, 1)',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
