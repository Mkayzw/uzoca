<?php
require_once("../includes/init.php");

use app\src\AgentDashboard;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

$agentDashboard = new AgentDashboard();

// Get agent's properties
$properties = $agentDashboard->getAgentProperties($_SESSION['user_id']);

// Now include the header
require_once("includes/Header.php");
?>

<div class="space-y-8">
<div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap -mb-4">
    <h3 class="header text-xl">
        My Properties
    </h3>
    <a href="/uzoca/agent/add-property.php" class="inline-flex items-center justify-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
        <i class="fr fi-rr-plus mr-2"></i>
        Add New Property
    </a>
</div>

<div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
    <?php if (empty($properties)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fr fi-rr-home text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-medium mb-2">No Properties Yet</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">
                Start by adding your first property to your portfolio
            </p>
            <a href="/uzoca/agent/add-property.php" class="inline-flex items-center justify-center px-6 py-2.5 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
                <i class="fr fi-rr-plus mr-2"></i>
                Add New Property
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($properties as $property): ?>
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden border border-slate-200 dark:border-slate-700">
                    <div class="relative">
                            <img src="<?php echo $property['main_image'] ?? '/uzoca/assets/images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($property['title']); ?>" 
                             class="w-full h-48 object-cover">
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $property['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo ucfirst($property['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                                <?php echo htmlspecialchars($property['summary']); ?>
                        </p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">
                                <i class="fr fi-rr-marker mr-1"></i>
                                <?php echo htmlspecialchars($property['location']); ?>
                            </span>
                            <span class="font-medium">
                                $<?php echo number_format($property['price'], 2); ?>
                            </span>
                        </div>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">
                                    <i class="fr fi-rr-tag mr-1"></i>
                                    <?php echo htmlspecialchars($property['category']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="border-t border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex items-center justify-between">
                            <a href="/uzoca/agent/edit-property.php?id=<?php echo $property['id']; ?>" 
                               class="text-sm text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">
                                <i class="fr fi-rr-edit mr-1"></i>
                                Edit
                            </a>
                            <button onclick="deleteProperty(<?php echo $property['id']; ?>)" 
                                    class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fr fi-rr-trash mr-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
function deleteProperty(id) {
    if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
        fetch('/uzoca/agent/delete-property.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'An error occurred while deleting the property.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the property.');
        });
    }
}
</script>

<?php require_once("../includes/Footer.php"); ?> 