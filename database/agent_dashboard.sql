-- Drop existing tables and their constraints
DROP TABLE IF EXISTS agent_commissions;
DROP TABLE IF EXISTS agent_activities;
DROP TABLE IF EXISTS agent_properties;

-- Drop existing foreign key constraints from bookings table
ALTER TABLE bookings DROP FOREIGN KEY IF EXISTS fk_bookings_agent;
ALTER TABLE bookings DROP FOREIGN KEY IF EXISTS bookings_ibfk_1;
ALTER TABLE bookings DROP FOREIGN KEY IF EXISTS bookings_ibfk_2;
ALTER TABLE bookings DROP FOREIGN KEY IF EXISTS bookings_ibfk_3;

-- Drop existing index on bookings table if it exists
DROP INDEX IF EXISTS idx_bookings_agent ON bookings;

-- Add agent_id, agent_fee and commission_amount to bookings table
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS agent_id INT NULL,
ADD COLUMN IF NOT EXISTS agent_fee DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS commission_amount DECIMAL(10,2) NULL;

-- Create agent_properties table
CREATE TABLE agent_properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    property_id INT NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    INDEX idx_agent_properties_agent (agent_id),
    INDEX idx_agent_properties_property (property_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create agent_commissions table
CREATE TABLE agent_commissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    INDEX idx_agent_commissions_agent (agent_id),
    INDEX idx_agent_commissions_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create agent_activities table
CREATE TABLE agent_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    activity_type ENUM('property_view', 'booking', 'commission') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES users(id),
    INDEX idx_agent_activities_agent (agent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraint to bookings table
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_agent FOREIGN KEY (agent_id) REFERENCES users(id);

-- Create index on bookings table
CREATE INDEX idx_bookings_agent ON bookings(agent_id);

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS after_booking_approval;
DROP TRIGGER IF EXISTS after_agent_activity;

-- Create trigger for commission calculation
CREATE TRIGGER after_booking_approval AFTER UPDATE ON bookings
FOR EACH ROW
INSERT INTO agent_commissions (agent_id, booking_id, amount)
SELECT NEW.agent_id, NEW.id, (NEW.agent_fee * ap.commission_rate / 100)
FROM agent_properties ap
WHERE NEW.status = 'approved' 
AND OLD.status != 'approved' 
AND NEW.agent_id IS NOT NULL
AND ap.property_id = NEW.property_id
AND ap.agent_id = NEW.agent_id
AND ap.status = 'active';

-- Create trigger for activity logging
CREATE TRIGGER after_agent_activity AFTER INSERT ON agent_properties
FOR EACH ROW
INSERT INTO agent_activities (agent_id, activity_type, description)
VALUES (NEW.agent_id, 'property_view', CONCAT('Property ', NEW.property_id, ' added to agent portfolio'));
