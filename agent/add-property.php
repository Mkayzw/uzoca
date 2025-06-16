<?php
require_once("../includes/init.php");

use app\src\AgentDashboard;

// Check if user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header("Location: /uzoca/login.php");
    exit();
}

$agentDashboard = new AgentDashboard();

// Now include the header
require_once("includes/Header.php");
?>

<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-bold mb-4">Add New Property</h1>
</div>
    <div class="bg-white p-6 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <form id="addPropertyForm" class="flex flex-col space-y-8">
            <!-- Image Upload Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <label class="h-[200px] col-span-12 sm:row-span-2 sm:col-span-6 md:col-span-3 cursor-pointer rounded-xl relative" for="main-image">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-white shadow-sm dark:shadow-none dark:text-slate-900 dark:bg-violet-100 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>Main Property Image</p>
                    </div>
                    <span class="sr-only">Choose main property image</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="main-image" required name="main-image">
                    <img class="rounded-xl absolute top-0 left-0 w-full h-full not-sr-only opacity-0" src="" alt="" />
                </label>

                <!-- Additional Images -->
                <?php for($i = 1; $i <= 5; $i++): ?>
                <label class="h-[200px] col-span-12 sm:row-span-2 sm:col-span-6 md:col-span-3 cursor-pointer rounded-xl relative" for="pic-<?= $i ?>">
                    <div class="grid gap-2 place-content-center justify-center text-center bg-white shadow-sm dark:shadow-none dark:text-slate-900 dark:bg-violet-100 h-full rounded-xl p-3">
                        <i class="fr fi-rr-picture"></i>
                        <p>Additional Image <?= $i ?></p>
                    </div>
                    <span class="sr-only">Choose additional image</span>
                    <input type="file" class="h-full cursor-pointer opacity-0 absolute top-0 left-0 w-full rounded-xl image-selector" id="pic-<?= $i ?>" name="pic-<?= $i ?>">
                    <img class="rounded-xl absolute top-0 left-0 w-full h-full not-sr-only opacity-0" src="" alt="" />
                </label>
                <?php endfor; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Title -->
            <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Property Title
                </label>
                <input type="text" id="title" name="title" required
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Location
                    </label>
                    <input type="text" id="location" name="location" required
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

            <!-- Price -->
            <div>
                    <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Price ($)
                </label>
                <input type="number" id="price" name="price" min="0" step="0.01" required
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
            </div>

                <!-- Property Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Property Category
                    </label>
                    <select id="category" name="category" required
                            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
                        <option value="For Rent">For Rent</option>
                        <option value="For Sale">For Sale</option>
                    </select>
                </div>

                <!-- Property Status -->
            <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Property Status
                </label>
                    <select id="status" name="status" required
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="sold">Sold</option>
                        <option value="rented">Rented</option>
                    </select>
                </div>
            </div>

            <!-- Property Summary -->
            <div>
                <label for="summary" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Property Summary
                </label>
                <textarea id="summary" name="summary" rows="3" required
                          class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100"></textarea>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Property Description
                </label>
                <textarea id="description" name="description" rows="4" required
                          class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-sky-500 dark:focus:ring-sky-400 focus:border-sky-500 dark:focus:border-sky-400 dark:bg-slate-800 dark:text-slate-100"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-8 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors duration-300">
                    <i class="fr fi-rr-plus mr-2"></i>
                    Add Property
                </button>
            </div>
        </form>
        </div>
</div>

<!-- Include CKEditor -->
<script src="../assets/editor/ckeditor.js"></script>
<script src="../assets/js/editor.js"></script>
<script>
    // Initialize CKEditor for description and summary
    createEditor('description');
    createEditor('summary');

    // Image preview functionality
    document.querySelectorAll('.image-selector').forEach(input => {
        input.addEventListener('change', function(e) {
            const img = this.parentElement.querySelector('img');
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.opacity = '1';
                }
                reader.readAsDataURL(file);
            }
        });
    });

    // Form submission
document.getElementById('addPropertyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/uzoca/agent/process-property.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/uzoca/agent/properties.php';
        } else {
            alert(data.message || 'An error occurred while adding the property.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the property.');
    });
});
</script>

<?php require_once("../includes/Footer.php"); ?> 