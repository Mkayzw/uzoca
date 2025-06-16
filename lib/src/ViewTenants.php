<?php

namespace app\src;

class ViewTenants
{
    private $conn;
    private $table = 'tenants';
    private $ownerID;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->ownerID = $_SESSION['user_id'] ?? null;
    }

    /**
     * Shows the list of tenants for a particular property owner
     */
    public function showTenants()
    {
        $stmt = $this->conn->prepare(
            "SELECT t.name, t.id_number, t.move_in_date, t.status, t.booking_code, p.title as property_title 
             FROM {$this->table} t 
             JOIN properties p ON t.property_id = p.id 
             JOIN property_landlords pl ON p.id = pl.property_id 
             WHERE pl.user_id = ? 
             ORDER BY t.move_in_date DESC",
            'i',
            $this->ownerID
        );
        $tenants = $stmt->fetch_all(MYSQLI_ASSOC);

        if (empty($tenants)) : ?>
            <p class="font-bold">
                You do not have any tenant(s) yet.
            </p>
        <?php
            return;
        endif;
        ?>
        <table class="w-full border-separate whitespace-nowrap table-auto mb-2">
            <thead class="text-left border border-slate-600">
                <tr class="text-sm">
                    <th class="py-4 px-4 border border-slate-600 header">
                        Tenant's Name
                    </th>
                    <th class="py-4 px-4 border border-slate-600 header">
                        Property
                    </th>
                    <th class="py-4 px-4 border border-slate-600 header">
                        ID Number
                    </th>
                    <th class="py-4 px-4 border border-slate-600 header">
                        Move-in Date
                    </th>
                    <th class="py-4 px-4 border border-slate-600 header">
                        Booking Code
                    </th>
                    <th class="py-4 px-4 border border-slate-600 header">
                        Status
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tenants as $tenant) : ?>
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-slate-50 dark:odd:bg-slate-700 dark:even:bg-slate-800 dark:hover:bg-slate-800">
                        <td class="py-2 px-4 border border-slate-600">
                            <?= htmlspecialchars($tenant['name']) ?>
                        </td>
                        <td class="py-2 px-4 border border-slate-600">
                            <?= htmlspecialchars($tenant['property_title']) ?>
                        </td>
                        <td class="py-2 px-4 border border-slate-600">
                            <?= htmlspecialchars($tenant['id_number']) ?>
                        </td>
                        <td class="py-2 px-4 border border-slate-600">
                            <?= date('M d, Y', strtotime($tenant['move_in_date'])) ?>
                        </td>
                        <td class="py-2 px-4 border border-slate-600">
                            <?= htmlspecialchars($tenant['booking_code']) ?>
                        </td>
                        <td class="py-2 px-4 border border-slate-600">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $tenant['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($tenant['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php
    }
}
