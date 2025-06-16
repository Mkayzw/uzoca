<?php

namespace app\src;

use app\config\Database;
use PDO;
use PDOException;

class AgentPayment {
    private $conn;
    private $agentId;
    private $paymentMethods = ['ecocash', 'mukuru', 'innbucks'];

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->agentId = $_SESSION['user_id'] ?? null;
    }

    /**
     * Create a new payment record
     * @param float $amount Payment amount
     * @param string $paymentMethod Payment method (ecocash, mukuru, innbucks)
     * @param string $description Optional payment description
     * @return array|false Returns payment data on success, false on failure
     */
    public function createPayment($amount, $paymentMethod, $description = '') {
        if (!$this->agentId) {
            throw new \Exception('Agent ID not found');
        }

        if (!in_array($paymentMethod, $this->paymentMethods)) {
            throw new \Exception('Invalid payment method');
        }

        try {
            $reference = 'PAY' . time() . rand(1000, 9999);
            
            $query = "INSERT INTO agent_payments (
                agent_id, amount, reference, description, payment_type, payment_method, status
            ) VALUES (
                :agent_id, :amount, :reference, :description, 'subscription', :payment_method, 'pending'
            )";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':agent_id', $this->agentId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':reference', $reference);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':payment_method', $paymentMethod);

            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->lastInsertId(),
                    'reference' => $reference,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'status' => 'pending'
                ];
            }

            return false;
        } catch (PDOException $e) {
            error_log("Error creating payment: " . $e->getMessage());
            throw new \Exception('Failed to create payment record');
        }
    }

    /**
     * Get payment details by reference
     * @param string $reference Payment reference
     * @return array|false Returns payment data on success, false if not found
     */
    public function getPaymentByReference($reference) {
        try {
            $query = "SELECT * FROM agent_payments WHERE reference = :reference AND agent_id = :agent_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reference', $reference);
            $stmt->bindParam(':agent_id', $this->agentId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment status
     * @param string $reference Payment reference
     * @param string $status New status (pending, completed, failed)
     * @return bool Returns true on success, false on failure
     */
    public function updatePaymentStatus($reference, $status) {
        if (!in_array($status, ['pending', 'completed', 'failed'])) {
            throw new \Exception('Invalid status');
        }

        try {
            $query = "UPDATE agent_payments SET 
                     status = :status,
                     completed_at = CASE WHEN :status = 'completed' THEN NOW() ELSE completed_at END
                     WHERE reference = :reference AND agent_id = :agent_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':reference', $reference);
            $stmt->bindParam(':agent_id', $this->agentId);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating payment status: " . $e->getMessage());
            throw new \Exception('Failed to update payment status');
        }
    }

    /**
     * Get payment history for the current agent
     * @param int $limit Number of records to return
     * @return array Returns array of payment records
     */
    public function getPaymentHistory($limit = 5) {
        try {
            $query = "SELECT * FROM agent_payments 
                     WHERE agent_id = :agent_id 
                     ORDER BY created_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':agent_id', $this->agentId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment history: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get payment settings for the current agent
     * @return array|false Returns payment settings on success, false if not found
     */
    public function getPaymentSettings() {
        try {
            $query = "SELECT * FROM agent_payment_settings WHERE agent_id = :agent_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':agent_id', $this->agentId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment settings: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment settings for the current agent
     * @param array $settings Array of payment settings
     * @return bool Returns true on success, false on failure
     */
    public function updatePaymentSettings($settings) {
        try {
            // First check if settings exist
            $checkQuery = "SELECT id FROM agent_payment_settings WHERE agent_id = :agent_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':agent_id', $this->agentId);
            $checkStmt->execute();

            if ($checkStmt->rowCount() === 0) {
                // Insert new settings
                $query = "INSERT INTO agent_payment_settings (
                    agent_id, ecocash_number, ecocash_name, mukuru_number, mukuru_name, 
                    innbucks_number, innbucks_name
                ) VALUES (
                    :agent_id, :ecocash_number, :ecocash_name, :mukuru_number, :mukuru_name,
                    :innbucks_number, :innbucks_name
                )";
            } else {
                // Update existing settings
                $query = "UPDATE agent_payment_settings SET 
                         ecocash_number = :ecocash_number,
                         ecocash_name = :ecocash_name,
                         mukuru_number = :mukuru_number,
                         mukuru_name = :mukuru_name,
                         innbucks_number = :innbucks_number,
                         innbucks_name = :innbucks_name
                         WHERE agent_id = :agent_id";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':agent_id', $this->agentId);
            $stmt->bindParam(':ecocash_number', $settings['ecocash_number']);
            $stmt->bindParam(':ecocash_name', $settings['ecocash_name']);
            $stmt->bindParam(':mukuru_number', $settings['mukuru_number']);
            $stmt->bindParam(':mukuru_name', $settings['mukuru_name']);
            $stmt->bindParam(':innbucks_number', $settings['innbucks_number']);
            $stmt->bindParam(':innbucks_name', $settings['innbucks_name']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating payment settings: " . $e->getMessage());
            throw new \Exception('Failed to update payment settings');
        }
    }
} 