<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/src/ViewProperties.php';

use app\config\Database;
use app\src\ViewProperties;

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Initialize ViewProperties
$viewProperties = new ViewProperties($conn);

$pageTitle = "UZOCA | Property List";
include 'includes/Header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Properties List</h2>
                <a href="add-property.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fr fi-rr-plus mr-2"></i>
        Add New Property
    </a>
</div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        <?php
                        $query = "SELECT p.*, u.name as owner_name, u.email as owner_email,
                                 (SELECT COUNT(*) FROM bookings WHERE property_id = p.id) as booking_count
                                 FROM properties p
                                 LEFT JOIN users u ON p.owner_id = u.id
                                 ORDER BY p.created_at DESC";
                        
                        $result = $conn->query($query);
                        
                        if ($result && $result->rowCount() > 0) {
                            while ($property = $result->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="<?php echo !empty($property['image']) ? '../uploads/' . $property['image'] : '../assets/img/default-property.jpg'; ?>" 
                                                     alt="<?php echo htmlspecialchars($property['title']); ?>">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                                    <?php echo htmlspecialchars($property['title']); ?>
                                                </div>
                                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                                    ID: <?php echo $property['id']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 dark:text-slate-100"><?php echo htmlspecialchars($property['owner_name']); ?></div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($property['owner_email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 dark:text-slate-100">₱<?php echo number_format($property['price'], 2); ?></div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400"><?php echo $property['type']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 dark:text-slate-100"><?php echo htmlspecialchars($property['location']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $property['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($property['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                        <?php echo $property['booking_count']; ?> bookings
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="view-property.php?id=<?php echo $property['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            View
                                        </a>
                                        <a href="edit-property.php?id=<?php echo $property['id']; ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                            Edit
                                        </a>
                                        <button onclick="deleteProperty(<?php echo $property['id']; ?>)" 
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                                    No properties found
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Property Modal -->
<div class="modal fade" id="viewPropertyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-lg">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Property Details</h5>
                <button type="button" class="close text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="p-4" id="propertyDetails">
                <!-- Property details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Property Modal -->
<div class="modal fade" id="editPropertyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-lg">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Edit Property</h5>
                <button type="button" class="close text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="p-4">
                <form id="editPropertyForm">
                    <input type="hidden" name="property_id" id="edit_property_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                            <input type="text" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="title" id="edit_title" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                            <textarea class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Price</label>
                                <input type="number" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="price" id="edit_price" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Location</label>
                                <input type="text" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="location" id="edit_location" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Bedrooms</label>
                                <input type="number" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="bedrooms" id="edit_bedrooms" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Bathrooms</label>
                                <input type="number" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="bathrooms" id="edit_bathrooms" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Area (sq ft)</label>
                                <input type="number" class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="area" id="edit_area" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Features</label>
                            <textarea class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="features" id="edit_features" rows="2"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                <select class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="status" id="edit_status" required>
                                    <option value="available">Available</option>
                                    <option value="rented">Rented</option>
            </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
                                <select class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" name="category" id="edit_category" required>
                                    <option value="house">House</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="studio">Studio</option>
            </select>
                            </div>
                        </div>
                    </div>
    </form>
            </div>
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" data-dismiss="modal">Close</button>
                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-sky-500 hover:bg-sky-600 rounded-lg" onclick="updateProperty()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewProperty(propertyId) {
    $.get('../lib/src/ajax/get_property.php', { id: propertyId }, function(response) {
        if (response.success) {
            const property = response.data;
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        ${property.image ? 
                            `<img src="../uploads/properties/${property.image}" class="img-fluid mb-3">` : 
                            '<div class="alert alert-info">No image available</div>'
                        }
                    </div>
                    <div class="col-md-6">
                        <h4>${property.title}</h4>
                        <p class="text-muted">${property.category}</p>
                        <hr>
                        <p><strong>Price:</strong> $${property.price}</p>
                        <p><strong>Location:</strong> ${property.location}</p>
                        <p><strong>Bedrooms:</strong> ${property.bedrooms}</p>
                        <p><strong>Bathrooms:</strong> ${property.bathrooms}</p>
                        <p><strong>Area:</strong> ${property.area} sq ft</p>
                        <p><strong>Status:</strong> ${property.status}</p>
                        <p><strong>Owner:</strong> ${property.owner_name}</p>
                        <p><strong>Owner Email:</strong> ${property.owner_email}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p>${property.description}</p>
                        <h5>Features</h5>
                        <p>${property.features || 'No features listed'}</p>
                    </div>
                </div>
            `;
            $('#propertyDetails').html(html);
            $('#viewPropertyModal').modal('show');
        } else {
            alert('Error loading property details');
        }
    });
}

function editProperty(propertyId) {
    $.get('../lib/src/ajax/get_property.php', { id: propertyId }, function(response) {
        if (response.success) {
            const property = response.data;
            $('#edit_property_id').val(property.id);
            $('#edit_title').val(property.title);
            $('#edit_description').val(property.description);
            $('#edit_price').val(property.price);
            $('#edit_location').val(property.location);
            $('#edit_bedrooms').val(property.bedrooms);
            $('#edit_bathrooms').val(property.bathrooms);
            $('#edit_area').val(property.area);
            $('#edit_features').val(property.features);
            $('#edit_status').val(property.status);
            $('#edit_category').val(property.category);
            $('#editPropertyModal').modal('show');
        } else {
            alert('Error loading property details');
        }
    });
}

function updateProperty() {
    const formData = new FormData($('#editPropertyForm')[0]);
    
    $.ajax({
        url: '../lib/src/ajax/update_property.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                alert('Property updated successfully');
                location.reload();
            } else {
                alert(response.message || 'Error updating property');
            }
        }
    });
}

function deleteProperty(propertyId) {
    if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
        window.location.href = `delete-property.php?id=${propertyId}`;
    }
}
</script>

<?php include 'includes/footer.php'; ?>