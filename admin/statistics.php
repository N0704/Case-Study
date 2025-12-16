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
            <main class="flex-1 p-6 mt-16 overflow-y-auto">
                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Posts by District -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Tin đăng theo khu vực</h2>
                        <canvas id="districtChart"></canvas>
                    </div>

                    <!-- Posts by Category -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Tin đăng theo loại</h2>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Trend -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Xu hướng đăng tin (6 tháng gần nhất)</h2>
                    <canvas id="monthlyChart"></canvas>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Posts -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Top 10 tin xem nhiều nhất</h2>
                        <div class="space-y-3">
                            <?php $rank = 1; while ($post = mysqli_fetch_assoc($top_posts)): ?>
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-orange-600 font-bold text-sm"><?= $rank++ ?></span>
                                </div>
                                <img src="../<?= htmlspecialchars($post['image'] ?? 'assets/no-image.jpg') ?>" 
                                     alt="" class="w-12 h-12 rounded object-cover">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate text-sm"><?= htmlspecialchars($post['title']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($post['district_name']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900"><?= number_format($post['count_view']) ?></p>
                                    <p class="text-xs text-gray-500">lượt xem</p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Top Users -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Top 10 người dùng tích cực</h2>
                        <div class="space-y-3">
                            <?php $rank = 1; while ($user = mysqli_fetch_assoc($top_users)): ?>
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-sm"><?= $rank++ ?></span>
                                </div>
                                <img src="../<?= htmlspecialchars($user['avatar']) ?>" 
                                     alt="" class="w-10 h-10 rounded-full object-cover">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 text-sm"><?= htmlspecialchars($user['fullname']) ?></p>
                                    <p class="text-xs text-gray-500">@<?= htmlspecialchars($user['username']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900"><?= $user['post_count'] ?></p>
                                    <p class="text-xs text-gray-500">tin đăng</p>
                                </div>
                            </div>
                            <?php endwhile; ?>
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
