<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../includes/init.php");

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

use app\src\AgentDashboard;

$agentDashboard = new AgentDashboard();

// Get current payment settings
$query = "SELECT * FROM agent_payment_settings WHERE agent_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<?php $pageTitle = "UZOCA | Payment Settings"; ?>
<?php require_once("includes/Header.php"); ?>

<div class="bg-white p-6 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
    <div class="mb-8">
        <h3 class="text-2xl font-semibold text-rose-500 dark:text-rose-400 mb-4">
            <i class="fr fi-rr-settings relative top-1.5"></i>
            Payment Settings
        </h3>
        <p class="text-slate-600 dark:text-slate-400">
            Configure your payment methods and preferences here.
        </p>
    </div>

    <form id="paymentSettingsForm" class="flex flex-col space-y-8">
        <!-- EcoCash Settings -->
        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-6 lg:p-8">
            <h4 class="text-lg font-semibold mb-6">EcoCash Settings</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        EcoCash Number
                    </label>
                    <input type="tel" 
                           name="ecocash_number" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="+263 77XXXXXXX" 
                           value="<?php echo htmlspecialchars($settings['ecocash_number'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Account Name
                    </label>
                    <input type="text" 
                           name="ecocash_name" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="Your name as registered with EcoCash" 
                           value="<?php echo htmlspecialchars($settings['ecocash_name'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Mukuru Settings -->
        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-6 lg:p-8">
            <h4 class="text-lg font-semibold mb-6">Mukuru Settings</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Mukuru Number
                    </label>
                    <input type="tel" 
                           name="mukuru_number" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="+263 77XXXXXXX" 
                           value="<?php echo htmlspecialchars($settings['mukuru_number'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Account Name
                    </label>
                    <input type="text" 
                           name="mukuru_name" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="Your name as registered with Mukuru" 
                           value="<?php echo htmlspecialchars($settings['mukuru_name'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- InnBucks Settings -->
        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-6 lg:p-8">
            <h4 class="text-lg font-semibold mb-6">InnBucks Settings</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        InnBucks Number
                    </label>
                    <input type="tel" 
                           name="innbucks_number" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="+263 77XXXXXXX" 
                           value="<?php echo htmlspecialchars($settings['innbucks_number'] ?? ''); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Account Name
                    </label>
                    <input type="text" 
                           name="innbucks_name" 
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100" 
                           placeholder="Your name as registered with InnBucks" 
                           value="<?php echo htmlspecialchars($settings['innbucks_name'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center justify-center px-8 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
                <i class="fr fi-rr-disk mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('paymentSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    
    try {
        const response = await fetch('/uzoca/agent/process-settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Payment settings updated successfully!');
        } else {
            alert(result.error || 'Failed to update payment settings');
        }
    } catch (error) {
        alert('An error occurred while updating payment settings');
        console.error('Error:', error);
    }
});
</script>

<?php require_once("../includes/Footer.php"); ?> 