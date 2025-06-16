<?php 
require_once(realpath(__DIR__ . '/../../vendor') . DIRECTORY_SEPARATOR . 'autoload.php');
$pageTitle = "All Properties" 
?>
<?php require_once("./includes/Header.php"); ?>
<?php

use app\src\Properties;

$properties = new Properties();

// Get filter parameters
$category = $_GET['category'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$search = $_GET['search'] ?? '';
?>
<div class="min-h-[60vh] lg:min-h-[70vh] grid place-content-center text-center bg-index-banner px-4 bg-fixed bg-center bg-cover text-slate-200 p-4 lg:p-8">
    <h1 class="header text-3xl">
        All Property Listings
    </h1>
</div>

<main class="space-y-12 py-12 px-4 lg:px-[10%] dark:bg-slate-900 dark:text-slate-300">
    <!-- Filters Section -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm">
        <form action="" method="GET" class="grid gap-6 md:grid-cols-2 lg:grid-cols-5">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" 
                           placeholder="Search by title, location...">
                    <i class="fr fi-rr-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                <select name="category" id="category" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200">
                    <option value="">All Categories</option>
                    <option value="rent" <?php echo $category === 'rent' ? 'selected' : ''; ?>>For Rent</option>
                    <option value="sale" <?php echo $category === 'sale' ? 'selected' : ''; ?>>For Sale</option>
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label for="min_price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Min Price</label>
                <input type="number" name="min_price" id="min_price" value="<?php echo htmlspecialchars($minPrice); ?>" 
                       class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" 
                       placeholder="Min Price">
            </div>

            <div>
                <label for="max_price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Max Price</label>
                <input type="number" name="max_price" id="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>" 
                       class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" 
                       placeholder="Max Price">
            </div>

            <!-- Filter Button -->
            <div class="md:col-span-2 lg:col-span-5 flex justify-end gap-4">
                <a href="/uzoca/properties" class="px-4 py-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                    Clear Filters
                </a>
                <button type="submit" class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    <section class="grid gap-8 sm:grid-cols-12">
        <?php 
        if (!empty($search) || !empty($category) || !empty($minPrice) || !empty($maxPrice)) {
            $properties->getFilteredProperties($search, $category, $minPrice, $maxPrice);
        } else {
            $properties->getAllProperties();
        }
        ?>
    </section>
</main>

<?php require_once("./includes/Footer.php") ?>