<?php
session_start();

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'landlord') {
    header("Location: /uzoca/login.php");
    exit();
}

$pageTitle = "UZOCA | Settings";
require_once("./includes/Header.php");

use app\assets\DB;

$DB = DB::getInstance();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // If changing password
    if (!empty($current_password)) {
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }

    if (empty($errors)) {
        try {
            // Update user information
            $stmt = $DB->prepare(
                "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?",
                'sssi',
                $name, $email, $phone, $_SESSION['user_id']
            );

            // If changing password
            if (!empty($current_password)) {
                // Verify current password
                $stmt = $DB->prepare(
                    "SELECT password FROM users WHERE id = ?",
                    'i',
                    $_SESSION['user_id']
                );
                $result = $stmt->fetch_assoc();

                if (password_verify($current_password, $result['password'])) {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $DB->prepare(
                        "UPDATE users SET password = ? WHERE id = ?",
                        'si',
                        $hashed_password, $_SESSION['user_id']
                    );
                } else {
                    $errors[] = "Current password is incorrect";
                }
            }

            if (empty($errors)) {
                $_SESSION['success'] = "Settings updated successfully";
                header("Location: settings.php");
                exit();
            }
        } catch (Exception $e) {
            $errors[] = "An error occurred while updating settings";
            error_log($e->getMessage());
        }
    }
}

// Get current user data
$stmt = $DB->prepare(
    "SELECT name, email, phone FROM users WHERE id = ?",
    'i',
    $_SESSION['user_id']
);
$user = $stmt->fetch_assoc();
?>

        <div class="space-y-8">
    <div class="flex flex-wrap justify-between gap-y-2 gap-x-4 items-center">
        <h3 class="header text-2xl">
            Account Settings
        </h3>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
                            </div>
                        <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8">
        <form method="POST" class="space-y-6">
            <div class="space-y-4">
                <h4 class="text-lg font-semibold">Profile Information</h4>
                
                                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                            </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                        </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                    <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="text-lg font-semibold">Change Password</h4>
                
                            <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Current Password</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                            </div>

                            <div>
                    <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">New Password</label>
                    <input type="password" name="new_password" id="new_password" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                        </div>

                                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 dark:bg-slate-800 dark:border-slate-600">
                            </div>
                        </div>

                        <div class="flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-sky-500 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                        Save Changes
                                </button>
                        </div>
                    </form>
    </div>
</div>

<?php require_once("./includes/Footer.php"); ?> 