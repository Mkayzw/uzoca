-- Drop existing triggers first
DROP TRIGGER IF EXISTS update_property_status;
DROP TRIGGER IF EXISTS generate_booking_code;

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist (in correct order)
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS tenants;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS property_landlords;
DROP TABLE IF EXISTS properties;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Create properties table
CREATE TABLE properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    bedrooms INT NOT NULL,
    bathrooms INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    capacity INT NOT NULL DEFAULT 1,
    status ENUM('available', 'unavailable', 'maintenance') NOT NULL DEFAULT 'available',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create property_landlords table (many-to-many relationship)
CREATE TABLE property_landlords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_property_landlord (property_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    id_number VARCHAR(50) NOT NULL,
    booking_code VARCHAR(50) NOT NULL,
    move_in_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_code (booking_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create tenants table
CREATE TABLE tenants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    id_number VARCHAR(50) NOT NULL,
    booking_code VARCHAR(50) NOT NULL,
    move_in_date DATE NOT NULL,
    status ENUM('active', 'inactive', 'evicted') NOT NULL DEFAULT 'active',
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    UNIQUE KEY unique_tenant_booking (booking_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    payment_date TIMESTAMP NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    reference_number VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reference (reference_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_properties_status ON properties(status);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_tenants_status ON tenants(status);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_property_landlords_user ON property_landlords(user_id);
CREATE INDEX idx_property_landlords_property ON property_landlords(property_id);

-- Create trigger for property status update
CREATE TRIGGER update_property_status
AFTER INSERT ON tenants
FOR EACH ROW
UPDATE properties 
SET status = 'unavailable' 
WHERE id = NEW.property_id 
AND (SELECT COUNT(*) FROM tenants WHERE property_id = NEW.property_id AND status = 'active') >= 
    (SELECT capacity FROM properties WHERE id = NEW.property_id);

-- Create trigger for booking code generation
CREATE TRIGGER generate_booking_code
BEFORE INSERT ON bookings
FOR EACH ROW
SET NEW.booking_code = CONCAT('BK', DATE_FORMAT(NOW(), '%y%m'), LPAD(NEW.id, 4, '0')); 