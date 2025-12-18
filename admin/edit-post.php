<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM motels WHERE id = $id"));

if (!$post) {
    $_SESSION['error'] = 'Tin đăng không tồn tại!';
    header('Location: posts.php');
    exit;
}

// Get users for dropdown
$users = mysqli_query($conn, "SELECT id, fullname, username FROM users ORDER BY fullname");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa tin đăng - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include('components/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 flex flex-col">
            <?php include('components/header.php'); ?>

            <!-- Main Content -->
            <main class="flex-1 p-8 mt-16 overflow-y-auto bg-gray-50/50">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center gap-4 mb-8">
                        <a href="posts.php" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-orange-600 transition shadow-sm">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Sửa tin đăng #<?= $post['id'] ?></h1>
                            <p class="text-gray-500 mt-1">Cập nhật thông tin chi tiết của tin đăng.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                        <form action="actions/update-post.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $post['id'] ?>">
                            
                            <!-- User Selection -->
                            <div class="mb-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <label class="block text-sm font-bold text-blue-800 mb-2">
                                    Người đăng tin (Chủ nhà) <span class="text-red-500">*</span>
                                </label>
                                <select name="user_id" required class="w-full px-4 py-2.5 border border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">-- Chọn người dùng --</option>
                                    <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                        <option value="<?= $u['id'] ?>" <?= $u['id'] == $post['user_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($u['fullname']) ?> (@<?= htmlspecialchars($u['username']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Title -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề tin <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Category -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Loại phòng <span class="text-red-500">*</span></label>
                                    <select name="category_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                        <?php
                                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                                        while ($c = mysqli_fetch_assoc($cats)) {
                                            $selected = $c['id'] == $post['category_id'] ? 'selected' : '';
                                            echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- District -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Khu vực <span class="text-red-500">*</span></label>
                                    <select name="district_id" required id="district-select" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                        <?php
                                        $dists = mysqli_query($conn, "SELECT * FROM districts ORDER BY name");
                                        while ($d = mysqli_fetch_assoc($dists)) {
                                            $selected = $d['id'] == $post['district_id'] ? 'selected' : '';
                                            echo "<option value='{$d['id']}' data-lat='{$d['latitude']}' data-lng='{$d['longitude']}' $selected>{$d['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Giá thuê (VNĐ) <span class="text-red-500">*</span></label>
                                    <input type="number" name="price" value="<?= $post['price'] ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Area -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Diện tích (m²) <span class="text-red-500">*</span></label>
                                    <input type="number" name="area" value="<?= $post['area'] ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ chính xác <span class="text-red-500">*</span></label>
                                    <input type="text" name="address" value="<?= htmlspecialchars($post['address']) ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả chi tiết <span class="text-red-500">*</span></label>
                                    <textarea name="description" required rows="5" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"><?= htmlspecialchars($post['description']) ?></textarea>
                                </div>

                                <!-- Utilities -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tiện ích</label>
                                    <input type="text" name="utilities" value="<?= htmlspecialchars($post['utilities']) ?>" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" value="<?= htmlspecialchars($post['phone']) ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition">
                                </div>

                                <!-- Image -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh</label>
                                    <div class="flex items-center gap-4 mb-3">
                                        <img src="../<?= htmlspecialchars($post['image']) ?>" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        <p class="text-sm text-gray-500">Ảnh hiện tại</p>
                                    </div>
                                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                </div>

                                <!-- Map -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Vị trí trên bản đồ <span class="text-red-500">*</span></label>
                                    <div id="map" class="w-full h-80 rounded-xl border border-gray-200 shadow-inner mb-2"></div>
                                    <input type="hidden" name="latitude" id="latitude" value="<?= $post['latitude'] ?>">
                                    <input type="hidden" name="longitude" id="longitude" value="<?= $post['longitude'] ?>">
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                                <a href="posts.php" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Hủy bỏ</a>
                                <button type="submit" class="px-6 py-2.5 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-500/30">
                                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const lat = <?= $post['latitude'] ?: 18.6750 ?>;
        const lng = <?= $post['longitude'] ?: 105.6880 ?>;
        
        const map = L.map('map').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([lat, lng]).addTo(map);

        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
        });

        document.getElementById('district-select').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const lat = option.getAttribute('data-lat');
            const lng = option.getAttribute('data-lng');
            
            if (lat && lng) {
                map.setView([lat, lng], 14);
            }
        });
    </script>
</body>
</html>
