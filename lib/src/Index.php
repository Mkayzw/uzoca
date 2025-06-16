<?php

namespace app\src;

use app\assets\DB;

class Index
{
    private $con;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    /**
     * Get recent houses in desc order for the index page
     */
    public function showIndexHouses()
    {
        $houses = $this->con->select(
            "id, title, index_img as image, price, location, type",
            "properties",
            "WHERE status = 'available' ORDER BY id DESC LIMIT 6"
        );

        if (!$houses || $houses->num_rows === 0) {
            echo '<p class="text-center text-gray-500">No properties available at the moment.</p>';
            return;
        }

        while ($house = $houses->fetch_assoc()) {
            $imagePath = !empty($house['image']) ? $house['image'] : 'default-property.jpg';
            ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-slate-800">
                <div class="relative">
                    <img src="assets/img/<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($house['title']) ?>" class="w-full h-48 object-cover">
                    <span class="absolute top-2 right-2 bg-sky-500 text-white px-2 py-1 rounded text-sm">
                        <?= htmlspecialchars($house['type']) ?>
                    </span>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2 dark:text-white">
                        <?= htmlspecialchars($house['title']) ?>
                    </h3>
                    <p class="text-gray-600 mb-2 dark:text-gray-300">
                        <?= htmlspecialchars($house['location']) ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sky-500 font-semibold">
                            ₦<?= number_format($house['price']) ?>
                        </span>
                        <a href="details?propertyID=<?= $house['id'] ?>" class="text-sky-500 hover:text-sky-600">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}
