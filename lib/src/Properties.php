<?php

namespace app\src;

use app\assets\DB;

class Properties
{
    private $con;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    public function getAllProperties()
    {
        // Get all available properties with agent information
        $houses = $this->con->select(
            "p.id, p.title, p.image, p.price, p.description as summary, p.location, p.type, p.status, 
             p.bedrooms, p.bathrooms, p.area, p.features,
             a.name as agent_name, a.email as agent_email, a.phone as agent_phone",
            "properties p 
             LEFT JOIN agents a ON p.agent_id = a.id",
            "WHERE p.status = 'available' ORDER BY p.id DESC"
        );

        if ($houses->num_rows < 1) : ?>
            <p class="text-rose-700 dark:text-rose-500 text-center lg:col-span-12 text-xl lg:text-2xl">
                No properties available at the moment. Please check back later.
            </p>
        <?php
            return;
        endif;

        while ($house = $houses->fetch_object()) : ?>
            <article class="lg:col-span-4 space-y-3 sm:col-span-6">
                <div class="relative">
                    <?php if (!empty($house->image)): ?>
                        <img class="property-listing-image" src="./assets/img/<?= $house->image ?>" alt="<?= $house->title ?>" title="<?= $house->title ?>" width="100%" height="200">
                    <?php else: ?>
                        <div class="property-listing-image bg-slate-200 flex items-center justify-center h-[200px]">
                            <i class="fr fi-rr-home text-4xl text-slate-400"></i>
                        </div>
                    <?php endif; ?>

                    <div class="absolute top-2.5 right-4 flex gap-2">
                        <button class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700" 
                                onclick="toggleFavorite(<?= $house->id ?>)">
                            <i class="fr fi-rr-heart text-xl text-rose-500 dark:text-white"></i>
                        </button>
                    </div>
                </div>

                <div class="px-2 space-y-3">
                    <div class="flex items-center flex-wrap gap-x-4 gap-y-1.5 justify-between">
                        <span class=<?= $house->type === 'rent' ? "text-green-500 dark:text-green-400" : "text-rose-500 dark:text-rose-400" ?>>
                            <i class="fr <?= $house->type === 'rent' ? 'fi-rr-recycle' : 'fi-rr-thumbtack' ?>"></i>
                            <?= ucfirst($house->type) ?>
                        </span>

                        <span class="text-sky-500 lining-nums font-semibold tracking-widest">
                            ₦ <?= number_format($house->price) ?>
                        </span>
                    </div>

                    <div>
                        <h2 class="header">
                            <?= $house->title ?>
                        </h2>

                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                            <?= $house->summary ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-bed"></i>
                            <?= $house->bedrooms ?> beds
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-bath"></i>
                            <?= $house->bathrooms ?> baths
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-ruler-combined"></i>
                            <?= $house->area ?> sq ft
                        </span>
                    </div>

                    <address class="text-sm text-slate-600 dark:text-slate-400">
                        <i class="fr fi-rr-map-marker-home"></i>
                        <?= $house->location ?>
                    </address>

                    <div class="flex items-center gap-2">
                        <a class="flex-1 inline-block text-center rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" 
                           href="property-details.php?id=<?= $house->id ?>">
                            View Details
                        </a>
                        <a class="flex-1 inline-block text-center rounded-lg py-1.5 px-3 text-sky-500 bg-sky-50 hover:bg-sky-100 dark:bg-slate-800 dark:hover:bg-slate-700" 
                           href="book-property.php?id=<?= $house->id ?>">
                            Book Now
                        </a>
                    </div>
                </div>
            </article>
<?php
        endwhile;
    }

