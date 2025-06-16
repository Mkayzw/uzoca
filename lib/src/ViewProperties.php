<?php

namespace app\src;

use app\assets\DB;

class ViewProperties
{
    private $conn;
    private $table = 'properties';
    private $ownerID;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->ownerID = $_SESSION['user_id'] ?? null;
    }

    /**
     * Get all properties for a particular property owner
     */
    public function showAdminProperties()
    {

        $houses = $this->conn->select("properties.id, title, index_img, price, summary, location, type, link", "properties JOIN property_landlords ON properties.id = property_landlords.property_id", "WHERE property_landlords.user_id = ? AND status = 'available' ORDER BY properties.id DESC LIMIT 6", $this->ownerID);

        // Check if there is any available apartment
        if ($houses->num_rows < 1) : ?>
            <p class="text-rose-700 dark:text-rose-500 text-center lg:col-span-12 text-xl">
                You do not have any property yet. Use the <a class="text-sky-500 dark:text-sky-600 hover:underline hover:underline-offset-4 active:underline active:underline-offset-4" href="/uzoca/admin/add-property"> Add New Property </a> button to get started.
            </p>
        <?php
            return;
        endif;

        while ($house = $houses->fetch_object()) : ?>
            <article class="lg:col-span-4 space-y-3 sm:col-span-6">
                <div class="relative">
                    <img class="property-listing-image" src="../assets/img/<?= $house->index_img ?>" alt="<?= $house->title ?>" title="<?= $house->title ?>" width="100%" height="200">

                    <i class="fr fi-rr-heart absolute top-2.5 right-4 text-2xl text-rose-500 dark:text-white"></i>
                </div>

                <div class="px-2 space-y-3">
                    <div class="flex items-center flex-wrap gap-x-4 gap-y-1.5 justify-between">
                        <span class=<?= $house->type === 'For Rent' ? "text-green-500 dark:text-green-400" : "text-rose-500 dark:text-rose-400" ?>>
                            <i class="fr <?= $house->type === 'For Rent' ? 'fi-rr-recycle' : 'fi-rr-thumbtack' ?>"></i>
                            <?= $house->type ?>
                        </span>

                        <span class="text-sky-500 lining-nums font-semibold tracking-widest">
                            ₦ <?= number_format($house->price) ?>
                        </span>
                    </div>

                    <div>
                        <h2 class="header">
                            <?= $house->title ?>
                        </h2>

                        <p>
                            <?= $house->summary ?>
                        </p>
                    </div>

                    <address>
                        <i class="fr fi-rr-map-marker-home"></i>
                        <?= $house->location ?>
                    </address>

                    <a class="inline-block rounded-lg py-1.5 px-3 text-white bg-sky-500 hover:bg-sky-600 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" href="view-property?propertyID=<?= $house->id ?>&propertyName=<?= $house->link ?>">
                        View Details
                    </a>
                </div>
            </article>
<?php
        endwhile;
    }

    /**
     * Show properties in a table format for the dashboard
     */
    public function showProperties()
    {
        $houses = $this->conn->select(
            "properties.id, title, price, location, type, status", 
            "properties JOIN property_landlords ON properties.id = property_landlords.property_id", 
            "WHERE property_landlords.user_id = ? AND status = 'available' ORDER BY properties.id DESC LIMIT 5", 
            $this->ownerID
        );

        if (!$houses || $houses->num_rows === 0) {
            echo '<tr><td colspan="4" class="py-4 text-center text-slate-500 dark:text-slate-400">No active listings found</td></tr>';
            return;
        }

        while ($house = $houses->fetch_object()) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-4">' . htmlspecialchars($house->title) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($house->location) . '</td>';
            echo '<td class="py-4">₦' . number_format($house->price) . '</td>';
            echo '<td class="py-4"><span class="px-2 py-1 text-xs font-medium rounded-full ' . 
                 ($house->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                 '">' . ucfirst($house->status) . '</span></td>';
            echo '</tr>';
        }
    }
}
