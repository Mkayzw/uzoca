<?php
require_once("../includes/init.php");

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

use app\src\AgentDashboard;
use app\src\UserProfile;

$agentDashboard = new AgentDashboard();
$userProfile = new UserProfile();
$user = $userProfile->getUserProfile();

$pageTitle = "UZOCA | Agent Profile";
require_once("includes/Header.php");
?>

<div class="px-4 space-y-12 lg:px-[2.5%] py-8 relative">
    <!-- Profile Header -->
    <div class="rounded-xl p-4 lg:p-8 lg:gap-8 space-y-4 bg-white dark:bg-slate-900 dark:text-slate-100">
        <div class="flex flex-col items-center text-center gap-6">
            <div class="relative">
                <img src="/uzoca/assets/images/<?php echo $user->profile_pic ?? 'default.png'; ?>" 
                     alt="Profile Picture" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-slate-200 dark:border-slate-700">
                <button type="button" 
                        class="absolute bottom-0 right-0 bg-sky-500 text-white p-2 rounded-full hover:bg-sky-600 transition-colors"
                        onclick="document.getElementById('profile-pic-input').click()">
                    <i class="fr fi-rr-camera"></i>
                </button>
                <input type="file" 
                       id="profile-pic-input" 
                       name="profile_pic" 
                       class="hidden" 
                       accept="image/*">
            </div>
            <div>
                <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($user->name ?? 'Agent'); ?></h1>
                <p class="text-slate-600 dark:text-slate-400 text-lg"><?php echo htmlspecialchars($user->email ?? ''); ?></p>
                <p class="text-slate-600 dark:text-slate-400 text-lg"><?php echo htmlspecialchars($user->phone ?? ''); ?></p>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="grid gap-8 md:grid-cols-2 max-w-4xl mx-auto">
        <!-- Personal Information -->
        <div class="rounded-xl p-6 lg:p-8 space-y-6 bg-white dark:bg-slate-900 dark:text-slate-100">
            <h2 class="text-2xl font-semibold text-center mb-6">Personal Information</h2>
            <form method="POST" action="/uzoca/lib/update_profile.php" class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-center mb-2">Full Name</label>
                    <input type="text" 
                           name="name" 
                           value="<?php echo htmlspecialchars($user->name ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg dark:bg-slate-800 dark:border-slate-700 text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-center mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($user->email ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg dark:bg-slate-800 dark:border-slate-700 text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-center mb-2">Phone</label>
                    <input type="tel" 
                           name="phone" 
                           value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" 
                           class="w-full px-4 py-3 border rounded-lg dark:bg-slate-800 dark:border-slate-700 text-center focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
                <div class="text-center pt-4">
                    <button type="submit" 
                            class="px-6 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors inline-flex items-center">
                        <i class="fr fi-rr-disk mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Status -->
        <div class="rounded-xl p-6 lg:p-8 space-y-6 bg-white dark:bg-slate-900 dark:text-slate-100">
            <h2 class="text-2xl font-semibold text-center mb-6">Account Status</h2>
            <div class="space-y-6">
                <!-- Subscription Status -->
                <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <h3 class="font-medium text-center mb-4">Subscription Status</h3>
                    <?php $agentDashboard->showSubscriptionStatus(); ?>
                </div>

                <!-- Account Statistics -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-lg text-center">
                        <h3 class="font-medium mb-2">Total Properties</h3>
                        <p class="text-3xl font-bold text-sky-500"><?php echo $agentDashboard->getTotalProperties(); ?></p>
                    </div>
                    <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-lg text-center">
                        <h3 class="font-medium mb-2">Total Bookings</h3>
                        <p class="text-3xl font-bold text-sky-500"><?php echo $agentDashboard->getTotalBookings(); ?></p>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="space-y-3">
                    <a href="/uzoca/agent/settings.php" 
                       class="block w-full px-6 py-3 text-center bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fr fi-rr-settings mr-2"></i>
                        Payment Settings
                    </a>
                    <a href="/uzoca/agent/subscription.php" 
                       class="block w-full px-6 py-3 text-center bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fr fi-rr-credit-card mr-2"></i>
                        Manage Subscription
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('profile-pic-input').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const formData = new FormData();
        formData.append('profile_pic', e.target.files[0]);
        formData.append('change-profile-pic', '1');

        fetch('/uzoca/lib/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error updating profile picture');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating profile picture');
        });
    }
});
</script> 