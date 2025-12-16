<?php
session_start();
include('../config/db.php');

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Get users
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $search ? "WHERE fullname LIKE '%$search%' OR username LIKE '%$search%' OR email LIKE '%$search%'" : '';

$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM motels WHERE user_id = u.id) as post_count
          FROM users u
          $where
          ORDER BY u.id DESC";
$users = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <i class="fas fa-plus mr-2"></i>Thêm người dùng
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

                <!-- Search -->
                <div class="bg-white rounded-xl shadow-md p-4 mb-6">
                    <form method="GET" class="flex gap-4">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số tin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900"><?= $u['id'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="../<?= htmlspecialchars($u['avatar']) ?>" 
                                             alt="" class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($u['fullname']) ?></p>
                                            <p class="text-xs text-gray-500">@<?= htmlspecialchars($u['username']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><?= $u['post_count'] ?> tin</td>
                                <td class="px-6 py-4">
                                    <?php if ($u['role'] == 1): ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                        Admin
                                    </span>
                                    <?php else: ?>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                        User
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    <button onclick='openEditModal(<?= json_encode($u) ?>)' 
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button onclick="openChangePasswordModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['fullname']) ?>')" 
                                       class="text-green-600 hover:text-green-800">
                                        <i class="fas fa-key"></i> Đổi MK
                                    </button>
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="actions/delete-user.php?id=<?= $u['id'] ?>" 
                                       class="text-red-600 hover:text-red-800"
                                       onclick="return confirm('Xóa người dùng này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-900">Thêm người dùng</h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="userForm" method="POST">
                    <input type="hidden" name="id" id="userId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="fullname" id="fullname" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tên đăng nhập <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" id="username" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="mb-4" id="passwordField">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mật khẩu <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Vai trò
                        </label>
                        <select name="role" id="role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>

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

    <!-- Change Password Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Đổi mật khẩu</h2>
                    <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <p class="text-gray-600 mb-4">Đổi mật khẩu cho: <span id="pwdUserFullname" class="font-bold"></span></p>

                <form action="actions/reset-password.php" method="POST">
                    <input type="hidden" name="id" id="pwdUserId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Mật khẩu mới <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Nhập mật khẩu mới">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-save mr-2"></i>Cập nhật
                        </button>
                        <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">
                            <i class="fas fa-times mr-2"></i>Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm người dùng';
            document.getElementById('userForm').action = 'actions/create-user.php';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function openEditModal(user) {
            document.getElementById('modalTitle').textContent = 'Sửa người dùng';
            document.getElementById('userForm').action = 'actions/update-user.php';
            document.getElementById('userId').value = user.id;
            document.getElementById('fullname').value = user.fullname;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('role').value = user.role;
            document.getElementById('password').required = false;
            document.getElementById('password').value = '';
            document.getElementById('passwordField').style.display = 'none';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function openChangePasswordModal(id, fullname) {
            document.getElementById('pwdUserId').value = id;
            document.getElementById('pwdUserFullname').textContent = fullname;
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
        }
    </script>
</body>
</html>
