<!-- Hero Section -->
<div class="bg-linear-to-r from-[#FF930F] to-[#FFF95B] pb-24 pt-20 mb-20 relative overflow-visible">
    <div class="px-19.5 text-center relative z-10">
        <h1 class="text-3xl font-bold text-white mb-6">Phòng trọ vừa ý, giá hợp lý!</h1>
        
        <!-- Tabs -->
        <div class="flex justify-center space-x-3 mb-6">
            <button class="text-white font-medium hover:bg-white/20 px-4 py-1.5 rounded-full transition">Nhà nguyên căn</button>
            <button class="bg-white text-orange-500 font-bold px-6 py-1.5 rounded-full shadow-sm">Phòng trọ</button>
            <button class="text-white font-medium hover:bg-white/20 px-4 py-1.5 rounded-full transition">Căn hộ mini</button>
        </div>

        <!-- Search Bar -->
        <form action="search.php" method="GET" class="bg-white rounded-xl p-2 shadow-lg max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-2">
            <div class="flex-1 w-full flex items-center px-4 py-2 md:py-0">
                <i class="fas fa-search text-gray-400 mr-3 text-lg"></i>
                <input type="text" name="keyword" class="w-full outline-none text-gray-700 placeholder-gray-400" placeholder="Tìm phòng trọ...">
            </div>
            <div class="w-full h-px bg-gray-200 md:w-px md:h-8"></div>
            
            <!-- District Dropdown -->
            <div class="relative flex items-center pl-3 py-2 text-gray-700 font-medium hover:bg-gray-50 rounded">
                <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                <select name="district" class="px-1 py-2 text-gray-700 font-medium bg-transparent appearance-none outline-none cursor-pointer">
                    <option value="">Chọn khu vực</option>
                    <?php
                    $districts = mysqli_query($conn, "SELECT * FROM districts ORDER BY name");
                    while ($d = mysqli_fetch_assoc($districts)) {
                        echo "<option value='{$d['id']}'>{$d['name']}</option>";
                    }
                    ?>
                </select>
                <i class="fas fa-caret-down text-gray-400 pointer-events-none mr-1.5"></i>
            </div>
            
            <div class="w-full h-px bg-gray-200 md:w-px md:h-8"></div>
            
            <!-- Category (Read-only) -->
            <div class="flex items-center px-4 py-2 text-gray-700 font-medium hover:bg-gray-50 rounded cursor-pointer">
                <i class="fas fa-home text-orange-500"></i>
                <span class="mx-2.5">
                    Phòng trọ
                </span>
                <input name="category" value="1" class="hidden">
                <i class="fas fa-caret-down text-gray-400"></i>
            </div>
            
            <button type="submit" class="w-full md:w-auto bg-[#FF6600] text-white font-semibold px-5 py-3 rounded-lg hover:bg-orange-600 transition shadow-md cursor-pointer">
                Tìm phòng
            </button>
        </form>
    </div>
</div>

<div class="px-19.5 -mt-32 mb-6 relative z-20">
    <div class="bg-white rounded-xl shadow-md flex items-center">
        <div class="w-full py-2 hover:bg-gray-50 rounded-xl transition cursor-pointer">
            <div class="flex items-center px-3 py-5 mx-2">
                <img src="assets/images/house.png" alt="Nhà nguyên căn" class="w-22 h-22 mr-4 object-cover">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Nhà nguyên căn</h3>
                    <p class="text-gray-500 text-sm">74.135 nhà</p>
                </div>
            </div>
        </div>
        <div class="w-px h-14 bg-gray-200"></div>
        <div class="w-full py-2 hover:bg-gray-50 rounded-xl transition cursor-pointer">
            <div class="flex items-center px-3 py-5 mx-2">
                <img src="assets/images/motel_room.png" alt="Phòng trọ" class="w-22 h-22 mr-4 object-cover">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Phòng trọ</h3>
                    <p class="text-gray-500 text-sm">47.388 phòng trọ</p>
                </div>
            </div>
        </div>
        <div class="w-px h-14 bg-gray-200"></div>
        <div class="w-full py-2 hover:bg-gray-50 rounded-xl transition cursor-pointer">
            <div class="flex items-center px-3 py-5 mx-2">
                <img src="assets/images/apartment.png" alt="Căn hộ mini" class="w-22 h-22 mr-4 object-cover">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Căn hộ mini</h3>
                    <p class="text-gray-500 text-sm">5.201 căn hộ</p>
                </div>
            </div>
        </div>
        <div class="w-px h-14 bg-gray-200"></div>
        <div class="w-full py-2 hover:bg-gray-50 rounded-xl transition cursor-pointer">
            <div class="flex items-center px-3 py-5 mx-2">
                <img src="assets/images/broker.png" alt="Môi giới" class="w-22 h-22 mr-4 object-cover">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">Môi giới</h3>
                    <p class="text-gray-500 text-sm">857 chuyên trang</p>
                </div>
            </div>
        </div>
    </div>
</div>