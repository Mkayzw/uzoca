<?php

namespace app\src;

use app\assets\DB;

class AdminIndex
{
    private $ownerID;
    private $con;

    public function __construct()
    {
        $this->ownerID = $_SESSION['user_id'];
        $this->con = DB::getInstance();
    }

    /**
     * Show the number of properties owned by a particular property owner
     */
    public function showPropertiesCount()
    {
        $propertyCount = $this->con->select("properties.id", "properties JOIN property_landlords ON properties.id = property_landlords.property_id", "WHERE property_landlords.user_id = ?", $this->ownerID)->num_rows;

        if ($propertyCount < 1) : ?>
            <p class="font-bold">
                You do not have any property yet.
            </p>
        <?php
            return;
        endif;
        ?>
        <p class="font-bold text-2xl">
            <?= $propertyCount ?>
        </p>

        <p>
            Propert<?= ($propertyCount > 1) ? "ies" : "y" ?>
        </p>
        <?php
    }

    /**
     * Show the number of tenants for a particular property owner
     */
    public function showTenantsCount()
    {
        $tenants = $this->con->select("tenants.id", "tenants JOIN properties ON tenants.property_id = properties.id JOIN property_landlords ON properties.id = property_landlords.property_id", "WHERE property_landlords.user_id = ?", $this->ownerID)->num_rows;

        if ($tenants < 1) : ?>
            <p class="font-bold">
                You do not have any tenant(s) yet.
            </p>
        <?php
            return;
        endif;
        ?>
        <p class="font-bold text-2xl">
            <?= $tenants ?>
        </p>

        <p>
            Tenant<?= ($tenants > 1) ? "s" : "" ?>
        </p>
<?php
    }
}
