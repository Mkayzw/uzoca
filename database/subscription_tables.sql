-- Table for agent subscriptions
CREATE TABLE IF NOT EXISTS agent_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    start_date DATETIME NOT NULL,
    expiry_date DATETIME NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_active_subscription (agent_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for agent payments
CREATE TABLE IF NOT EXISTS agent_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reference VARCHAR(50) NOT NULL,
    description TEXT,
    payment_method ENUM('ecocash', 'mukuru', 'innbucks') NOT NULL,
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reference (reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add subscription check to bookings table
ALTER TABLE bookings
ADD COLUMN is_agent_booking BOOLEAN DEFAULT FALSE,
ADD COLUMN agent_fee DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN payment_method ENUM('ecocash', 'mukuru', 'innbucks') DEFAULT NULL,
ADD COLUMN payment_reference VARCHAR(50) DEFAULT NULL,
ADD COLUMN payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
ADD COLUMN payment_completed_at DATETIME DEFAULT NULL; 