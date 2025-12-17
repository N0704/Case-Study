<?php
    session_start();
    include('config/db.php');

    // Check login
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng tin mới</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body class="bg-gray-100">
    <?php include('components/header.php'); ?>
    <main class="pt-24 pb-8">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-xl shadow-md p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Đăng tin cho thuê phòng trọ</h1>
                
                <form action="actions/create-post.php" method="POST" enctype="multipart/form-data">
                    <!-- Tiêu đề -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tiêu đề tin đăng <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="VD: Cho thuê phòng trọ giá rẻ gần trường ĐH Vinh">
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mô tả chi tiết <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" required rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Mô tả chi tiết về phòng trọ..."></textarea>
                    </div>

                    <!-- Giá & Diện tích -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Giá thuê (VNĐ/tháng) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="price" required min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="2000000">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Diện tích (m²) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="area" required min="0" step="0.1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="25">
                        </div>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Địa chỉ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Số nhà, tên đường, phường/xã">
                    </div>

                    <!-- Khu vực & Loại phòng -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Khu vực <span class="text-red-500">*</span>
                            </label>
                            <select name="district_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Chọn khu vực</option>
                                <?php
                                $districts = mysqli_query($conn, "SELECT * FROM districts ORDER BY name");
                                while ($d = mysqli_fetch_assoc($districts)) {
                                    echo "<option value='{$d['id']}'>{$d['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Loại phòng <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Chọn loại phòng</option>
                                <?php
                                $categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
                                while ($cat = mysqli_fetch_assoc($categories)) {
                                    echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Số điện thoại liên hệ <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="0123456789">
                    </div>

                    <!-- Tiện ích -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tiện ích
                        </label>
                        <input type="text" name="utilities"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="VD: Wifi, Điều hòa, Nóng lạnh (cách nhau bởi dấu phẩy)">
                        <p class="text-sm text-gray-500 mt-1">Nhập các tiện ích, cách nhau bởi dấu phẩy</p>
                    </div>

                    <!-- Vị trí trên bản đồ -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Vị trí trên bản đồ (Click để chọn vị trí)
                            </label>
                            <button type="button" id="get-location" 
                                class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi
                            </button>
                        </div>
                        <div id="map" class="w-full h-96 rounded-lg border-2 border-gray-300"></div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Click vào bản đồ để chọn vị trí chính xác của phòng trọ
                        </p>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <div id="coords-display" class="mt-2 text-sm text-gray-600 hidden">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            Tọa độ: <span id="lat-display"></span>, <span id="lng-display"></span>
                        </div>
                    </div>

                    <!-- Upload ảnh -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Hình ảnh <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" name="image" id="image" accept="image/*" required class="hidden">
                            <label for="image" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Click để chọn ảnh</p>
                                <p class="text-sm text-gray-500 mt-1">JPG, PNG (Max 5MB)</p>
                            </label>
                            <div id="preview" class="mt-4 hidden">
                                <img id="preview-img" class="max-h-64 mx-auto rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-orange-500 text-white font-semibold py-3 rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-paper-plane mr-2"></i>Đăng tin
                        </button>
                        <a href="index.php"
                            class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-300 transition text-center">
                            <i class="fas fa-times mr-2"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include('components/footer.php'); ?>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (ev) {
                document.getElementById('preview')?.classList.remove('hidden');
                document.getElementById('preview-img').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    const map = L.map('map').setView([18.6750, 105.6880], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    let marker = null;

    const districtCoords = <?php
        $districts_coords = mysqli_query(
            $conn,
            "SELECT id, latitude, longitude 
            FROM districts 
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL"
        );

        $coords = [];
        while ($dc = mysqli_fetch_assoc($districts_coords)) {
            $coords[$dc['id']] = [
                (float)$dc['latitude'],
                (float)$dc['longitude']
            ];
        }

        echo json_encode($coords, JSON_UNESCAPED_UNICODE);
    ?>;

    const districtSelect = document.querySelector('select[name="district_id"]');
    if (districtSelect) {
        districtSelect.addEventListener('change', function () {
            const districtId = this.value;
            if (districtId && districtCoords[districtId]) {
                map.setView(districtCoords[districtId], 14);
            }
        });
    }

    const getLocationBtn = document.getElementById('get-location');
    if (getLocationBtn) {
        getLocationBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Trình duyệt không hỗ trợ Geolocation!');
                return;
            }

            const btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang lấy vị trí...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);

                    map.setView([lat, lng], 15);

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    document.getElementById('lat-display').textContent = lat;
                    document.getElementById('lng-display').textContent = lng;
                    document.getElementById('coords-display')?.classList.remove('hidden');

                    if (marker) map.removeLayer(marker);

                    marker = L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup('Vị trí của bạn')
                        .openPopup();

                    btn.innerHTML = '<i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi';
                },
                function (error) {
                    alert('Không thể lấy vị trí: ' + error.message);
                    btn.innerHTML = '<i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi';
                }
            );
        });
    }

    map.on('click', function (e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        document.getElementById('lat-display').textContent = lat;
        document.getElementById('lng-display').textContent = lng;
        document.getElementById('coords-display')?.classList.remove('hidden');

        if (marker) map.removeLayer(marker);

        marker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup('Vị trí phòng trọ')
            .openPopup();
    });
    </script>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
</html>
