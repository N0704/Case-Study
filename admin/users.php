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
            <main class="flex-1 p-8 mt-16 overflow-y-auto bg-gray-50/50">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Quản lý người dùng 👥</h1>
                        <p class="text-gray-500 mt-1">Quản lý tài khoản, phân quyền và mật khẩu người dùng.</p>
                    </div>
                    <button onclick="openAddModal()" class="bg-orange-600 text-white px-5 py-2.5 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-500/30 flex items-center font-medium">
                        <i class="fas fa-plus mr-2"></i>Thêm người dùng
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

                <!-- Search -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-8">
                    <form method="GET" class="flex gap-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm theo tên, username, email..." 
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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Người dùng</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Hoạt động</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vai trò</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                <tr class="hover:bg-gray-50/80 transition group">
                                    <td class="px-6 py-4 text-sm text-gray-500">#<?= $u['id'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <img src="../<?= htmlspecialchars($u['avatar']) ?>" 
                                                     alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200 group-hover:border-orange-200 transition">
                                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 group-hover:text-orange-600 transition"><?= htmlspecialchars($u['fullname']) ?></p>
                                                <p class="text-xs text-gray-500">@<?= htmlspecialchars($u['username']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['email']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <?= $u['post_count'] ?> tin đăng
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($u['role'] == 1): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                            <i class="fas fa-shield-alt mr-1.5"></i>Admin
                                        </span>
                                        <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            User
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick='openEditModal(<?= json_encode($u) ?>)' 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Sửa thông tin">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button onclick="openChangePasswordModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['fullname']) ?>')" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition" title="Đổi mật khẩu">
                                                <i class="fas fa-key text-xs"></i>
                                            </button>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="actions/delete-user.php?id=<?= $u['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Xóa người dùng"
                                               onclick="return confirm('Xóa người dùng này?')">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </a>
                                            <?php endif; ?>
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
    <div id="userModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-100">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-900">Thêm người dùng</h2>
                    <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="userForm" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="userId">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Họ và tên <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="fullname" id="fullname" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="Nhập họ tên đầy đủ">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Tên đăng nhập <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" id="username" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="Nhập tên đăng nhập">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="example@email.com">
                    </div>

                    <div id="passwordField">
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Mật khẩu <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="Nhập mật khẩu">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Vai trò
                        </label>
                        <select name="role" id="role"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition cursor-pointer">
                            <option value="0">User (Người dùng thường)</option>
                            <option value="1">Admin (Quản trị viên)</option>
                        </select>
                    </div>

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

    <!-- Change Password Modal -->
    <div id="passwordModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-100">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Đổi mật khẩu</h2>
                    <button onclick="closePasswordModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="bg-blue-50 text-blue-800 px-4 py-3 rounded-xl mb-6 text-sm flex items-center">
                    <i class="fas fa-info-circle mr-2 text-lg"></i>
                    <span>Đổi mật khẩu cho: <span id="pwdUserFullname" class="font-bold"></span></span>
                </div>

                <form action="actions/reset-password.php" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="pwdUserId">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Mật khẩu mới <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition"
                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-xl hover:bg-gray-200 transition font-medium">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="flex-1 bg-green-600 text-white py-2.5 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-500/30 font-medium">
                            <i class="fas fa-key mr-2"></i>Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm người dùng mới';
            document.getElementById('userForm').action = 'actions/create-user.php';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function openEditModal(user) {
            document.getElementById('modalTitle').textContent = 'Cập nhật thông tin';
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
