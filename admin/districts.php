<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get districts
$districts = mysqli_query($conn, "SELECT * FROM districts ORDER BY id");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khu vực - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/output.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <?php include('components/sidebar.php'); ?>
        
        <div class="flex-1 ml-64 flex flex-col">
            <?php include('components/header.php'); ?>

            <!-- Main Content -->
            <main class="flex-1 p-6 mt-16 overflow-y-auto">
                <!-- Actions -->
                <div class="flex justify-end mb-6">
                    <button onclick="openAddModal()" class="bg-orange-600 text-white px-5 py-2.5 rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-500/30 flex items-center font-medium">
                        <i class="fas fa-plus mr-2"></i>Thêm khu vực
                    </button>
                </div>
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

                <!-- Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên khu vực</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tọa độ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số tin đăng</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($d = mysqli_fetch_assoc($districts)): ?>
                            <?php
                            $count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE district_id = {$d['id']}");
                            $count = mysqli_fetch_assoc($count_query)['count'];
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900"><?= $d['id'] ?></td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($d['name']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php if (!empty($d['latitude']) && !empty($d['longitude'])): ?>
                                        <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                                        <?= $d['latitude'] ?>, <?= $d['longitude'] ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?= $count ?> tin</td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    <button onclick='openEditModal(<?= json_encode($d) ?>)' 
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button onclick="deleteDistrict(<?= $d['id'] ?>, <?= $count ?>)" 
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="districtModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-900">Thêm khu vực</h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="districtForm" method="POST">
                    <input type="hidden" name="id" id="districtId">
                    
                    <!-- Tên khu vực -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tên khu vực <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="districtName" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="VD: Gần Đại Học Vinh">
                    </div>

                    <!-- Map -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Vị trí trên bản đồ (Click để chọn)
                            </label>
                            <button type="button" id="get-location-modal" 
                                class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition">
                                <i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi
                            </button>
                        </div>
                        <div id="modalMap" class="w-full h-96 rounded-lg border-2 border-gray-300"></div>
                        <input type="hidden" name="latitude" id="modalLatitude">
                        <input type="hidden" name="longitude" id="modalLongitude">
                        <div id="modal-coords-display" class="mt-2 text-sm text-gray-600 hidden">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            Tọa độ: <span id="modal-lat-display"></span>, <span id="modal-lng-display"></span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-save mr-2"></i>Lưu
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let modalMap = null;
        let modalMarker = null;
        let isEditMode = false;

        function openAddModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Thêm khu vực';
            document.getElementById('districtForm').action = 'actions/create-district.php';
            document.getElementById('districtForm').reset();
            document.getElementById('districtId').value = '';
            document.getElementById('modal-coords-display').classList.add('hidden');
            
            document.getElementById('districtModal').classList.remove('hidden');
            
            setTimeout(() => {
                initModalMap(18.6750, 105.6880);
            }, 100);
        }

        function openEditModal(district) {
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Sửa khu vực';
            document.getElementById('districtForm').action = 'actions/update-district.php';
            document.getElementById('districtId').value = district.id;
            document.getElementById('districtName').value = district.name;
            document.getElementById('modalLatitude').value = district.latitude || '';
            document.getElementById('modalLongitude').value = district.longitude || '';
            
            if (district.latitude && district.longitude) {
                document.getElementById('modal-lat-display').textContent = district.latitude;
                document.getElementById('modal-lng-display').textContent = district.longitude;
                document.getElementById('modal-coords-display').classList.remove('hidden');
            }
            
            document.getElementById('districtModal').classList.remove('hidden');
            
            setTimeout(() => {
                const lat = district.latitude || 18.6750;
                const lng = district.longitude || 105.6880;
                initModalMap(lat, lng);
                
                if (district.latitude && district.longitude) {
                    modalMarker = L.marker([lat, lng]).addTo(modalMap)
                        .bindPopup('Vị trí hiện tại')
                        .openPopup();
                }
            }, 100);
        }

        function closeModal() {
            document.getElementById('districtModal').classList.add('hidden');
            if (modalMap) {
                modalMap.remove();
                modalMap = null;
                modalMarker = null;
            }
        }

        function initModalMap(lat, lng) {
            if (modalMap) {
                modalMap.remove();
            }
            
            modalMap = L.map('modalMap').setView([lat, lng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(modalMap);
            
            modalMap.on('click', function(e) {
                const lat = e.latlng.lat.toFixed(6);
                const lng = e.latlng.lng.toFixed(6);
                
                document.getElementById('modalLatitude').value = lat;
                document.getElementById('modalLongitude').value = lng;
                document.getElementById('modal-lat-display').textContent = lat;
                document.getElementById('modal-lng-display').textContent = lng;
                document.getElementById('modal-coords-display').classList.remove('hidden');
                
                if (modalMarker) {
                    modalMap.removeLayer(modalMarker);
                }
                
                modalMarker = L.marker([lat, lng]).addTo(modalMap)
                    .bindPopup('Vị trí khu vực')
                    .openPopup();
            });
            
            // Get location button
            document.getElementById('get-location-modal').addEventListener('click', function() {
                if (navigator.geolocation) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang lấy...';
                    const btn = this;
                    
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude.toFixed(6);
                            const lng = position.coords.longitude.toFixed(6);
                            
                            modalMap.setView([lat, lng], 15);
                            
                            document.getElementById('modalLatitude').value = lat;
                            document.getElementById('modalLongitude').value = lng;
                            document.getElementById('modal-lat-display').textContent = lat;
                            document.getElementById('modal-lng-display').textContent = lng;
                            document.getElementById('modal-coords-display').classList.remove('hidden');
                            
                            if (modalMarker) {
                                modalMap.removeLayer(modalMarker);
                            }
                            
                            modalMarker = L.marker([lat, lng]).addTo(modalMap)
                                .bindPopup('Vị trí của bạn')
                                .openPopup();
                            
                            btn.innerHTML = '<i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi';
                        },
                        function(error) {
                            alert('Không thể lấy vị trí: ' + error.message);
                            btn.innerHTML = '<i class="fas fa-location-arrow mr-1"></i>Vị trí của tôi';
                        }
                    );
                }
            });
        }

        function deleteDistrict(id, count) {
            if (count > 0) {
                alert('Không thể xóa khu vực này vì còn ' + count + ' tin đăng!');
                return;
            }
            
            if (confirm('Bạn có chắc chắn muốn xóa khu vực này?')) {
                window.location.href = 'actions/delete-district.php?id=' + id;
            }
        }
    </script>
</body>
</html>
