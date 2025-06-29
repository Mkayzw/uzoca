<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/Database.php';
require_once __DIR__ . '/lib/src/Register.php';

use app\config\Database;
use lib\src\Register;

$database = new Database();
$conn = $database->getConnection();
$registerUser = new Register();
$message = $registerUser->registerUser();

// If registration was successful and we have a redirect, don't show the form
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirectUrl = '';
    switch($_SESSION['role']) {
        case 'agent':
            $redirectUrl = '/uzoca/agent/subscription.php';
            break;
        case 'landlord':
            $redirectUrl = '/uzoca/landlord/dashboard.php';
            break;
        case 'admin':
            $redirectUrl = '/uzoca/admin/index.php';
            break;
        default:
            $redirectUrl = '/uzoca/login.php';
    }
    header("Location: $redirectUrl");
    exit();
}

// If we have a success message, show it and redirect after 3 seconds
if (strpos($message, 'successful') !== false) {
    echo "<script>
        setTimeout(function() {
            window.location.href = '/uzoca/login.php';
        }, 3000);
    </script>";
}
?>
<?php $pageTitle = "Register" ?>
<?php require_once("./includes/Header.php"); ?>

<main class="dark:bg-slate-900 dark:text-slate-400">
    <div class="grid place-items-center lg:place-content-center w-full lg:max-w-[50%] lg:mx-auto min-h-screen py-8 px-4 ">
        <form class="bg-slate-100 py-8 px-4 w-full rounded-xl lg:px-12 dark:bg-slate-800" method="POST">
            <div class="text-center mx-auto w-[90%] mb-8">

                <h3 class="text-center header text-2xl">
                    Sign Up
                </h3>

                <?php if ($message): ?>
                <div class="rounded-md p-4 mt-4 <?php echo strpos($message, 'successful') !== false ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900'; ?>">
                    <p class="text-sm <?php echo strpos($message, 'successful') !== false ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'; ?>">
                        <?php echo $message; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <div class="grid gap-4 lg:grid-cols-12">
                <label class="flex items-center bg-white text-slate-900 rounded-lg lg:col-span-6 dark:bg-slate-900 dark:text-slate-400 shadow-sm border-1 border-slate-100" for="name">
                    <span class="rounded-l-lg pl-4">
                        <i class="fr fi-rr-form relative top-0.5"></i>
                    </span>

                    <input class="rounded-r-lg input pl-2" type="text" placeholder="Full name" name="name" id="name" autocomplete="off" required value="<?= $registerUser->setName() ?>" />
                </label>

                <label class="flex items-center bg-white text-slate-900 rounded-lg lg:col-span-6 dark:bg-slate-900 dark:text-slate-400 shadow-sm border-1 border-slate-100" for="phoneNumber">
                    <span class="rounded-l-lg pl-4">
                        <i class="fr fi-rr-phone-call relative top-0.5"></i>
                    </span>

                    <input class="rounded-r-lg input pl-2" type="tel" placeholder="Phone Number" name="phoneNumber" id="phoneNumber" autocomplete="off" required minlength="11" maxlength="11" value="<?= $registerUser->setPhoneNumber() ?>" />
                </label>

                <label class="flex items-center bg-white text-slate-900 rounded-lg lg:col-span-6 dark:bg-slate-900 dark:text-slate-400 shadow-sm border-1 border-slate-100" for="email">
                    <span class="rounded-l-lg pl-4">
                        <i class="fr fi-rr-envelope relative top-0.5"></i>
                    </span>

                    <input class="rounded-r-lg input pl-2" type="text" placeholder="Email address" name="email" id="email" autocomplete="off" required value="<?= $registerUser->setEmail() ?>" />
                </label>

                <label class="flex items-center bg-white text-slate-900 rounded-lg lg:col-span-6 dark:bg-slate-900 dark:text-slate-400 shadow-sm border-1 border-slate-100" for="password">
                    <span class="rounded-l-lg pl-4">
                        <i class="fr fi-rr-lock relative top-0.5"></i>
                    </span>

                    <input class="rounded-r-lg input pl-2" type="password" placeholder="Password" name="password" id="password" required autocomplete="off" />
                </label>

                <button class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 py-1.5 w-auto px-4 text-white rounded-lg lg:col-span-12 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:bg-sky-700" type="submit" name="submit">
                    Sign Up
                </button>
            </div>

            <p class="mt-4">
                Already have an account?
                <a class="text-sky-400 hover:text-sky-600 hover:underline hover:underline-offset-4 active:underline active:underline-offset-4 dark:dark:text-sky-600 dark:hover:text-sky-700 dark:focus:text-sky-700 focus:underline" href="/uzoca/login.php">
                    Login instead
                </a>
            </p>
        </form>
    </div>
</main>

<?php require_once("./includes/Footer.php"); ?>
