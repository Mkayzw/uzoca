<?php require_once("./includes/Header.php"); ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Or
                <a href="login.php" class="font-medium text-sky-600 hover:text-sky-500">
                    sign in to your account
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="/uzoca/register.php" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="user-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Register as</label>
                    <select name="user-type" id="user-type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-sky-500 focus:border-sky-500 sm:text-sm rounded-md">
                        <option value="landlord">Landlord</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>

                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input id="name" name="name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" placeholder="Full Name">
                </div>

                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" placeholder="Email address">
                </div>

                <div>
                    <label for="phone" class="sr-only">Phone Number</label>
                    <input id="phone" name="phone" type="tel" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" placeholder="Phone Number">
                </div>

                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" placeholder="Password">
                </div>

                <div>
                    <label for="password-confirm" class="sr-only">Confirm Password</label>
                    <input id="password-confirm" name="password-confirm" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-sky-500 focus:border-sky-500 focus:z-10 sm:text-sm" placeholder="Confirm Password">
                </div>
            </div>

            <!-- Agent Subscription Info -->
            <div id="agent-subscription-info" class="<?php echo $showAgentInfo ? '' : 'hidden'; ?> bg-sky-50 dark:bg-slate-800 p-4 rounded-md">
                <h3 class="text-lg font-medium text-sky-900 dark:text-sky-100">Agent Subscription Required</h3>
                <p class="mt-2 text-sm text-sky-700 dark:text-sky-300">
                    As an agent, you'll need to subscribe to our service. After registration, you'll be redirected to choose a subscription plan.
                </p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 dark:bg-red-900/50 p-4 rounded-md">
                    <p class="text-sm text-red-700 dark:text-red-300">
                        <?php
                        switch ($_GET['error']) {
                            case 'emptyfields':
                                echo 'Please fill in all fields.';
                                break;
                            case 'passwordmismatch':
                                echo 'Passwords do not match.';
                                break;
                            case 'emailtaken':
                                echo 'Email is already registered.';
                                break;
                            case 'sqlerror':
                                echo 'An error occurred. Please try again.';
                                break;
                            default:
                                echo 'An error occurred.';
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <div>
                <button type="submit" name="register-submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                    Register
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('user-type').addEventListener('change', function() {
    const subscriptionInfo = document.getElementById('agent-subscription-info');
    if (this.value === 'agent') {
        subscriptionInfo.classList.remove('hidden');
    } else {
        subscriptionInfo.classList.add('hidden');
    }
});
</script>

<?php require_once("./includes/Footer.php"); ?> 