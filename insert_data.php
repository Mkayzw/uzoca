<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("includes/init.php");

use app\assets\DB;

try {
    $db = DB::getInstance();
    
    // Insert default plans
    $plans = [
        [
            'name' => 'Basic Plan',
            'description' => 'Basic features for new agents',
            'price' => 29.99,
            'duration' => 30,
            'features' => json_encode([
                'Up to 5 properties',
                'Basic support',
                'Standard listing'
            ])
        ],
        [
            'name' => 'Professional Plan',
            'description' => 'Advanced features for professional agents',
            'price' => 49.99,
            'duration' => 30,
            'features' => json_encode([
                'Up to 20 properties',
                'Priority support',
                'Featured listings',
                'Analytics dashboard'
            ])
        ],
        [
            'name' => 'Enterprise Plan',
            'description' => 'Premium features for large agencies',
            'price' => 99.99,
            'duration' => 30,
            'features' => json_encode([
                'Unlimited properties',
                '24/7 support',
                'Premium listings',
                'Advanced analytics',
                'API access'
            ])
        ]
    ];
    
    foreach ($plans as $plan) {
        $db->prepare(
            "INSERT IGNORE INTO plans (name, description, price, duration, features) VALUES (?, ?, ?, ?, ?)",
            "ssdds",
            $plan['name'],
            $plan['description'],
            $plan['price'],
            $plan['duration'],
            $plan['features']
        );
    }
    
    // Insert test agent if not exists
    $agentPassword = password_hash('agent123', PASSWORD_DEFAULT);
    $db->prepare(
        "INSERT IGNORE INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)",
        "sssss",
        'Test Agent',
        '+1234567890',
        'agent@test.com',
        $agentPassword,
        'agent'
    );
    
    // Get the agent ID
    $result = $db->prepare(
        "SELECT id FROM users WHERE email = ?",
        "s",
        'agent@test.com'
    );
    $agent = $result->fetch_assoc();
    
    if ($agent) {
        // Insert test property
        $db->prepare(
            "INSERT IGNORE INTO properties (name, description, address, agent_id) VALUES (?, ?, ?, ?)",
            "sssi",
            'Test Property',
            'A beautiful test property',
            '123 Test Street, Test City',
            $agent['id']
        );
        
        // Get the property ID
        $result = $db->prepare(
            "SELECT id FROM properties WHERE name = ?",
            "s",
            'Test Property'
        );
        $property = $result->fetch_assoc();
        
        if ($property) {
            // Insert test rooms
            $rooms = [
                [
                    'name' => 'Room 101',
                    'description' => 'A cozy single room',
                    'price' => 99.99
                ],
                [
                    'name' => 'Room 102',
                    'description' => 'A spacious double room',
                    'price' => 149.99
                ]
            ];
            
            foreach ($rooms as $room) {
                $db->prepare(
                    "INSERT IGNORE INTO rooms (property_id, name, description, price) VALUES (?, ?, ?, ?)",
                    "issd",
                    $property['id'],
                    $room['name'],
                    $room['description'],
                    $room['price']
                );
            }
        }
    }
    
    echo "<h1>Initial Data Inserted Successfully</h1>";
    echo "<p>Default plans and test data have been inserted into the database.</p>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
} 