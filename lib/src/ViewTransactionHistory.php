<?php

namespace app\src;

use app\assets\DB;

class ViewTransactionHistory
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function showTransactionHistory()
    {
        $ownerId = $_SESSION['user_id'];
        
        // Get approved bookings for properties owned by this landlord
        $query = "SELECT b.*, t.name as tenant_name, 
                        p.title as property_title, r.name as room_name,
                        b.created_at as booking_date, b.status
                 FROM bookings b
                 JOIN tenants t ON b.tenant_id = t.id
                 JOIN properties p ON b.property_id = p.id
                 JOIN rooms r ON b.room_id = r.id
                 JOIN property_landlords pl ON p.id = pl.property_id
                 WHERE pl.user_id = ? AND b.status = 'approved'
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query, 'i', $ownerId);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        if (empty($result)) {
            echo '<tr><td colspan="6" class="py-4 text-center text-slate-500 dark:text-slate-400">No approved bookings found</td></tr>';
            return;
        }

        foreach ($result as $row) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-4">' . date('M j, Y', strtotime($row['booking_date'])) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($row['tenant_name']) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($row['property_title']) . '</td>';
            echo '<td class="py-4">₦' . number_format($row['amount'], 2) . '</td>';
            echo '<td class="py-4">Admin Payment</td>';
            echo '<td class="py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span></td>';
            echo '</tr>';
        }
    }

    public function showIndexTransactions()
    {
        $query = "SELECT COALESCE(SUM(p.price), 0) as total_income
                 FROM properties p
                 JOIN property_landlords pl ON p.id = pl.property_id
                 LEFT JOIN tenants t ON p.id = t.property_id
                 WHERE pl.user_id = ?
                 AND (t.status = 'active' OR t.status IS NULL)
                 AND (MONTH(t.move_in_date) = MONTH(CURRENT_DATE()) OR t.move_in_date IS NULL)
                 AND (YEAR(t.move_in_date) = YEAR(CURRENT_DATE()) OR t.move_in_date IS NULL)";
        
        $stmt = $this->conn->prepare($query, 'i', $_SESSION['user_id']);
        $result = $stmt->fetch_assoc();
        
        $monthlyIncome = $result['total_income'] ?? 0;
        
        echo '<div class="flex flex-col gap-2">';
        echo '<p class="text-2xl font-semibold">$' . number_format($monthlyIncome, 2) . '</p>';
        echo '<p class="text-sm text-slate-500 dark:text-slate-400">Monthly Income</p>';
        echo '</div>';
    }

    public function showIndexTenants()
    {
        $ownerId = $_SESSION['user_id'];
        
        // Get last 5 tenants from approved bookings
        $query = "SELECT DISTINCT t.*, p.title as property_title, b.created_at as booking_date
                 FROM tenants t
                 JOIN bookings b ON t.id = b.tenant_id
                 JOIN properties p ON b.property_id = p.id
                 JOIN property_landlords pl ON p.id = pl.property_id
                 WHERE pl.user_id = ? AND b.status = 'approved'
                 ORDER BY b.created_at DESC LIMIT 5";
        
        $stmt = $this->conn->prepare($query, 'i', $ownerId);
        $result = $stmt->fetch_all(MYSQLI_ASSOC);

        if (empty($result)) {
            echo '<tr><td colspan="4" class="py-4 text-center text-slate-500 dark:text-slate-400">No tenants found</td></tr>';
            return;
        }

        foreach ($result as $row) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-4">' . htmlspecialchars($row['name']) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($row['email']) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($row['property_title']) . '</td>';
            echo '<td class="py-4">' . date('M j, Y', strtotime($row['booking_date'])) . '</td>';
            echo '</tr>';
        }
    }
} 