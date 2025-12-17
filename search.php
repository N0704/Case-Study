<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm phòng trọ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-100">
    <?php include('config/db.php'); ?>
    <?php include('components/header.php'); ?>
    
    <main class="pt-20 pb-8">
        <div class="px-19.5">
            <div class="flex gap-6">
                <!-- Sidebar Filters -->
                <aside class="w-80 shrink-0">
                    <div class="bg-white rounded-xl shadow-md p-5 sticky top-24">
                        <h3 class="text-lg font-bold mb-4">Bộ lọc tìm kiếm</h3>
                        
                        <form method="GET" action="search.php" id="filterForm">
                            <!-- Khu vực -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2">Khu vực</label>
                                <select name="district" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Tất cả khu vực</option>
                                    <?php
                                    $districts = mysqli_query($conn, "SELECT * FROM districts ORDER BY name");
                                    while ($d = mysqli_fetch_assoc($districts)) {
                                        $selected = (isset($_GET['district']) && $_GET['district'] == $d['id']) ? 'selected' : '';
                                        echo "<option value='{$d['id']}' $selected>{$d['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Loại phòng -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2">Loại phòng</label>
                                <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Tất cả loại</option>
                                    <?php
                                    $categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
                                    while ($cat = mysqli_fetch_assoc($categories)) {
                                        $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                                        echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Khoảng giá -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2">Khoảng giá</label>
                                <div class="flex gap-2">
                                    <input type="number" name="price_min" placeholder="Từ" 
                                        value="<?= $_GET['price_min'] ?? '' ?>"
                                        class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <input type="number" name="price_max" placeholder="Đến" 
                                        value="<?= $_GET['price_max'] ?? '' ?>"
                                        class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button type="button" onclick="setPrice(0, 2000000)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-orange-100 rounded-full">< 2 triệu</button>
                                    <button type="button" onclick="setPrice(2000000, 3000000)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-orange-100 rounded-full">2-3 triệu</button>
                                    <button type="button" onclick="setPrice(3000000, 5000000)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-orange-100 rounded-full">3-5 triệu</button>
                                    <button type="button" onclick="setPrice(5000000, 999999999)" class="text-xs px-3 py-1 bg-gray-100 hover:bg-orange-100 rounded-full">> 5 triệu</button>
                                </div>
                            </div>

                            <!-- Diện tích -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2">Diện tích (m²)</label>
                                <div class="flex gap-2">
                                    <input type="number" name="area_min" placeholder="Từ" 
                                        value="<?= $_GET['area_min'] ?? '' ?>"
                                        class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <input type="number" name="area_max" placeholder="Đến" 
                                        value="<?= $_GET['area_max'] ?? '' ?>"
                                        class="w-1/2 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                            </div>

                            <!-- Từ khóa -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2">Từ khóa</label>
                                <input type="text" name="keyword" placeholder="Tìm kiếm..." 
                                    value="<?= $_GET['keyword'] ?? '' ?>"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-2.5 rounded-lg hover:bg-orange-600 transition">
                                <i class="fas fa-search mr-2"></i>Tìm kiếm
                            </button>
                            
                            <a href="search.php" class="block text-center text-sm text-gray-500 hover:text-orange-500 mt-3">
                                Xóa bộ lọc
                            </a>
                        </form>
                    </div>
                </aside>

                <!-- Results -->
                <div class="flex-1">
                    <!-- Header -->
                    <div class="bg-white rounded-xl shadow-md p-4 mb-4 flex justify-between items-center">
                        <div>
                            <?php
                            $where = ["m.approve = 1"];

                            $filters = [
                                'district'  => 'm.district_id = %d',
                                'category'  => 'm.category_id = %d',
                                'price_min' => 'm.price >= %d',
                                'price_max' => 'm.price <= %d',
                                'area_min'  => 'm.area >= %d',
                                'area_max'  => 'm.area <= %d',
                            ];

                            foreach ($filters as $key => $condition) {
                                if (!empty($_GET[$key])) {
                                    $value = (int)$_GET[$key];
                                    $where[] = sprintf($condition, $value);
                                }
                            }

                            // keyword xử lý riêng
                            if (!empty($_GET['keyword'])) {
                                $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
                                $where[] = "(m.title LIKE '%$keyword%' 
                                        OR m.description LIKE '%$keyword%' 
                                        OR m.address LIKE '%$keyword%')";
                            }

                            $where_clause = implode(' AND ', $where);

                            
                            // Pagination
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $per_page = 9;
                            $offset = ($page - 1) * $per_page;
                            
                            // Count total
                            $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM motels m WHERE $where_clause");
                            $total = mysqli_fetch_assoc($count_query)['total'];
                            $total_pages = ceil($total / $per_page);
                            
                            // Sort
                            $sort = $_GET['sort'] ?? 'newest';
                            $order_by = match($sort) {
                                'price_asc' => 'm.price ASC',
                                'price_desc' => 'm.price DESC',
                                'area_desc' => 'm.area DESC',
                                'views' => 'm.count_view DESC',
                                default => 'm.id DESC'
                            };
                            ?>
                            <h2 class="text-lg font-bold">Tìm thấy <?= number_format($total) ?> kết quả</h2>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600">Sắp xếp:</span>
                            <select name="sort" onchange="updateSort(this.value)" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá thấp → cao</option>
                                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá cao → thấp</option>
                                <option value="area_desc" <?= $sort == 'area_desc' ? 'selected' : '' ?>>Diện tích lớn nhất</option>
                                <option value="views" <?= $sort == 'views' ? 'selected' : '' ?>>Xem nhiều nhất</option>
                            </select>
                        </div>
                    </div>

                    <!-- Grid Results -->
                    <div class="grid grid-cols-3 gap-4">
                        <?php
                        $query = "SELECT m.*, d.name as district_name 
                                  FROM motels m 
                                  LEFT JOIN districts d ON m.district_id = d.id
                                  WHERE $where_clause 
                                  ORDER BY $order_by 
                                  LIMIT $per_page OFFSET $offset";
                        $results = mysqli_query($conn, $query);
                        
                        while ($r = mysqli_fetch_assoc($results)) {
                            $image = $r['image'] ?? 'assets/no-image.jpg';
                            $created_at = date('d/m', strtotime($r['created_at']));
                        ?>
                        <div class="bg-white rounded-lg hover:shadow-lg transition duration-200 overflow-hidden group cursor-pointer">
                            <a href="room-detail.php?id=<?= $r['id'] ?>" class="block relative aspect-4/3 overflow-hidden">
                                <img src="<?= htmlspecialchars($image) ?>"
                                    alt="<?= htmlspecialchars($r['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                                <div class="absolute bottom-2 left-2">
                                    <span class="text-[11px] font-semibold text-white flex items-center">
                                        <i class="far fa-clock mr-1"></i>
                                        <?= $created_at ?>
                                    </span>
                                </div>
                                
                                <button class="absolute top-2 right-2 text-white hover:text-red-500 transition bg-black/20 w-8 h-8 rounded-full flex items-center justify-center">
                                    <i class="far fa-heart"></i>
                                </button>
                            </a>

                            <div class="p-3">
                                <a href="room-detail.php?id=<?= $r['id'] ?>">
                                    <h3 class="text-base font-medium text-gray-900 line-clamp-2 mb-2 leading-snug hover:text-orange-500 transition">
                                        <?= htmlspecialchars($r['title']) ?>
                                    </h3>
                                </a>

                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[#D0021B] font-bold text-base">
                                        <?= number_format($r['price']) ?> đ/tháng
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        <?= $r['area'] ?> m²
                                    </span>
                                </div>

                                <div class="flex items-center text-gray-500 text-sm pt-2 border-t border-gray-100">
                                    <i class="fas fa-map-marker-alt mr-1.5 w-3 text-center text-gray-400"></i>
                                    <?= htmlspecialchars($r['district_name'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="mt-6 flex justify-center gap-2">
                        <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="px-4 py-2 rounded-lg <?= $i == $page ? 'bg-orange-500 text-white' : 'bg-white border border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include('components/footer.php'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        
        function setPrice(min, max) {
            document.querySelector('input[name="price_min"]').value = min;
            document.querySelector('input[name="price_max"]').value = max;
        }
        
        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            url.searchParams.delete('page');
            window.location = url;
        }
    </script>
</body>
</html>
