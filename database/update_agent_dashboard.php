<?php
require_once '../includes/init.php';

// Function to execute SQL file
function executeSQLFile($file) {
    global $conn;
    
    try {
        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $conn->query($statement);
            }
        }
        
        return [
            'success' => true,
            'message' => "SQL file executed successfully: " . basename($file)
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Error executing SQL file: " . $e->getMessage()
        ];
    }
}

// Main update process
echo "<h2>Agent Dashboard Database Update Process</h2>";

// Step 1: Update agent dashboard
echo "<h3>Step 1: Updating Agent Dashboard</h3>";
$agentResult = executeSQLFile(__DIR__ . '/agent_dashboard.sql');
echo "<p>" . $agentResult['message'] . "</p>";

if (!$agentResult['success']) {
    echo "<p style='color: red;'>Agent dashboard update failed.</p>";
    exit;
}

// Final status
echo "<h3>Update Process Complete</h3>";
echo "<p>Please check the results above for any errors.</p>";

// Add verification steps
echo "<h3>Verification Steps</h3>";
echo "<p>To verify the update was successful, check that:</p>";
echo "<ol>";
echo "<li>The agent_properties table exists and has the correct columns</li>";
echo "<li>The agent_commissions table exists and has the correct columns</li>";
echo "<li>The agent_activities table exists and has the correct columns</li>";
echo "<li>The bookings table has been updated with agent_id and commission_amount columns</li>";
echo "</ol>";

// Add troubleshooting section
echo "<h3>Troubleshooting</h3>";
echo "<p>If you encounter any issues:</p>";
echo "<ol>";
echo "<li>Check the database error logs</li>";
echo "<li>Verify that all required tables exist</li>";
echo "<li>Ensure you have the necessary permissions to modify the database</li>";
echo "<li>Check that the agent_dashboard.sql file exists and is readable</li>";
echo "</ol>"; 