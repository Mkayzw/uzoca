<?php
require_once('../../includes/init.php');

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: /uzoca/login.php");
    exit();
}

$pageTitle = "UZOCA | Add New Property";
require_once('../includes/Header.php');
?>

<div class="space-y-8">
    <div class="bg-white p-6 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <h2 class="text-2xl font-semibold mb-4">Add New Property</h2>

        <form id="addLandlordPropertyForm" class="flex flex-col space-y-6" action="/uzoca/landlord/process-property-add.php" method="POST" enctype="multipart/form-data">
            <!-- Property Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Property Title</label>
                <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Location</label>
                <input type="text" id="location" name="location" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100"></textarea>
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Price ($)</label>
                <input type="number" id="price" name="price" min="0" step="0.01" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Property Image</label>
                <input type="file" id="image" name="image" accept="image/*" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                    Add Property
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once('../../includes/Footer.php'); ?> 