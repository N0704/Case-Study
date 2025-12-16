<header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 bg-transparent">
    <div class="px-7">
        <div class="flex justify-between items-center py-3">

            <div class="flex items-center space-x-4 shrink-0">
                <a href="index.html" class="bg-white rounded-full hover:bg-gray-100">
                    <div id="logo-container"
                        class="flex items-center justify-center h-10 px-1.5">
                        <img src="assets/images/logo.png" alt="" class="h-10 mt-0.5">
                    </div>
                </a>

                <div id="sticky-region"
                    class="hidden opacity-0 transition-all duration-300 items-center bg-gray-100 rounded-full px-4 h-10 cursor-pointer hover:bg-gray-200 whitespace-nowrap">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                    <span class="text-sm font-bold text-gray-700 mr-2">Chọn khu vực</span>
                    <i class="fas fa-caret-down text-gray-400"></i>
                </div>

                <a id="broker-link" href="#"
                    class="flex items-center justify-center rounded-xl text-white/90 text-sm font-medium h-10 px-3 hover:bg-white/20">
                    Kênh môi giới
                </a>
            </div>
            
            <div class="flex-1 flex items-center justify-center px-4 min-w-0">
                <!-- Nav -->
                <div id="nav-links"
                    class="hidden xl:flex items-center space-x-8 text-sm font-medium text-white/80 transition-opacity duration-300 whitespace-nowrap">
                    <a href="#" class="font-bold text-white">Trang chủ</a>

                    <a href="#" class=" hover:text-white">
                        Thuê phòng trọ
                    </a>

                    <a href="#" class="hover:text-white">Căn hộ mini</a>

                    <a href="#" class="hover:text-white">Nhà nguyên căn</a>

                    <a href="#" class="hover:text-white">Tin mới</a>
                </div>

                <!-- Sticky search -->
                <form action="search.php" method="GET" id="sticky-search" class="hidden w-full max-w-2xl opacity-0 transition-all duration-300">
                    <div class="flex w-full bg-gray-100 rounded-full pr-1.5 pl-4 h-10 items-center">
                        <i data-lucide="search" class="w-4.5 h-4.5 text-gray-400 mr-2.5"></i>
                        <input type="text" name="keyword"
                            class="flex-1 bg-transparent outline-none text-sm text-gray-700"
                            placeholder="Tìm phòng trọ...">
                        <button type="submit"
                            class="bg-[#FF6600] text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-orange-600 transition ml-2 shrink-0">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right -->
            <div class="flex items-center space-x-3 shrink-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged in -->
                    <a id="manage-link" href="my-posts.php"
                        class="bg-white text-black px-4 h-10 flex items-center rounded-full text-sm font-medium hover:bg-gray-100">
                        Quản lý tin
                    </a>

                    <a id="post-link" href="post.php"
                        class="bg-gray-900 text-white px-4 h-10 flex items-center rounded-full text-sm font-bold hover:bg-gray-800 ">
                        Đăng tin
                    </a>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" id="user-link"
                            class="bg-white text-black px-1.5 h-10 flex items-center justify-center rounded-full gap-2 text-sm hover:bg-gray-100">
                            <img src="<?= $_SESSION['avatar'] ?? 'assets/images/default-avatar.png' ?>" alt="" class="w-8 h-8 rounded-full object-cover">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($_SESSION['fullname']) ?></p>
                                <p class="text-sm text-gray-500">@<?= htmlspecialchars($_SESSION['username']) ?></p>
                            </div>
                            <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-3 w-4"></i>Trang cá nhân
                            </a>
                            <a href="my-posts.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-list mr-3 w-4"></i>Tin đã đăng
                            </a>
                            <?php if ($_SESSION['role'] == 1): ?>
                            <a href="admin/index.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog mr-3 w-4"></i>Quản trị
                            </a>
                            <?php endif; ?>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-3 w-4"></i>Đăng xuất
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Not logged in -->
                    <a href="login.php"
                        class="bg-white text-black px-4 h-10 flex items-center rounded-full text-sm font-medium hover:bg-gray-100">
                        Đăng nhập
                    </a>

                    <a href="register.php"
                        class="bg-orange-500 text-white px-4 h-10 flex items-center rounded-full text-sm font-bold hover:bg-orange-600">
                        Đăng ký
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

<script>
window.addEventListener('scroll', () => {
    const header = document.getElementById('main-header');
    const brokerLink = document.getElementById('broker-link');
    const navLinks = document.getElementById('nav-links');
    const stickyRegion = document.getElementById('sticky-region');
    const stickySearch = document.getElementById('sticky-search');
    const manageLink = document.getElementById('manage-link');
    const userLink = document.getElementById('user-link');

    const isScrolled = window.scrollY > 50;

    /* HEADER BG */
    header.classList.toggle('bg-white', isScrolled);
    header.classList.toggle('shadow-md', isScrolled);
    header.classList.toggle('bg-transparent', !isScrolled);

    /* NAV LINKS */
    if (isScrolled) {
        navLinks.classList.add('hidden');
        brokerLink.classList.add('hidden');
        navLinks.classList.remove('xl:flex');
        brokerLink.classList.remove('flex');
        manageLink.classList.add('border', 'border-gray-200');
        userLink.classList.add('border', 'border-gray-200');
    } else {
        navLinks.classList.remove('hidden');
        brokerLink.classList.remove('hidden');
        navLinks.classList.add('xl:flex');
        brokerLink.classList.add('flex');
        manageLink.classList.remove('border', 'border-gray-200');
        userLink.classList.remove('border', 'border-gray-200');
    }

    /* STICKY REGION */
    stickyRegion.classList.toggle('hidden', !isScrolled);
    stickyRegion.classList.toggle('flex', isScrolled);
    stickyRegion.classList.toggle('opacity-0', !isScrolled);
    stickyRegion.classList.toggle('opacity-100', isScrolled);

    /* STICKY SEARCH */
    stickySearch.classList.toggle('hidden', !isScrolled);
    stickySearch.classList.toggle('opacity-0', !isScrolled);
    stickySearch.classList.toggle('opacity-100', isScrolled);

});
</script>

