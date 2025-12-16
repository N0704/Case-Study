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
            <main class="flex-1 p-6 mt-16 overflow-y-auto">
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

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <form method="GET" class="flex gap-4">
                        <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Tất cả trạng thái</option>
                            <option value="0" <?= $status_filter === '0' ? 'selected' : '' ?>>Chờ duyệt</option>
                            <option value="1" <?= $status_filter === '1' ? 'selected' : '' ?>>Đã duyệt</option>
                            <option value="2" <?= $status_filter === '2' ? 'selected' : '' ?>>Đã ẩn</option>
                        </select>

                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm..." 
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        
                        <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-search mr-2"></i>Tìm
                        </button>
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tin đăng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chủ nhà</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giá</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lượt xem</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($p = mysqli_fetch_assoc($posts)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900"><?= $p['id'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="../<?= htmlspecialchars($p['image'] ?? 'assets/no-image.jpg') ?>" 
                                             alt="" class="w-16 h-16 rounded object-cover">
                                        <div class="max-w-xs">
                                            <p class="font-medium text-gray-900 truncate"><?= htmlspecialchars($p['title']) ?></p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($p['district_name']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($p['user_name']) ?></td>
                                <td class="px-6 py-4 text-sm font-semibold text-orange-600"><?= number_format($p['price']) ?> đ</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <i class="fas fa-eye text-gray-400 mr-1"></i><?= number_format($p['count_view']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($p['is_rented']): ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-200 text-gray-700">
                                        Đã thuê
                                    </span>
                                    <?php elseif ($p['approve'] == 0): ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                        Chờ duyệt
                                    </span>
                                    <?php elseif ($p['approve'] == 1): ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Đã duyệt
                                    </span>
                                    <?php else: ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                        Đã ẩn
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    <a href="../room-detail.php?id=<?= $p['id'] ?>" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($p['approve'] == 0): ?>
                                    <a href="actions/approve-post.php?id=<?= $p['id'] ?>" 
                                       class="text-green-600 hover:text-green-800" title="Duyệt"
                                       onclick="return confirm('Duyệt tin này?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($p['approve'] == 1): ?>
                                    <a href="actions/hide-post.php?id=<?= $p['id'] ?>" 
                                       class="text-orange-600 hover:text-orange-800" title="Ẩn"
                                       onclick="return confirm('Ẩn tin này?')">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($p['approve'] == 2): ?>
                                    <a href="actions/approve-post.php?id=<?= $p['id'] ?>" 
                                       class="text-green-600 hover:text-green-800" title="Hiện lại"
                                       onclick="return confirm('Hiện lại tin này?')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="actions/delete-post-admin.php?id=<?= $p['id'] ?>" 
                                       class="text-red-600 hover:text-red-800" title="Xóa"
                                       onclick="return confirm('Xóa tin này vĩnh viễn?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
