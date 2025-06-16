<?php

namespace app\src;

use app\config\Database;

class AgentDashboard
{
    private $conn;
    private $agentId;
    private $agentFee = 20; // $20 agent fee

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->agentId = $_SESSION['user_id'] ?? null;
    }

    public function showSubscriptionStatus()
    {
        $query = "SELECT * FROM agent_subscriptions WHERE agent_id = :agent_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agent_id', $this->agentId);
        $stmt->execute();
        $subscription = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$subscription || strtotime($subscription['expires_at']) < time()) {
            echo "<div class='bg-rose-100 text-rose-600 p-4 rounded-lg'>
                    <p class='font-semibold'>Subscription Expired</p>
                    <p class='text-sm mt-1'>Your subscription has expired. Please renew to continue using the platform.</p>
                    <a href='/agent/subscription.php' class='inline-block mt-2 text-rose-600 hover:text-rose-700 font-medium'>
                        Renew Subscription
                    </a>
                  </div>";
        } else {
            $daysLeft = ceil((strtotime($subscription['expires_at']) - time()) / (60 * 60 * 24));
            echo "<div class='bg-green-100 text-green-600 p-4 rounded-lg'>
                    <p class='font-semibold'>Active Subscription</p>
                    <p class='text-sm mt-1'>Your subscription is active. {$daysLeft} days remaining.</p>
                    <a href='/agent/subscription.php' class='inline-block mt-2 text-green-600 hover:text-green-700 font-medium'>
                        View Details
                    </a>
                  </div>";
        }
    }

    public function showPendingBookings()
    {
        $query = "SELECT b.*, t.name as tenant_name, p.name as property_name, r.room_number 
                 FROM bookings b 
                 JOIN tenants t ON b.tenant_id = t.id 
                 JOIN rooms r ON b.room_id = r.id 
                 JOIN properties p ON r.property_id = p.id 
                 WHERE b.agent_id = :agent_id AND b.status = 'pending' 
                 ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agent_id', $this->agentId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . htmlspecialchars($row['tenant_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['property_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td class='py-3'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
            echo "<td class='py-3'>$" . number_format($this->agentFee, 2) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-600'>Pending</span></td>";
            echo "<td class='py-3'>
                    <div class='flex gap-2'>
                        <button onclick='approveBooking(" . $row['id'] . ")' class='text-green-500 hover:text-green-600'>
                            <i class='fr fi-rr-check'></i>
                        </button>
                        <button onclick='rejectBooking(" . $row['id'] . ")' class='text-rose-500 hover:text-rose-600'>
                            <i class='fr fi-rr-cross'></i>
                        </button>
                    </div>
                  </td>";
            echo "</tr>";
        }
    }

    public function showApprovedBookings()
    {
        $query = "SELECT b.*, t.name as tenant_name, p.name as property_name, r.room_number 
                 FROM bookings b 
                 JOIN tenants t ON b.tenant_id = t.id 
                 JOIN rooms r ON b.room_id = r.id 
                 JOIN properties p ON r.property_id = p.id 
                 WHERE b.agent_id = :agent_id AND b.status = 'approved' 
                 ORDER BY b.updated_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agent_id', $this->agentId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . htmlspecialchars($row['tenant_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['property_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td class='py-3'>" . date('M d, Y', strtotime($row['updated_at'])) . "</td>";
            echo "<td class='py-3'>$" . number_format($this->agentFee, 2) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs bg-green-100 text-green-600'>Approved</span></td>";
            echo "</tr>";
        }
    }

    public function showPaymentHistory()
    {
        $query = "SELECT * FROM payments 
                 WHERE agent_id = :agent_id 
                 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agent_id', $this->agentId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $statusClass = $row['status'] === 'completed' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600';
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
            echo "<td class='py-3'>" . ucfirst($row['type']) . "</td>";
            echo "<td class='py-3'>$" . number_format($row['amount'], 2) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs " . $statusClass . "'>" . ucfirst($row['status']) . "</span></td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['reference']) . "</td>";
            echo "</tr>";
        }
    }

    public function approveBooking($bookingId)
    {
        $query = "UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = :id AND agent_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bookingId);
        $stmt->bindParam(':agent_id', $this->agentId);
        return $stmt->execute();
    }

    public function rejectBooking($bookingId)
    {
        $query = "UPDATE bookings SET status = 'rejected', updated_at = NOW() WHERE id = :id AND agent_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bookingId);
        $stmt->bindParam(':agent_id', $this->agentId);
        return $stmt->execute();
    }

    public function updateSubscription($months)
    {
        $amount = $months * 5; // $5 per month
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$months} months"));

        $query = "INSERT INTO agent_subscriptions (agent_id, amount, months, expires_at) 
                 VALUES (:agent_id, :amount, :months, :expires_at)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':agent_id', $this->agentId);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':months', $months);
        $stmt->bindParam(':expires_at', $expiresAt);
        return $stmt->execute();
    }
} 