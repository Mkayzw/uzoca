<?php

namespace app\src;

use app\assets\DB;

class BookingPayment {
    private $conn;
    private $paymentMethods = ['ecocash', 'mukuru', 'innbucks'];
    private $agentFee = 20.00; // $20 for agent listings when booked through agent
    private $adminFee = 15.00; // $15 for admin bookings
    private $agentCommission = 5.00; // $5 commission when agent listing is booked through admin

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Create a new booking payment
     * @param int $propertyId Property ID
     * @param int $userId User ID (tenant)
     * @param string $paymentMethod Payment method
     * @param bool $isAgentBooking Whether the booking is made through agent
     * @return array|false Returns payment data on success, false on failure
     */
    public function createBookingPayment($propertyId, $userId, $paymentMethod, $isAgentBooking = false) {
        try {
            // Get property details to determine if it's an agent or admin listing
            $property = $this->getPropertyDetails($propertyId);
            if (!$property) {
                throw new \Exception('Property not found');
            }

            // Determine fee based on property type and booking method
            if ($property['agent_id']) {
                // Agent property
                if ($isAgentBooking) {
                    $fee = $this->agentFee; // Full $20 goes to agent
                    $commission = 0; // No commission needed as agent gets full fee
                } else {
                    $fee = $this->adminFee; // $15 for admin booking
                    $commission = $this->agentCommission; // $5 commission for agent
                }
            } else {
                // Admin/Landlord property
                $fee = $this->adminFee; // $15 for admin listings
                $commission = 0; // No commission for admin properties
            }
            
            // Generate unique reference
            $reference = 'BOOK-' . time() . '-' . rand(1000, 9999);
            
            // Create payment record
            $query = "INSERT INTO bookings (
                user_id, property_id, agent_id, agent_fee, commission_amount, payment_method, 
                payment_reference, payment_status, is_agent_booking, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiiddssi", 
                $userId, 
                $propertyId, 
                $property['agent_id'], 
                $fee,
                $commission,
                $paymentMethod,
                $reference,
                $isAgentBooking
            );

            if ($stmt->execute()) {
                $bookingId = $this->conn->insert_id;

                // If it's an agent property and not booked through agent, create commission record
                if ($property['agent_id'] && !$isAgentBooking && $commission > 0) {
                    $this->createAgentCommission($property['agent_id'], $bookingId, $commission);
                }

                // Get admin's PayNow account details for the selected payment method
                $adminAccount = $this->getAdminPayNowAccount($paymentMethod);
                
                // Generate PayNow QR code with admin's account details
                $qrCode = $this->generatePayNowQR($reference, $fee, $paymentMethod, $adminAccount);
                
                return [
                    'id' => $bookingId,
                    'reference' => $reference,
                    'amount' => $fee,
                    'qr_code' => $qrCode,
                    'payment_method' => $paymentMethod,
                    'is_agent_booking' => $isAgentBooking,
                    'admin_account' => $adminAccount
                ];
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error creating booking payment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get property details
     * @param int $propertyId Property ID
     * @return array|false Property details or false if not found
     */
    private function getPropertyDetails($propertyId) {
        $query = "SELECT id, agent_id, title FROM properties WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get admin's PayNow account details for the selected payment method
     * @param string $paymentMethod Payment method
     * @return array|false Admin account details or false if not found
     */
    private function getAdminPayNowAccount($paymentMethod) {
        $query = "SELECT account_number, account_name 
                 FROM admin_payment_accounts 
                 WHERE payment_method = ? AND status = 'active' 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $paymentMethod);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Generate PayNow QR code
     * @param string $reference Payment reference
     * @param float $amount Payment amount
     * @param string $paymentMethod Payment method
     * @param array $adminAccount Admin's PayNow account details
     * @return string QR code URL
     */
    private function generatePayNowQR($reference, $amount, $paymentMethod, $adminAccount) {
        // In a real implementation, this would integrate with PayNow API
        // For now, we'll simulate the response with admin's account details
        $data = [
            'reference' => $reference,
            'amount' => $amount,
            'method' => $paymentMethod,
            'account' => $adminAccount['account_number'],
            'name' => $adminAccount['account_name']
        ];
        
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode(json_encode($data));
    }

    /**
     * Create agent commission record
     * @param int $agentId Agent ID
     * @param int $bookingId Booking ID
     * @param float $amount Commission amount
     * @return bool Success status
     */
    private function createAgentCommission($agentId, $bookingId, $amount) {
        $query = "INSERT INTO agent_commissions (
            agent_id, booking_id, amount, status, created_at
        ) VALUES (?, ?, ?, 'pending', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iid", $agentId, $bookingId, $amount);
        return $stmt->execute();
    }

    /**
     * Update payment status
     * @param string $reference Payment reference
     * @param string $status New status
     * @return bool Success status
     */
    public function updatePaymentStatus($reference, $status) {
        try {
            $this->conn->begin_transaction();

            // Update booking payment status
            $query = "UPDATE bookings SET 
                     payment_status = ?,
                     payment_completed_at = CASE WHEN ? = 'completed' THEN NOW() ELSE NULL END
                     WHERE payment_reference = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sss", $status, $status, $reference);
            $stmt->execute();

            if ($status === 'completed') {
                // Get booking details
                $query = "SELECT id, agent_id, is_agent_booking, agent_fee, commission_amount 
                         FROM bookings WHERE payment_reference = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param("s", $reference);
                $stmt->execute();
                $booking = $stmt->get_result()->fetch_assoc();

                if ($booking && $booking['agent_id']) {
                    if ($booking['is_agent_booking']) {
                        // If booked through agent, update agent's balance with full fee
                        $this->updateAgentBalance($booking['agent_id'], $booking['agent_fee']);
                    } else if ($booking['commission_amount'] > 0) {
                        // If booked through admin, update commission status
                        $query = "UPDATE agent_commissions SET 
                                 status = 'paid',
                                 payment_date = NOW()
                                 WHERE booking_id = ?";
                        $stmt = $this->conn->prepare($query);
                        $stmt->bind_param("i", $booking['id']);
                        $stmt->execute();

                        // Update agent's balance with commission
                        $this->updateAgentBalance($booking['agent_id'], $booking['commission_amount']);
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            error_log("Error updating payment status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update agent's balance
     * @param int $agentId Agent ID
     * @param float $amount Amount to add
     * @return bool Success status
     */
    private function updateAgentBalance($agentId, $amount) {
        $query = "UPDATE agent_balances SET 
                 balance = balance + ?,
                 updated_at = NOW()
                 WHERE agent_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $amount, $agentId);
        return $stmt->execute();
    }

    /**
     * Get payment details
     * @param string $reference Payment reference
     * @return array|false Payment details or false if not found
     */
    public function getPaymentDetails($reference) {
        $query = "SELECT b.*, p.title as property_title, 
                        u.name as tenant_name, u.email as tenant_email,
                        a.name as agent_name, a.email as agent_email
                 FROM bookings b
                 JOIN properties p ON b.property_id = p.id
                 JOIN users u ON b.user_id = u.id
                 LEFT JOIN users a ON b.agent_id = a.id
                 WHERE b.payment_reference = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $reference);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function createTenantFromBooking($bookingId, $paymentId) {
        try {
            // Get booking details including user information
            $query = "SELECT b.*, u.name, u.email, u.phone, u.address, u.profile_picture
                     FROM bookings b
                     JOIN users u ON b.user_id = u.id
                     WHERE b.id = :booking_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->execute();
            
            $booking = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$booking) {
                throw new \Exception("Booking not found");
            }

            // Check if user already exists as a tenant
            $checkQuery = "SELECT id FROM users WHERE email = :email AND role = 'tenant'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $booking['email']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                // User already exists as a tenant, just update their information
                $updateQuery = "UPDATE users 
                              SET name = :name,
                                  phone = :phone,
                                  address = :address,
                                  profile_picture = :profile_picture,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE email = :email AND role = 'tenant'";
                
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':name', $booking['name']);
                $updateStmt->bindParam(':phone', $booking['phone']);
                $updateStmt->bindParam(':address', $booking['address']);
                $updateStmt->bindParam(':profile_picture', $booking['profile_picture']);
                $updateStmt->bindParam(':email', $booking['email']);
                $updateStmt->execute();
                
                return true;
            }

            // Create new tenant user
            $insertQuery = "INSERT INTO users (name, email, phone, address, profile_picture, role, created_at, updated_at)
                           VALUES (:name, :email, :phone, :address, :profile_picture, 'tenant', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':name', $booking['name']);
            $insertStmt->bindParam(':email', $booking['email']);
            $insertStmt->bindParam(':phone', $booking['phone']);
            $insertStmt->bindParam(':address', $booking['address']);
            $insertStmt->bindParam(':profile_picture', $booking['profile_picture']);
            $insertStmt->execute();
            
            // Get the new tenant's ID
            $tenantId = $this->conn->lastInsertId();
            
            // Update the booking with the new tenant ID
            $updateBookingQuery = "UPDATE bookings SET user_id = :tenant_id WHERE id = :booking_id";
            $updateBookingStmt = $this->conn->prepare($updateBookingQuery);
            $updateBookingStmt->bindParam(':tenant_id', $tenantId);
            $updateBookingStmt->bindParam(':booking_id', $bookingId);
            $updateBookingStmt->execute();
            
            return true;
        } catch (\Exception $e) {
            error_log("Error creating tenant: " . $e->getMessage());
            return false;
        }
    }

    public function processPayment($bookingId, $amount, $paymentMethod, $referenceNumber) {
        try {
            $this->conn->beginTransaction();

            // Insert payment record
            $query = "INSERT INTO booking_payments (booking_id, reference_number, amount, payment_method, status, created_at)
                     VALUES (:booking_id, :reference_number, :amount, :payment_method, 'completed', CURRENT_TIMESTAMP)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->bindParam(':reference_number', $referenceNumber);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->execute();
            
            $paymentId = $this->conn->lastInsertId();

            // Update booking status
            $updateBookingQuery = "UPDATE bookings SET status = 'active' WHERE id = :booking_id";
            $updateBookingStmt = $this->conn->prepare($updateBookingQuery);
            $updateBookingStmt->bindParam(':booking_id', $bookingId);
            $updateBookingStmt->execute();

            // Create or update tenant
            $this->createTenantFromBooking($bookingId, $paymentId);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            error_log("Error processing payment: " . $e->getMessage());
            return false;
        }
    }
} 