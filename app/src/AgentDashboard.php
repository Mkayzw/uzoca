<?php

namespace app\src;

error_log('Loaded AgentDashboard: ' . __FILE__);

use app\config\Database;
use PDO;
use app\assets\DB;

class AgentDashboard {
    private $conn;
    private $agentId;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->agentId = $_SESSION['user_id'] ?? null;
    }

    public function getConnection() {
        return $this->conn;
    }

    // ... existing code ...

    public function getPaymentDetails($reference) {
        $query = "SELECT * FROM agent_payments WHERE reference = :reference AND agent_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reference", $reference);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function processPayment($reference, $paymentMethod) {
        // In a real implementation, this would verify the payment with the respective payment provider
        // For now, we'll simulate a successful payment
        
        $query = "SELECT * FROM agent_payments WHERE reference = :reference AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reference", $reference);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Update payment status
            $updateQuery = "UPDATE agent_payments SET 
                          status = 'completed',
                          payment_method = :payment_method,
                          completed_at = NOW()
                          WHERE reference = :reference";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":reference", $reference);
            $updateStmt->bindParam(":payment_method", $paymentMethod);
            
            if ($updateStmt->execute()) {
                // Calculate subscription expiry date
                $months = $payment['amount'] / 5; // $5 per month
                $expiryDate = date('Y-m-d H:i:s', strtotime("+$months months"));
                
                // Create or update subscription
                $subscriptionQuery = "INSERT INTO agent_subscriptions (agent_id, start_date, expiry_date, status) 
                                    VALUES (:agent_id, NOW(), :expiry_date, 'active')
                                    ON DUPLICATE KEY UPDATE 
                                    start_date = NOW(),
                                    expiry_date = :expiry_date,
                                    status = 'active'";
                $subscriptionStmt = $this->conn->prepare($subscriptionQuery);
                $subscriptionStmt->bindParam(":agent_id", $this->agentId);
                $subscriptionStmt->bindParam(":expiry_date", $expiryDate);
                
                if ($subscriptionStmt->execute()) {
                    // Send confirmation email
                    $this->sendPaymentConfirmationEmail($payment);
                    return true;
                }
            }
        }
        
        return false;
    }

    private function sendPaymentConfirmationEmail($payment) {
        // Get agent details
        $query = "SELECT * FROM users WHERE id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate confirmation QR code
        $confirmationData = [
            'reference' => $payment['reference'],
            'amount' => $payment['amount'],
            'date' => $payment['created_at'],
            'status' => 'paid'
        ];
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode(json_encode($confirmationData));

        // Email content
        $subject = "Payment Confirmation - UZOCA";
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .details { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
                    .qr-code { text-align: center; margin: 20px 0; }
                    .footer { text-align: center; padding: 20px; color: #64748b; font-size: 14px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Payment Confirmation</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$agent['name']},</p>
                        <p>Thank you for your payment. Your subscription has been successfully activated.</p>
                        
                        <div class='details'>
                            <h2>Payment Details</h2>
                            <p><strong>Reference:</strong> {$payment['reference']}</p>
                            <p><strong>Amount:</strong> \${$payment['amount']}</p>
                            <p><strong>Date:</strong> " . date('F j, Y H:i', strtotime($payment['created_at'])) . "</p>
                            <p><strong>Status:</strong> Completed</p>
                        </div>

                        <div class='qr-code'>
                            <img src='{$qrCodeUrl}' alt='Payment Confirmation QR Code'>
                            <p>This QR code serves as proof of payment</p>
                        </div>

                        <p>You can now access all features of your subscription. If you have any questions, please don't hesitate to contact us.</p>
                        
                        <p>Best regards,<br>The UZOCA Team</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        // Send email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: UZOCA <noreply@uzoca.com>" . "\r\n";

        mail($agent['email'], $subject, $message, $headers);
    }

    public function checkPaymentStatus($reference) {
        $query = "SELECT status FROM agent_payments WHERE reference = :reference AND agent_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reference", $reference);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            return $payment['status'];
        }

        return null;
    }

    public function showSubscriptionStatus() {
        if (!$this->agentId) return;

        $query = "SELECT * FROM agent_subscriptions WHERE agent_id = :agent_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $subscription['status'];
            if (isset($subscription['expiry_date']) && !empty($subscription['expiry_date'])) {
                $expiryDate = new \DateTime($subscription['expiry_date']);
                $now = new \DateTime();
                $isActive = $status === 'active' && $expiryDate > $now;

                echo '<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">';
                echo '<div>';
                echo '<h3 class="text-lg font-semibold mb-2">Current Plan</h3>';
                echo '<p class="text-slate-600 dark:text-slate-400">Expires on ' . $expiryDate->format('F j, Y') . '</p>';
                echo '</div>';
                echo '<div class="flex items-center gap-2">';
                echo '<span class="px-3 py-1 rounded-full text-sm ' . ($isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">';
                echo $isActive ? 'Active' : 'Expired';
                echo '</span>';
                if (!$isActive) {
                    echo '<a href="/uzoca/agent/subscription.php" class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">Renew Now</a>';
                }
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">';
                echo '<div>';
                echo '<h3 class="text-lg font-semibold mb-2">No Expiry Date</h3>';
                echo '<p class="text-slate-600 dark:text-slate-400">Please contact support.</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">';
            echo '<div>';
            echo '<h3 class="text-lg font-semibold mb-2">No Active Subscription</h3>';
            echo '<p class="text-slate-600 dark:text-slate-400">Subscribe to access all features</p>';
            echo '</div>';
            echo '<a href="/uzoca/agent/subscription.php" class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">Subscribe Now</a>';
            echo '</div>';
        }
    }

    public function generatePayNowQR($amount, $description) {
        // In a real implementation, this would integrate with PayNow API
        // For now, we'll simulate the response
        $reference = 'PAY' . time() . rand(1000, 9999);
        
        // Store pending payment in database
        $query = "INSERT INTO agent_payments (agent_id, amount, reference, description, status) 
                 VALUES (:agent_id, :amount, :reference, :description, 'pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":reference", $reference);
        $stmt->bindParam(":description", $description);
        
        if ($stmt->execute()) {
            // In a real implementation, this would be the actual PayNow QR code URL
            return [
                'qr_code_url' => "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=paynow://$reference",
                'reference' => $reference
            ];
        }
        
        return false;
    }

    public function checkSubscriptionStatus() {
        if (!$this->agentId) return false;

        $query = "SELECT * FROM agent_subscriptions 
                 WHERE agent_id = :agent_id 
                 AND status = 'active' 
                 AND expiry_date > NOW()
                 ORDER BY created_at DESC 
                 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAgentProperties($agentId) {
        $query = "SELECT * FROM properties WHERE agent_id = :agent_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $agentId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProperty($propertyData) {
        $query = "INSERT INTO properties (
            agent_id, 
            title, 
            description, 
            summary,
            location, 
            price, 
            category,
            status,
            main_image,
            additional_images,
            created_at
        ) VALUES (
            :agent_id, 
            :title, 
            :description, 
            :summary,
            :location, 
            :price, 
            :category,
            :status,
            :main_image,
            :additional_images,
            NOW()
        )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $propertyData['agent_id']);
        $stmt->bindParam(":title", $propertyData['title']);
        $stmt->bindParam(":description", $propertyData['description']);
        $stmt->bindParam(":summary", $propertyData['summary']);
        $stmt->bindParam(":location", $propertyData['location']);
        $stmt->bindParam(":price", $propertyData['price']);
        $stmt->bindParam(":category", $propertyData['category']);
        $stmt->bindParam(":status", $propertyData['status']);
        $stmt->bindParam(":main_image", $propertyData['main_image']);
        $stmt->bindParam(":additional_images", $propertyData['additional_images']);

        return $stmt->execute();
    }

    public function deleteProperty($propertyId, $agentId) {
        // First get the property to check ownership and get image URLs
        $query = "SELECT * FROM properties WHERE id = :id AND agent_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $propertyId);
        $stmt->bindParam(":agent_id", $agentId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $property = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete the property
            $deleteQuery = "DELETE FROM properties WHERE id = :id AND agent_id = :agent_id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(":id", $propertyId);
            $deleteStmt->bindParam(":agent_id", $agentId);
            
            if ($deleteStmt->execute()) {
                // Delete the main image if it exists
                if (!empty($property['main_image'])) {
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . $property['main_image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                // Delete additional images if they exist
                if (!empty($property['additional_images'])) {
                    $additionalImages = json_decode($property['additional_images'], true);
                    foreach ($additionalImages as $image) {
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $image;
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }
                return true;
            }
        }
        
        return false;
    }

    public function getAgentBookings($agentId) {
        $query = "SELECT b.*, 
                        t.name as tenant_name, 
                        t.email as tenant_email,
                        p.title as property_name,
                        r.name as room_name,
                        r.price as room_price
                 FROM bookings b 
                 INNER JOIN users t ON b.tenant_id = t.id 
                 INNER JOIN properties p ON b.property_id = p.id 
                 LEFT JOIN rooms r ON b.room_id = r.id 
                 WHERE p.agent_id = :agent_id 
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $agentId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function showPendingBookings() {
        if (!$this->agentId) return;

        $query = "SELECT b.*, p.title as property_title, u.name as tenant_name, u.email as tenant_email 
                 FROM bookings b 
                 JOIN properties p ON b.property_id = p.id 
                 JOIN users u ON b.user_id = u.id 
                 WHERE p.agent_id = :agent_id AND b.status = 'pending'
                 ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($bookings)) {
            echo '<tr><td colspan="7" class="py-4 text-center text-slate-500 dark:text-slate-400">No pending bookings found</td></tr>';
            return;
        }

        foreach ($bookings as $booking) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-4">' . htmlspecialchars($booking['tenant_name']) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($booking['property_title']) . '</td>';
            echo '<td class="py-4">' . htmlspecialchars($booking['room_number'] ?? 'N/A') . '</td>';
            echo '<td class="py-4">' . date('M j, Y', strtotime($booking['created_at'])) . '</td>';
            echo '<td class="py-4">$' . number_format($booking['agent_fee'] ?? 0, 2) . '</td>';
            echo '<td class="py-4"><span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span></td>';
            echo '<td class="py-4">';
            echo '<button onclick="viewBookingDetails(' . $booking['id'] . ')" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">';
            echo '<i class="fr fi-rr-eye mr-1"></i>View</button>';
            echo '</td>';
            echo '</tr>';
        }
    }

    public function showPaymentHistory() {
        if (!$this->agentId) return;

        $query = "SELECT * FROM agent_payments 
                 WHERE agent_id = :agent_id 
                 ORDER BY created_at DESC 
                 LIMIT 5";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($payments)) {
            echo '<tr><td colspan="5" class="py-4 text-center text-slate-500 dark:text-slate-400">No payment history found</td></tr>';
            return;
        }

        foreach ($payments as $payment) {
            echo '<tr class="border-b border-slate-200 dark:border-slate-700">';
            echo '<td class="py-4">' . date('M j, Y', strtotime($payment['created_at'])) . '</td>';
            echo '<td class="py-4">' . ucfirst($payment['payment_type']) . '</td>';
            echo '<td class="py-4">$' . number_format($payment['amount'], 2) . '</td>';
            echo '<td class="py-4"><span class="px-2 py-1 text-xs font-medium rounded-full ' . 
                 ($payment['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                 ($payment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) . 
                 '">' . ucfirst($payment['status']) . '</span></td>';
            echo '<td class="py-4">' . htmlspecialchars($payment['reference']) . '</td>';
            echo '</tr>';
        }
    }

    public function getTotalProperties() {
        if (!$this->agentId) return 0;

        $query = "SELECT COUNT(*) as total FROM properties JOIN property_landlords ON properties.id = property_landlords.property_id WHERE property_landlords.user_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getTotalBookings() {
        if (!$this->agentId) return 0;

        $query = "SELECT COUNT(*) as total FROM bookings b INNER JOIN properties p ON b.property_id = p.id JOIN property_landlords pl ON p.id = pl.property_id WHERE pl.user_id = :agent_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Get the count of pending bookings for the agent
     */
    public function getPendingBookingsCount() {
        if (!$this->agentId) return 0;

        $query = "SELECT COUNT(*) as total FROM bookings b INNER JOIN properties p ON b.property_id = p.id JOIN property_landlords pl ON p.id = pl.property_id WHERE pl.user_id = :agent_id AND b.status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Get the total earnings for the current month
     */
    public function getMonthlyEarnings() {
        if (!$this->agentId) return 0;

        $query = "SELECT COALESCE(SUM(amount), 0) as total 
                 FROM agent_payments 
                 WHERE agent_id = :agent_id 
                 AND status = 'completed' 
                 AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                 AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $this->agentId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function render() {
        $this->renderContent();
    }

    private function renderContent() {
        $currentPage = basename($_SERVER['PHP_SELF']);
        
        switch ($currentPage) {
            case 'dashboard.php':
                $this->renderDashboard();
                break;
            case 'properties.php':
                $this->renderProperties();
                break;
            case 'bookings.php':
                $this->renderBookings();
                break;
            case 'tenants.php':
                $this->renderTenants();
                break;
            case 'profile.php':
                $this->renderProfile();
                break;
            default:
                $this->renderDashboard();
        }
    }

    private function renderDashboard() {
        // Dashboard content is now handled in index.php
        return;
    }

    private function renderProperties() {
        // Properties content is now handled in properties.php
        return;
    }

    private function renderBookings() {
        // Bookings content is now handled in bookings.php
        return;
    }

    private function renderTenants() {
        // Tenants content is now handled in tenants.php
        return;
    }

    private function renderProfile() {
        // Profile content is now handled in profile.php
        return;
    }
} 