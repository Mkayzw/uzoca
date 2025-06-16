<?php
require_once("includes/init.php");

try {
    // Create agent_subscriptions table
    $subscriptionsQuery = "CREATE TABLE IF NOT EXISTS agent_subscriptions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        agent_id INT NOT NULL,
        plan_type ENUM('basic', 'premium', 'enterprise') NOT NULL DEFAULT 'basic',
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status ENUM('pending', 'active', 'cancelled', 'expired') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES users(id)
    )";
    
    $result = $conn->query($subscriptionsQuery);
    if (!$result) {
        throw new Exception("Failed to create agent_subscriptions table");
    }
    
    // Create agent_payments table
    $paymentsQuery = "CREATE TABLE IF NOT EXISTS agent_payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        agent_id INT NOT NULL,
        subscription_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        transaction_id VARCHAR(255) NOT NULL,
        payment_method ENUM('ecocash', 'mukuru', 'innbucks') NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES users(id),
        FOREIGN KEY (subscription_id) REFERENCES agent_subscriptions(id)
    )";
    
    $result = $conn->query($paymentsQuery);
    if (!$result) {
        throw new Exception("Failed to create agent_payments table");
    }
    
    echo "Tables created successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 