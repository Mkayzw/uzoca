<?php
require_once("includes/init.php");

try {
    // Create agent_payment_settings table
    $query = "CREATE TABLE IF NOT EXISTS agent_payment_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        agent_id INT NOT NULL,
        ecocash_number VARCHAR(20),
        ecocash_name VARCHAR(255),
        mukuru_number VARCHAR(20),
        mukuru_name VARCHAR(255),
        innbucks_number VARCHAR(20),
        innbucks_name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES users(id)
    )";

    $result = $conn->query($query);
    
    if ($result) {
        echo "Agent payment settings table created successfully!";
    } else {
        throw new Exception("Failed to create agent payment settings table");
    }
} catch (Exception $e) {
    error_log("Error creating agent payment settings table: " . $e->getMessage());
    echo "Error creating agent payment settings table: " . $e->getMessage();
} 