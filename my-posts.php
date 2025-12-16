<?php
    session_start();
    include('config/db.php');
    
    // Check login
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Get user's posts
    $query = "SELECT m.*, d.name as district_name, c.name as category_name 
              FROM motels m 
              LEFT JOIN districts d ON m.district_id = d.id
              LEFT JOIN categories c ON m.category_id = c.id
              WHERE m.user_id = $user_id 
              ORDER BY m.created_at DESC";
    $posts = mysqli_query($conn, $query);
    
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tin đăng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-50 font-sans text-gray-800">
    <?php include('components/header.php'); ?>
    
    <main class="pt-24 pb-12 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Quản lý tin đăng</h1>
                    <p class="text-gray-500 mt-1 text-sm">Bạn đang có <span class="font-semibold text-gray-900"><?= mysqli_num_rows($posts) ?></span> tin đăng trong hệ thống</p>
                </div>
                <a href="post.php" class="inline-flex items-center px-5 py-2.5 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 focus:ring-4 focus:ring-orange-300 transition shadow-lg shadow-orange-500/30">
                    <i class="fas fa-plus mr-2"></i>Đăng tin mới
                </a>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <span class="font-medium"><?= $_SESSION['success'] ?></span>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                <span class="font-medium"><?= $_SESSION['error'] ?></span>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <!-- Posts List -->
            <div class="space-y-4">
                <?php if (mysqli_num_rows($posts) > 0): ?>
                    <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:border-orange-200 hover:shadow-md transition-all duration-300 group">
                        <div class="flex flex-col sm:flex-row">
                            <!-- Image -->
                            <div class="sm:w-64 h-48 sm:h-auto shrink-0 relative overflow-hidden">
                                <img src="<?= htmlspecialchars($post['image'] ?? 'assets/no-image.jpg') ?>" 
                                     alt="<?= htmlspecialchars($post['title']) ?>"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                <div class="absolute top-2 left-2">
                                    <?php if ($post['is_rented']): ?>
                                        <span class="px-2.5 py-1 bg-gray-900/80 text-white text-xs font-semibold rounded-md backdrop-blur-sm">
                                            Đã thuê
                                        </span>
                                    <?php elseif ($post['approve'] == 1): ?>
                                        <span class="px-2.5 py-1 bg-green-600/90 text-white text-xs font-semibold rounded-md backdrop-blur-sm">
                                            Đang hiển thị
                                        </span>
                                    <?php elseif ($post['approve'] == 2): ?>
                                        <span class="px-2.5 py-1 bg-red-600/90 text-white text-xs font-semibold rounded-md backdrop-blur-sm">
                                            Đã ẩn
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-yellow-500/90 text-white text-xs font-semibold rounded-md backdrop-blur-sm">
                                            Chờ duyệt
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 p-5 flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-4">
                                        <h3 class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-orange-600 transition">
                                            <a href="room-detail.php?id=<?= $post['id'] ?>">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </a>
                                        </h3>
                                        <!-- Price -->
                                        <span class="text-lg font-bold text-orange-600 whitespace-nowrap">
                                            <?= number_format($post['price'] / 1000000, 1) ?> tr/tháng
                                        </span>
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-y-2 gap-x-4 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                                            <span class="ml-1"><?= htmlspecialchars($post['district_name']) ?></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-vector-square w-4 text-gray-400"></i>
                                            <span class="ml-1"><?= $post['area'] ?> m²</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="far fa-clock w-4 text-gray-400"></i>
                                            <span class="ml-1"><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="far fa-eye w-4 text-gray-400"></i>
                                            <span class="ml-1"><?= number_format($post['count_view']) ?> lượt xem</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex gap-2">
                                        <a href="actions/toggle-rented.php?id=<?= $post['id'] ?>" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition border <?= $post['is_rented'] ? 'border-gray-300 text-gray-700 hover:bg-gray-50' : 'border-gray-200 text-[#FF6600] hover:bg-gray-50' ?>">
                                            <i class="fas <?= $post['is_rented'] ? 'fa-undo' : 'fa-check' ?> mr-1.5"></i>
                                            <?= $post['is_rented'] ? 'Mở lại tin' : 'Đã cho thuê' ?>
                                        </a>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="room-detail.php?id=<?= $post['id'] ?>" 
                                           class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition" title="Xem chi tiết">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="edit-post.php?id=<?= $post['id'] ?>" 
                                           class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-full transition" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deletePost(<?= $post['id'] ?>)" 
                                           class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition" title="Xóa tin">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
                        <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-3xl text-orange-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Chưa có tin đăng nào</h3>
                        <p class="text-gray-500 mb-6 max-w-md mx-auto">Bạn chưa đăng tin nào. Hãy đăng tin ngay để tiếp cận hàng ngàn người thuê tiềm năng.</p>
                        <a href="post.php" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-500/30">
                            <i class="fas fa-plus mr-2"></i>Đăng tin ngay
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include('components/footer.php'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <script>
        function deletePost(id) {
            if (confirm('Bạn có chắc chắn muốn xóa tin này? Hành động này không thể hoàn tác.')) {
                window.location.href = 'actions/delete-post.php?id=' + id;
            }
        }
    </script>
</body>
</html>