    public function getFilteredProperties($search = '', $category = '', $minPrice = '', $maxPrice = '')
    {
        $conditions = ["p.status = 'available'"];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(p.title LIKE ? OR p.location LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($category)) {
            $conditions[] = "p.type = ?";
            $params[] = $category;
        }

        if (!empty($minPrice)) {
            $conditions[] = "p.price >= ?";
            $params[] = $minPrice;
        }

        if (!empty($maxPrice)) {
            $conditions[] = "p.price <= ?";
            $params[] = $maxPrice;
        }

        $whereClause = implode(" AND ", $conditions);
        $houses = $this->con->select(
            "p.id, p.title, p.image, p.price, p.description as summary, p.location, p.type, p.status,
             p.bedrooms, p.bathrooms, p.area, p.features,
             a.name as agent_name, a.email as agent_email, a.phone as agent_phone",
            "properties p 
             LEFT JOIN agents a ON p.agent_id = a.id",
            "WHERE {$whereClause} ORDER BY p.id DESC",
            ...$params
        );

        if ($houses->num_rows < 1) : ?>
            <p class="text-rose-700 dark:text-rose-500 text-center lg:col-span-12 text-xl lg:text-2xl">
                No properties found matching your criteria. Try adjusting your filters.
            </p>
        <?php
            return;
        endif;

        while ($house = $houses->fetch_object()) : ?>
            <article class="lg:col-span-4 space-y-3 sm:col-span-6">
                <div class="relative">
                    <?php if (!empty($house->image)): ?>
                        <img class="property-listing-image" src="./assets/img/<?= $house->image ?>" alt="<?= $house->title ?>" title="<?= $house->title ?>" width="100%" height="200">
                    <?php else: ?>
                        <div class="property-listing-image bg-slate-200 flex items-center justify-center h-[200px]">
                            <i class="fr fi-rr-home text-4xl text-slate-400"></i>
                        </div>
                    <?php endif; ?>

                    <div class="absolute top-2.5 right-4 flex gap-2">
                        <button class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700" 
                                onclick="toggleFavorite(<?= $house->id ?>)">
                            <i class="fr fi-rr-heart text-xl text-rose-500 dark:text-white"></i>
                        </button>
                    </div>
                </div>

                <div class="px-2 space-y-3">
                    <div class="flex items-center flex-wrap gap-x-4 gap-y-1.5 justify-between">
                        <span class=<?= $house->type === 'rent' ? "text-green-500 dark:text-green-400" : "text-rose-500 dark:text-rose-400" ?>>
                            <i class="fr <?= $house->type === 'rent' ? 'fi-rr-recycle' : 'fi-rr-thumbtack' ?>"></i>
                            <?= ucfirst($house->type) ?>
                        </span>

                        <span class="text-sky-500 lining-nums font-semibold tracking-widest">
                            ₦ <?= number_format($house->price) ?>
                        </span>
                    </div>

                    <div>
                        <h2 class="header">
                            <?= $house->title ?>
                        </h2>

                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                            <?= $house->summary ?>
                        </p>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-bed"></i>
                            <?= $house->bedrooms ?> beds
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-bath"></i>
                            <?= $house->bathrooms ?> baths
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fr fi-rr-ruler-combined"></i>
                            <?= $house->area ?> sq ft
                        </span>
                    </div>

                    <address class="text-sm text-slate-600 dark:text-slate-400">
                        <i class="fr fi-rr-map-marker-home"></i>
                        <?= $house->location ?>
                    </address>

                    <div class="flex items-center gap-2">
                        <a class="flex-1 inline-block text-center rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" 
                           href="property-details.php?id=<?= $house->id ?>">
                        View Details
                    </a>
                        <a class="flex-1 inline-block text-center rounded-lg py-1.5 px-3 text-sky-500 bg-sky-50 hover:bg-sky-100 dark:bg-slate-800 dark:hover:bg-slate-700" 
                           href="book-property.php?id=<?= $house->id ?>">
                            Book Now
                        </a>
                    </div>
                </div>
            </article>
<?php
        endwhile;
    }

