<?php
require_once("../includes/init.php");

use app\src\AgentPayment;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

$agentPayment = new AgentPayment();

// Get subscription months
$months = isset($_GET['months']) ? (int)$_GET['months'] : 0;
if (!in_array($months, [1, 3, 6])) {
    header("Location: /uzoca/agent/subscription.php");
    exit();
}

// Calculate amount based on months
$amount = $months * 5; // $5 per month

// Now include the header
require_once("includes/Header.php");
?>

<div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap -mb-4">
    <h3 class="header text-xl">
        Complete Your Subscription
    </h3>
</div>

<div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-semibold mb-2">Payment Details</h2>
            <p class="text-slate-600 dark:text-slate-400">
                Choose your preferred payment method to complete your subscription
            </p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fr fi-rr-info text-blue-500 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Payment Information</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Please select your preferred payment method and complete the payment using the provided details.</p>
                        <p class="mt-2">Amount: $<?php echo number_format($amount, 2); ?></p>
                        <p>Duration: <?php echo $months; ?> month<?php echo $months > 1 ? 's' : ''; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 mb-8">
            <!-- EcoCash -->
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:border-sky-500 dark:hover:border-sky-400 transition-colors duration-300 cursor-pointer" onclick="selectPaymentMethod('ecocash', event)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fr fi-rr-phone-call text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">EcoCash</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Pay using EcoCash mobile money</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-mono text-sm">+263 0773592085</p>
                    </div>
                </div>
            </div>

            <!-- Mukuru -->
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:border-sky-500 dark:hover:border-sky-400 transition-colors duration-300 cursor-pointer" onclick="selectPaymentMethod('mukuru', event)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fr fi-rr-bank text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Mukuru</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Pay using Mukuru money transfer</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-mono text-sm">+263 0783677131</p>
                    </div>
                </div>
            </div>

            <!-- InnBucks -->
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:border-sky-500 dark:hover:border-sky-400 transition-colors duration-300 cursor-pointer" onclick="selectPaymentMethod('innbucks', event)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fr fi-rr-credit-card text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">InnBucks</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Pay using InnBucks</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-mono text-sm">+263 0783677131</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedMethod = null;

function selectPaymentMethod(method, event) {
    // Prevent any default behavior
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    selectedMethod = method;
    // Remove selected state from all methods
    document.querySelectorAll('.border').forEach(el => {
        el.classList.remove('border-sky-500', 'dark:border-sky-400');
    });
    // Add selected state to chosen method
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('border-sky-500', 'dark:border-sky-400');
    }
    
    // Create payment record
    const data = {
        months: parseInt('<?php echo $months; ?>'),
        amount: parseFloat('<?php echo $amount; ?>'),
        method: method
    };

    fetch('/uzoca/agent/create-payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const infoBox = document.querySelector('.bg-blue-50');
            infoBox.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fr fi-rr-check text-green-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Payment Method Selected</h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            <p>You have selected ${method.toUpperCase()} as your payment method. Please complete the payment using the provided details.</p>
                            <p class="mt-2">Amount: $<?php echo number_format($amount, 2); ?></p>
                            <p>Reference: ${data.reference}</p>
                        </div>
                    </div>
                </div>
            `;

            // Start polling for payment status
            pollPaymentStatus(data.reference);
        } else {
            alert(data.error || 'Failed to process payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your payment');
    });
}

function pollPaymentStatus(reference) {
    const checkStatus = () => {
        fetch(`/uzoca/agent/check-payment.php?reference=${reference}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'completed') {
                // Create subscription
                fetch('/uzoca/agent/create-subscription.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        reference: reference,
                        months: parseInt('<?php echo $months; ?>')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/uzoca/agent/payment-confirmation.php?reference=${reference}`;
                    } else {
                        alert(data.error || 'Failed to create subscription');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while creating your subscription');
                });
            } else if (data.status === 'failed') {
                alert('Payment failed. Please try again.');
            } else {
                // Continue polling
                setTimeout(checkStatus, 5000);
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
    };

    // Start polling
    checkStatus();
}
</script>

<?php require_once("../includes/Footer.php"); ?> 