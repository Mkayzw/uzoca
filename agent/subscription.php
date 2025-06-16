<?php
require_once("../includes/init.php");
$pageTitle = "UZOCA | Agent Subscription";
require_once("includes/Header.php");

use app\src\AgentDashboard;

$agentDashboard = new AgentDashboard();
?>

<div class="space-y-8">
    <!-- Current Subscription -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <div>
            <h2 class="text-2xl font-semibold mb-4">
                Current Subscription
            </h2>
        </div>
        <div class="overflow-x-auto">
            <?php $agentDashboard->showSubscriptionStatus(); ?>
        </div>
    </div>

    <!-- Subscription Plans -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <div>
            <h2 class="text-2xl font-semibold mb-4">
                Subscription Plans
            </h2>
            <p class="text-slate-600 dark:text-slate-400">
                Choose the plan that best suits your needs
            </p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Monthly Plan -->
            <div class="space-y-4">
                <a href="/uzoca/agent/process-subscription.php?months=1" 
                   class="block w-full px-6 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors text-center">
                    Choose Monthly
                </a>
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4 text-center">Monthly</h3>
                    <p class="text-3xl font-bold text-sky-500 mb-4 text-center">$5<span class="text-base font-normal text-slate-600 dark:text-slate-400">/month</span></p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>Basic Features</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>5 Properties</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>Email Support</span>
                    </li>
                </ul>
                </div>
            </div>

            <!-- Quarterly Plan -->
            <div class="space-y-4">
                <a href="/uzoca/agent/process-subscription.php?months=3" 
                   class="block w-full px-6 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors text-center">
                    Choose Quarterly
                </a>
                <div class="border-2 border-sky-500 dark:border-sky-400 rounded-lg p-6 relative">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-sky-500 text-white px-4 py-1 rounded-full text-sm">
                        Most Popular
                </div>
                    <h3 class="text-xl font-semibold mb-4 text-center">Quarterly</h3>
                    <p class="text-3xl font-bold text-sky-500 mb-4 text-center">$12<span class="text-base font-normal text-slate-600 dark:text-slate-400">/3 months</span></p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>All Basic Features</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>15 Properties</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>Priority Support</span>
                    </li>
                </ul>
                </div>
            </div>

            <!-- Semi-Annual Plan -->
            <div class="space-y-4">
                <a href="/uzoca/agent/process-subscription.php?months=6" 
                   class="block w-full px-6 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors text-center">
                    Choose Semi-Annual
                </a>
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-4 text-center">Semi-Annual</h3>
                    <p class="text-3xl font-bold text-sky-500 mb-4 text-center">$20<span class="text-base font-normal text-slate-600 dark:text-slate-400">/6 months</span></p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>All Premium Features</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>Unlimited Properties</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fr fi-rr-check text-green-500 mr-2"></i>
                            <span>24/7 Support</span>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
            <h2 class="text-2xl header">
                Frequently Asked Questions
            </h2>
        </div>
        <div class="space-y-4">
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                <h3 class="text-lg font-semibold mb-2">How does the subscription work?</h3>
                <p class="text-slate-600 dark:text-slate-400">
                    Your subscription gives you access to all features of the platform. You can choose between monthly, quarterly, or semi-annual billing cycles.
                </p>
            </div>
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                <h3 class="text-lg font-semibold mb-2">Can I cancel my subscription?</h3>
                <p class="text-slate-600 dark:text-slate-400">
                    Yes, you can cancel your subscription at any time. Your access will continue until the end of your current billing period.
                </p>
            </div>
            <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                <h3 class="text-lg font-semibold mb-2">What payment methods are accepted?</h3>
                <p class="text-slate-600 dark:text-slate-400">
                    We accept PayNow payments for all subscriptions. The payment process is secure and straightforward.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">Need more help?</h3>
                <p class="text-slate-600 dark:text-slate-400">
                    Contact our support team at <a href="mailto:support@uzoca.com" class="text-sky-500 hover:text-sky-600">support@uzoca.com</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function subscribe(months) {
    try {
        // Redirect to payment page
        window.location.href = 'process-subscription.php?months=' + months;
    } catch (error) {
        alert('An error occurred while processing your subscription. Please try again.');
    }
}

// Add click event listeners to all subscribe buttons
document.addEventListener('DOMContentLoaded', function() {
    const subscribeButtons = document.querySelectorAll('button[data-months]');
    subscribeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const months = this.getAttribute('data-months');
            subscribe(parseInt(months));
        });
    });
});
</script>

<?php require_once("../includes/Footer.php"); ?> 