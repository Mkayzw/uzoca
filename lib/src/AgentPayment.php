<?php
namespace app\src;

use app\assets\DB;

class AgentPayment {
    private $conn;
    private $table = 'agent_payments';
    private $settings_table = 'agent_payment_settings';
    private $subscription_table = 'agent_subscriptions';

    public function __construct() {
        $this->conn = DB::getInstance();
    }

    public function createPayment($amount, $method, $description) {
        try {
            $agent_id = $_SESSION['user_id'];
            $reference = 'PAY-' . time() . '-' . rand(1000, 9999);
            
            $stmt = $this->conn->prepare("INSERT INTO agent_payments (agent_id, amount, payment_method, description, reference, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("idsss", $agent_id, $amount, $method, $description, $reference);
            
            if ($stmt->execute()) {
                return [
                    'id' => $this->conn->insert_id,
                    'reference' => $reference
                ];
            }
            
            throw new \Exception("Failed to create payment: " . $stmt->error);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getPaymentByReference($reference) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM agent_payments WHERE reference = ?");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $reference);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updatePaymentStatus($reference, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE agent_payments SET status = ? WHERE reference = ?");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("ss", $status, $reference);
            return $stmt->execute();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getPaymentSettings() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM payment_settings WHERE id = 1");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updatePaymentSettings($monthly_fee, $yearly_fee) {
        try {
            $stmt = $this->conn->prepare("UPDATE payment_settings SET monthly_fee = ?, yearly_fee = ? WHERE id = 1");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("dd", $monthly_fee, $yearly_fee);
            return $stmt->execute();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createSubscription($payment_id, $months) {
        try {
            $agent_id = $_SESSION['user_id'];
            $stmt = $this->conn->prepare("INSERT INTO agent_subscriptions (agent_id, payment_id, months, start_date, end_date, status) VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? MONTH), 'active')");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("iiii", $agent_id, $payment_id, $months, $months);
            return $stmt->execute();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getActiveSubscription($agent_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM agent_subscriptions WHERE agent_id = ? AND end_date > NOW() AND status = 'active' ORDER BY end_date DESC LIMIT 1");
            if (!$stmt) {
                throw new \Exception("Database error: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $agent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }
            
            return $result->fetch_assoc();
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
} 