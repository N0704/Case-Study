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
            <main class="flex-1 p-8 mt-16 overflow-y-auto bg-gray-50/50">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Quản lý khu vực 🗺️</h1>
                        <p class="text-gray-500 mt-1">Thêm và quản lý các khu vực, quận huyện trên bản đồ.</p>
                    </div>
                    <button onclick="openAddModal()" class="bg-orange-600 text-white px-5 py-2.5 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-500/30 flex items-center font-medium">
                        <i class="fas fa-plus mr-2"></i>Thêm khu vực
                    </button>
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

                <!-- Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tên khu vực</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tọa độ</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Số tin đăng</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while ($d = mysqli_fetch_assoc($districts)): ?>
                                <?php
                                $count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE district_id = {$d['id']}");
                                $count = mysqli_fetch_assoc($count_query)['count'];
                                ?>
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-6 py-4 text-sm text-gray-500">#<?= $d['id'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                                <i class="fas fa-map-marked-alt text-sm"></i>
                                            </div>
                                            <span class="font-bold text-gray-900 group-hover:text-orange-600 transition"><?= htmlspecialchars($d['name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 font-mono text-xs">
                                        <?php if (!empty($d['latitude']) && !empty($d['longitude'])): ?>
                                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-700">
                                                <?= $d['latitude'] ?>, <?= $d['longitude'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <?= $count ?> tin
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick='openEditModal(<?= json_encode($d) ?>)' 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Sửa khu vực">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button onclick="deleteDistrict(<?= $d['id'] ?>, <?= $count ?>)" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Xóa khu vực">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
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

    <!-- Add/Edit Modal -->
    <div id="districtModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all scale-100 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-900">Thêm khu vực</h2>
                    <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="districtForm" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="districtId">
                    
                    <!-- Tên khu vực -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Tên khu vực <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="districtName" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="VD: Gần Đại Học Vinh">
                    </div>

                    <!-- Map -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-700">
                                Vị trí trên bản đồ (Click để chọn)
                            </label>
                            <button type="button" id="get-location-modal" 
                                class="px-3 py-1.5 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg hover:bg-blue-100 transition flex items-center">
                                <i class="fas fa-location-arrow mr-1.5"></i>Vị trí của tôi
                            </button>
                        </div>
                        <div id="modalMap" class="w-full h-80 rounded-xl border border-gray-200 shadow-inner"></div>
                        <input type="hidden" name="latitude" id="modalLatitude">
                        <input type="hidden" name="longitude" id="modalLongitude">
                        <div id="modal-coords-display" class="mt-2 text-xs font-mono text-gray-500 bg-gray-50 inline-block px-2 py-1 rounded border border-gray-200 hidden">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1.5"></i>
                            <span id="modal-lat-display"></span>, <span id="modal-lng-display"></span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 transition font-medium">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="flex-1 bg-orange-600 text-white py-2.5 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-500/30 font-medium">
                            <i class="fas fa-save mr-2"></i>Lưu thay đổi
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
            document.getElementById('modalTitle').textContent = 'Thêm khu vực mới';
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
            document.getElementById('modalTitle').textContent = 'Cập nhật khu vực';
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
