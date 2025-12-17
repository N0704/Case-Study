<header class="bg-white/80 backdrop-blur-md shadow-sm h-16 flex items-center justify-between px-8 fixed top-0 right-0 left-64 z-20 transition-all duration-300">
    <!-- Left: Page Title -->
    <div class="flex items-center">
        <button class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden mr-4">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">
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

    <!-- Right: Actions & Profile -->
    <div class="flex items-center gap-6">
        <!-- Search (Optional placeholder) -->
        <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-1.5 focus-within:ring-2 focus-within:ring-orange-100 transition">
            <i class="fas fa-search text-gray-400 text-sm"></i>
            <input type="text" placeholder="Tìm kiếm nhanh..." class="bg-transparent border-none text-sm text-gray-700 placeholder-gray-400 focus:outline-none ml-2 w-48">
        </div>

        <!-- Notifications -->
        <button class="relative p-2 text-gray-400 hover:text-orange-500 transition-colors">
            <i class="far fa-bell text-xl"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        </button>

        <!-- User Profile -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700 group-hover:text-orange-600 transition"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin') ?></p>
                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Administrator</p>
                </div>
                <img src="../<?= htmlspecialchars($_SESSION['avatar'] ?? 'assets/images/default-avatar.png') ?>" 
                     alt="Admin" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:border-orange-200 transition">
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-2 border border-gray-100 z-50"
                 style="display: none;">
                
                <div class="px-4 py-2 border-b border-gray-50 md:hidden">
                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin') ?></p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>

                <a href="../index.php" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                    <i class="fas fa-globe w-6 text-center text-gray-400 group-hover:text-orange-500"></i> Xem trang chủ
                </a>
                <a href="../profile.php" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition">
                    <i class="far fa-user w-6 text-center text-gray-400 group-hover:text-orange-500"></i> Hồ sơ cá nhân
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="../logout.php" class="flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                    <i class="fas fa-sign-out-alt w-6 text-center"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</header>
