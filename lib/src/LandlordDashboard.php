<?php

namespace app\src;

use PDO;
use PDOException;

class LandlordDashboard {
    protected $conn;

    public function __construct() {
        try {
            $database = new \app\config\Database();
            $this->conn = $database->getConnection();
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new PDOException("Database connection failed");
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getTenants($landlordId) {
        try {
            $query = "SELECT t.*, p.title as property_title 
                     FROM tenants t 
                     JOIN properties p ON t.property_id = p.id 
                     JOIN property_landlords pl ON p.id = pl.property_id 
                     WHERE pl.user_id = :landlord_id 
                     ORDER BY t.move_in_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':landlord_id', $landlordId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching tenants: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalProperties() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT COUNT(*) as total 
                 FROM properties p 
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTotalTenants() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT COUNT(*) as total 
                 FROM tenants t 
                 JOIN properties p ON t.property_id = p.id 
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function showRecentTenants() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT t.name, t.id_number, t.move_in_date, t.status, t.booking_code, p.title as property_title 
                 FROM tenants t 
                 JOIN properties p ON t.property_id = p.id 
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id 
                 ORDER BY t.move_in_date DESC 
                 LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            echo '<tr><td colspan="6" class="py-4 text-center text-slate-500 dark:text-slate-400">No recent tenants found</td></tr>';
            return;
        }

        foreach ($result as $tenant) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-3">' . htmlspecialchars($tenant['name']) . '</td>';
            echo '<td class="py-3">' . htmlspecialchars($tenant['property_title']) . '</td>';
            echo '<td class="py-3">' . htmlspecialchars($tenant['id_number']) . '</td>';
            echo '<td class="py-3">' . date('M d, Y', strtotime($tenant['move_in_date'])) . '</td>';
            echo '<td class="py-3">' . htmlspecialchars($tenant['booking_code']) . '</td>';
            echo '<td class="py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full ' . 
                 ($tenant['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . 
                 '">' . ucfirst($tenant['status']) . '</span></td>';
            echo '</tr>';
        }
    }

    public function getPropertyListings() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT p.*, 
                        (SELECT COUNT(*) FROM tenants t WHERE t.property_id = p.id) as current_tenants,
                        (SELECT COUNT(*) FROM bookings b WHERE b.property_id = p.id AND b.status = 'pending') as pending_bookings
                 FROM properties p 
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id 
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSimilarListings($propertyId) {
        $query = "SELECT p.*, u.name as agent_name 
                 FROM properties p 
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 JOIN users u ON pl.user_id = u.id 
                 WHERE p.id != :property_id 
                 AND p.title LIKE (SELECT CONCAT('%', title, '%') FROM properties WHERE id = :property_id)
                 AND p.status = 'available'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function verifyBookingCode($code) {
        $query = "SELECT t.*, p.title as property_title, p.location 
                 FROM tenants t 
                 JOIN properties p ON t.property_id = p.id 
                 WHERE t.booking_code = :code";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updatePropertyStatus($propertyId, $status) {
        $query = "UPDATE properties 
                 SET status = :status 
                 WHERE id = :property_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function showPaymentHistory() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT p.*, t.name as tenant_name 
                 FROM payments p 
                 JOIN tenants t ON p.tenant_id = t.id 
                 JOIN properties pr ON t.property_id = pr.id 
                 JOIN property_landlords pl ON pr.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id 
                 ORDER BY p.payment_date DESC 
                 LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            echo '<tr><td colspan="5" class="py-4 text-center text-slate-500 dark:text-slate-400">No payment history found</td></tr>';
            return;
        }

        foreach ($result as $payment) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-3">' . date('M d, Y', strtotime($payment['payment_date'])) . '</td>';
            echo '<td class="py-3">' . htmlspecialchars($payment['payment_type']) . '</td>';
            echo '<td class="py-3">$' . number_format($payment['amount'], 2) . '</td>';
            echo '<td class="py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full ' . 
                 ($payment['status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') . 
                 '">' . ucfirst($payment['status']) . '</span></td>';
            echo '<td class="py-3">' . htmlspecialchars($payment['reference_number']) . '</td>';
            echo '</tr>';
        }
    }

    public function addPropertyListing($data) {
        $query = "INSERT INTO properties (title, description, price, location, bedrooms, bathrooms, type, status, capacity) 
                 VALUES (:title, :description, :price, :location, :bedrooms, :bathrooms, :type, 'available', :capacity)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $data['title'], \PDO::PARAM_STR);
        $stmt->bindParam(':description', $data['description'], \PDO::PARAM_STR);
        $stmt->bindParam(':price', $data['price'], \PDO::PARAM_STR);
        $stmt->bindParam(':location', $data['location'], \PDO::PARAM_STR);
        $stmt->bindParam(':bedrooms', $data['bedrooms'], \PDO::PARAM_INT);
        $stmt->bindParam(':bathrooms', $data['bathrooms'], \PDO::PARAM_INT);
        $stmt->bindParam(':type', $data['type'], \PDO::PARAM_STR);
        $stmt->bindParam(':capacity', $data['capacity'], \PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $propertyId = $this->conn->lastInsertId();
            
            // Link property to landlord
            $query = "INSERT INTO property_landlords (property_id, user_id) VALUES (:property_id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], \PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }

    public function getPendingBookings() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT b.*, p.title as property_title, p.location,
                        t.name, t.id_number, t.booking_code,
                        b.created_at as booking_date, b.status
                 FROM bookings b 
                 JOIN properties p ON b.property_id = p.id 
                 JOIN tenants t ON b.tenant_id = t.id
                 JOIN property_landlords pl ON p.id = pl.property_id 
                 WHERE pl.user_id = :landlord_id AND b.status = 'pending'
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function approveBooking($bookingId) {
        $query = "UPDATE bookings SET status = 'approved' WHERE id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId, \PDO::PARAM_INT);
            return $stmt->execute();
    }

    public function checkRoomAvailability($propertyId) {
        $query = "SELECT COUNT(*) as total_rooms
                 FROM rooms r 
                 WHERE r.property_id = :property_id
                 AND r.status = 'available'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['total_rooms'] > 0;
    }

    public function updateSimilarListings($propertyId) {
        $query = "UPDATE properties p1
                 JOIN properties p2 ON p1.title LIKE CONCAT('%', p2.title, '%')
                 SET p1.status = 'unavailable'
                 WHERE p2.id = :property_id
                 AND NOT EXISTS (
                     SELECT 1 FROM rooms r 
                     WHERE r.property_id = p1.id 
                     AND r.status = 'available'
                 )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getMonthlyIncome() {
        $landlordId = $_SESSION['user_id'];
        $query = "SELECT SUM(p.price * (SELECT COUNT(*) FROM tenants t WHERE t.property_id = p.id AND t.status = 'active')) as total_income
                 FROM properties p
                 JOIN property_landlords pl ON p.id = pl.property_id
                 WHERE pl.user_id = :landlord_id
                 AND MONTH(p.created_at) = MONTH(CURRENT_DATE())
                 AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':landlord_id', $landlordId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_income'] ?? 0;
    }

    public function findMatchingProperty($address) {
        $query = "SELECT p.*, pl.user_id as owner_id 
                 FROM properties p
                 JOIN property_landlords pl ON p.id = pl.property_id
                 WHERE p.location LIKE :address LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':address', $address, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function addTenantFromAgentApproval($tenantData, $propertyId) {
        $query = "INSERT INTO tenants (name, id_number, property_id, booking_code, move_in_date, status)
                 VALUES (:name, :id_number, :property_id, :booking_code, :move_in_date, 'active')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $tenantData['name'], \PDO::PARAM_STR);
        $stmt->bindParam(':id_number', $tenantData['id_number'], \PDO::PARAM_STR);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->bindParam(':booking_code', $tenantData['booking_code'], \PDO::PARAM_STR);
        $stmt->bindParam(':move_in_date', $tenantData['move_in_date'], \PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function handleAgentBookingApproval($bookingData) {
        try {
            $this->conn->beginTransaction();

            // Find matching property by address
            $matchingProperty = $this->findMatchingProperty($bookingData['address']);
            
            if (!$matchingProperty) {
                throw new \PDOException("No matching property found for this address");
            }

            // Add tenant to the matching property
            $tenantData = [
                'name' => $bookingData['tenant_name'],
                'id_number' => $bookingData['id_number'],
                'booking_code' => $bookingData['booking_code'],
                'move_in_date' => $bookingData['move_in_date']
            ];

            if (!$this->addTenantFromAgentApproval($tenantData, $matchingProperty['id'])) {
                throw new \PDOException("Failed to add tenant to property");
            }

            // Update the booking status
            $query = "UPDATE bookings 
                     SET status = 'approved', 
                         property_id = :property_id,
                         approved_at = NOW()
                     WHERE id = :booking_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property_id', $matchingProperty['id'], \PDO::PARAM_INT);
            $stmt->bindParam(':booking_id', $bookingData['booking_id'], \PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new \PDOException("Failed to update booking status");
            }

            $this->conn->commit();
            return [
                'success' => true,
                'property_id' => $matchingProperty['id']
            ];
        } catch (\PDOException $e) {
            $this->conn->rollBack();
            error_log("Error handling agent booking approval: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getPropertyIncome($propertyId) {
        $query = "SELECT SUM(amount) as total_income FROM payments WHERE property_id = :property_id AND status = 'completed'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_income'] ?? 0;
    }

    private function updatePropertyStatusIfFull($propertyId) {
        $query = "SELECT COUNT(*) as occupied_rooms FROM tenants WHERE property_id = :property_id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->execute();
        $occupiedRooms = $stmt->fetch(PDO::FETCH_ASSOC)['occupied_rooms'];

        $query = "SELECT capacity FROM properties WHERE id = :property_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':property_id', $propertyId, \PDO::PARAM_INT);
        $stmt->execute();
        $capacity = $stmt->fetch(PDO::FETCH_ASSOC)['capacity'];

        if ($occupiedRooms >= $capacity) {
            $this->updatePropertyStatus($propertyId, 'unavailable');
        }
    }
} 