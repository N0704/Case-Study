<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get filter params
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build query
$where = [];
if ($status_filter !== '') {
    $where[] = "m.approve = " . (int)$status_filter;
}
if ($search) {
    $where[] = "(m.title LIKE '%$search%' OR m.address LIKE '%$search%' OR u.fullname LIKE '%$search%')";
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT m.*, u.fullname as user_name, d.name as district_name, c.name as category_name
          FROM motels m
          LEFT JOIN users u ON m.user_id = u.id
          LEFT JOIN districts d ON m.district_id = d.id
          LEFT JOIN categories c ON m.category_id = c.id
          $where_sql
          ORDER BY m.created_at DESC";
$posts = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tin đăng - Admin</title>
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
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">Quản lý tin đăng 📰</h1>
                    <p class="text-gray-500 mt-1">Duyệt, ẩn và quản lý tất cả tin đăng trên hệ thống.</p>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <span class="font-medium"><?= $_SESSION['success'] ?></span>
                </div>
                <?php unset($_SESSION['success']); endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                    <span class="font-medium"><?= $_SESSION['error'] ?></span>
                </div>
                <?php unset($_SESSION['error']); endif; ?>

                <!-- Filters -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
                    <form method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="relative min-w-[200px]">
                            <select name="status" onchange="this.form.submit()" class="w-full appearance-none border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 text-gray-700 font-medium cursor-pointer hover:bg-white transition">
                                <option value="">Tất cả trạng thái</option>
                                <option value="0" <?= $status_filter === '0' ? 'selected' : '' ?>>🟡 Chờ duyệt</option>
                                <option value="1" <?= $status_filter === '1' ? 'selected' : '' ?>>🟢 Đã duyệt</option>
                                <option value="2" <?= $status_filter === '2' ? 'selected' : '' ?>>🔴 Đã ẩn</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-3.5 text-gray-400 pointer-events-none text-xs"></i>
                        </div>

                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm theo tiêu đề, địa chỉ, người đăng..." 
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                        </div>
                        
                        <button type="submit" class="bg-orange-600 text-white px-6 py-2.5 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-500/30 font-medium flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>Tìm kiếm
                        </button>
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tin đăng</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Chủ nhà</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Giá & Lượt xem</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while ($p = mysqli_fetch_assoc($posts)): ?>
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-4">
                                            <div class="relative w-20 h-20 rounded-lg overflow-hidden shrink-0 border border-gray-100">
                                                <img src="../<?= htmlspecialchars($p['image'] ?? 'assets/no-image.jpg') ?>" 
                                                     alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                                <div class="absolute top-0 left-0 bg-black/50 text-white text-[10px] px-1.5 py-0.5 rounded-br-lg backdrop-blur-sm">
                                                    #<?= $p['id'] ?>
                                                </div>
                                            </div>
                                            <div class="max-w-xs">
                                                <p class="font-bold text-gray-900 line-clamp-2 mb-1 group-hover:text-orange-600 transition"><?= htmlspecialchars($p['title']) ?></p>
                                                <p class="text-xs text-gray-500 flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-1.5 text-orange-500"></i><?= htmlspecialchars($p['district_name']) ?>
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <i class="far fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                <?= strtoupper(substr($p['user_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($p['user_name']) ?></p>
                                                <p class="text-xs text-gray-500">Chủ nhà</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-bold text-orange-600"><?= number_format($p['price']) ?> đ</span>
                                            <span class="text-xs text-gray-500 flex items-center">
                                                <i class="fas fa-eye mr-1.5"></i><?= number_format($p['count_view']) ?> lượt xem
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($p['is_rented']): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>Đã thuê
                                        </span>
                                        <?php elseif ($p['approve'] == 0): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>Chờ duyệt
                                        </span>
                                        <?php elseif ($p['approve'] == 1): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Đã duyệt
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>Đã ẩn
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="../room-detail.php?id=<?= $p['id'] ?>" target="_blank" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Xem chi tiết">
                                                <i class="fas fa-external-link-alt text-xs"></i>
                                            </a>
                                            
                                            <?php if ($p['approve'] == 0): ?>
                                            <a href="actions/approve-post.php?id=<?= $p['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition" title="Duyệt tin"
                                               onclick="return confirm('Duyệt tin này?')">
                                                <i class="fas fa-check text-xs"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($p['approve'] == 1): ?>
                                            <a href="actions/hide-post.php?id=<?= $p['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition" title="Ẩn tin"
                                               onclick="return confirm('Ẩn tin này?')">
                                                <i class="fas fa-eye-slash text-xs"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($p['approve'] == 2): ?>
                                            <a href="actions/approve-post.php?id=<?= $p['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition" title="Hiện lại"
                                               onclick="return confirm('Hiện lại tin này?')">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <a href="actions/delete-post-admin.php?id=<?= $p['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Xóa vĩnh viễn"
                                               onclick="return confirm('Xóa tin này vĩnh viễn?')">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
