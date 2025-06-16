<?php
namespace app\src;

use app\config\Database;

class Index {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function showIndexHouses() {
        $query = "SELECT p.*, i.image_path 
                 FROM properties p 
                 LEFT JOIN property_images i ON p.id = i.property_id 
                 WHERE p.status = 'available' 
                 GROUP BY p.id 
                 ORDER BY p.created_at DESC 
                 LIMIT 6";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $properties = $stmt->fetchAll();
        
        if ($properties && count($properties) > 0) {
            foreach ($properties as $property) {
                $image = $property['image_path'] ?? '/uzoca/assets/img/default-property.jpg';
                ?>
                <div class="lg:col-span-4 sm:col-span-6">
                    <div class="rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                        <img class="w-full h-48 object-cover" src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($property['title']) ?>">
                        <div class="p-4 space-y-2">
                            <h3 class="header text-lg">
                                <?= htmlspecialchars($property['title']) ?>
                            </h3>
                            <p class="text-sky-500">
                                <?= htmlspecialchars($property['price']) ?>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <?= htmlspecialchars($property['location']) ?>
                            </p>
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="fr fi-rr-bed"></i>
                                    <?= htmlspecialchars($property['bedrooms']) ?> beds
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fr fi-rr-bath"></i>
                                    <?= htmlspecialchars($property['bathrooms']) ?> baths
                                </span>
                            </div>
                            <a href="/uzoca/property-details.php?id=<?= $property['id'] ?>" class="block text-center py-2 px-4 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-span-12 text-center text-gray-600 dark:text-gray-400">No properties available at the moment.</div>';
        }
    }

    public function render() {
        // Render logic here
        echo "Welcome to the Index page!";
    }
} 