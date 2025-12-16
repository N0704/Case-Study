<div class="px-19.5 mb-4">
    <div class="bg-white rounded-xl shadow-md flex flex-col">
        <h2 class="px-5 pt-5 pb-1 text-lg font-semibold">Tin được xem nhiều nhất</h2>
        <div class="grid grid-cols-4 gap-4 px-5 mt-2 mb-4">
    <!-- Card -->
    <?php
    $qr = mysqli_query($conn, "SELECT m.*, d.name as district_name 
                                FROM motels m 
                                LEFT JOIN districts d ON m.district_id = d.id 
                                WHERE m.approve = 1 
                                ORDER BY m.count_view DESC LIMIT 4");

    while ($r = mysqli_fetch_assoc($qr)) {
        $image = $r['image'] ?? 'assets/no-image.jpg';
        $created_at = date('d/m', strtotime($r['created_at']));
    ?>
        <div class="col-span-1 bg-white rounded-lg hover:shadow-md transition duration-200 overflow-hidden group h-full flex flex-col relative cursor-pointer">
            <a href="room-detail.php?id=<?= $r['id'] ?>" class="block relative aspect-4/3 overflow-hidden">
                <img src="<?= htmlspecialchars($image) ?>"
                    alt="<?= htmlspecialchars($r['title']) ?>"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                <div class="absolute bottom-2 left-2 bg-transparent px-2 py-0.5">
                    <span class="text-[11px] font-semibold text-white flex items-center">
                        <i class="far fa-clock mr-1"></i>
                        <?= $created_at ?>
                    </span>
                </div>
            </a>

            <div class="px-3 pt-3 flex flex-col flex-1">
                <a href="room-detail.php?id=<?= $r['id'] ?>" class="flex-1">
                    <h3 class="text-base font-medium text-gray-900 line-clamp-2 mb-1.5 leading-snug transition">
                        <?= htmlspecialchars($r['title']) ?>
                    </h3>
                </a>

                <div class="mt-auto">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[#D0021B] font-bold text-base">
                            <?= number_format($r['price']) ?> đ/tháng
                        </span>
                        <span class="text-gray-500 text-sm">
                            <?= $r['area'] ?> m²
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-1.5 w-3 text-center text-gray-400"></i>
                            <?= htmlspecialchars($r['district_name'] ?? '') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Like Button -->
            <button class="absolute top-2 right-3 text-gray-300 hover:text-red-500 transition bg-transparent cursor-pointer">
                <i class="far fa-heart"></i>
            </button>
        </div>
    <?php
    }
    ?>
    <!-- End Card -->
        </div>
    </div>
</div>