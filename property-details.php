<?php
require_once("includes/init.php");

use app\src\Property;

if (!isset($_GET['id'])) {
    header("Location: /uzoca/properties.php");
    exit();
}

$property = new Property();
$propertyDetails = $property->getPropertyById($_GET['id']);

if (!$propertyDetails) {
    header("Location: /uzoca/properties.php");
    exit();
}

$pageTitle = "UZOCA | " . $propertyDetails['title'];
require_once("includes/Header.php");
?>

<div class="space-y-8">
    <div class="flex items-center gap-x-4 gap-y-2 justify-between flex-wrap">
        <a href="/uzoca/properties.php" class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700">
            <i class="fr fi-rr-arrow-small-left"></i>
            Back to Properties
        </a>
    </div>

    <div class="bg-white p-4 lg:p-8 rounded-xl dark:bg-slate-900 dark:text-slate-100 space-y-8 shadow-sm">
        <!-- Property Images -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="relative">
                <img src="<?php echo $propertyDetails['main_image'] ?? '/uzoca/assets/images/placeholder.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($propertyDetails['title']); ?>" 
                     class="w-full h-96 object-cover rounded-lg">
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1.5 text-sm font-medium rounded-full <?php echo $propertyDetails['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                        <?php echo ucfirst($propertyDetails['status']); ?>
                    </span>
                </div>
            </div>
            
            <?php if (!empty($propertyDetails['additional_images'])): ?>
            <div class="grid grid-cols-2 gap-4">
                <?php 
                $additionalImages = json_decode($propertyDetails['additional_images'], true);
                foreach ($additionalImages as $image): 
                ?>
                <img src="<?php echo $image; ?>" 
                     alt="Additional property image" 
                     class="w-full h-48 object-cover rounded-lg">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Property Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h1 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($propertyDetails['title']); ?></h1>
                    <p class="text-slate-600 dark:text-slate-400">
                        <i class="fr fi-rr-marker mr-1"></i>
                        <?php echo htmlspecialchars($propertyDetails['location']); ?>
                    </p>
                </div>

                <div class="prose dark:prose-invert max-w-none">
                    <h2 class="text-xl font-semibold mb-4">Description</h2>
                    <?php echo $propertyDetails['description']; ?>
                </div>

                <div class="prose dark:prose-invert max-w-none">
                    <h2 class="text-xl font-semibold mb-4">Summary</h2>
                    <?php echo $propertyDetails['summary']; ?>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-lg">
                    <div class="text-2xl font-bold text-sky-500 dark:text-sky-400 mb-4">
                        $<?php echo number_format($propertyDetails['price'], 2); ?>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Category</span>
                            <span class="font-medium"><?php echo htmlspecialchars($propertyDetails['category']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Status</span>
                            <span class="font-medium"><?php echo ucfirst($propertyDetails['status']); ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($propertyDetails['agent_name']): ?>
                <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-lg">
                    <h3 class="font-semibold mb-4">Contact Agent</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <?php if ($propertyDetails['agent_image']): ?>
                        <img src="<?php echo $propertyDetails['agent_image']; ?>" 
                             alt="<?php echo htmlspecialchars($propertyDetails['agent_name']); ?>" 
                             class="w-12 h-12 rounded-full object-cover">
                        <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center">
                            <i class="fr fi-rr-user text-slate-500"></i>
                        </div>
                        <?php endif; ?>
                        <div>
                            <div class="font-medium"><?php echo htmlspecialchars($propertyDetails['agent_name']); ?></div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">Property Agent</div>
                        </div>
                    </div>
                    <?php if ($propertyDetails['agent_email']): ?>
                    <a href="mailto:<?php echo htmlspecialchars($propertyDetails['agent_email']); ?>" 
                       class="block w-full text-center py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                        <i class="fr fi-rr-envelope mr-2"></i>
                        Contact via Email
                    </a>
                    <?php endif; ?>
                    <?php if ($propertyDetails['agent_phone']): ?>
                    <a href="tel:<?php echo htmlspecialchars($propertyDetails['agent_phone']); ?>" 
                       class="block w-full text-center py-2 mt-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                        <i class="fr fi-rr-phone-call mr-2"></i>
                        Contact via Phone
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once("includes/Footer.php"); ?> 