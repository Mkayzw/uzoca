<?php

namespace app\src;

use app\config\Database;

class LandlordApproval
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function showPendingLandlords()
    {
        $query = "SELECT * FROM landlords WHERE status = 'pending' ORDER BY date_applied DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td class='py-3'>" . date('M d, Y', strtotime($row['date_applied'])) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-600'>Pending</span></td>";
            echo "<td class='py-3'>
                    <div class='flex gap-2'>
                        <button onclick='approveLandlord(" . $row['id'] . ")' class='text-green-500 hover:text-green-600'>
                            <i class='fr fi-rr-check'></i>
                        </button>
                        <button onclick='rejectLandlord(" . $row['id'] . ")' class='text-rose-500 hover:text-rose-600'>
                            <i class='fr fi-rr-cross'></i>
                        </button>
                    </div>
                  </td>";
            echo "</tr>";
        }
    }

    public function showRoomManagement()
    {
        $query = "SELECT r.*, p.name as property_name 
                 FROM rooms r 
                 JOIN properties p ON r.property_id = p.id 
                 ORDER BY p.name, r.room_number";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $status = $row['occupied'] >= $row['capacity'] ? 'Fully Occupied' : 'Available';
            $statusClass = $row['occupied'] >= $row['capacity'] ? 'bg-rose-100 text-rose-600' : 'bg-green-100 text-green-600';
            
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . htmlspecialchars($row['property_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['capacity']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['occupied']) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs " . $statusClass . "'>" . $status . "</span></td>";
            echo "<td class='py-3'>
                    <button onclick='viewRoomDetails(" . $row['id'] . ")' class='text-sky-500 hover:text-sky-600'>
                        <i class='fr fi-rr-eye'></i>
                    </button>
                  </td>";
            echo "</tr>";
        }
    }

    public function showApprovedTenants()
    {
        $query = "SELECT t.*, r.room_number, p.name as property_name 
                 FROM tenants t 
                 JOIN rooms r ON t.room_id = r.id 
                 JOIN properties p ON r.property_id = p.id 
                 WHERE t.status = 'approved' 
                 ORDER BY t.date_approved DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo "<tr class='border-b border-slate-200 dark:border-slate-700'>";
            echo "<td class='py-3'>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['property_name']) . "</td>";
            echo "<td class='py-3'>" . htmlspecialchars($row['room_number']) . "</td>";
            echo "<td class='py-3'>" . date('M d, Y', strtotime($row['date_approved'])) . "</td>";
            echo "<td class='py-3'><span class='px-2 py-1 rounded-full text-xs bg-green-100 text-green-600'>Active</span></td>";
            echo "</tr>";
        }
    }

    public function approveLandlord($landlordId)
    {
        $query = "UPDATE landlords SET status = 'approved', date_approved = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $landlordId);
        return $stmt->execute();
    }

    public function rejectLandlord($landlordId)
    {
        $query = "UPDATE landlords SET status = 'rejected' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $landlordId);
        return $stmt->execute();
    }

    public function updateRoomStatus($roomId)
    {
        $query = "UPDATE rooms r 
                 SET status = CASE 
                    WHEN (SELECT COUNT(*) FROM tenants WHERE room_id = r.id AND status = 'active') >= r.capacity 
                    THEN 'fully_occupied' 
                    ELSE 'available' 
                 END 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $roomId);
        return $stmt->execute();
    }
} 