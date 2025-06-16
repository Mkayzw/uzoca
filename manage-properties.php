<?php
require_once 'includes/init.php';
require_once 'lib/src/Properties.php';

use app\src\Properties;

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$properties = new Properties();

// Handle property deletion
if (isset($_POST['delete_property'])) {
    try {
        $propertyId = (int)$_POST['property_id'];
        if ($properties->deleteProperty($propertyId, $userId, $userRole)) {
            $_SESSION['success'] = 'Property deleted successfully';
        }
    } catch (\Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle property updates
if (isset($_POST['update_property'])) {
    try {
        $propertyId = (int)$_POST['property_id'];
        $updateData = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'location' => $_POST['location'],
            'bedrooms' => $_POST['bedrooms'],
            'bathrooms' => $_POST['bathrooms'],
            'area' => $_POST['area'],
            'features' => $_POST['features'],
            'status' => $_POST['status'],
            'category' => $_POST['category']
        ];
        
        if ($properties->updateProperty($propertyId, $updateData, $userId, $userRole)) {
            $_SESSION['success'] = 'Property updated successfully';
        }
    } catch (\Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Get user's properties
$userProperties = $properties->getUserProperties($userId, $userRole);

$pageTitle = "Manage Properties";
include 'includes/header.php';
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manage Properties</h1>
            <a href="add-property.php" class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                Add New Property
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($userProperties as $property): ?>
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden">
                    <img src="<?= htmlspecialchars($property['image_url']) ?>" 
                         alt="<?= htmlspecialchars($property['title']) ?>" 
                         class="w-full h-48 object-cover">
                    
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">
                            <?= htmlspecialchars($property['title']) ?>
                        </h3>
                        
                        <div class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                            <p class="mb-1">
                                <i class="fr fi-rr-marker mr-2"></i>
                                <?= htmlspecialchars($property['location']) ?>
                            </p>
                            <p class="mb-1">
                                <i class="fr fi-rr-dollar mr-2"></i>
                                $<?= number_format($property['price'], 2) ?>
                            </p>
                            <p class="mb-1">
                                <i class="fr fi-rr-home mr-2"></i>
                                <?= $property['bedrooms'] ?> beds, <?= $property['bathrooms'] ?> baths
                            </p>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="px-2 py-1 text-sm rounded-full <?= $property['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($property['status']) ?>
                            </span>
                            
                            <div class="flex gap-2">
                                <button onclick="editProperty(<?= htmlspecialchars(json_encode($property)) ?>)" 
                                        class="p-2 text-sky-500 hover:text-sky-600">
                                    <i class="fr fi-rr-edit"></i>
                                </button>
                                
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                    <button type="submit" name="delete_property" class="p-2 text-red-500 hover:text-red-600">
                                        <i class="fr fi-rr-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- Edit Property Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-2xl w-full mx-4">
        <h2 class="text-xl font-bold mb-4">Edit Property</h2>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="property_id" id="edit_property_id">
            
            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" name="title" id="edit_title" required
                       class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" id="edit_description" required rows="3"
                          class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Price</label>
                    <input type="number" name="price" id="edit_price" required step="0.01"
                           class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Location</label>
                    <input type="text" name="location" id="edit_location" required
                           class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Bedrooms</label>
                    <input type="number" name="bedrooms" id="edit_bedrooms" required
                           class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Bathrooms</label>
                    <input type="number" name="bathrooms" id="edit_bathrooms" required
                           class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Area (sq ft)</label>
                    <input type="number" name="area" id="edit_area" required
                           class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Features</label>
                <textarea name="features" id="edit_features" rows="2"
                          class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" id="edit_status" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                        <option value="available">Available</option>
                        <option value="rented">Rented</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category" id="edit_category" required
                            class="w-full px-3 py-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">
                        <option value="for_rent">For Rent</option>
                        <option value="for_sale">For Sale</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200">
                    Cancel
                </button>
                <button type="submit" name="update_property"
                        class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editProperty(property) {
    document.getElementById('edit_property_id').value = property.id;
    document.getElementById('edit_title').value = property.title;
    document.getElementById('edit_description').value = property.description;
    document.getElementById('edit_price').value = property.price;
    document.getElementById('edit_location').value = property.location;
    document.getElementById('edit_bedrooms').value = property.bedrooms;
    document.getElementById('edit_bathrooms').value = property.bathrooms;
    document.getElementById('edit_area').value = property.area;
    document.getElementById('edit_features').value = property.features;
    document.getElementById('edit_status').value = property.status;
    document.getElementById('edit_category').value = property.category;
    
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php include 'includes/footer.php'; ?> 