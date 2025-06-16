<?php

namespace app\src;

use app\assets\DB;

class AgentDashboard
{
    private $con;
    private $agentId;

    public function __construct()
    {
        $this->agentId = $_SESSION['user_id'] ?? null;
        if (!$this->agentId) {
            throw new \Exception('Agent ID not found in session');
        }
        $this->con = DB::getInstance();
    }

    /**
     * Show pending bookings for the agent
     */
    public function showPendingBookings()
    {
        try {
            $sql = "SELECT b.*, t.name as tenant_name, p.name as property_name, r.name as room_name 
                     FROM bookings b 
                    JOIN users t ON b.tenant_id = t.id 
                     JOIN properties p ON b.property_id = p.id 
                     JOIN rooms r ON b.room_id = r.id 
                     WHERE b.agent_id = ? AND b.status = 'pending' 
                     ORDER BY b.created_at DESC 
                     LIMIT 5";
            
            $result = $this->con->query($sql, [$this->agentId]);
            
            if ($result && $result->num_rows > 0) {
                while ($booking = $result->fetch_assoc()) {
                    echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
                    echo "<td class='py-3'>" . htmlspecialchars($booking['tenant_name']) . "</td>";
                    echo "<td class='py-3'>" . htmlspecialchars($booking['property_name']) . "</td>";
                    echo "<td class='py-3'>" . htmlspecialchars($booking['room_name']) . "</td>";
                    echo "<td class='py-3'>" . date('M d, Y', strtotime($booking['created_at'])) . "</td>";
                    echo "<td class='py-3'>Ksh " . number_format($booking['agent_fee'], 2) . "</td>";
                    echo "<td class='py-3'><span class='px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-600'>Pending</span></td>";
                    echo "<td class='py-3'>";
                    echo "<a href='/uzoca/agent/bookings/view.php?id=" . $booking['id'] . "' class='text-sky-500 hover:text-sky-600'>View</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='py-3 text-center text-slate-500'>No pending bookings found</td></tr>";
            }
        } catch (\Exception $e) {
            error_log("Error showing pending bookings: " . $e->getMessage());
            echo "<tr><td colspan='7' class='py-3 text-center text-red-500'>Error loading bookings</td></tr>";
        }
    }

    /**
     * Show payment history for the agent
     */
    public function showPaymentHistory()
    {
        try {
            $sql = "SELECT * FROM payments 
                    WHERE agent_id = ? 
                    ORDER BY created_at DESC 
                     LIMIT 5";
            
            $result = $this->con->query($sql, [$this->agentId]);
            
            if ($result && $result->num_rows > 0) {
                while ($payment = $result->fetch_assoc()) {
                    echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
                    echo "<td class='py-3'>" . date('M d, Y', strtotime($payment['created_at'])) . "</td>";
                    echo "<td class='py-3'>" . htmlspecialchars($payment['type']) . "</td>";
                    echo "<td class='py-3'>Ksh " . number_format($payment['amount'], 2) . "</td>";
                    echo "<td class='py-3'>";
                    $statusClass = $payment['status'] === 'completed' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600';
                    echo "<span class='px-2 py-1 text-xs font-semibold rounded-full " . $statusClass . "'>" . ucfirst($payment['status']) . "</span>";
                    echo "</td>";
                    echo "<td class='py-3'>" . htmlspecialchars($payment['reference']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='py-3 text-center text-slate-500'>No payment history found</td></tr>";
            }
        } catch (\Exception $e) {
            error_log("Error showing payment history: " . $e->getMessage());
            echo "<tr><td colspan='5' class='py-3 text-center text-red-500'>Error loading payment history</td></tr>";
        }
    }

    /**
     * Get total properties count for the agent
     */
    public function getTotalProperties()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM properties WHERE agent_id = ?";
            $result = $this->con->query($sql, [$this->agentId]);
            if ($result && $row = $result->fetch_assoc()) {
                return $row['total'];
            }
            return 0;
        } catch (\Exception $e) {
            error_log("Error getting total properties: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total bookings count for the agent
     */
    public function getTotalBookings()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM bookings WHERE agent_id = ?";
            $result = $this->con->query($sql, [$this->agentId]);
            if ($result && $row = $result->fetch_assoc()) {
                return $row['total'];
            }
            return 0;
        } catch (\Exception $e) {
            error_log("Error getting total bookings: " . $e->getMessage());
            return 0;
        }
    }
} 