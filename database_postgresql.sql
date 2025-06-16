
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'landlord' CHECK (role IN ('admin', 'agent', 'landlord')),
    phone VARCHAR(20),
    profile_pic VARCHAR(255) DEFAULT 'profile-pic.jpg',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Create agent_payment_settings table
CREATE TABLE IF NOT EXISTS agent_payment_settings (
    id SERIAL PRIMARY KEY,
    agent_id INTEGER NOT NULL,
    ecocash_number VARCHAR(20),
    ecocash_name VARCHAR(255),
    mukuru_number VARCHAR(20),
    mukuru_name VARCHAR(255),
    innbucks_number VARCHAR(20),
    innbucks_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (agent_id) REFERENCES users(id)
);

-- Create agent_subscriptions table
CREATE TABLE IF NOT EXISTS agent_subscriptions (
    id SERIAL PRIMARY KEY,
    agent_id INTEGER NOT NULL,
    plan_type VARCHAR(20) NOT NULL DEFAULT 'basic' CHECK (plan_type IN ('basic', 'premium', 'enterprise')),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'active', 'cancelled', 'expired')),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (agent_id) REFERENCES users(id)
);

-- Create agent_payments table
CREATE TABLE IF NOT EXISTS agent_payments (
    id SERIAL PRIMARY KEY,
    agent_id INTEGER NOT NULL,
    subscription_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    payment_method VARCHAR(20) NOT NULL CHECK (payment_method IN ('ecocash', 'mukuru', 'innbucks')),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed')),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (agent_id) REFERENCES users(id),
    FOREIGN KEY (subscription_id) REFERENCES agent_subscriptions(id)
);

-- Create properties table
CREATE TABLE IF NOT EXISTS properties (
    id SERIAL PRIMARY KEY,
    landlord_id INTEGER NOT NULL,
    agent_id INTEGER,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    address VARCHAR(500) NOT NULL,
    city VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    bedrooms INTEGER NOT NULL,
    bathrooms INTEGER NOT NULL,
    property_type VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'available' CHECK (status IN ('available', 'occupied', 'maintenance')),
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (landlord_id) REFERENCES users(id),
    FOREIGN KEY (agent_id) REFERENCES users(id)
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id SERIAL PRIMARY KEY,
    property_id INTEGER NOT NULL,
    tenant_id INTEGER NOT NULL,
    agent_id INTEGER,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed')),
    payment_status VARCHAR(20) DEFAULT 'unpaid' CHECK (payment_status IN ('unpaid', 'partial', 'paid')),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (tenant_id) REFERENCES users(id),
    FOREIGN KEY (agent_id) REFERENCES users(id)
);

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Create rooms table (if you have specific room management)
CREATE TABLE IF NOT EXISTS rooms (
    id SERIAL PRIMARY KEY,
    property_id INTEGER NOT NULL,
    room_number VARCHAR(50),
    room_type VARCHAR(50),
    price DECIMAL(10,2),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    FOREIGN KEY (property_id) REFERENCES properties(id)
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_properties_landlord ON properties(landlord_id);
CREATE INDEX IF NOT EXISTS idx_properties_agent ON properties(agent_id);
CREATE INDEX IF NOT EXISTS idx_properties_status ON properties(status);
CREATE INDEX IF NOT EXISTS idx_bookings_property ON bookings(property_id);
CREATE INDEX IF NOT EXISTS idx_bookings_tenant ON bookings(tenant_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);

-- Insert default admin user (update password hash as needed)
INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@uzoca.com', '$2y$10$example_hash_here', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Create update trigger function for updated_at columns
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_properties_updated_at BEFORE UPDATE ON properties FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_bookings_updated_at BEFORE UPDATE ON bookings FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_agent_payment_settings_updated_at BEFORE UPDATE ON agent_payment_settings FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_agent_subscriptions_updated_at BEFORE UPDATE ON agent_subscriptions FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_agent_payments_updated_at BEFORE UPDATE ON agent_payments FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_rooms_updated_at BEFORE UPDATE ON rooms FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
