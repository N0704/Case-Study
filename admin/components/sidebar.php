<?php
// Simple admin check - in production, use proper session management
$is_admin = true; // TODO: Replace with actual session check

if (!$is_admin) {
    header('Location: ../index.php');
    exit;
}
?>
<aside class="w-64 bg-white min-h-screen fixed left-0 top-0 border-r border-gray-200 z-20 flex flex-col transition-all duration-300">
    <!-- Brand -->
    <div class="h-16 flex items-center px-6 border-b border-gray-100">
        <a href="../index.php" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-sm">
                <i class="fas fa-building"></i>
            </div>
            <span class="text-xl font-bold text-gray-800 tracking-tight">AdminPortal</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Menu chính</p>
        
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' ?>">
            <i class="fas fa-chart-line w-5 text-center <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
            <span>Dashboard</span>
        </a>

        <a href="districts.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= basename($_SERVER['PHP_SELF']) == 'districts.php' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' ?>">
            <i class="fas fa-map-marked-alt w-5 text-center <?= basename($_SERVER['PHP_SELF']) == 'districts.php' ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
            <span>Quản lý khu vực</span>
        </a>

        <a href="posts.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= basename($_SERVER['PHP_SELF']) == 'posts.php' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' ?>">
            <i class="fas fa-home w-5 text-center <?= basename($_SERVER['PHP_SELF']) == 'posts.php' ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
            <span>Quản lý tin đăng</span>
        </a>

        <a href="users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' ?>">
            <i class="fas fa-users w-5 text-center <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
            <span>Quản lý người dùng</span>
        </a>

        <a href="statistics.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?= basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' ?>">
            <i class="fas fa-chart-bar w-5 text-center <?= basename($_SERVER['PHP_SELF']) == 'statistics.php' ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-600' ?>"></i>
            <span>Thống kê báo cáo</span>
        </a>

        <div class="border-t border-gray-100 my-6 mx-2"></div>
        
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Hệ thống</p>

        <a href="../index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group font-medium">
            <i class="fas fa-globe w-5 text-center text-gray-400 group-hover:text-gray-600"></i>
            <span>Xem trang chủ</span>
        </a>

        <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 group font-medium">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span>Đăng xuất</span>
        </a>
    </nav>
    
    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-gray-100 bg-gray-50">
        <p class="text-xs text-gray-400 text-center">© 2025 Admin Panel v1.0</p>
    </div>
</aside>
