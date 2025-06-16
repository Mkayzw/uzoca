<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection without database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<h1>Database Setup</h1>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS uzoca";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Database created or already exists</p>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db("uzoca");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'agent', 'landlord') NOT NULL DEFAULT 'landlord',
        phone VARCHAR(20),
        profile_pic VARCHAR(255) DEFAULT 'profile-pic.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Users table created or already exists</p>";
    } else {
        throw new Exception("Error creating users table: " . $conn->error);
    }
    
    // Create agent_payment_settings table
    $sql = "CREATE TABLE IF NOT EXISTS agent_payment_settings (
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
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Agent payment settings table created or already exists</p>";
    } else {
        throw new Exception("Error creating agent_payment_settings table: " . $conn->error);
    }
    
    // Create agent_subscriptions table
    $sql = "CREATE TABLE IF NOT EXISTS agent_subscriptions (
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
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Agent subscriptions table created or already exists</p>";
    } else {
        throw new Exception("Error creating agent_subscriptions table: " . $conn->error);
    }
    
    // Create agent_payments table
    $sql = "CREATE TABLE IF NOT EXISTS agent_payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        agent_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method ENUM('ecocash', 'mukuru', 'innbucks') NOT NULL,
        reference_number VARCHAR(50) NOT NULL,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (agent_id) REFERENCES users(id)
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Agent payments table created or already exists</p>";
    } else {
        throw new Exception("Error creating agent_payments table: " . $conn->error);
    }
    
    echo "<p style='color: green;'>Database setup completed successfully!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
} 