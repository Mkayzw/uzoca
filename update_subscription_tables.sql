-- Drop existing tables if they exist
DROP TABLE IF EXISTS agent_payments;
DROP TABLE IF EXISTS agent_subscriptions;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS plans;

-- Create subscription_plans table
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    duration_months INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    features TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create subscriptions table
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    plan_id INT NOT NULL,
    status ENUM('active', 'cancelled', 'expired') DEFAULT 'active',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- Create agent_payments table
CREATE TABLE IF NOT EXISTS agent_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('ecocash', 'mukuru', 'innbucks') NOT NULL,
    description TEXT,
    reference VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id)
);

-- Insert default subscription plans
INSERT INTO subscription_plans (name, description, duration_months, price, features) VALUES
('Basic Monthly', 'Basic plan for 1 month', 1, 5.00, '["Full access to listings", "Booking management", "Payment processing", "Basic support"]'),
('Basic Quarterly', 'Basic plan for 3 months', 3, 15.00, '["Full access to listings", "Booking management", "Payment processing", "Priority support", "Analytics dashboard"]'),
('Basic Semi-Annual', 'Basic plan for 6 months', 6, 30.00, '["Full access to listings", "Booking management", "Payment processing", "Premium support", "Advanced analytics", "Custom reports"]'); 