    /**
     * Delete a property listing
     * @param int $propertyId Property ID
     * @param int $userId User ID (agent/landlord/admin)
     * @param string $userRole User role
     * @return bool Success status
     */
    public function deleteProperty($propertyId, $userId, $userRole) {
        try {
            // Verify user has permission to delete this property
            if (!$this->canManageProperty($propertyId, $userId, $userRole)) {
                throw new \Exception('You do not have permission to delete this property');
            }

            $query = "DELETE FROM properties WHERE id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("i", $propertyId);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error deleting property: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a property listing
     * @param int $propertyId Property ID
     * @param array $data Property data to update
     * @param int $userId User ID (agent/landlord/admin)
     * @param string $userRole User role
     * @return bool Success status
     */
    public function updateProperty($propertyId, $data, $userId, $userRole) {
        try {
            // Verify user has permission to update this property
            if (!$this->canManageProperty($propertyId, $userId, $userRole)) {
                throw new \Exception('You do not have permission to update this property');
            }

            // Build update query based on provided data
            $updates = [];
            $types = "";
            $values = [];

            $allowedFields = [
                'title', 'description', 'price', 'location', 'bedrooms', 
                'bathrooms', 'area', 'features', 'status', 'category'
            ];

            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $updates[] = "$field = ?";
                    $types .= "s";
                    $values[] = $value;
                }
            }

            if (empty($updates)) {
                throw new \Exception('No valid fields to update');
            }

            // Add property ID to values array
            $types .= "i";
            $values[] = $propertyId;

            $query = "UPDATE properties SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param($types, ...$values);
            
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error updating property: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if user has permission to manage a property
     * @param int $propertyId Property ID
     * @param int $userId User ID
     * @param string $userRole User role
     * @return bool True if user can manage the property
     */
    private function canManageProperty($propertyId, $userId, $userRole) {
        // Admin can manage all properties
        if ($userRole === 'admin') {
            return true;
        }

        // Get property details
        $query = "SELECT agent_id, landlord_id FROM properties WHERE id = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        $property = $result->fetch_assoc();

        if (!$property) {
            return false;
        }

        // Agent can manage their own properties
        if ($userRole === 'agent' && $property['agent_id'] == $userId) {
            return true;
        }

        // Landlord can manage their own properties
        if ($userRole === 'landlord' && $property['landlord_id'] == $userId) {
            return true;
        }

        return false;
    }

    /**
     * Get properties for a specific user (agent/landlord)
     * @param int $userId User ID
     * @param string $userRole User role
     * @return array Properties list
     */
    public function getUserProperties($userId, $userRole) {
        $query = "SELECT p.*, 
                        COALESCE(a.name, l.name) as owner_name,
                        COALESCE(a.email, l.email) as owner_email
                 FROM properties p
                 LEFT JOIN users a ON p.agent_id = a.id
                 LEFT JOIN users l ON p.landlord_id = l.id
                 WHERE ";

        if ($userRole === 'agent') {
            $query .= "p.agent_id = ?";
        } elseif ($userRole === 'landlord') {
            $query .= "p.landlord_id = ?";
        } else {
            throw new \Exception('Invalid user role');
        }

        $query .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        
        return $properties;
    }

    /**
     * Get all properties for admin view
     * @return array Properties list
     */
    public function getAllPropertiesForAdmin() {
        $query = "SELECT p.*, 
                        COALESCE(a.name, l.name) as owner_name,
                        COALESCE(a.email, l.email) as owner_email,
                        COUNT(b.id) as booking_count
                 FROM properties p
                 LEFT JOIN users a ON p.agent_id = a.id
                 LEFT JOIN users l ON p.landlord_id = l.id
                 LEFT JOIN bookings b ON p.id = b.property_id
                 GROUP BY p.id
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        
        return $properties;
    }

    /**
     * Get property details by ID
     * @param int $propertyId Property ID
     * @return array|false Property details or false if not found
     */
    public function getPropertyById($propertyId) {
        $query = "SELECT p.*, 
                        COALESCE(a.name, l.name) as owner_name,
                        COALESCE(a.email, l.email) as owner_email
                 FROM properties p
                 LEFT JOIN users a ON p.agent_id = a.id
                 LEFT JOIN users l ON p.landlord_id = l.id
                 WHERE p.id = ?";
        
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get property bookings
     * @param int $propertyId Property ID
     * @return array Bookings list
     */
    public function getPropertyBookings($propertyId) {
        $query = "SELECT b.*, u.name as tenant_name, u.email as tenant_email
                 FROM bookings b
                 JOIN users u ON b.user_id = u.id
                 WHERE b.property_id = ?
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        
        return $bookings;
    }
}
