<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phòng trọ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-100">
    <?php 
    include('config/db.php'); 
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Get motel details
    $query = "SELECT m.*, c.name as category_name, d.name as district_name, u.fullname, u.avatar, u.created_at as user_joined
              FROM motels m 
              LEFT JOIN categories c ON m.category_id = c.id
              LEFT JOIN districts d ON m.district_id = d.id
              LEFT JOIN users u ON m.user_id = u.id
              WHERE m.id = $id";
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        header('Location: index.php');
        exit;
    }
    
    $room = mysqli_fetch_assoc($result);
    
    // Update view count
    mysqli_query($conn, "UPDATE motels SET count_view = count_view + 1 WHERE id = $id");
    ?>
    
    <?php include('components/header.php'); ?>
    
    <main class="pt-20 pb-8">
        <div class="px-19.5">
            <!-- Breadcrumb -->
            <div class="mb-4 text-sm">
                <a href="index.php" class="text-gray-500 hover:text-orange-500">Trang chủ</a>
                <span class="mx-2 text-gray-400">/</span>
                <a href="search.php" class="text-gray-500 hover:text-orange-500">Tìm kiếm</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-700"><?= htmlspecialchars($room['title']) ?></span>
            </div>

            <div class="flex gap-6">
                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Image -->
                    <div class="bg-gray-900 rounded-xl shadow-md overflow-hidden mb-6">
                        <?php if (!empty($room['image'])): ?>
                        <img src="<?= htmlspecialchars($room['image']) ?>" alt="Room image" class="w-full h-[400px] object-contain">
                        <?php else: ?>
                        <img src="assets/no-image.jpg" alt="No image" class="w-full h-[400px] object-contain">
                        <?php endif; ?>
                    </div>

                    <!-- Title & Main Info -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-4">
                        <div class="flex justify-between items-start mb-2">
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900 flex-1 pr-4 leading-snug">
                                <?= htmlspecialchars($room['title']) ?>
                            </h1>
                            <button class="flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-full text-sm font-medium hover:bg-gray-50 transition shrink-0">
                                <i class="far fa-heart"></i> Lưu
                            </button>
                        </div>

                        <div class="text-sm text-gray-500 mb-4">
                            <span><?= $room['category_name'] ?></span>
                        </div>

                        <div class="flex items-center gap-6 mb-4">
                            <span class="text-[#D0021B] text-xl font-bold"><?= number_format($room['price']) ?> triệu/tháng</span>
                            <span class="text-gray-900 font-medium"><?= $room['area'] ?> m²</span>
                        </div>

                        <div class="flex items-start gap-2 text-sm text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mt-1 text-gray-400"></i>
                            <span><?= htmlspecialchars($room['address']) ?>, <?= htmlspecialchars($room['district_name']) ?></span>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <i class="far fa-clock text-gray-400"></i>
                            <span>Cập nhật <?= date('d/m/Y', strtotime($room['created_at'])) ?></span>
                        </div>
                    </div>

                    <!-- Attributes Section -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-4">
                        <div class="grid grid-cols-1 gap-y-4">
                            <div class="flex items-center py-2 border-b border-gray-100 last:border-0">
                                <div class="w-40 flex items-center text-gray-500 gap-2">
                                    <i class="fas fa-home"></i> Loại hình nhà ở
                                </div>
                                <div class="text-gray-900 font-medium"><?= $room['category_name'] ?></div>
                            </div>
                            <div class="flex items-center py-2 border-b border-gray-100 last:border-0">
                                <div class="w-40 flex items-center text-gray-500 gap-2">
                                    <i class="fas fa-vector-square"></i> Diện tích
                                </div>
                                <div class="text-gray-900 font-medium"><?= $room['area'] ?> m²</div>
                            </div>
                            <div class="flex items-center py-2 border-b border-gray-100 last:border-0">
                                <div class="w-40 flex items-center text-gray-500 gap-2">
                                    <i class="fas fa-map-marker-alt"></i> Khu vực
                                </div>
                                <div class="text-gray-900 font-medium"><?= $room['district_name'] ?></div>
                            </div>
                            <?php if (!empty($room['utilities'])): ?>
                            <div class="flex items-start py-2 border-b border-gray-100 last:border-0">
                                <div class="w-40 flex items-center text-gray-500 gap-2 shrink-0">
                                    <i class="fas fa-ellipsis-h"></i> Tiện ích
                                </div>
                                <div class="text-gray-900 font-medium">
                                    <?= str_replace(',', ', ', $room['utilities']) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Mô tả chi tiết</h2>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line mb-6">
                            <?= nl2br(htmlspecialchars($room['description'])) ?>
                        </div>
                        
                        <div class="inline-flex items-center bg-gray-100 rounded-full px-4 py-2">
                            <span class="text-gray-700 font-medium mr-2">SĐT Liên hệ: <span class="phone-text"><?= substr($room['phone'], 0, 6) ?> ***</span></span>
                            <button onclick="showPhoneInDesc(this, '<?= $room['phone'] ?>')" class="text-blue-600 font-bold text-sm hover:underline">Hiện SĐT</button>
                        </div>
                    </div>

                    <!-- Address & Map -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Địa chỉ</h2>
                        <div class="flex items-start gap-3 mb-4">
                            <i class="fas fa-map-marker-alt text-orange-500 mt-1"></i>
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($room['address']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($room['district_name']) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($room['latitude'] && $room['longitude']): ?>
                        <div class="rounded-lg overflow-hidden h-80 bg-gray-200">
                            <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q=<?= $room['latitude'] ?>,<?= $room['longitude'] ?>&hl=vi&z=15&output=embed">
                            </iframe>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Similar Rooms -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold mb-4">Tin đăng tương tự</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <?php
                            $similar = mysqli_query($conn, "SELECT m.*, d.name as district_name 
                                FROM motels m 
                                LEFT JOIN districts d ON m.district_id = d.id
                                WHERE m.id != $id 
                                AND m.category_id = {$room['category_id']} 
                                AND m.approve = 1 
                                ORDER BY RAND() 
                                LIMIT 3");
                            
                            while ($s = mysqli_fetch_assoc($similar)) {
                                $s_image = $s['image'] ?? 'assets/no-image.jpg';
                            ?>
                            <a href="room-detail.php?id=<?= $s['id'] ?>" class="block bg-white rounded-lg hover:shadow-md transition overflow-hidden group">
                                <div class="relative aspect-4/3 overflow-hidden">
                                    <img src="<?= htmlspecialchars($s_image) ?>" 
                                         alt="<?= htmlspecialchars($s['title']) ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                </div>
                                <div class="p-3">
                                    <h3 class="font-medium text-gray-900 line-clamp-2 mb-2 text-sm"><?= htmlspecialchars($s['title']) ?></h3>
                                    <p class="text-[#D0021B] font-bold"><?= number_format($s['price']) ?> đ/tháng</p>
                                </div>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="w-96 shrink-0">
                    <!-- Contact Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 mb-4">
                            <div class="relative">
                                <img src="<?= htmlspecialchars($room['avatar'] ?? 'assets/images/default-avatar.png') ?>" 
                                     alt="<?= htmlspecialchars($room['fullname']) ?>"
                                     class="w-14 h-14 rounded-full object-cover border border-gray-100">
                                <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg leading-tight"><?= htmlspecialchars($room['fullname']) ?></h3>
                                <p class="text-xs text-gray-500 flex items-center mt-1">
                                    <i class="far fa-user-circle mr-1"></i> Cá nhân
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                            <span class="flex items-center"><i class="fas fa-circle text-[8px] text-green-500 mr-1.5"></i>Hoạt động 3 giờ trước</span>
                            <span>Phản hồi: ---</span>
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-100">
                            <span class="flex items-center"><i class="fas fa-list-ul mr-2 text-gray-400"></i>1 tin đăng</span>
                            <span class="flex items-center"><i class="far fa-calendar-alt mr-2 text-gray-400"></i>Tham gia <?= date('Y', strtotime($room['user_joined'])) ?></span>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $room['user_id']): ?>
                            <!-- Owner Actions -->
                            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 mb-6">
                                <p class="text-sm font-semibold text-blue-800 mb-3 text-center">Đây là tin đăng của bạn</p>
                                <div class="space-y-2">
                                    <a href="actions/toggle-rented.php?id=<?= $room['id'] ?>" 
                                       class="flex items-center justify-center w-full py-2.5 rounded-lg text-sm font-medium transition <?= $room['is_rented'] ? 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' : 'bg-green-600 text-white hover:bg-green-700 shadow-sm' ?>">
                                        <i class="fas <?= $room['is_rented'] ? 'fa-undo' : 'fa-check-circle' ?> mr-2"></i>
                                        <?= $room['is_rented'] ? 'Đánh dấu chưa thuê' : 'Đã cho thuê' ?>
                                    </a>
                                    <a href="edit-post.php?id=<?= $room['id'] ?>" class="flex items-center justify-center w-full bg-white text-blue-600 border border-blue-200 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-50 transition">
                                        <i class="fas fa-edit mr-2"></i>Chỉnh sửa tin
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex gap-3 mb-4">
                                <button class="flex-1 bg-gray-100 text-gray-800 font-bold py-3 rounded-lg hover:bg-gray-200 transition">
                                    Chat
                                </button>
                                <button onclick="showPhone(this, '<?= $room['phone'] ?>')" 
                                   class="flex-1 bg-[#FF6600] text-white font-bold py-3 rounded-lg hover:bg-[#e65c00] transition flex items-center justify-center">
                                    <i class="fas fa-phone-alt mr-2 transform -scale-x-100"></i> 
                                    <span>Hiện số <?= substr($room['phone'], 0, 4) ?> ***</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
                                <span class="text-xs font-medium text-gray-500 whitespace-nowrap">Chat nhanh:</span>
                                <button class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-full text-xs text-gray-700 whitespace-nowrap transition">Nhà này còn không ạ ?</button>
                                <button class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-full text-xs text-gray-700 whitespace-nowrap transition">Thời hạn thuê tối đa</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold mb-6">Bình luận</h3>
                        
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                <i class="far fa-comment-dots text-3xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm mb-1">Chưa có bình luận nào.</p>
                            <p class="text-gray-400 text-xs">Hãy để lại bình luận cho người bán.</p>
                        </div>

                        <div class="mt-4 flex gap-3 items-center pt-4 border-t border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden shrink-0">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <!-- Show current user avatar if logged in, else default -->
                                    <!-- Note: We need to fetch current user avatar in header or session. For now using placeholder or session avatar if available -->
                                    <img src="assets/images/default-avatar.png" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-gray-400 w-full h-full flex items-center justify-center"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 relative">
                                <input type="text" placeholder="Bình luận..." class="w-full bg-gray-100 border-none rounded-full py-2 px-4 text-sm focus:ring-1 focus:ring-orange-500 focus:bg-white transition placeholder-gray-500">
                                <button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php include('components/footer.php'); ?>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function showPhone(btn, phone) {
            const span = btn.querySelector('span');
            span.textContent = phone;
            btn.onclick = null; // Remove handler after showing
        }

        function showPhoneInDesc(btn, phone) {
            const container = btn.parentElement;
            const phoneText = container.querySelector('.phone-text');
            phoneText.textContent = phone;
            btn.style.display = 'none'; // Hide button
        }
    </script>
</body>
</html>
