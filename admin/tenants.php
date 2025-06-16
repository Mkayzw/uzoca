<?php
$page_title = "UZOCA | Tenants";
require_once './includes/Header.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../lib/src/UserProfile.php';

use app\config\Database;
use app\src\UserProfile;

$database = new Database();
$conn = $database->getConnection();
$userProfile = new UserProfile($conn);
$query = "SELECT u.*, b.property_id, p.title as property_title, bp.status as payment_status
          FROM users u
          LEFT JOIN bookings b ON u.id = b.user_id
          LEFT JOIN properties p ON b.property_id = p.id
          LEFT JOIN booking_payments bp ON b.id = bp.booking_id
          WHERE u.role = 'tenant'
          ORDER BY u.created_at DESC";
$result = $conn->query($query);
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-4rem)]">
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-slate-900 rounded-lg shadow-sm h-full">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Tenants List</h3>
            </div>
            <div class="p-4 h-[calc(100%-4rem)] overflow-y-auto">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-slate-200 dark:border-slate-700">
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Tenant</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Contact</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Property</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Status</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Joined Date</th>
                                <th class="pb-3 font-semibold text-slate-900 dark:text-slate-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->rowCount() > 0): ?>
                                <?php while ($tenant = $result->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr class="border-b border-slate-200 dark:border-slate-700">
                                        <td class="py-4">
                                            <div class="flex items-center gap-3">
                                                <img src="<?php echo $tenant['profile_picture'] ?: '../assets/images/default-avatar.png'; ?>" 
                                                     alt="<?php echo $tenant['name']; ?>" 
                                                     class="w-10 h-10 rounded-full object-cover">
                                                <div>
                                                    <span class="font-medium text-slate-900 dark:text-slate-100"><?php echo $tenant['name']; ?></span>
                                                    <br>
                                                    <span class="text-sm text-slate-500 dark:text-slate-400"><?php echo $tenant['email']; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div>
                                                <span class="text-slate-900 dark:text-slate-100"><?php echo $tenant['phone']; ?></span>
                                                <br>
                                                <span class="text-sm text-slate-500 dark:text-slate-400"><?php echo $tenant['address']; ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <?php if ($tenant['property_title']): ?>
                                                <a href="property-details.php?id=<?php echo $tenant['property_id']; ?>" class="text-sky-500 hover:text-sky-600 dark:text-sky-400 dark:hover:text-sky-300">
                                                    <?php echo $tenant['property_title']; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-500 dark:text-slate-400">No property assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4">
                                            <?php if ($tenant['payment_status']): ?>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php 
                                                    echo $tenant['payment_status'] === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                        ($tenant['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                        'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200'); 
                                                ?>">
                                                    <?php echo ucfirst($tenant['payment_status']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-slate-500 dark:text-slate-400">No payment</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 text-slate-900 dark:text-slate-100"><?php echo date('M d, Y', strtotime($tenant['created_at'])); ?></td>
                                        <td class="py-4">
                                            <div class="flex gap-2">
                                                <button type="button" 
                                                        class="p-2 text-sky-500 hover:bg-sky-50 dark:hover:bg-slate-800 rounded-lg" 
                                                        onclick="viewTenant(<?php echo $tenant['id']; ?>)">
                                                    <i class="fr fi-rr-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="p-2 text-sky-500 hover:bg-sky-50 dark:hover:bg-slate-800 rounded-lg" 
                                                        onclick="editTenant(<?php echo $tenant['id']; ?>)">
                                                    <i class="fr fi-rr-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-slate-500 dark:text-slate-400">No tenants found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Tenant Modal -->
<div class="modal fade" id="viewTenantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-lg">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Tenant Details</h5>
                <button type="button" class="close text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="p-4" id="tenantDetails">
                <!-- Tenant details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Tenant Modal -->
<div class="modal fade" id="editTenantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-lg">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Edit Tenant</h5>
                <button type="button" class="close text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="p-4">
                <form id="editTenantForm">
                    <input type="hidden" id="editTenantId" name="id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
                        <input type="text" id="editTenantName" name="name" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" id="editTenantEmail" name="email" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phone</label>
                        <input type="tel" id="editTenantPhone" name="phone" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Address</label>
                        <textarea id="editTenantAddress" name="address" rows="3" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-slate-800 dark:text-slate-100"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-sky-500 hover:bg-sky-600 rounded-lg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
function viewTenant(tenantId) {
    $.get('../lib/src/ajax/get_tenant.php', { id: tenantId }, function(response) {
        if (response.success) {
            const tenant = response.data;
            let html = `
                <div class="tenant-details">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="${tenant.profile_picture || '../assets/images/default-avatar.png'}" 
                             alt="${tenant.name}" 
                             class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h4 class="text-lg font-semibold text-slate-900 dark:text-slate-100">${tenant.name}</h4>
                            <p class="text-slate-500 dark:text-slate-400">${tenant.email}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Phone</p>
                            <p class="text-slate-900 dark:text-slate-100">${tenant.phone}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Address</p>
                            <p class="text-slate-900 dark:text-slate-100">${tenant.address}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Property</p>
                            <p class="text-slate-900 dark:text-slate-100">${tenant.property_title || 'No property assigned'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Payment Status</p>
                            <p class="text-slate-900 dark:text-slate-100">${tenant.payment_status || 'No payment'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Joined Date</p>
                            <p class="text-slate-900 dark:text-slate-100">${tenant.created_at}</p>
                        </div>
                    </div>
                </div>
            `;
            $('#tenantDetails').html(html);
            $('#viewTenantModal').modal('show');
        } else {
            alert('Error loading tenant details');
        }
    });
}

function editTenant(tenantId) {
    $.get('../lib/src/ajax/get_tenant.php', { id: tenantId }, function(response) {
        if (response.success) {
            const tenant = response.data;
            $('#editTenantId').val(tenant.id);
            $('#editTenantName').val(tenant.name);
            $('#editTenantEmail').val(tenant.email);
            $('#editTenantPhone').val(tenant.phone);
            $('#editTenantAddress').val(tenant.address);
            $('#editTenantModal').modal('show');
        } else {
            alert('Error loading tenant details');
        }
    });
}

$('#editTenantForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.post('../lib/src/ajax/update_tenant.php', formData, function(response) {
        if (response.success) {
            alert('Tenant updated successfully');
            location.reload();
        } else {
            alert(response.message || 'Error updating tenant');
        }
    });
});
</script>

<?php require_once '../includes/Footer.php'; ?>