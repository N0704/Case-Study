<header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 fixed top-0 right-0 left-64 z-10">
    <!-- Left: Search or Breadcrumbs (Placeholder) -->
    <div class="flex items-center">
        <button class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden mr-4">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-xl font-semibold text-gray-800">
            <?php 
            $page_titles = [
                'index.php' => 'Dashboard',
                'districts.php' => 'Quản lý khu vực',
                'posts.php' => 'Quản lý tin đăng',
                'users.php' => 'Quản lý người dùng',
                'statistics.php' => 'Thống kê báo cáo'
            ];
            $current_page = basename($_SERVER['PHP_SELF']);
            echo $page_titles[$current_page] ?? 'Admin Panel';
            ?>
        </h2>
    </div>

    <!-- Right: User Menu -->
    <div class="flex items-center gap-4">
        <!-- Notifications (Demo) -->
        <button class="relative p-2 text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-bell text-xl"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
        </button>

        <!-- User Profile -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                <img src="../<?= htmlspecialchars($_SESSION['avatar'] ?? 'assets/images/default-avatar.png') ?>" 
                     alt="Admin" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                <div class="hidden md:block text-left">
                    <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin') ?></p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 ml-1"></i>
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false" 
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100 z-50"
                 style="display: none;">
                <a href="../index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-globe mr-2 text-gray-400"></i> Xem trang chủ
                </a>
                <a href="../profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-user mr-2 text-gray-400"></i> Hồ sơ cá nhân
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="../logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</header>
